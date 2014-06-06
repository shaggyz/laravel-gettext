<?php

namespace Xinax\LaravelGettext\Config;
use \Xinax\LaravelGettext\Exceptions\RequiredConfigurationFileException;
use \Xinax\LaravelGettext\Exceptions\RequiredConfigurationKeyException;
use \Config;

class ConfigManager{

	/**
	 * Config singleton
	 */
	protected $config = null;

	/**
	 * Returns the package configuration container
	 * @return Models\Config
	 */
	public function get(){

		if(is_null($this->config)){
			$this->setConfig();
		}

		return $this->config;

	}

	/**
	 * Sets the configuration
	 */
	protected function setConfig(){

		$config = Config::get('laravel-gettext::config');

		if(!$config || !count($config)){
			throw new RequiredConfigurationFileException(
				"You need to publish the package configuration file");
		}

		$this->config = $this->generateFromArray($config);

	}

	/**
	 * Creates the configuration container and 
	 * checks from required fields
	 */
	protected function generateFromArray(array $config){

		$requiredKeys = array('locale', 'fallback-locale', 'encoding');

		foreach ($requiredKeys as $key) {
			if(!array_key_exists($key, $config)){
				throw new RequiredConfigurationKeyException(
					"Unconfigured required value: $key");
			}
		}

		$container = new Models\Config();
		$container->setLocale($config['locale'])
			->setEncoding($config['encoding'])
			->setFallbackLocale($config['fallback-locale']);

		return $container;

	}


}