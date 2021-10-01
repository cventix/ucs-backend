<?php


namespace App\Components\SessionHolder\Exceptions;


use Exception;

class SessionUpdateFailedException extends Exception
{

    /**
     * SessionUpdateFailedException constructor.
     * @param $message
     */
    public function __construct($message = '')
    {
        $this->message = 'Session Holder: Update failed. (' . $message . ')';
    }
}
