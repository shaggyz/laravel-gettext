<?php

namespace Xinax\LaravelGettext\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Xinax\LaravelGettext\Config\ConfigManager;
use Xinax\LaravelGettext\Exceptions\FileCreationException;

class CreateDirectories extends Command {

	/**
	 * The console command name.
	 * @var string
	 */
	protected $name = 'laravel-gettext:directories';

	/**
	 * Package configuration
	 */
	protected $configuration;

	/**
	 * The console command description.
	 * @var string
	 */
	protected $description = 
		'Generates the needed directory structure for laravel-gettext';

	/**
	 * Create a new command instance.
	 * @return void
	 */
	public function __construct(){

		$configManager = new ConfigManager;
		$this->configuration = $configManager->get();

		parent::__construct();

	}

	/**
	 * Execute the console command.
	 * @return mixed
	 */
	public function fire(){
		$this->filesystemStructure();
	}


    /**
     * Checks and generates the needed directory structure
     */
    protected function filesystemStructure(){

    	$domainPath = $this->getDomainPath();

    	// Directories created counter
    	$dirCount = 0;

    	try {
	
			// Translation files base path
	    	if(!file_exists($domainPath)){
	    		if(!@mkdir($domainPath)){
	    			throw new FileCreationException(
	    				"I can't create directories on $domainPath");	
	    		} 

	    		$dirCount++;
				$this->comment("Base directory created ($domainPath)");
	    		
	    	}

	    	foreach ($this->configuration->getSupportedLocales() as $locale){
	    		
	    		$localePath = $this->getDomainPath($locale);
	    		
	    		if(!file_exists($localePath)){
	    			if(!@mkdir($localePath)){
		    			throw new FileCreationException(
		    				"I can't create directories on: $localePath");
		    		}

		    		$dirCount++;
		    		$this->comment("Directory for $locale created ($localePath)");

	    		}
	    	}   

	    	$msg = "Structure is right! no directory creation were needed.";
	    	if($dirCount){
	    		$msg = "Done! $dirCount directories were created.";	
	    	} 
	    	
	    	$this->info($msg);

    	} catch(\Exception $e){
    		$this->error($e->getMessage());
    	}

    }

	/**
	 * Constructs and returns the full path to 
	 * translaition files 
	 * @return String
	 */	
	protected function getDomainPath($append=null){
		
		$path = array(
			app_path(),
			$this->configuration->getTranslationsPath(),
			"i18n"
		);

		if(!is_null($append)){
			array_push($path, $append);
		}

		return implode(DIRECTORY_SEPARATOR, $path);

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
