# Laravel Gettext

<img src="https://api.travis-ci.org/xinax/laravel-gettext.svg?branch=master" alt="TravisCI">

*Laravel Gettext* is a package compatible with the version 4.2.x of the great Laravel PHP Framework. It provides a simple way to add localization support to Laravel applications. It is designed to work with *GNU Gettext* and *PoEdit*.

### 1. Requirements

- Composer - http://www.getcomposer.org
- Laravel 4.2.* - http://www.laravel.com
- php-gettext - http://www.php.net/manual/en/book.gettext.php
- GNU Gettext on system (and production server!) - http://www.gnu.org/software/gettext/
- PoEdit - http://poedit.net/

### 2. Install

Add the composer repository to your *composer.json* file:

```json
    "xinax/laravel-gettext": "1.x"
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
     * Default locale: this will be the default for your application. 
     * Is to be supposed that all strings are written in this language.
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

```php
    // an example view in blade
    {{ _('Translated string') }}
```

##### B. Translate with PoEdit

Open the PO file for the language that you want to translate with PoEdit. The PO files are located by default in *app/lang/i18n/lang_to_be_translated/LC_MESSAGES/messages.po*. 

<img src="https://raw.github.com/xinax/laravel-gettext/master/doc/poedit.png" />

Once PoEdit is loaded press the Update button to load all localized strings. You can repeat this step anytime you add a new localized string. 

Fill translation fields in PoEdit, when you ready save the file. The first time that you do this the MO files will be generated for each locale.

Refresh your browser. You're done!!!


##### C. Runtime methods

No route files included with this package. To change configuration on runtime you have these methods:

```php
    /**
     * Sets the Current locale.
     * Example param value: 'es_ES'
     *
     * @param mixed $locale the locale
     * @return LaravelGettext
     */
    LaravelGettext::setLocale($locale);
```

```php
    /**
     * Gets the Current locale.
     * Example returned value: 'es_ES'
     *
     * @return String
     */
     LaravelGettext::getLocale();
```

```php
    /**
     * Gets the language portion of the locale.
     * Eg from en_GB, returns en
     *
     * @return mixed
     */
    LaravelGettext::getLocaleLanguage()
```

```php
    /**
     * Sets the Current encoding.
     * Example param value: 'UTF-8'     
     * 
     * @param mixed $encoding the encoding
     * @return LaravelGettext
     */
     LaravelGettext::setEncoding($encoding);
```

```php
    /**
     * Gets the Current encoding.
     * Example returned value: 'UTF-8'       
     * 
     * @return String
     */
     LaravelGettext::getEncoding();
```



### 5. Extra notes

##### A. Route and controller implementation example:

app/routes.php

```php
    Route::get('/lang/{locale?}', [
        'as'=>'lang', 
        'uses'=>'HomeController@changeLang'
    ]);
```

app/controllers/HomeController.php
```php
    /**
     * Changes the current language and returns to previous page
     * @return Redirect
     */
    public function changeLang($locale=null){
        
        LaravelGettext::setLocale($locale);
        return Redirect::to(URL::previous());

    }
```

##### B. A basic language selector example:

```php
  <ul>
      @foreach(Config::get('laravel-gettext::config.supported-locales') as $locale)
            <li><a href="/lang/{{$locale}}">{{$locale}}</a></li>
      @endforeach
  </ul>
```

##### C. Built-in language selector:

You can use the built-in language selector in your views:

```php
    LaravelGettext::getSelector()->render();
```

It also supports custom labels:

```php
    LaravelGettext::getSelector([
        'en_US' => 'English',
        'es_ES' => 'Spanish',
        'de_DE' => 'Dutch',
    ])->render();
```    

##### D. Adding directories to search translations

You can achieve this editing the *source-paths* configuration array. By default app/views and app/controlles are set.

```php
    /**
     * Paths where PoEdit will search recursively for strings to translate. 
     * All paths are relative to app/ (don't use trailing slash).
     *
     * If you have already .po files with translations and the need to add 
     * another directory remember to call artisan gettext:update after do this.
     */
    'source-paths' => array(
        'controllers',
        'views',
    ),
```

Remember update your gettext files:

```bash
    php artisan gettext:update
```

This command will update your PO files and will keep the current translations intact. After this you can open PoEdit and click on update button to add the new text strings in the new paths.

##### D. About gettext cache

Sometimes when you edit/add translations on PO files the changes does not appear instantly. This is because the gettext cache system holds content. The most quick fix is restart your web server.

### 6. Contributing

If you want to help with the development of this package, you can:

- Warn about errors that you find, in issues section 
- Send me a pull request with your patch
- Fix my disastrous English in the documentation/comments ;-)
- Make a fork and create your own version of laravel-gettext
- Give a star to project!
