<?php

namespace Deyji\Manage;

use Illuminate\Support\Facades\Facade;

class ManageFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'manage';
    }
}
