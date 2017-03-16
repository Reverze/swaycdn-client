<?php

namespace SwayCDN\Client\Service\Exception\Cache;

class PackageCacheException extends \Exception
{
    /**
     * Throws an exception when package cache profile is not exists
     * @param string $packageName
     * @return \SwayCDN\Client\Service\Exception\Cache\PackageCacheException
     */
    public static function packageCacheProfileNotExists(string $packageName) : PackageCacheException
    {
        return (new PackageCacheException(sprintf("Cache profile is not exists for package '%s'", $packageName)));
    }
}

?>