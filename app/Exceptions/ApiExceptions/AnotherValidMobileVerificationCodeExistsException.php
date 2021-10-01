<?php

namespace App\Exceptions\ApiExceptions;

use App\Exceptions\ApiException;

class AnotherValidMobileVerificationCodeExistsException extends ApiException
{
    protected $message = 'Another valid mobile verification code exists.';
    protected $code = 400;
}
