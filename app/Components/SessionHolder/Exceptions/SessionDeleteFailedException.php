<?php


namespace App\Components\SessionHolder\Exceptions;


use Exception;

class SessionDeleteFailedException extends Exception
{
    protected $message = 'Session Holder: Delete failed.';
}
