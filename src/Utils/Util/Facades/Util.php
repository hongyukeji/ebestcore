<?php

namespace System\Utils\Util\Facades;

use Illuminate\Support\Facades\Facade;

class Util extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'util';
    }
}