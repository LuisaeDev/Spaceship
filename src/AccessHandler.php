<?php

namespace LuisaeDev\Spaceship;

use App\Models\User;
use Carbon\Carbon;
use LuisaeDev\Spaceship\Exceptions\SpaceshipException;
use LuisaeDev\Spaceship\Models\SpaceshipAccess as AccessModel;
use LuisaeDev\Spaceship\Traits\SharedCollection;

class AccessHandler
{
    use SharedCollection;

    /** @param array Exposed get and set public properties definition */
    private array $getterProps = ['id', 'uuid', 'user', 'punched_at'];

    private array $setterProps = [];

    /**
     * Constructor.
     *
     * @param  int|string  $accessId Access identifier for obtain the access model
     */
    public function __construct(int|string $accessId)
    {

        // Search the model for its 'id' or 'uuid' at the shared collection
        if (is_int($accessId)) {
            $this->useModel(['id', $accessId]);
        } else {
            $this->useModel(['uuid', $accessId]);
        }

        // Obtain the model from the DB
        if (! $this->hasModel()) {

            // Search for the 'id' or 'uuid' column
            $query = AccessModel::query()
                ->with('user');

            if (is_int($accessId)) {
                $query->where($accessId);
            } else {
                $query->where('uuid', $accessId);
            }
            $model = $query->first();

            // Store the model at the shared collection
            if ($model) {
                $this->addModel($model);
            } else {
                throw new SpaceshipException('access_no_exists', [
                    'id' => $accessId,
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
     * Return the role related to the current access.
     *
     * @return RoleHandler|null
     */
    public function getRole(): ?RoleHandler
    {
        if (! $this->hasModel()) {
            return null;
        } else {
            return new RoleHandler($this->getModel()->spaceship_role_id);
        }
    }

    /**
     * Return the space related to the current access.
     *
     * @return SpaceHandler|null
     */
    public function getSpace(): ?SpaceHandler
    {
        if (! $this->hasModel()) {
            return null;
        } else {
            return new SpaceHandler($this->getModel()->spaceship_space_id);
        }
    }

    /**
     * Return the user related to the current access.
     *
     * @return User|null
     */
    public function getUser(): ?User
    {
        if (! $this->hasModel()) {
            return null;
        } else {
            return $this->getModel()->user;
        }
    }

    /**
     * Check if the access and its space and role, are all activeted
     *
     * @return bool
     */
    public function canPass(): bool
    {
        // Return if the model was not obtained
        if (! $this->hasModel()) {
            return false;
        }

        if (($this->isActive()) && ($this->getSpace()->isActive()) && ($this->getRole()->isActive())) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Certify the access ownership for a specific user.
     *
     * @param  User  $user
     * @return bool
     */
    public function isOwnershipOf(User $user): bool
    {
        // Return if the model was not obtained
        if (! $this->hasModel()) {
            return false;
        }

        return $user->id == $this->getModel()->user->id;
    }

    /**
     * Check if the access is active.
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
     * Activate/deactivate the access.
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
     * Delete the current access.
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
     * Set a timestamp as the last punching time.
     *
     * @return void
     */
    public function punch(): void
    {
        // Return if the model was not obtained
        if (! $this->hasModel()) {
            return;
        }

        $this->getModel()->punched_at = Carbon::now();
        $this->getModel()->save();
    }
}
