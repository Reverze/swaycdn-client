<?php

namespace SwayCDN\Client\Service\Exception\Cache;

class CacheLifetimeException extends \Exception
{
    /**
     * Throws an exception when cache lifetime is not specified
     * @return \SwayCDN\Client\Service\Exception\Cache\CacheLifetimeException
     */
    public static function cacheLifetimeNotSpecified() : CacheLifetimeException
    {
        return (new CacheLifetimeException(sprintf("Cache lifetime is not specified")));
    }
    
    /**
     * Throws an exception when cache lifetime is invalid
     * @return \SwayCDN\Client\Service\Exception\Cache\CacheLifetimeException
     */
    public static function invalidCacheLifetime() : CacheLifetimeException
    {
        return (new CacheLifetimeException("Cache lifetime is invalid (must be numeric and grather or equal 0)"));
    }
}


?>
