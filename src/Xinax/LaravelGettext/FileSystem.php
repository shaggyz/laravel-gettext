<?php namespace Xinax\LaravelGettext;

use Xinax\LaravelGettext\Config\Models\Config;
use Xinax\LaravelGettext\Exceptions\LocaleFileNotFoundException;
use Xinax\LaravelGettext\Exceptions\DirectoryNotFoundException;
use Xinax\LaravelGettext\Exceptions\FileCreationException;

class FileSystem {

    /**
     * Package configuration model
     * @var Config
     */
    protected $configuration;

    /**
     * Sets configuration
     * 
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->configuration = $config;
    }

    /**
     * Build views in order to parse php files
     *
     * @param Array $viewPaths
     * @param String $outputDir
     *
     * @return Boolean status
     */
    public function compileViews(Array $viewPaths, $outputDir)
    {
        // Check the output directory
        $targetDir = $outputDir . '/views';

        if ( !is_dir($targetDir) || !is_writable($targetDir)) {

            if ( !mkdir($targetDir) ) {
                throw new DirectoryNotFoundException(
                    "I need a writeable directory in $targetDir to compile views!"
                );    
            }
            
        }

        foreach ( $viewPaths as $path ) {

            $fs = new \Illuminate\Filesystem\Filesystem($path);
            $glob = $fs->glob(realpath($path) . '/{,**/}*.php', GLOB_BRACE);
            $compiler = new \Illuminate\View\Compilers\BladeCompiler($fs, $targetDir);

            foreach ($glob as $file) {
                
                $compiler->setPath($file);
                $contents = $compiler->compileString($fs->get($file));
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
            $this->configuration->getBasePath(),
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
     * @param  Boolean $write
     * @return Integer | String
     */
    public function createPOFile($path, $locale, $write = true)
    {

        $project = $this->configuration->getProject();
        $timestamp = date("Y-m-d H:iO");
        $translator = $this->configuration->getTranslator();
        $encoding = $this->configuration->getEncoding();

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
        $template .= '"X-Poedit-Basepath: ' . $this->getRelativePath($this->configuration->getBasePath(), $path . '/LC_MESSAGES/') . '\n' . "\"\n";
        $template .= '"X-Poedit-SourceCharset: ' . $encoding . '\n' . "\"\n";

        // Source paths
        $sourcePaths = $this->configuration->getSourcePaths();

        $i = 0;
        foreach ($sourcePaths as $sourcePath) {
            $template .= '"X-Poedit-SearchPath-' . $i . ': ' . $sourcePath . '\n' . "\"\n";
            $i++;
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
     * Adds a new locale directory + .po file
     *
     * @param  String                $localePath
     * @param  String                $locale
     * @throws FileCreationException
     */
    public function addLocale($localePath, $locale)
    {
        if (!@mkdir($localePath)) {
            throw new FileCreationException(
                "I can't create the directory: $localePath");
        }

        $localeGettext = $localePath .
            DIRECTORY_SEPARATOR .
            "LC_MESSAGES";

        if (!@mkdir($localeGettext)) {
            throw new FileCreationException(
                "I can't create the directory: $localeGettext");
        }

        foreach ($this->configuration->getAllDomains() as $domain) {

            $poPath = $localeGettext .
            DIRECTORY_SEPARATOR .
            $domain .
            ".po";

            if (!$this->createPOFile($poPath, $locale)) {
                throw new FileCreationException(
                    "I can't create the file: $poPath");
            }

        }

        

    }

    /**
     * Update the .po file headers (mainly source-file paths)
     *
     * @param  String                      $localePath
     * @param  String                      $locale
     * @throws LocaleFileNotFoundException
     * @return Boolean 
     */
    public function updateLocale($localePath, $locale)
    {

        $localePOPath = implode(array(
            $localePath,
            "LC_MESSAGES",
            $this->configuration->getDomain() . ".po",
        ), DIRECTORY_SEPARATOR);

        if (!file_exists($localePOPath) ||
            !$localeContents = file_get_contents($localePOPath)
        ) {
            throw new LocaleFileNotFoundException(
                "I can't read $localePOPath verify your locale structure");
        }

        $newHeader = $this->createPOFile($localePath, $locale, false);

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
    * @param String $path
    * @param String $from
    * @return String $path
    * @author Laurent Goussard
    **/
    public function getRelativePath($path, $from = __FILE__)
    {
        $path = explode(DIRECTORY_SEPARATOR, $path);
        $from = explode(DIRECTORY_SEPARATOR, dirname($from.'.'));
        $common = array_intersect_assoc($path, $from);

        $base = array('.');
        if ( $pre_fill = count( array_diff_assoc($from, $common) ) ) {
            $base = array_fill(0, $pre_fill, '..');
        }
        $path = array_merge( $base, array_diff_assoc($path, $common) );

        return implode(DIRECTORY_SEPARATOR, $path);
    }    

    /**
     * Checks for the translations base directory, also tries to create it if not exist.
     * Returns a boolean that indicates if any directory was created.
     *
     * @throws FileCreationException If directory is unreachable
     * @return Boolean
     */
    public function checkBasePath()
    {
        $domainPath = $this->getDomainPath();

        // Translation files base path
        if (!file_exists($domainPath)) {
            if (!@mkdir($domainPath)) {
                throw new FileCreationException(
                    "I can't create the directory: $domainPath");
            }
            return true;
        }        

        return false;
    }

    /**
     * Checks the needed directory structure
     *
     * @throws Exceptions\DirectoryNotFoundException
     * @return boolean
     */
    public function filesystemStructure()
    {
        // Base path
        $this->checkBasePath();
        $domainPath = $this->getDomainPath();

        // Translation files base path
        if (!file_exists($domainPath)) {
            throw new Exceptions\DirectoryNotFoundException(
                "Missing base required directory: $domainPath");
        }

        foreach ($this->configuration->getSupportedLocales() as $locale) {

            // Default locale is not needed
            if ($locale == $this->configuration->getLocale()) {
                continue;
            }

            $localePath = $this->getDomainPath($locale);
            if (!file_exists($localePath)) {
                throw new Exceptions\DirectoryNotFoundException(
                    "Missing locale required directory: $localePath");
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
        $localePaths = array();

        // Locale directories
        foreach ($this->configuration->getSupportedLocales() as $locale) {

            // We don't want a locale folder for the default language
            if ($locale == $this->configuration->getLocale()) {
                continue;
            }

            $localePath = $this->getDomainPath($locale);

            if (!file_exists($localePath)) {

                // Locale directory is created
                $this->addLocale($localePath, $locale);

                array_push($localePaths, $localePath);

            }

        }

        return $localePaths;
    }



}