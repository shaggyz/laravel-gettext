<?php

namespace Xinax\LaravelGettext\Adapters;

interface AdapterInterface
{
    /**
     * Get the current locale
     *
     * @return string
     */
    public function getLocale();

    /**
     * Sets the locale on the adapter
     *
     * @param string $locale
     * @return boolean
     */
    public function setLocale($locale);

    /**
     * Get the application path
     *
     * @return string
     */
    public function getApplicationPath();
}
