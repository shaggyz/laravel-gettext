<?php namespace Xinax\LaravelGettext;

use Xinax\LaravelGettext\Config\Models\Config;
use Xinax\LaravelGettext\Exceptions\LocaleFileNotFoundException;
use Xinax\LaravelGettext\Exceptions\DirectoryNotFoundException;
use Xinax\LaravelGettext\Exceptions\FileCreationException;

class FileSystem {

    /**
     * Package configuration model
     *
     * @var Config
     */
    protected $configuration;

    /**
     * File system base path
     * All paths will be relative to this
     *
     * @var String
     */
    protected $basePath;

    /**
     * Storage path for file generation
     *
     * @var String
     */
    protected $storagePath;

    /**
     * Storage directory name for view compilation
     *
     * @var String
     */
    protected $storageContainer;

    /**
     * Sets configuration
     *
     * @param Config $config
     * @param String $basePath
     * @param String $storagePath
     */
    public function __construct(Config $config, $basePath, $storagePath)
    {
        $this->configuration = $config;
        $this->basePath = $basePath;
        $this->storagePath = $storagePath;
        $this->storageContainer = "framework";
    }

    /**
     * Build views in order to parse php files
     *
     * @param Array $viewPaths
     * @param String $domain
     *
     * @return Boolean status
     */
    public function compileViews(Array $viewPaths, $domain)
    {
        // Check the output directory
        $targetDir = $this->storagePath . DIRECTORY_SEPARATOR . $this->storageContainer;

        if (!file_exists($targetDir)) {
            $this->createDirectory($targetDir);
        }

        // Domain separation
        $domainDir = $targetDir . DIRECTORY_SEPARATOR . $domain;
        $this->clearDirectory($domainDir);
        $this->createDirectory($domainDir);

        foreach ( $viewPaths as $path ) {

            $path = $this->basePath . DIRECTORY_SEPARATOR . $path;

            $fs = new \Illuminate\Filesystem\Filesystem($path);
            $files = $fs->allFiles(realpath($path));
            $compiler = new \Illuminate\View\Compilers\BladeCompiler($fs, $domainDir);

            foreach ($files as $file) {
                $filePath = $file->getRealPath();
                $compiler->setPath($filePath);
                $contents = $compiler->compileString($fs->get($filePath));
                $compiledPath = $compiler->getCompiledPath($compiler->getPath());

                $fs->put($compiledPath . '.php', $contents);
            }

        }

        return true;
    }

    /**
     * Constructs and returns the full path to
     * translation files
     *
     * @param  String $append
     * @return String
     */
    public function getDomainPath($append = null)
    {
        $path = array(
            $this->basePath,
            $this->configuration->getTranslationsPath(),
            "i18n"
        );

        if (!is_null($append)) {
            array_push($path, $append);
        }

        return implode(DIRECTORY_SEPARATOR, $path);

    }

