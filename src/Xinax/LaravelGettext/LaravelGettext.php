<?php

namespace Xinax\LaravelGettext;

class LaravelGettext{

	/**
	 * Config container
	 * @type Xinax\LaravelGettext\Config\Models\Config
	 */
	protected $config;

	/**
	 * Current encoding
	 * @type String
	 */
	protected $encoding;

	/**
	 * Current locale
	 * @type String
	 */
	protected $locale;

	/**
	 * Check dependencies
	 */
	public function __construct(Config\ConfigManager $configManager){

		// Dependencies will be checked 
		// on first package call
		$this->checkDependencies();

		// Sets the locale config
		$this->config = $configManager->get();

        // Default values
        $this->locale = $this->config->getLocale();
        $this->encoding = $this->config->getEncoding();

	}

	/**
	 * Checks the php and system dependencies 
	 * to implement gettext safely
	 */
	public function checkDependencies(){

		// Php module check
		if(!function_exists('gettext')){
			throw new Exceptions\MissingPhpGettextModuleException(
				"You need to install the php-gettext module to use LaravelGettext."
			);
		}
		
	}

    /**
     * Gets the Current encoding.
     * @return mixed
     */
    public function getEncoding(){
        return $this->encoding;
    }

    /**
     * Sets the Current encoding.
     * @param mixed $encoding the encoding
     * @return self
     */
    public function setEncoding($encoding){
        $this->encoding = $encoding;
        return $this;
    }

    /**
     * Gets the Current locale.
     * @return mixed
     */
    public function getLocale(){
        return $this->locale;
    }

    /**
     * Sets the Current locale.
     * @param mixed $locale the locale
     * @return self
     */
    public function setLocale($locale){

        if($locale){
            if(!$this->isLocaleSupported($locale)){
                throw new Exceptions\LocaleNotSupportedException("Locale $locale is not supported");
            }
            $this->locale = $locale;
        }
        
        return $this;
    }

    /**
     * Returns a boolean that indicates if $locale 
     * is supported by configuration
     */
    protected function isLocaleSupported($locale){
        return in_array($locale, $this->config->getSupportedLocales());
    }
}