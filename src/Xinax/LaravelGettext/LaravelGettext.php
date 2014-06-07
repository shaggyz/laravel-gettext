<?php

namespace Xinax\LaravelGettext;

class LaravelGettext{

	/**
	 * Check dependencies
	 */
	public function __construct(Gettext $gettext){

		// Dependencies will be checked 
		// on first package call
		$this->checkDependencies();

        // Gettext dependency
        $this->gettext = $gettext;

	}

	/**
	 * Checks the php and system dependencies 
	 * to implement gettext safely
	 */
	public function checkDependencies(){

		// Php module check
		if(!function_exists('gettext')){
			throw new Exceptions\MissingPhpGettextModuleException(
				"You need to install the php-gettext module for this package."
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
        return $this->gettext->getLocale();
    }

    /**
     * Sets the Current locale.
     * @param mixed $locale the locale
     * @return self
     */
    public function setLocale($locale){

        if($locale != $this->getLocale()){
            $this->gettext->setLocale($locale);
        }

        return $this;
    }

}