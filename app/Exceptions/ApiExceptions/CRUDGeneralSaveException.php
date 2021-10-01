<?php


namespace App\Exceptions\ApiExceptions;


use App\Exceptions\ApiException;

class CRUDGeneralSaveException extends ApiException
{
    protected $message = 'General save error.';
    protected $code = 500;
}
