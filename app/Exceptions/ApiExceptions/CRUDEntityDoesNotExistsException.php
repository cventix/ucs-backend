<?php


namespace App\Exceptions\ApiExceptions;


use App\Exceptions\ApiException;

class CRUDEntityDoesNotExistsException extends ApiException
{
    protected $message = 'Entity does not exists.';
    protected $code = 500;
}
