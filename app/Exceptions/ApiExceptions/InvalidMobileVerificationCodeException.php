<?php

namespace App\Exceptions\ApiExceptions;

use App\Exceptions\ApiException;

class InvalidMobileVerificationCodeException extends ApiException
{
    protected $message = 'Invalid verification code.';
    protected $code = 400;
}
