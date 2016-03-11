<?php

namespace Xinax\LaravelGettext;

use Xinax\LaravelGettext\Composers\LanguageSelector;
use Xinax\LaravelGettext\Translators\TranslatorInterface;

class LaravelGettext
{
    /**
     * Translator handler
     *
     * @var TranslatorInterface
     */
    protected $translator;
    
    /**
     * @param TranslatorInterface $gettext
     * @throws Exceptions\MissingPhpGettextModuleException
     */
    public function __construct(TranslatorInterface $gettext)
    {
        $this->translator = $gettext;
    }

    /**
     * Get the current encoding
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->translator->getEncoding();
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
        return $this->translator->getLocale();
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
            $this->translator->setLocale($locale);
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
        $this->translator->setDomain($domain);
        return $this;
    }

    /**
     * Returns the current domain
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->translator->getDomain();
    }

    /**
     * Translates a message with the current handler
     *
     * @param $message
     * @return string
     */
    public function translate($message)
    {
        return $this->translator->translate($message);
    }


}
