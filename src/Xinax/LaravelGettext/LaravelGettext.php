<?php

namespace Xinax\LaravelGettext;

use Xinax\LaravelGettext\Composers\LanguageSelector;

class LaravelGettext
{
    /**
     * Check dependencies
     */
    public function __construct(Gettext $gettext)
    {
        // Dependencies will be checked on first package call
        if (!function_exists('gettext')) {
            throw new Exceptions\MissingPhpGettextModuleException(
                "You need to install the php-gettext module for this package."
            );
        }

        // Gettext dependency
        $this->gettext = $gettext;
    }

    /**
     * Gets the Current encoding.
     *
     * @return mixed
     */
    public function getEncoding()
    {
        return $this->gettext->getEncoding();
    }

    /**
     * Sets the Current encoding.
     *
     * @param mixed $encoding the encoding
     * @return self
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
        return $this;
    }

    /**
     * Gets the Current locale.
     *
     * @return mixed
     */
    public function getLocale()
    {
        return $this->gettext->getLocale();
    }

    /**
     * Sets the Current locale.
     *
     * @param mixed $locale the locale
     * @return self
     */
    public function setLocale($locale)
    {
        if ($locale != $this->getLocale()) {
            $this->gettext->setLocale($locale);
        }

        return $this;
    }

    /**
     * Gets the language portion of the locale.
     * Eg from en_GB, returns en
     *
     * @param  String $locale
     * @return mixed
     */
    public function getLocaleLanguage($locale = null)
    {
        if(!$locale){
            $locale = $this->getLocale();
        }

        $localeArray = explode('_', $locale);

        if (isset($localeArray[0])) {
            return $localeArray[0];
        }
    }

    /**
     * Returns the language selector object
     *
     * @param  Array $labels 
     * @return LanguageSelector         
     */
    public function getSelector($labels = [])
    {
        return LanguageSelector::create($labels, $this);
    }

    /**
     * Sets the current domain
     * 
     * @param String $domain
     */
    public function setDomain($domain)
    {
        $this->gettext->setDomain($domain);
    }

    /**
     * Returns the current domain
     *
     * @return String
     */
    public function getDomain()
    {
        return $this->gettext->getDomain();
    }

}
