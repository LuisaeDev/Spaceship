<?php

namespace LuisaeDev\Spaceship\Commands;

use Illuminate\Console\Command;
use LuisaeDev\Spaceship\Exceptions\SpaceshipException;
use LuisaeDev\Spaceship\Facades\Spaceship;

class CreateRoleCommand extends Command
{
    public $signature = 'spaceship:create-role {roleName}';

    public $description = 'Create a new Spaceship role';

    public function handle(): int
    {
        $roleName = $this->argument('roleName');

        try {
            Spaceship::createSpace($roleName);

            $this->comment(trans('The role ":name" has been created', [
                'name' => $roleName,
            ]));

            return self::SUCCESS;
        } catch (SpaceshipException $e) {
            $this->error(trans('The role ":name" is already created', [
                'name' => $roleName,
            ]));

            return self::FAILURE;
        }
    }
}
