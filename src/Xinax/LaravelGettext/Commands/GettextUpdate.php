<?php

namespace Xinax\LaravelGettext\Commands;
use Xinax\LaravelGettext\Exceptions\DirectoryNotFoundException;

class GettextUpdate extends BaseCommand {

	/**
	 * The console command name.
	 * @var string
	 */
	protected $name = 'gettext:update';

	/**
	 * The console command description.
	 * @var string
	 */
	protected $description = 'Update PO files (when you modify configuration).';

	/**
	 * Execute the console command.
	 * @return mixed
	 */
	public function fire(){
		
		$domainPath = $this->getDomainPath();
		
		try {
			
			// Translation files base path
	    	if(!file_exists($domainPath)){
				throw new DirectoryNotFoundException(
					"You need to call gettext:create (No locale directory)");
	    	}

	    	$updatedCount = 0;
	    	$addedCount = 0;
	    	foreach ($this->configuration->getSupportedLocales() as $locale){

	    		// We don't want a locale folder for the default language
	    		if($locale == $this->configuration->getLocale()){
	    			continue;
	    		}

	    		$localePath = $this->getDomainPath($locale);

	    		// New locale without .po file
	    		if(!file_exists($localePath)){
	    			$this->addLocale($localePath, $locale);
	    			$this->comment("New locale was added: $locale ($localePath)");
	    			$addedCount++;
	    		} else {
	    			$this->updateLocale($localePath, $locale);
	    			$updatedCount++;
	    		}

	    	}

	    	$this->info("Done!");

	    	if($addedCount){
	    		$this->info("$addedCount new locales were added.");
	    	}

	    	if($updatedCount){
	    		$this->info("$updatedCount locales were updated.");
	    	}

		} catch (\Exception $e) {
			$this->error($e->getMessage());
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
