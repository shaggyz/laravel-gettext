<?php
/**
 * Created by PhpStorm.
 * User: aaflalo
 * Date: 03/02/17
 * Time: 10:10 AM
 */
namespace Xinax\LaravelGettext\Storages;

interface Storage
{
    /**
     * Getter for domain
     *
     * @return String
     */
    public function getDomain();

    /**
     * @param string $domain
     *
     * @return $this
     */
    public function setDomain($domain);

    /**
     * Getter for locale
     *
     * @return String
     */
    public function getLocale();

    /**
     * @param string $locale
     *
     * @return $this
     */
    public function setLocale($locale);

    /**
     * Getter for locale
     *
     * @return String
     */
    public function getEncoding();

    /**
     * @param string $encoding
     *
     * @return $this
     */
    public function setEncoding($encoding);

    /**
     * Getter for configuration
     *
     * @return \Xinax\LaravelGettext\Config\Models\Config
     */
    public function getConfiguration();
}