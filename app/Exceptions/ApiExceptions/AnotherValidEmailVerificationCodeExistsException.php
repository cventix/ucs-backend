<?php


namespace App\Exceptions\ApiExceptions;


use App\Exceptions\ApiException;

class AnotherValidEmailVerificationCodeExistsException extends ApiException
{
    protected $message = 'Another valid email verification code exists.';
    protected $code = 400;
}
