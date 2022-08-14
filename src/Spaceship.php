<?php

namespace LuisaeDev\Spaceship;

use LuisaeDev\Spaceship\Models\SpaceshipSpace as SpaceModel;
use LuisaeDev\Spaceship\Models\SpaceshipRole as RoleModel;
use LuisaeDev\Spaceship\Models\SpaceshipAccess as AccessModel;
use LuisaeDev\Spaceship\Contracts\SpaceshipInterface;
use LuisaeDev\Spaceship\Exceptions\SpaceshipException as SpaceshipException;
use App\Models\User;

class Spaceship implements SpaceshipInterface
{

    public function __construct()
    {
    }

    /**
     * Create a new space model.
     *
     * @param string|array $data Space name or attributes to add for the new space model
     * @return SpaceHandler Corresponding space handler instance
     */
    public function createSpace(string|array $data): SpaceHandler
    {   
        if (is_string($data)) {
            $data = [
                'name' => $data
            ];
        }

        // Check if there exists another space with the same name
        if (isset($data['name'])) {
            $spaceModel = SpaceModel::where('name', $data['name'])->first();
            if ($spaceModel) {
                throw new SpaceshipException('space_already_exists', [
                    'name' => $data['name']
                ]);
            }
        }

        // Create the new space model
        $spaceModel = new SpaceModel(array_merge([
            'name'        => null,
            'alias'       => null,
            'binded_id'   => null,
            'binded_data' => null,
            'is_active'   => 1
        ], $data));

        // Register the model at SpaceHandler class
        SpaceHandler::registerModel($spaceModel);

        return new SpaceHandler($spaceModel->id);
    }

    /**
     * Get a space handler instance.
     *
     * @param int|string|null $spaceId Space identifier. When null, default space will be defined
     * @return SpaceHandler
     */
    public function getSpace(int|string|null $spaceId = null): SpaceHandler
    {
        return new SpaceHandler($spaceId);
    }

    /**
     * Create a new role model.
     *
     * @param string $name Unique name for the role
     * @return RoleHandler Corresponding role handler instance
     */
    static public function createRole(string $name): RoleHandler
    {
        
        // Check if there exists another role with the same name
        $roleModel = RoleModel::where('name', $name)->first();
        if ($roleModel) {
            throw new SpaceshipException('role_already_exists', [
                'name' => $name
            ]);        
        }

        // Create the new role model
        $roleModel = RoleModel::create([
            'name' => $name,
            'is_active' => 1
        ]);

        // Register the model at RoleHandler class
        RoleHandler::registerModel($roleModel);

        return new RoleHandler($roleModel->id);
    }

    /**
     * Get a role handler instance.
     *
     * @param int|string $roleId Role identifier
     * @return RoleHandler
     */
    public function getRole(int|string $roleId): RoleHandler
    {
        return new RoleHandler($roleId);
    }

    /**
     * Create a new access model.
     *
     * @param SpaceHandler $space
     * @param User $user
     * @param RoleHandler|string $role
     * @return AccessHandler Corresponding access handler instance
     */
    public function createAccess(SpaceHandler $space, User $user, RoleHandler|string $role): AccessHandler
    {
        
        // Check if there exists an access with the same user and space
        $access = AccessModel::query()
            ->where([
                [ 'spaceship_space_id', $space->id ],
                [ 'user_id', $user->id ]
            ])
            ->first();
        if ($access) {
            throw new SpaceshipException('user_already_has_access', [
                'user_id' => $user->id
            ]);        
        }

        // Obtain the role model if it was specified by its name
        if (is_string($role)) {
            $role = new RoleHandler($role);
        }

        // Create the access model
        $accessModel = AccessModel::create([
            'spaceship_space_id' => $space->id,
            'user_id' => $user->id,
            'spaceship_role_id' => $role->id,
            'is_active' => 1
        ]);
        $accessModel->load('user');

        // Register the model at AccessHandler class
        AccessHandler::registerModel($accessModel);

        return new AccessHandler($accessModel->id);
    }

    /**
     * Get an access handler instance.
     *
     * @param int|string $accessId Access identifier
     * @return AccessHandler
     */
    public function getAccess(int|string $accessId): AccessHandler
    {
        return new AccessHandler($accessId);
    }
}