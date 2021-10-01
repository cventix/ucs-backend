<?php

namespace App\Exceptions\ApiExceptions;

use App\Exceptions\ApiException;

class MobileAlreadyVerifiedException extends ApiException
{
    protected $message = 'Mobile already verified.';
    protected $code = 400;
}
