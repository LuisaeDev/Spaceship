<?php

namespace LuisaeDev\Spaceship\Commands;

use Illuminate\Console\Command;
use LuisaeDev\Spaceship\Exceptions\SpaceshipException;
use LuisaeDev\Spaceship\Facades\Spaceship;

class CreateSpaceCommand extends Command
{
    public $signature = 'spaceship:create-space {spaceName}';

    public $description = 'Create a new Spaceship space';

    public function handle(): int
    {

        $spaceName = $this->argument('spaceName');;

        try {

            Spaceship::createSpace($spaceName);

            $this->comment(trans('The space ":name" has been created', [
                'name' => $spaceName
            ]));

            return self::SUCCESS;

        } catch(SpaceshipException $e) {

            $this->error(trans('The space ":name" is already created', [
                'name' => $spaceName
            ]));
            
            return self::FAILURE;
        }
    }
}