<?php


namespace App\Exceptions\ApiExceptions;


use App\Exceptions\ApiException;

class UserDeactivatedException extends ApiException
{
    protected $message = 'Deactivated user.';
    protected $code = 400;
}
