<?php

return array(

	/**
	 * Default locale: this will be the default for your application
	 * All localized strings is to be supposed that are written on this language
	 */
	'locale' => 'es_ES',

	/**
	 * Supported locales: An array containing all allowed locales 
	 */
	'supported-locales' => array(
		'es_ES',
		'en_US',
	),	

	/**
	 * Default encoding.
	 */
	'encoding' => 'UTF-8',

	/**
	 * -----------------------------------------------------------------------
	 * Every normal configuration work would ends here. 
	 * Te following values are for special cases.
	 * -----------------------------------------------------------------------
	 **/

	/**
	 * Fallback: When default locale is not available
	 */
	'fallback-locale' => 'en_US',

	/**
	 * Domain used for translations:
	 * It is the file name for .po and .mo files
	 */
	'domain' => 'messages',

	/**
	 * Base translations path 
	 * (don't use slash at end)
	 */
	'translations-path' => 'lang',

	/**
	 * Project name: used on .po file's headers
	 */
	'project' => 'MultilanguageLaravelApplication',

	/**
	 * Translator contact data (used on .po headers too)
	 */
	'translator' => 'James Translator <james@translations.colm>',

);