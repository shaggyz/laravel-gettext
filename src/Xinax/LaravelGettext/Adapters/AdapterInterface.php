<?php

namespace Xinax\LaravelGettext\Adapters;

interface AdapterInterface
{

    /**
     * Returns the adapter current locale
     */
    public function getLocale();

    /**
     * Sets the locale on current addapter
     */
    public function setLocale($locale);

    /**
     * Return the application path
     */
    public function getApplicationPath();

}
