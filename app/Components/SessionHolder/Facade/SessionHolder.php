<?php

namespace App\Components\SessionHolder\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * Class Payment
 * @package App\Components\Meeting\Facade
 */
class SessionHolder extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor()
    {
        return 'session-holder';
    }
}
