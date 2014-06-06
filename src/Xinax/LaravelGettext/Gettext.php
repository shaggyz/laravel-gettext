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
		$this->filesystemStructure();

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

            	// var_dump($domain);
            	// var_dump($gettextLocale);
            	// var_dump($this->getDomainPath());
            	// die;

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
	protected function getDomainPath($append=null){
		
		$path = array(
			app_path(),
			$this->configuration->getTranslationsPath(),
			"i18n"
		);

		if(!is_null($append)){
			array_push($path, $append);
		}

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
     * Checks the needed directory structure
     */
    protected function filesystemStructure(){

    	$domainPath = $this->getDomainPath();

    	// Translation files base path
    	if(!file_exists($domainPath)){
    		throw new Exceptions\DirectoryNotFoundException(
    			"Missing base required directory: $domainPath");
    		
    	}

    	foreach ($this->configuration->getSupportedLocales() as $locale) {

    		// Default locale is not needed
    		if($locale == $this->configuration->getLocale()){
    			continue;
    		}

    		$localePath = $this->getDomainPath($locale);
    		if(!file_exists($localePath)){
    			throw new Exceptions\DirectoryNotFoundException(
    				"Missing locale required directory: $localePath");
    		}
    	}

    }
    
    /**
     * Return the current locale
     */
    public function __toString(){
    	return $this->getLocale();
    }

}