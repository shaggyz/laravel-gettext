<?php

namespace Xinax\LaravelGettext;
use \Session;

class Gettext{

	/**
	 * Config container
	 * @type Xinax\LaravelGettext\Config\Models\Config
	 */
	protected $configuration;

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
	 * Session identifier to store active locale 
	 */
	const SESSION_IDENTIFIER = "laravel-gettext-locale";

	/**
	 * Sets the configuration dependency injection
	 */
	public function __construct(Config\ConfigManager $configurationManager){
		
		// Sets the locale configuration
		$this->configuration = $configurationManager->get();

		// Encoding is set on configuration
		$this->encoding = $this->configuration->getEncoding();

		// Sets defaults for boot
		$locale = $this->configuration->getLocale();
		if(Session::has(self::SESSION_IDENTIFIER)){
			$locale = Session::get(self::SESSION_IDENTIFIER);
		}

		$this->setLocale($locale);

	}

	/**
	 * Sets the current locale
	 */
	public function setLocale($locale){

		if($locale){
            
            if(!$this->isLocaleSupported($locale)){
                throw new Exceptions\LocaleNotSupportedException(
                	"Locale $locale is not supported");
            }

            try {

            	$domain = $this->configuration->getDomain();
            	$gettextLocale = $locale . "." . $this->encoding;

	            putenv("LC_ALL=$gettextLocale");
				setlocale(LC_ALL, $gettextLocale);
				bindtextdomain($domain, $this->getDomainPath());
				textdomain($domain);

				$this->locale = $locale;	
				Session::set(self::SESSION_IDENTIFIER, $locale);

            } catch (\Exception $e) {
		        
		        $this->locale = $this->configuration->getFallbackLocale();
		        throw new \Exception($e->getMessage());

            }

        }
		
	}

	/**
	 * Returns the current locale string identifier
	 * @return String
	 */
	public function getLocale(){
		
		return $this->locale . "." . $this->encoding;
	}

	/**
	 * Constructs and returns the full path to 
	 * translaition files 
	 * @return String
	 */	
	protected function getDomainPath(){
		
		$path = array(
			app_path(),
			$this->configuration->getTranslationsPath(),
			"i18n"
		);

		return implode(DIRECTORY_SEPARATOR, $path);

	}

    /**
     * Returns a boolean that indicates if $locale 
     * is supported by configuration
     * @return Boolean
     */
    protected function isLocaleSupported($locale){
        return in_array($locale, $this->configuration->getSupportedLocales());
    }
    
    /**
     * Return the current locale
     */
    public function __toString(){
    	return $this->getLocale();
    }

}