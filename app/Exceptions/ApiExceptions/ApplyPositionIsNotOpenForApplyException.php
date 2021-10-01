<?php


namespace App\Exceptions\ApiExceptions;


use App\Exceptions\ApiException;

class ApplyPositionIsNotOpenForApplyException extends ApiException
{
    protected $message = 'Position is not open for apply.';
    protected $code = 400;
}
