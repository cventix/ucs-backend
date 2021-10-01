<?php


namespace App\Exceptions\ApiExceptions;


use App\Exceptions\ApiException;

class AuthInvalidVerificationCodeException extends ApiException
{
    protected $message = 'Invalid verify code.';
    protected $code = 400;
}