    /**
     * Creates a configured .po file on $path. If write is true the file will
     * be created, otherwise the file contents are returned.
     *
     * @param  String  $path
     * @param  String  $locale
     * @param  String  $domain
     * @param  Boolean $write
     * @return Integer | String
     */
    public function createPOFile($path, $locale, $domain, $write = true)
    {
        $project = $this->configuration->getProject();
        $timestamp = date("Y-m-d H:iO");
        $translator = $this->configuration->getTranslator();
        $encoding = $this->configuration->getEncoding();

        // L5 new structure, language resources are now here
        $relativePath = "../../../../../app";

        $template = 'msgid ""' . "\n";
        $template .= 'msgstr ""' . "\n";
        $template .= '"Project-Id-Version: ' . $project . '\n' . "\"\n";
        $template .= '"POT-Creation-Date: ' . $timestamp . '\n' . "\"\n";
        $template .= '"PO-Revision-Date: ' . $timestamp . '\n' . "\"\n";
        $template .= '"Last-Translator: ' . $translator . '\n' . "\"\n";
        $template .= '"Language-Team: ' . $translator . '\n' . "\"\n";
        $template .= '"Language: ' . $locale . '\n' . "\"\n";
        $template .= '"MIME-Version: 1.0' . '\n' . "\"\n";
        $template .= '"Content-Type: text/plain; charset=' . $encoding . '\n' . "\"\n";
        $template .= '"Content-Transfer-Encoding: 8bit' . '\n' . "\"\n";
        $template .= '"X-Generator: Poedit 1.5.4' . '\n' . "\"\n";
        $template .= '"X-Poedit-KeywordsList: _' . '\n' . "\"\n";
        $template .= '"X-Poedit-Basepath: ' . $relativePath . '\n' . "\"\n";
        $template .= '"X-Poedit-SourceCharset: ' . $encoding . '\n' . "\"\n";

        // Source paths
        $sourcePaths = $this->configuration->getSourcesFromDomain($domain);

        // Compiled views on paths
        if (count($sourcePaths)) {

            // View compilation
            $this->compileViews($sourcePaths, $domain);
            array_push($sourcePaths, $this->getStorageForDomain($domain));

            $i = 0;
            foreach ($sourcePaths as $sourcePath) {
                $template .= '"X-Poedit-SearchPath-' . $i . ': ' . $sourcePath . '\n' . "\"\n";
                $i++;
            }

        }

        if ($write) {

            // File creation
            $file = fopen($path, "w");
            $result = fwrite($file, $template);
            fclose($file);

            return $result;

        } else {

            // Contents for update
            return $template . "\n";
        }

    }

    /**
     * Tries to create a directory in $path
     *
     * @param $path
     * @throws Exceptions\FileCreationException
     */
    protected function createDirectory($path)
    {
        if (!mkdir($path)) {
            throw new FileCreationException(
                "I can't create the directory: $path");
        }
    }

    /**
     * Adds a new locale directory + .po file
     *
     * @param  String                $localePath
     * @param  String                $locale
     * @throws FileCreationException
     */
    public function addLocale($localePath, $locale)
    {
        $this->createDirectory($localePath);

        $gettextPath = $localePath .
            DIRECTORY_SEPARATOR .
            "LC_MESSAGES";

        $this->createDirectory($gettextPath);

        // File generation for each domain
        foreach ($this->configuration->getAllDomains() as $domain) {

            $localePOPath = implode(array(
                $localePath,
                "LC_MESSAGES",
                $domain . ".po",
            ), DIRECTORY_SEPARATOR);

            if (!$this->createPOFile($localePOPath, $locale, $domain)) {
                throw new FileCreationException(
                    "I can't create the file: $localePOPath");
            }

        }

    }

    /**
     * Update the .po file headers (mainly source-file paths) by domain
     *
     * @param  String                      $localePath
     * @param  String                      $locale
     * @param  String                      $domain
     * @throws LocaleFileNotFoundException
     * @return Boolean
     */
    public function updateLocale($localePath, $locale, $domain)
    {
        $localePOPath = implode(array(
            $localePath,
            "LC_MESSAGES",
            $domain . ".po",
        ), DIRECTORY_SEPARATOR);

        if (!file_exists($localePOPath) ||
            !$localeContents = file_get_contents($localePOPath)
        ) {
            throw new LocaleFileNotFoundException(
                "I can't read $localePOPath verify your locale structure");
        }

        $newHeader = $this->createPOFile($localePOPath, $locale, $domain, false);

        // Header replacement
        $localeContents = preg_replace('/^([^#])+:?/', $newHeader, $localeContents);

        if (!file_put_contents($localePOPath, $localeContents)) {
            throw new LocaleFileNotFoundException("I can't write on $localePOPath");
        }

        return true;

    }

    /**
     * Return the relative path from a file or directory to another
     *
     * @param    String          $from
     * @param    String          $to
     * @return   String          $path
     * @author   Laurent Goussard
     **/
    public function getRelativePath($from, $to)
    {
        // some compatibility fixes for Windows paths
        $from = is_dir($from) ? rtrim($from, '\/') . '/' : $from;
        $to   = is_dir($to)   ? rtrim($to, '\/') . '/'   : $to;
        $from = str_replace('\\', '/', $from);
        $to   = str_replace('\\', '/', $to);

        $from     = explode('/', $from);
        $to       = explode('/', $to);
        $relPath  = $to;

        foreach($from as $depth => $dir) {
            // find first non-matching dir
            if($dir === $to[$depth]) {
                // ignore this directory
                array_shift($relPath);
            } else {
                // get number of remaining dirs to $from
                $remaining = count($from) - $depth;
                if($remaining > 1) {
                    // add traversals up to first matching dir
                    $padLength = (count($relPath) + $remaining - 1) * -1;
                    $relPath = array_pad($relPath, $padLength, '..');
                    break;
                } else {
                    $relPath[0] = './' . $relPath[0];
                }
            }
        }

        return implode('/', $relPath);

    }

