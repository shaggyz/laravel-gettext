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
	    			if(!@mkdir($localePath)){
		    			throw new FileCreationException(
		    				"I can't create the directory: $localePath");
		    		}

		    		$localeGettext = $localePath . 
		    				DIRECTORY_SEPARATOR . 
		    				"LC_MESSAGES";

					if(!@mkdir($localeGettext)){
		    			throw new FileCreationException(
		    				"I can't create the directory: $localeGettext");
		    		}		    		

		    		$poPath = $localeGettext . 
	    				  	DIRECTORY_SEPARATOR . 
	    				 	$this->configuration->getDomain() . 
	    				 	".po";

		    		if(!$this->createPOFile($poPath)){
		    			throw new FileCreationException(
		    				"I can't create the file: $poPath");	
		    		}

		    		$dirCount++;
		    		$this->comment("Directory for $locale created ($localeGettext)");

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
     * Creates an configured .po file on $path
     * @return Integer
     */
    protected function createPOFile($path){

    	$project = $this->configuration->getProject();
    	$timestamp = date("Y-m-d H:iO");
    	$translator = $this->configuration->getTranslator();
    	$encoding = $this->configuration->getEncoding();

    	$template = 'msgid ""'."\n";
		$template .= 'msgstr ""'."\n";
		$template .= '"Project-Id-Version: '.$project.'\n'."\"\n";
		$template .= '"POT-Creation-Date: '.$timestamp.'\n'."\"\n";
		$template .= '"PO-Revision-Date: '.$timestamp.'\n'."\"\n";
		$template .= '"Last-Translator: '.$translator.'\n'."\"\n";
		$template .= '"Language-Team: '.$translator.'\n'."\"\n";
		$template .= '"MIME-Version: 1.0'.'\n'."\"\n";
		$template .= '"Content-Type: text/plain; charset='.$encoding.'\n'."\"\n";
		$template .= '"Content-Transfer-Encoding: 8bit'.'\n'."\"\n";
		$template .= '"X-Generator: Poedit 1.5.4'.'\n'."\"\n";
		$template .= '"X-Poedit-KeywordsList: _'.'\n'."\"\n";
		$template .= '"X-Poedit-Basepath:'.app_path().'\n'."\"\n";
		$template .= '"X-Poedit-SourceCharset: '.$encoding.'\n'."\"\n";
		$template .= '"X-Poedit-SearchPath-0: controllers'.'\n'."\"\n";
		$template .= '"X-Poedit-SearchPath-1: views'.'\n'."\"\n";
		//$template .= '"X-Poedit-SearchPath-2: views/layouts'.'\n'."\"\n";

		$file = fopen($path, "w");
		$result = fwrite($file, $template);
		fclose($file);
		
		return $result;

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
