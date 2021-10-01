<?php


namespace App\Exceptions\ApiExceptions;


use App\Exceptions\ApiException;

class TagRequestTypeIsNotTaggableOrDoesNotExistsException extends ApiException
{
    protected $message = 'Invalid taggable type to tagging.';
    protected $code = 400;
}
