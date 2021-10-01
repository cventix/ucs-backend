<?php


namespace App\Components\SessionHolder\Exceptions;


use Exception;

class SessionRegistrationFailedException extends Exception
{

    /**
     * SessionRegistrationFailedException constructor.
     * @param $message
     */
    public function __construct($message = '')
    {
        $this->message = 'Session Holder: Registration failed. (' . $message . ')';
    }
}
