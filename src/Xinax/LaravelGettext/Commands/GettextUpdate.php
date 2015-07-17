<?php

namespace Xinax\LaravelGettext\Commands;

use Xinax\LaravelGettext\Exceptions\DirectoryNotFoundException;
use Symfony\Component\Console\Input\InputOption;

class GettextUpdate extends BaseCommand
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'gettext:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update PO files (when you modify configuration).';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $this->prepare();

        $domainPath = $this->fileSystem->getDomainPath();

        try {

            // Translation files base path
            if (!file_exists($domainPath)) {
                throw new DirectoryNotFoundException(
                    "You need to call gettext:create (No locale directory)");
            }

            $updatedCount = 0;
            $addedCount = 0;
            $domains = $this->configuration->getAllDomains();

            foreach ($this->configuration->getSupportedLocales() as $locale) {

                $localePath = $this->fileSystem->getDomainPath($locale);

                // New locale without .po file
                if (!file_exists($localePath)) {
                    
                    $this->fileSystem->addLocale($localePath, $locale);
                    $this->comment("New locale was added: $locale ($localePath)");
                    $addedCount++;

                } else {

                    // Domain by command line argument
                    if ($this->option('domain')) {
                        $domains = [$this->option('domain')];
                    }

                    // Update by domain(s)
                    foreach ($domains as $domain) {
                        $this->fileSystem->updateLocale($localePath, $locale, $domain);
                        $this->comment("PO file for locale: $locale/$domain updated successfuly");
                        $updatedCount++;    
                    }
                    
                }

            }

            $this->info("Done!");

            if ($addedCount) {
                $this->info("$addedCount new locales were added.");
            }

            if ($updatedCount) {
                $this->info("$updatedCount locales updated.");
            }

        } catch (\Exception $e) {
            $this->error($e->getFile() . ":" . $e->getLine() . " = " . $e->getMessage());
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array();
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        $options = array(
            array(
                'domain',
                '-d',
                InputOption::VALUE_OPTIONAL, 
                'Update files only for this domain',
                null
            )
        );

        return $options;
    }

}
