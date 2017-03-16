<?php

namespace SwayCDN\Client\Service\Exception\Cache;

class RepositoryCacheException extends \Exception
{
    /**
     * Throws an exception when repository was not found in package cache profile
     * @param string $packageName
     * @param string $repositoryName
     * @return \SwayCDN\Client\Service\Exception\Cache\RepositoryCacheException
     */
    public static function repositoryNotFound(string $packageName, string $repositoryName) : RepositoryCacheException
    {
        return (new RepositoryCacheException(sprintf("Repository '%s' was not found in package '%s'", $repositoryName, $packageName)));
    }
}


?>
