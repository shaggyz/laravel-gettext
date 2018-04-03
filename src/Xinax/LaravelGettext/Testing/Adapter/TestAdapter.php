<?php
/**
 * Created by PhpStorm.
 * User: aaflalo
 * Date: 18-02-23
 * Time: 11:50
 */

namespace Xinax\LaravelGettext\Testing\Adapter;

use Xinax\LaravelGettext\Adapters\AdapterInterface;

class TestAdapter implements AdapterInterface
{
    /**
     * @var string
     */
    private $locale = 'en_US';

    /**
     * Get the current locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Sets the locale on the adapter
     *
     * @param string $locale
     *
     * @return boolean
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return true;
    }

    /**
     * Get the application path
     *
     * @return string
     */
    public function getApplicationPath()
    {
        return app_path();
    }
}
