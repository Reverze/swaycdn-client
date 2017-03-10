<?php

namespace SwayCDN\Client\Service\Exception\Cache;


class CacheException extends \Exception
{
    /**
     * Throws an exception when empty parameters array was passed
     * @return \SwayCDN\Client\Service\Exception\Cache\CacheException
     */
    public static function emptyParameters() : CacheException
    {
        return (new CacheException("Array with no defined parameters was passed", 7321));
    }
    
}


?>
