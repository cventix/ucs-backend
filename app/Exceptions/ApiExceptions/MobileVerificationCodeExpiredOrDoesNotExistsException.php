<?php

namespace App\Exceptions\ApiExceptions;

use App\Exceptions\ApiException;

class MobileVerificationCodeExpiredOrDoesNotExistsException extends ApiException
{
    protected $message = 'Verification code expired or does not exists.';
    protected $code = 400;
}