    /**
     * Checks the needed directories. Optionally checks
     * each locale directory, if $checkLocales is true.
     *
     * @param bool $checkLocales
     * @return bool
     * @throws Exceptions\DirectoryNotFoundException
     */
    public function checkDirectoryStructure($checkLocales = false)
    {
        // Application base path
        if (!file_exists($this->basePath)) {
            throw new Exceptions\DirectoryNotFoundException(
                "Missing root path directory: " . $this->basePath .
                ", check the 'base-path' key in your configuration."
            );
        }

        // Domain path
        $domainPath = $this->getDomainPath();

        // Translation files domain path
        if (!file_exists($domainPath)) {
            throw new Exceptions\DirectoryNotFoundException(
                "Missing base required directory: $domainPath" .
                "<br>Remember run <b>artisan gettext:create</b> for first time."
            );
        }

        if ($checkLocales) {
            foreach ($this->configuration->getSupportedLocales() as $locale) {

                // Default locale is not needed
                if ($locale == $this->configuration->getLocale()) {
                    continue;
                }

                $localePath = $this->getDomainPath($locale);
                if (!file_exists($localePath)) {
                    $hint = "<br>May be you forget run <b>artisan gettext:update</b>?";
                    throw new Exceptions\DirectoryNotFoundException(
                        "Missing locale required directory: $localePath" . $hint);
                }

            }
        }

        return true;
    }

    /**
     * Creates the localization directories and files, by domain
     * Returns an array with all created paths
     *
     * @return Array paths
     */
    public function generateLocales()
    {
        // Application base path
        $this->createDirectory($this->getDomainPath());

        $localePaths = array();

        // Locale directories
        foreach ($this->configuration->getSupportedLocales() as $locale) {

            $localePath = $this->getDomainPath($locale);

            if (!file_exists($localePath)) {

                // Locale directory is created
                $this->addLocale($localePath, $locale);

                array_push($localePaths, $localePath);

            }

        }

        return $localePaths;
    }


    /**
     * Gets the package configuration model.
     *
     * @return Config
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Sets the Package configuration model.
     *
     * @param Config $configuration the configuration
     * @return self
     */
    public function setConfiguration(Config $configuration)
    {
        $this->configuration = $configuration;
        return $this;
    }

    /**
     * Gets the File system base path
     * All paths will be relative to this.
     *
     * @return String
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * Sets the File system base path
     * All paths will be relative to this.
     *
     * @param String $basePath the base path
     * @return self
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
        return $this;
    }

    /**
     * Gets the Storage path for file generation.
     *
     * @return String
     */
    public function getStoragePath()
    {
        return $this->storagePath;
    }

    /**
     * Sets the Storage path for file generation.
     *
     * @param String $storagePath the storage path
     * @return self
     */
    public function setStoragePath($storagePath)
    {
        $this->storagePath = $storagePath;
        return $this;
    }

    /**
     * Returns the full path for a domain storage directory
     *
     * @param  String $domain
     * @return String
     */
    public function getStorageForDomain($domain)
    {
        $domainPath = $this->storagePath .
            DIRECTORY_SEPARATOR .
            $this->storageContainer .
            DIRECTORY_SEPARATOR .
            $domain;

        return $this->getRelativePath($this->basePath, $domainPath);
    }

    /**
     * Removes the directory contents recursively
     *
     * @param  String $path
     * @return void
     */
    public static function clearDirectory($path)
    {
        if (!file_exists($path)) {
            return;
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileinfo) {
            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            $todo($fileinfo->getRealPath());
        }

        rmdir($path);
    }
}
