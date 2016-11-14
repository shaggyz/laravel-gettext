# Laravel Gettext

*Laravel Gettext* is a package compatible with the great Laravel PHP Framework. It provides a simple way to add localization support to Laravel applications. It is designed to work with *GNU gettext* and *Poedit*. Former versions of this package (before 4.x) works with the native php-gettext module. Current versions uses the Symfony translation package by default instead of native php extension.

[![Stable build Status](https://travis-ci.org/xinax/laravel-gettext.png?branch=4.0.3)](https://travis-ci.org/xinax/laravel-gettext) <a href="https://github.com/xinax/laravel-gettext/tree/4.0.3">Latest Laravel 5.3.x stable release (4.0.3)</a>

> Note: This documentation applies to laravel 5.3.x and 4.x branch. For older versions of laravel check the following links:

### Older versions

[![Stable build Status](https://travis-ci.org/xinax/laravel-gettext.png?branch=3.1.0)](https://travis-ci.org/xinax/laravel-gettext) <a href="https://github.com/xinax/laravel-gettext/tree/3.1.0">Latest Laravel 5.2.x stable release (3.1.0)</a>

[![Stable build Status](https://travis-ci.org/xinax/laravel-gettext.png?branch=3.0.3)](https://travis-ci.org/xinax/laravel-gettext) <a href="https://github.com/xinax/laravel-gettext/tree/3.0.3">Latest Laravel 5.1.x stable release (3.0.3)</a>

[![Stable build Status](https://travis-ci.org/xinax/laravel-gettext.png?branch=2.0.3)](https://travis-ci.org/xinax/laravel-gettext) <a href="https://github.com/xinax/laravel-gettext/tree/2.0.3">Latest Laravel 5.0 stable release (2.0.3)</a>

[![Stable build Status](https://travis-ci.org/xinax/laravel-gettext.png?branch=1.0.3)](https://travis-ci.org/xinax/laravel-gettext) <a href="https://github.com/xinax/laravel-gettext/tree/1.0.3">Latest Laravel 4.x stable release (1.0.3)</a>

[![Dev build Status](https://travis-ci.org/xinax/laravel-gettext.png?branch=master)](https://travis-ci.org/xinax/laravel-gettext) <a href="https://github.com/xinax/laravel-gettext/tree/master">Development master</a> Unstable, only for development (dev-master)

### 1. Requirements

- Composer - http://www.getcomposer.org
- Laravel 5.3.* - http://www.laravel.com
- Poedit - https://poedit.net/

Optional requirements if you want to use the native php-gettext extension:

- php-gettext - http://www.php.net/manual/en/book.gettext.php
- GNU gettext on system (and production server!) - https://www.gnu.org/software/gettext/

> You will need to update the 'handler' option to 'gettext' in order to use the native php-gettext module.

### 2. Install

Add the composer repository to your *composer.json* file:

```json
    "xinax/laravel-gettext": "4.x"
```

And run composer update. Once it's installed, you can register the service provider in config/app.php in the providers array:

```php
    'providers' = [
        // ...
        Xinax\LaravelGettext\LaravelGettextServiceProvider::class,
        // ...
    ]
```

Now you need to publish the configuration file in order to set your own application values:

```bash
    php artisan vendor:publish
```

This command creates the package configuration file in: ```config/laravel-gettext.php```.

You also need to register the LaravelGettext middleware in the ```app/Http/Kernel.php``` file:

```php
    protected $middlewareGroups = [
        'web' => [
            // ...
            \Xinax\LaravelGettext\Middleware\GettextMiddleware::class,
        ],
        // ...
    ]
```

> Be sure to add the line after ```Illuminate\Session\Middleware\StartSession```, otherwise the locale won't be saved into the session.

### 3. Configuration

At this time your application has full gettext support. Now you need to set some configuration values in ```config/laravel-gettext.php```.

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
     * Supported locales: An array containing all allowed languages
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

Ok, now it's configured. It's time to generate the directory structure and translation files for the first time.

> Make sure you have write permissions on ```resources/``` before you run this command

```bash
    php artisan gettext:create
```

With this command the needed directories and files are created on **resources/lang/i18n**

### 4. Workflow

##### A. Write strings :D

By default *LaravelGettext* looks on app/Http/Controllers and resources/views recursively searching for translations. Translations are all texts printed with the **__()** function. Let's look a simple view example:

```php
    // an example view file
    echo 'Non translated string';
    echo __('Translated string');
    echo __('Another translated string');
    // with parameter
    $str = 'parameter';
    $n = 2;
    echo __('Translated string with %s', $str);
    echo __('%dnd translated string with %s', [$n, $str]);
```

```php
    // an example view in blade
    {{ __('Translated string') }}
```

> Poedit doesn't "understand" blade syntax. When using blade views you must run ```php artisan gettext:update``` in order to compile all blade views to plain php before update the translations in Poedit

##### B. Plural strings

The plural translations follow the same pattern above. Plural translations are all texts printed with the **_n()** function, and it follow the <a href="http://php.net/manual/en/function.ngettext.php">php ngettext</a>. Let's look a simple view example:

```php
    // an example view file
    $n = 2;
    echo ($n > 1) ? 'Non translated plural string' : 'Non translated string';
    echo _n('Translated string', 'Translated plural string', $n);
    // with parameter
    $str = 'parameter';
    echo _n('Translated string %s', 'Translated plural string %s', 2, $str);
```

```php
    // an example view in blade
    {{ _n('Translated string', 'Translated plural string', $n) }}
```

> The Poedit keywords are defined in configuration file with this default pattern:
```php
    ['_n:1,2', 'ngettext:1,2']
```
See <a href="http://docs.translatehouse.org/projects/localization-guide/en/latest/l10n/pluralforms.html?id=l10n/pluralforms">Plural forms</a> used by Poedit to configure for your language.

##### C. Translate with Poedit

Open the PO file for the language that you want to translate with Poedit. The PO files are located by default in **resources/lang/i18n/[locale]/LC_MESSAGES/[domain].po**. If you have multiple gettext domains, one file is generated by each domain.

<img src="https://raw.github.com/xinax/laravel-gettext/master/doc/poedit.png" />

Once Poedit is loaded press the Update button to load all localized strings. You can repeat this step anytime you add a new localized string.

Fill translation fields in Poedit and save the file. The first time that you do this the MO files will be generated for each locale.

##### C. Runtime methods

To change configuration on runtime you have these methods:

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

```php
    /**
     * Sets the current domain
     *
     * @param String $domain
     */
    LaravelGettext::setDomain($domain);
```

```php
    /**
     * Returns the current domain
     *
     * @return String
     */
    LaravelGettext::getDomain();
```

```php
    /**
     * Returns the language selector object
     *
     * @param  Array $labels
     * @return LanguageSelector
     */
    LaravelGettext::getSelector($labels = []);
```


### 5. Features and examples:

#### A. Route and controller implementation example:

app/Http/routes.php

```php
    Route::get('/lang/{locale?}', [
        'as'=>'lang',
        'uses'=>'HomeController@changeLang'
    ]);
```

app/Http/Controllers/HomeController.php

```php
    /**
     * Changes the current language and returns to previous page
     * @return Redirect
     */
    public function changeLang($locale=null)
    {
        LaravelGettext::setLocale($locale);
        return Redirect::to(URL::previous());
    }
```

#### B. A basic language selector example:

```php
  <ul>
      @foreach(Config::get('laravel-gettext.supported-locales') as $locale)
            <li><a href="/lang/{{$locale}}">{{$locale}}</a></li>
      @endforeach
  </ul>
```

#### C. Built-in language selector:

You can use the built-in language selector in your views:

```php
    // Plain php:
    LaravelGettext::getSelector()->render();

    // Blade views:
    {!! LaravelGettext::getSelector()->render() !!}
```

It also supports custom labels:

```php
    LaravelGettext::getSelector([
        'en_US' => 'English',
        'es_ES' => 'Spanish',
        'de_DE' => 'Deutsch',
    ])->render();
```

#### D. Adding source directories and domains

You can achieve this editing the **source-paths** configuration array. By default resources/views and app/Http/Controllers are set.

```php
    /**
     * Paths where Poedit will search recursively for strings to translate.
     * All paths are relative to app/ (don't use trailing slash).
     *
     * Remember to call artisan gettext:update after change this.
     */
    'source-paths' => array(
        'Http/Controllers',
        '../resources/views',
        'foo/bar',              // app/foo/bar
    ),
```

You may want your **translations in different files**. Translations in GNUGettext are separated by domains, domains are simply context names.

Laravel-Gettext set always a default domain that contains all paths that doesn't belong to any domain, its name is established by the 'domain' configuration option.

To add a new domain just wrap your paths in the desired domain name, like this example:

```php
    'source-paths' => array(
        'frontend' => array(
            'Http/Controllers',
            '../resources/views/frontend',
        ),
        'backend' => array(
            '../resources/views/backend',
        ),
        '../resources/views/misc',
    ),
```

This configuration generates three translation files by each language: **messages.po**, **frontend.po** and **backend.po**

To change the current domain in runtime (a route-middleware would be a nice place for do this):

```php
    LaravelGettext::setDomain("backend");
```

**Remember:** *update your gettext files every time you change the 'source-paths'* option, otherwise is not necessary.

```bash
    php artisan gettext:update
```

This command will update your PO files and will keep the current translations intact. After this you can open Poedit and click on update button to add the new text strings in the new paths.

You can update only the files of a single domain with the same command:

```bash
    php artisan gettext:update --domain backend
```

#### E. About gettext cache (only applies to php-gettext native module)

Sometimes when you edit/add translations on PO files the changes does not appear instantly. This is because the gettext cache system holds content. The most quick fix is restart your web server.

### 6. Contributing

If you want to help with the development of this package, you can:

- Warn about errors that you find, in issues section
- Send me a pull request with your patch
- Fix my disastrous English in the documentation/comments ;-)
- Make a fork and create your own version of laravel-gettext
- Give a star!
