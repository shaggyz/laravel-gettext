<?php

namespace Xinax\LaravelGettext;

use Xinax\LaravelGettext\Composers\LanguageSelector;

class LaravelGettext
{
    /**
     * @param Gettext $gettext
     * @throws Exceptions\MissingPhpGettextModuleException
     */
    public function __construct(Gettext $gettext)
    {
        if (!function_exists('gettext')) {
            throw new Exceptions\MissingPhpGettextModuleException(
                "You need to install the php-gettext module for this package."
            );
        }

        $this->gettext = $gettext;
    }

    /**
     * Get the current encoding
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->gettext->getEncoding();
    }

    /**
     * Set the current encoding
     *
     * @param string $encoding
     * @return $this
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
        return $this;
    }

    /**
     * Gets the Current locale.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->gettext->getLocale();
    }

    /**
     * Set current locale
     *
     * @param string $locale
     * @return $this
     * @throws Exceptions\LocaleNotSupportedException
     * @throws \Exception
     */
    public function setLocale($locale)
    {
        if ($locale != $this->getLocale()) {
            $this->gettext->setLocale($locale);
        }

        return $this;
    }

    /**
     * Get the language portion of the locale
     * (ex. en_GB returns en)
     *
     * @param string|null $locale
     * @return string|null
     */
    public function getLocaleLanguage($locale = null)
    {
        if (is_null($locale)) {
            $locale = $this->getLocale();
        }

        $localeArray = explode('_', $locale);

        if (!isset($localeArray[0])) {
            return null;
        }

        return $localeArray[0];
    }

    /**
     * Get the language selector object
     *
     * @param array $labels
     * @return LanguageSelector
     */
    public function getSelector($labels = [])
    {
        return LanguageSelector::create($this, $labels);
    }

    /**
     * Sets the current domain
     * 
     * @param string $domain
     * @return $this
     */
    public function setDomain($domain)
    {
        $this->gettext->setDomain($domain);
        return $this;
    }

    /**
     * Returns the current domain
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->gettext->getDomain();
    }
}
