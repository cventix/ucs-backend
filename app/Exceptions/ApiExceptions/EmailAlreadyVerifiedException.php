<?php

namespace App\Exceptions\ApiExceptions;

use App\Exceptions\ApiException;

class EmailAlreadyVerifiedException extends ApiException
{
    protected $message = 'Email already verified.';
    protected $code = 400;
}
