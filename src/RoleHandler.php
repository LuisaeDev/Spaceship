<?php

namespace LuisaeDev\Spaceship;

use LuisaeDev\Spaceship\Models\SpaceshipRole as RoleModel;
use LuisaeDev\Spaceship\Exceptions\SpaceshipException;

class RoleHandler extends SharedCollection {

    /** @param array Exposed public properties definition */
    private array $properties = ['id', 'name'];

    /**
     * Constructor.
     *
     * @param int|string $roleId Role identifier for obtain the role model
     */
    public function __construct(int|string $roleId)
    {

        // Search the model for its 'id' or 'name' at the shared collection
        if (is_int($roleId)) {
            $this->useModel(['id', $roleId]);
        } else {
            $this->useModel(['name', $roleId]);
        }

        // Obtain the model from the DB
        if (! $this->hasModel()) {

            // Search for the 'id' or 'name' column
            $query = RoleModel::query();
            if (is_int($roleId)) {
                $query->where('id', $roleId);
            } else {
                $query->where('name', $roleId);
            }
            $model = $query->first();

            // Store the model at the shared collection
            if ($model) {
                $this->addModel($model);
            } else {
                throw new SpaceshipException('role_no_exists', [
                    'id' => $roleId
                ]);
            }
        }
    }

	/**
	 * Magic __get method.
	 */
	public function __get(string $property)
	{
        // Return if the model was not obtained
        if (! $this->hasModel()) {
            return null;
        }

        // Return the property from the model
        if (in_array($property, $this->properties)) {
            return $this->getModel()->{$property};
        } else {
            return null;
        }
	}

    /**
     * Check if the current role is equal from any of those specified.
     *
     * @param string|array $roleName Role or roles to check
     * @return boolean
     */
    public function is(string|array $roleName): bool
    {
        // Return if the model was not obtained
        if (! $this->hasModel()) {
            return false;
        }

        if ((is_string($roleName)) && ($this->getModel()->name == $roleName)) {
            return true;
        } else if ((is_array($roleName)) && (in_array($this->getModel()->name, $roleName))) {
            return true;
        }
        return false;
    }

    /**
     * Check if the current role is different from any of those specified.
     *
     * @param string|array $roleName Role or roles to check
     * @return boolean
     */
    public function isNot(string|array $roleName): bool
    {
        // Return if the model was not obtained
        if (! $this->hasModel()) {
            return false;
        }

        return ! $this->is($roleName);
    }

    /**
     * Check if the current role can perform a specified action.
     *
     * @param string|array $actionName Single action or several actions to check
     * @return boolean
     */
    public function can(string|array $actionName): bool
    {
        // Return if the model was not obtained
        if (! $this->hasModel()) {
            return false;
        }

        // If it is a single actions to check
        if (is_string($actionName)) {
            return in_array($actionName, $this->permissions()['actions']);
        
        // If they are several actions to check
        } else if (is_array($actionName)) {
            foreach ($actionName as $action) {
                if (! in_array($action, $this->permissions()['actions'])) {
                    return false;
                }
            }
            return true;
        }
    }

    /**
     * Return a specified parameter related to the role.
     *
     * @param string $paramName
     * @return mixed
     */
    public function param(string $paramName): mixed
    {
        // Return if the model was not obtained
        if (! $this->hasModel()) {
            return null;
        }

        if (array_key_exists($paramName, $this->permissions()['params'])) {
            return $this->permissions()['params'][$paramName];
        } else {
            return null;
        }
    }

    /**
     * Check if the role is active.
     *
     * @return boolean
     */
    public function isActive(): bool
    {
        // Return if the model was not obtained
        if (! $this->hasModel()) {
            return false;
        }

        return $this->getModel()->is_active;
    }

    /**
     * Activate/deactivate the role.
     *
     * @param bool $status
     * @return boolean|null
     */
    public function activate(bool $status = true): ?bool
    {
        // Return if the model was not obtained
        if (! $this->hasModel()) {
            return null;
        }

        $this->getModel()->is_active = $status;
        $this->getModel()->save();

        return $status;
    }

    /**
     * Delete the current role.
     *
     * @return void
     */
    public function destroy(): void
    {
        // Return if the model was not obtained
        if (! $this->hasModel()) {
            return;
        }

        $model = $this->getModel();
        $this->forgetModel();
        $model->delete();
    }

    /**
     * Obtain and return the permission (actions and params) related to the current role.
     *
     * @return array
     */
    private function permissions(): array
    {
        $defPermissions = [
            'actions' => [],
            'params' => []
        ];

        // Return if the model was not obtained
        if (! $this->hasModel()) {
            return $defPermissions;
        }

        // Load the permission form config
        $rolePermissions = config('spaceship.permissions.' . $this->getModel()->name);

        // Return the permissions
        if ($rolePermissions) {
            return array_merge($defPermissions, $rolePermissions);
        } else {
            return $defPermissions;
        }
    }
}