<?php


namespace App\Components\SessionHolder\Exceptions;


use Exception;

class InvalidSessionHolderConfiguration extends Exception
{

    /**
     * InvalidSessionHolderConfiguration constructor.
     * @param $field
     */
    public function __construct($field)
    {
        $this->message = 'Session Holder: Invalid configuration for ' . $field . ' field.';
    }
}
