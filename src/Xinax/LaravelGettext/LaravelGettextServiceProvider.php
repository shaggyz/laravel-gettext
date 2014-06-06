<?php 

namespace Xinax\LaravelGettext;
use Illuminate\Support\ServiceProvider;

/**
 * Laravel gettext main service provider
 */
class LaravelGettextServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 * @return void
	 */
	public function boot(){
		$this->package('xinax/laravel-gettext');
	}

	/**
	 * Register the service provider.
	 * @return void
	 */
	public function register(){
		
		// Main class register
		$this->app['laravel-gettext'] = $this->app->share(function($app){
			$gettext = new Gettext(new Config\ConfigManager);
			return new LaravelGettext($gettext);
		});

		// Auto alias :D
		$this->app->booting(function(){
			$loader = \Illuminate\Foundation\AliasLoader::getInstance();
			$loader->alias('LaravelGettext', 'Xinax\LaravelGettext\Facades\LaravelGettext');
		});
	}

	/**
	 * Get the services provided by the provider.
	 * @return array
	 */
	public function provides(){
		return array('laravel-gettext');
	}

}
