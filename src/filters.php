<?php

App::before(function($request)
{
	/**
	 * The package need to be initialize, the locale will 
	 * be available from the first method call. If you have 
	 * async calls in your project, this filter starts the 
	 * locale environment before each request.
	 */
    LaravelGettext::getLocale();
});