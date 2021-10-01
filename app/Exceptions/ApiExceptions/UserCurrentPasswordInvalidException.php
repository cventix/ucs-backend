<?php


namespace App\Exceptions\ApiExceptions;


use App\Exceptions\ApiException;

class UserCurrentPasswordInvalidException extends ApiException
{
    protected $message = 'Invalid current password.';
    protected $code = 400;
}
