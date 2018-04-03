<?php
/**
 * Created by PhpStorm.
 * User: aaflalo
 * Date: 18-03-01
 * Time: 10:23
 */

namespace Xinax\LaravelGettext\FileLoader\Cache;

use Symfony\Component\Translation\Exception\InvalidResourceException;
use Symfony\Component\Translation\Loader\FileLoader;

class ApcuFileCacheLoader extends FileLoader
{

    /**
     * @var FileLoader
     */
    private $underlyingFileLoader;

    /**
     * ApcuFileCacheLoader constructor.
     *
     * @param FileLoader $underlyingFileLoader
     */
    public function __construct(FileLoader $underlyingFileLoader)
    {
        $this->underlyingFileLoader = $underlyingFileLoader;
    }


    /**
     * @param string $resource
     *
     * @return array
     *
     * @throws InvalidResourceException if stream content has an invalid format
     */
    protected function loadResource($resource)
    {
        if (!extension_loaded('apcu')) {
            return $this->underlyingFileLoader->loadResource($resource);
        }

        return $this->cachedMessages($resource);
    }

    /**
     * Calculate the checksum for the file
     *
     * @param $resource
     *
     * @return string
     */
    private function checksum($resource)
    {
        return filemtime($resource) . '-' . filesize($resource);
    }

    /**
     * Checksum saved in cache
     *
     * @param $resource
     *
     * @return string
     */
    private function cacheChecksum($resource)
    {
        return apcu_fetch($resource . '-checksum');
    }

    /**
     * Set the cache checksum
     *
     * @param $resource
     * @param $checksum
     *
     * @return array|bool
     */
    private function setCacheChecksum($resource, $checksum)
    {
        return apcu_store($resource . '-checksum', $checksum);
    }

    /**
     * Return the cached messages
     *
     * @param $ressource
     *
     * @return array
     */
    private function cachedMessages($ressource)
    {
        if ($this->cacheChecksum($ressource) == ($currentChecksum = $this->checksum($ressource))) {
            return apcu_fetch($ressource . '-messages');
        }

        $messages = $this->underlyingFileLoader->loadResource($ressource);

        apcu_store($ressource . '-messages', $messages);
        $this->setCacheChecksum($ressource, $currentChecksum);

        return $messages;
    }
}
