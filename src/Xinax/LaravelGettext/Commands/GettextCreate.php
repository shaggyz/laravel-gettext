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
     * Execute the console command
     */
    public function fire()
    {
        $this->prepare();

        // Directories created counter
        $dirCreations = 0;

        try {
            // Locales
            $localesGenerated = $this->fileSystem->generateLocales();

            foreach ($localesGenerated as $localePath) {
                $this->comment(sprintf("Locale directory created (%s)", $localePath));
                $dirCreations++;
            }

            $this->info("Finished");

            $msg = "The directory structure is right. No directory creation were needed.";

            if ($dirCreations) {
                $msg = $dirCreations . " directories has been created.";
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
        return [];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [];
    }
}
