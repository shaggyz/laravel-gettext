<?php 

namespace Xinax\LaravelGettext\Facades;

use Illuminate\Support\Facades\Facade;
 
class LaravelGettext extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-gettext';
    }
}
