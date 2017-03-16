<?php

namespace SwayCDN\Client\Service\Exception\Cache;

class DoctrineCacheException extends \Exception
{
    /**
     * Throws an exception when doctrine cache service name is not defined
     * @return \SwayCDN\Client\Service\Exception\Cache\DoctrineCacheException
     */
    public static function doctrineServiceNameNotDefined() : DoctrineCacheException
    {
        return (new DoctrineCacheException("Doctrine cache service name is not defined"));
    }
    
    /**
     * Throws an exception when doctrine cache service was not found
     * @param string $expectedServiceName
     * @return \SwayCDN\Client\Service\Exception\Cache\DoctrineCacheException
     */
    public static function doctrineServiceNotFound(string $expectedServiceName) : DoctrineCacheException
    {
        return (new DoctrineCacheException(sprintf("Doctrine cache service not exists (known as: '%s')", $expectedServiceName)));
    }
    
    /**
     * Throws an exception when doctrine cache service is not instance of CacheProvider
     * @param string $serviceName
     * @return \SwayCDN\Client\Service\Exception\Cache\DoctrineCacheException
     */
    public static function invalidDoctrineService(string $serviceName) : DoctrineCacheException
    {
        return (new DoctrineCacheException(sprintf("Doctrine cache service '%s' is not instanceof CacheProvider", $serviceName)));
    }
}


?>