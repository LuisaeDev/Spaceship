<?php

namespace LuisaeDev\Spaceship\Exceptions;

use Exception;

class SpaceshipException extends Exception
{
    protected $messages = [
        'space_no_exists' => 'The space "(:id)" no exists',
        'role_no_exists' => 'The role "(:id)" no exists',
        'access_no_exists' => 'The access "(:id)" no exists',
        'space_already_exists' => 'The space "(:name)" already exists',
        'role_already_exists' => 'The role "(:name)" already exists',
        'user_already_has_access' => 'The user "(:user_id)" already has an access',
    ];

    public function __construct(string $code, array $params = [])
    {
        $this->code = $code;
        $message = $this->messages[$this->code];
        $this->message = trans($message, $params);
    }
}
