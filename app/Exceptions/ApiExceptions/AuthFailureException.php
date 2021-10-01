<?php


namespace App\Exceptions\ApiExceptions;


use App\Exceptions\ApiException;

class AuthFailureException extends ApiException
{
    protected $message = 'Authentication failure.';
    protected $code = 403;
}
