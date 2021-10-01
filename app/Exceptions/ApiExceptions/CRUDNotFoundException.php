<?php


namespace App\Exceptions\ApiExceptions;


use App\Exceptions\ApiException;

class CRUDNotFoundException extends ApiException
{
    protected $code = 404;
}
