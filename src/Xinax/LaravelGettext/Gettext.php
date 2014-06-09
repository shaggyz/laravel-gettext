<?php

namespace Xinax\LaravelGettext;

use Xinax\LaravelGettext\Config\ConfigManager;
use Xinax\LaravelGettext\Session\SessionHandler;
use Xinax\LaravelGettext\Adapters\AdapterInterface;

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
	 * Framework adapter
	 * @type Xinax\Adapters\LaravelAdapter
	 */
	protected $adapter;

	/**
	 * Sets the configuration and session manager
	 */	
	public function __construct(ConfigManager $configMan, 
											SessionHandler $sessionHandler,
											AdapterInterface $adapter){
		
		// Sets the package configuration and session handler
		$this->configuration = $configMan->get();
		$this->session = $sessionHandler;
		$this->adapter = $adapter;

		// Encoding is set on configuration
		$this->encoding = $this->configuration->getEncoding();

		// Sets defaults for boot
		$locale = $this->session->get($this->configuration->getLocale());

		$this->setLocale($locale);

	}

	/**
	 * Sets the current locale code
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
				$this->session->set($locale);

				// Laravel built-in locale
				if($this->configuration->getSyncLaravel()){
					$this->adapter->setLocale($locale);
				}

            } catch (\Exception $e) {
		        
		        $this->locale = $this->configuration->getFallbackLocale();
		        $a = $e->getFile() . ":" . $e->getLine();
		        throw new \Exception($a.$e->getMessage());

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
			$this->adapter->getApplicationPath(),
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
    public function filesystemStructure(){

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
}