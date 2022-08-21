<?php

namespace LuisaeDev\Spaceship;

use App\Models\User;
use Illuminate\Support\Collection;
use LuisaeDev\Spaceship\Exceptions\SpaceshipException;
use LuisaeDev\Spaceship\Facades\Spaceship as SpaceshipFacade;
use LuisaeDev\Spaceship\Models\SpaceshipAccess as AccessModel;
use LuisaeDev\Spaceship\Models\SpaceshipSpace as SpaceModel;
use LuisaeDev\Spaceship\Traits\SharedCollection;

class SpaceHandler
{
    use SharedCollection;

    /** @param array Property for store all AccessHandler instances obtained for the current space */
    private array $accesses = [];

    /** @param array Exposed get and set public properties definition */
    private array $getterProps = ['id', 'name', 'alias'];

    private array $setterProps = ['binded_id', 'binded_data'];

    /**
     * Constructor.
     *
     * @param  int|string  $spaceId Space identifier
     */
    public function __construct(int|string $spaceId)
    {

        // Search the model for its 'id', 'alias' or 'name' at the shared collection
        if (is_int($spaceId)) {
            $this->useModel(['id', $spaceId]);
        } else {
            $this->useModel(['alias', $spaceId]);
            if (! $this->hasModel()) {
                $this->useModel(['name', $spaceId]);
            }
        }

        // Obtain the model from the DB
        if (! $this->hasModel()) {

            // Search for the 'id' or 'name' column
            $query = SpaceModel::query();
            if (is_int($spaceId)) {
                $query->where('id', $spaceId);
            } else {
                $query->where('name', $spaceId);
            }
            $model = $query->first();

            // Store the model at the shared collection
            if ($model) {
                $this->addModel($model);
            } else {
                throw new SpaceshipException('space_no_exists', [
                    'id' => $spaceId,
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
     * Check if a specific user has access to the current space.
     *
     * @param  User  $user
     * @return bool
     */
    public function hasAccess(User $user): bool
    {
        // Check if the space model was obtained
        if (! $this->hasModel()) {
            return false;
        }

        // Check if the user has an access for the current space
        $access = $this->getAccess($user);

        if ($access) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if a specific user can access to the current space.
     *
     * It will validate if the space, access and role are activated.
     *
     * @param  User  $user
     * @return bool
     */
    public function canAccess(User $user): bool
    {
        // Check if the space model was obtained
        if (! $this->hasModel()) {
            return false;
        }

        // Check if the user has an access for the current space
        $access = $this->getAccess($user);

        // Validate if the access and its space and role are active
        if ((! $access) || (! $access->canPass())) {
            return false;
        }

        return true;
    }

    /**
     * Return a user's access related to the current space.
     *
     * @param  User  $user
     * @return AccessHandler|null
     */
    public function getAccess(User $user): ?AccessHandler
    {
        // Check if the space model was obtained
        if (! $this->hasModel()) {
            return null;
        }

        // Verify if the user's access was not obtained before
        if (! array_key_exists($user->id, $this->accesses)) {

            // Check if the user has an access for the current space
            $access = AccessModel::where([
                ['user_id', $user->id],
                ['spaceship_space_id', $this->getModel()->id],
            ])
                ->with('user')
                ->first();

            // If the access exists
            if ($access) {

                // Register the model at AccessHandler class
                AccessHandler::registerModel($access);

                // Its corresponding AccessHandler instance will be stored at accesses property
                $this->accesses[$user->id] = new AccessHandler($access->id);
            } else {
                return null;
            }
        }

        return $this->accesses[$user->id];
    }

    /**
     * Return a collection with all accesses models related to the current space.
     *
     * @return Collection
     */
    public function getAccesses(): Collection
    {
        // Check if the space model was obtained
        if (! $this->hasModel()) {
            return collect([]);
        }

        return $this->getModel()->accesses->load(['role', 'user:id,name,email']);
    }

    /**
     * Return the corresponding user's role, related to the current space.
     *
     * @param  User  $user
     * @return RoleHandler|null
     */
    public function getRole(User $user): ?RoleHandler
    {
        // Check if the space model was obtained
        if (! $this->hasModel()) {
            return null;
        }

        // Check if the user has an access for the current space
        $access = $this->getAccess($user);

        if ($access) {
            return $access->getRole();
        } else {
            return null;
        }
    }

    /**
     * Return the binded id related to the current space.
     *
     * @return mixed
     */
    public function bindedId(mixed $value = null): mixed
    {
        // Check if the space model was obtained
        if (! $this->hasModel()) {
            return null;
        }

        return $this->getModel()->binded_id;
    }

    /**
     * Return the binded data related to the current space.
     *
     * @return array|null
     */
    public function bindedData(): ?array
    {
        // Check if the space model was obtained
        if (! $this->hasModel()) {
            return null;
        }

        // If the binded data is null
        if (is_null($this->getModel()->binded_data)) {
            return [];
        }

        return $this->getModel()->binded_data;
    }

    /**
     * Allow access for a specific user to the current space.
     *
     * @param  User  $user
     * @param  RoleHandler|string  $role
     * @return AccessHandler|null
     */
    public function allowAccess(User $user, RoleHandler|string $role): ?AccessHandler
    {
        // Check if the space model was obtained
        if (! $this->hasModel()) {
            return null;
        }

        // Create and return the new access
        return SpaceshipFacade::createAccess($this, $user, $role);
    }

    /**
     * Rovoke an user's access from the current space.
     *
     * @param  User  $user
     * @return void
     */
    public function revokeAccess(User $user): void
    {
        // Return if the model was not obtained
        if (! $this->hasModel()) {
            return;
        }

        // Check if the user has an access for the current space, and it will be revoked
        $access = $this->getAccess($user);
        if ($access) {
            $access->destroy();
            unset($this->accesses[$user->id]);
        }
    }

    /**
     * Check if the space is active.
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
     * Activate/deactivate the space.
     *
     * @param  bool  $status
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
     * Delete the current space.
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
        $this->accesses = [];
    }
}
