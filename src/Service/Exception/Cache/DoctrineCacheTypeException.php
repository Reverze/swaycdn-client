<?php

namespace SwayCDN\Client\Service\Exception\Cache;

class DoctrineCacheTypeException extends \Exception
{
    /**
     * Throws an exception when cache type is not defined
     * @return \SwayCDN\Client\Service\Exception\Cache\DoctrineCacheTypeException
     */
    public static function cacheTypeNotDefined() : DoctrineCacheTypeException
    {
        return (new DoctrineCacheTypeException("You must define which type of cache is going to use"));
    }
}

?>