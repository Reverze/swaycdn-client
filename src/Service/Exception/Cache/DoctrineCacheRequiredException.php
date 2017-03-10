<?php

namespace SwayCDN\Client\Service\Exception\Cache;

class DoctrineCacheRequiredException extends \Exception
{
    /**
     * Throws an exception when doctrine cache driver required
     * @return \SwayCDN\Client\Service\Exception\Cache\DoctrineCacheRequired
     */
    public static function doctrineCacheDriverRequired() : DoctrineCacheRequiredException
    {
        return (new DoctrineCacheRequiredException("Cache manager requires doctrine cache driver. Parameter 'doctrine_cache_service' not defined!"));
    }
}

?>