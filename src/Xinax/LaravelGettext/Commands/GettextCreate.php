<?php

namespace Xinax\LaravelGettext\Commands;

class GettextCreate extends BaseCommand
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'gettext:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description =
        'Generates the initial directories and files for laravel-gettext.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $domainPath = $this->fileSystem->getDomainPath();

        // Compile views
        $this->filesystem->compileViews(\Config::get('view.paths'), storage_path());

        // Directories created counter
        $dirCreations = 0;

        try {

            // i18n base directory
            if ($this->fileSystem->checkBasePath()) {
                $this->comment("Base directory created ($domainPath)");    
                $dirCreations++;
            }

            // Locales
            $localesGenerated = $this->fileSystem->generateLocales();
            foreach ($localesGenerated as $localePath) {
                $this->comment("Directory for $locale created ($localePath)");
                $dirCreations++;
            }

            $this->info("Done!");
            
            if ($dirCreations) {
                $msg = "$dirCreations directories were created.";
            } else {
                $msg = "The directory structure is right. No directory creation were needed.";
            }

            $this->info($msg);

        } catch (\Exception $e) {
            $this->error($e->getFile() . ":" . $e->getLine() . " - " . $e->getMessage());
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
        return array();
    }
}
