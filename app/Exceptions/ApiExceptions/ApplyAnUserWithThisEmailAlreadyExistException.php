<?php


namespace App\Exceptions\ApiExceptions;


use App\Exceptions\ApiException;

class ApplyAnUserWithThisEmailAlreadyExistException extends ApiException
{
    protected $message = 'An user with this email already exists.';
    protected $code = 400;
}
