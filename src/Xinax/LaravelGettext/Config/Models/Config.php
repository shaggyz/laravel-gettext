<?php

namespace Xinax\LaravelGettext\Config\Models;

class Config{

	/**
	 * Charset encoding for files (UTF-8)
	 * @type
	 */
	protected $encoding = 'UTF-8';

	/**
	 * Full ISO Locale (en_EN)
	 * @type String
	 */
	protected $locale;

	/**
	 * Fallback locale
	 * @type String
	 */
	protected $fallbackLocale;

    /**
     * Gets the Charset encoding for files (UTF-8).
     * @return mixed
     */
    public function getEncoding(){
        return $this->encoding;
    }

    /**
     * Sets the Charset encoding for files (UTF-8).
     * @param mixed $encoding the encoding
     * @return self
     */
    public function setEncoding($encoding){
        $this->encoding = $encoding;
        return $this;
    }

    /**
     * Gets the Full ISO Locale (en_EN).
     * @return mixed
     */
    public function getLocale(){
        return $this->locale;
    }

    /**
     * Sets the Full ISO Locale (en_EN).
     * @param mixed $locale the locale
     * @return self
     */
    public function setLocale($locale){
        $this->locale = $locale;
        return $this;
    }

    /**
     * Gets the Fallback locale.
     * @return mixed
     */
    public function getFallbackLocale(){
        return $this->fallbackLocale;
    }

    /**
     * Sets the Fallback locale.
     * @param mixed $fallbackLocale the fallback locale
     * @return self
     */
    public function setFallbackLocale($fallbackLocale){
        $this->fallbackLocale = $fallbackLocale;
        return $this;
    }

    /**
     * Return the current locale
     */
    public function __toString(){
    	return implode(array(
    		$this->locale,
    		$this->encoding
		), ".");
    }
}