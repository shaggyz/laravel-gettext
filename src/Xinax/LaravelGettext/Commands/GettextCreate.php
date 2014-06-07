<?php

namespace Xinax\LaravelGettext\Commands;
use Xinax\LaravelGettext\Exceptions\FileCreationException;

class GettextCreate extends BaseCommand {

	/**
	 * The console command name.
	 * @var string
	 */
	protected $name = 'gettext:create';

	/**
	 * Package configuration
	 */
	protected $configuration;

	/**
	 * The console command description.
	 * @var string
	 */
	protected $description = 
		'Generates the initial directories and files for laravel-gettext.';

	/**
	 * Execute the console command.
	 * @return mixed
	 */
	public function fire(){

		$domainPath = $this->getDomainPath();

    	// Directories created counter
    	$dirCount = 0;

    	try {
	
			// Translation files base path
	    	if(!file_exists($domainPath)){
	    		if(!@mkdir($domainPath)){
	    			throw new FileCreationException(
	    				"I can't create the directory: $domainPath");	
	    		} 

	    		$dirCount++;
				$this->comment("Base directory created ($domainPath)");
	    		
	    	}

	    	foreach ($this->configuration->getSupportedLocales() as $locale){

	    		// We don't want a locale folder for the default language
	    		if($locale == $this->configuration->getLocale()){
	    			continue;
	    		}
	    		
	    		$localePath = $this->getDomainPath($locale);

	    		if(!file_exists($localePath)){

	    			$this->addLocale($localePath, $locale);
		    		$dirCount++;
		    		$this->comment("Directory for $locale created ($localePath)");

	    		}
	    	}   

	    	$this->info("Done!");

	    	$msg = "Structure is right! no directory creation were needed.";
	    	if($dirCount){
	    		$msg = "$dirCount directories were created.";	
	    	} 
	    	
	    	$this->info($msg);

    	} catch(\Exception $e){
    		$this->error($e->getFile() . ":" . $e->getLine() . " - " . $e->getMessage());
    	}
	}

	/**
	 * Get the console command arguments.
	 * @return array
	 */
	protected function getArguments(){
		return array();
	}

	/**
	 * Get the console command options.
	 * @return array
	 */
	protected function getOptions(){
		return array();
	}

}
