<?php

namespace Xinax\LaravelGettext;

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
        $this->gettext->filesystemStructure();
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
     * @return mixed
     */
    public function getLocaleLanguage()
    {
        $locale = $this->getLocale();
        $localeArray = explode('_', $locale);

        if (isset($localeArray[0])) {
            return $localeArray[0];
        }
    }
}
