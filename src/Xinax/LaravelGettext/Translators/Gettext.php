<?php

namespace Xinax\LaravelGettext\Translators;

use Xinax\LaravelGettext\FileSystem;
use Xinax\LaravelGettext\Adapters\AdapterInterface;
use Xinax\LaravelGettext\Config\Models\Config;
use Xinax\LaravelGettext\Exceptions\LocaleNotSupportedException;
use Xinax\LaravelGettext\Exceptions\MissingPhpGettextModuleException;
use Xinax\LaravelGettext\Exceptions\UndefinedDomainException;

use Illuminate\Support\Facades\Session;

/**
 * Class implemented by the php-gettext module translator
 * @package Xinax\LaravelGettext\Translators
 */
class Gettext extends BaseTranslator implements TranslatorInterface
{
    /**
     * Initializes the gettext module translator
     *
     * @param Config $config
     * @param AdapterInterface $adapter
     * @param FileSystem $fileSystem
     *
     * @throws \Xinax\LaravelGettext\Exceptions\LocaleNotSupportedException
     * @throws \Xinax\LaravelGettext\Exceptions\MissingPhpGettextModuleException
     * @throws \Exception
     */
    public function __construct(
        Config $config,
        AdapterInterface $adapter,
        FileSystem $fileSystem
    ) {
        // Sets the package configuration and session handler
        $this->configuration = $config;
        $this->adapter = $adapter;
        $this->fileSystem = $fileSystem;

        // General domain
        $this->domain = $this->configuration->getDomain();

        // Encoding is set from configuration
        $this->encoding = $this->configuration->getEncoding();

        // Sets defaults for boot
        $locale = $this->session->get($this->configuration->getLocale());

        $this->setLocale($locale);
    }

    /**
     * Sets the current locale code
     */
    public function setLocale($locale)
    {
        if (!$this->isLocaleSupported($locale)) {
            throw new LocaleNotSupportedException(
                sprintf('Locale %s is not supported', $locale)
            );
        }

        try {
            $customLocale = $this->configuration->getCustomLocale() ? "C." : $locale . ".";
            $gettextLocale = $customLocale . $this->encoding;

            // All locale functions are updated: LC_COLLATE, LC_CTYPE,
            // LC_MONETARY, LC_NUMERIC, LC_TIME and LC_MESSAGES
            putenv("LC_ALL=$gettextLocale");
            putenv("LANGUAGE=$gettextLocale");
            setlocale(LC_ALL, $gettextLocale);

            $this->locale = $locale;
            $this->session->set($locale);

            // Domain
            $this->setDomain($this->domain);

            // Laravel built-in locale
            if ($this->configuration->isSyncLaravel()) {
                $this->adapter->setLocale($locale);
            }

            return $this->getLocale();
        } catch (\Exception $e) {
            $this->locale = $this->configuration->getFallbackLocale();
            $exceptionPosition = $e->getFile() . ":" . $e->getLine();
            throw new \Exception($exceptionPosition . $e->getMessage());

        }
    }

    /**
     * Sets the current domain and updates gettext domain application
     *
     * @param   String                      $domain
     * @throws  UndefinedDomainException    If domain is not defined
     * @return  self
     */
    public function setDomain($domain)
    {
        if (!in_array($domain, $this->configuration->getAllDomains())) {
            throw new UndefinedDomainException("Domain '$domain' is not registered.");
        }

        $customLocale = $this->configuration->getCustomLocale() ? "/" . $this->locale : "";
        
        bindtextdomain($domain, $this->fileSystem->getDomainPath() . $customLocale);
        bind_textdomain_codeset($domain, $this->encoding);

        $this->domain = textdomain($domain);

        return $this;
    }

    /**
     * Translates a message with gettext
     *
     * @param $message
     */
    public function translate($message)
    {
        return gettext($message);
    }

    /**
     * Translates a plural message with gettext
     *
     * @param $singular
     * @param $plural
     * @param $count
     *
     * @return string
     */
    public function translatePlural($singular, $plural, $count)
    {
        return ngettext($singular, $plural, $count);
    }
}
