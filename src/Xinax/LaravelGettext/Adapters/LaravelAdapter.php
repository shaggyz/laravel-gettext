<?php

namespace Xinax\LaravelGettext\Adapters;

use Illuminate\Support\Facades\App;

class LaravelAdapter implements AdapterInterface
{

    /**
     * Returns the adapter current locale
     */
    public function setLocale($locale)
    {
        App::setLocale(substr($locale, 0, 2));
    }

    /**
     * Sets the locale on current addapter
     */
    public function getLocale()
    {
        return App::getLocale();
    }

    /**
     * Return the application path
     */
    public function getApplicationPath()
    {
        return app_path();
    }
}
