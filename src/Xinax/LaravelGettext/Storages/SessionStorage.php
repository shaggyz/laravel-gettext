<?php
/**
 * Created by PhpStorm.
 * User: aaflalo
 * Date: 03/02/17
 * Time: 10:08 AM
 */

namespace Xinax\LaravelGettext\Storages;

use Session;

class SessionStorage implements Storage
{
    /**
     * Config container
     *
     * @type \Xinax\LaravelGettext\Config\Models\Config
     */
    protected $configuration;

    /**
     * SessionStorage constructor.
     *
     * @param \Xinax\LaravelGettext\Config\Models\Config $configuration
     */
    public function __construct(\Xinax\LaravelGettext\Config\Models\Config $configuration)
    {
        $this->configuration = $configuration;
    }


    /**
     * Getter for domain
     *
     * @return String
     */
    public function getDomain()
    {
        return $this->sessionGet('domain', $this->configuration->getDomain());
    }

    /**
     * @param String $domain
     *
     * @return $this
     */
    public function setDomain($domain)
    {
        $this->sessionSet('domain', $domain);

        return $this;
    }

    /**
     * Getter for locale
     *
     * @return String
     */
    public function getLocale()
    {
        return $this->sessionGet('locale', $this->configuration->getLocale());
    }

    /**
     * @param String $locale
     *
     * @return $this
     */
    public function setLocale($locale)
    {
        $this->sessionSet('locale', $locale);

        return $this;
    }

    /**
     * Getter for configuration
     *
     * @return \Xinax\LaravelGettext\Config\Models\Config
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }


    /**
     * Return a value from session with an optional default
     *
     * @param      $key
     * @param null $default
     *
     * @return mixed
     */
    protected function sessionGet($key, $default = null)
    {
        $token = $this->configuration->getSessionIdentifier() . "-" . $key;

        return Session::get($token, $default);
    }

    /**
     * Sets a value in session session
     *
     * @param $key
     * @param $value
     *
     * @return mixed
     */
    protected function sessionSet($key, $value)
    {
        $token = $this->configuration->getSessionIdentifier() . "-" . $key;
        Session::put($token, $value);

        return $this;
    }

    /**
     * Getter for locale
     *
     * @return String
     */
    public function getEncoding()
    {
        return $this->sessionGet('encoding', $this->configuration->getEncoding());
    }

    /**
     * @param string $encoding
     *
     * @return $this
     */
    public function setEncoding($encoding)
    {
        $this->sessionSet('encoding', $encoding);

        return $this;
    }
}