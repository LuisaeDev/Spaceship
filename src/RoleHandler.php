<?php

namespace LuisaeDev\Spaceship;

use LuisaeDev\Spaceship\Exceptions\SpaceshipException;
use LuisaeDev\Spaceship\Models\SpaceshipRole as RoleModel;
use LuisaeDev\Spaceship\Traits\SharedCollection;

class RoleHandler
{
    use SharedCollection;

    /** @param array Exposed get and set public properties definition */
    private array $getterProps = ['id', 'name'];
    private array $setterProps = [];

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
                    'id' => $roleId,
                ]);
            }
        }
    }

    /**
     * Magic __get method.
     */
    public function __get(string $prop)
    {
        // Return if the model was not obtained
        if (! $this->hasModel()) {
            return null;
        }

        // Return the property from the model
        if (in_array($prop, $this->getterProps)) {
            return $this->getModel()->{$prop};
        } else {
            return null;
        }
    }

    /**
     * Magic __set method.
     */
    public function __set(string $prop, mixed $value): void
    {
        // Return if the model was not obtained
        if (! $this->hasModel()) {
            return;
        }

        // Return the property from the model
        if (in_array($prop, $this->setterProps)) {
            $this->getModel()->{$prop} = $value;
            $this->getModel()->save();
        }
    }

    /**
     * Check if the current role is equal to the specified.
     *
     * @param string $roleName Role to check
     * @return bool|null
     */
    public function is(string $roleName): bool
    {
        // Return if the model was not obtained
        if (! $this->hasModel()) {
            return null;
        }

        if ($this->getModel()->name == $roleName) {
            return true;
        }

        return false;
    }

    /**
     * Check if the current role is equal from any of those specified.
     *
     * @param array $roleNames Roles to check
     * @return bool|null
     */
    public function isAny(string ...$roleNames): bool
    {
        // Return if the model was not obtained
        if (! $this->hasModel()) {
            return null;
        }

        if (in_array($this->getModel()->name, $roleNames)) {
            return true;
        }

        return false;
    }

    /**
     * Check if the current role is distinct from all those specified.
     *
     * @param array $roleNames Roles to check
     * @return bool|null
     */
    public function unless(string ...$roleNames): bool
    {
        // Return if the model was not obtained
        if (! $this->hasModel()) {
            return null;
        }

        if (in_array($this->getModel()->name, $roleNames)) {
            return false;
        }

        return true;
    }

    /**
     * Check if the current role can perform some actions.
     *
     * @param array $actions Actions to check
     * @return bool
     */
    public function can(string ...$actions): bool
    {
        // Return if the model was not obtained
        if (! $this->hasModel()) {
            return false;
        }

        // Check every action
        foreach ($actions as $action) {
            if (! in_array($action, $this->permissions()['actions'])) {
                return false;
            }
        }

        return true;
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
     * @return bool
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
     * @return bool|null
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
            'params' => [],
        ];

        // Return if the model was not obtained
        if (! $this->hasModel()) {
            return $defPermissions;
        }

        // Load the permission form config
        $rolePermissions = config('spaceship.permissions.'.$this->getModel()->name);

        // Return the permissions
        if ($rolePermissions) {
            return array_merge($defPermissions, $rolePermissions);
        } else {
            return $defPermissions;
        }
    }
}
