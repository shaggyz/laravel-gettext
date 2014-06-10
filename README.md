# Laravel Gettext

*Laravel Gettext* is a package compatible with the version 4.2.x of the great Laravel PHP Framework. It provides a simple way to add localization support to Laravel applications. It is designed to work with *GNU Gettext* and *PoEdit*.

### 1. Requirements

- Composer - http://www.getcomposer.org
- Laravel 4.2.* - http://www.laravel.com
- php-gettext - http://www.php.net/manual/en/book.gettext.php
- GNU Gettext on system (and production server!) - http://www.gnu.org/software/gettext/
- PoEdit - http://poedit.net/

### 2. Instalation

Add the composer repository to your *composer.json* file:

```json
	"xinax/laravel-gettext": "dev-master"
```

And run composer update. Once it's installed, you can register the service provider in app/config/app.php in the providers array:

```php
  'providers' => array(
      'Xinax\LaravelGettext\LaravelGettextServiceProvider',
  )
```

Now you need to publish the configuration file in order to set your own application values:

```bash
	php artisan config:publish xinax/laravel-gettext
```

This command set the package configuration file in: *app/config/packages/xinax/laravel-getttext/config.php*.

### 3. Configuration

At this time your application have full gettext support. Now you need to set some configuration values in *config.php*.

```php
	/**
	 * Default locale: this will be the default for your application all 
	 * localized strings. Is to be supposed that all strings are written 
	 * on this language.
	 */
	'locale' => 'es_ES', 
```

```php
	/**
	 * Supported locales: An array containing all allowed locales 
	 */
	'supported-locales' => array(
		'es_ES',
		'en_US',
		'it_IT',
		'es_AR',
	),	
```

```php
	/**
	 * Default charset encoding.
	 */
	'encoding' => 'UTF-8',
```

Ok, now is configured. Is time to generate the directory structure and translation files for first time:

```bash
	php artisan gettext:create
```

With this command the needed directories and files are created on *app/lang/i18n*

### 4. Workflow

##### A. Write strings :D

By default *LaravelGettext* looks on app/controllers and app/views recursively searching for translations. Translations are all texts printed with the *_()* function. Let's look a simple view example:

```php
	// an example view file
    echo 'Non translated string';
    echo _('Translated string');
    echo _('Another translated string');
```

Important Note: on blade templates you should use *<?= _('Foo') ?>* instead of *{{ _('Foo') }}*. 

##### B. Translate with PoEdit

Open the PO file for the language that you want to translate with PoEdit. The PO files are located by default in *app/lang/i18n/lang_to_be_translated/LC_MESSAGES/messages.po*. 

```python
	# POEDIT images with the view example strings loaded
```

Once PoEdit is loaded press the Update button to load all localized strings. You can repeat this step anytime you add a new localized string. 

Fill translation fields in PoEdit, when you ready save the file. The first time that you do this the MO files will be generated for each locale.

Refresh your browser. You're done!!!


##### C. Runtime methods

No route files included with this package. To change configuration on runtime you have these methods:

```php
    /**
     * Sets the Current locale.
     * Example param value: 'es_ES'
     * @param mixed $locale the locale
     * @return LaravelGettext
     */
	LaravelGettext::setLocale($locale);
```

```php
	/**
     * Gets the Current locale.
     * Example returned value: 'es_ES'
     * @return String
     */
     LaravelGettext::getLocale();
```

```php
	/**
     * Sets the Current encoding.
     * Example param value: 'UTF-8'     
     * @param mixed $encoding the encoding
     * @return LaravelGettext
     */
     LaravelGettext::setEncoding($encoding);
```

```php
    /**
     * Gets the Current encoding.
     * Example returned value: 'UTF-8'       
     * @return String
     */
     LaravelGettext::getEncoding();
```

### 5. Extra notes

TODO

### 6. Contributing

TODO

