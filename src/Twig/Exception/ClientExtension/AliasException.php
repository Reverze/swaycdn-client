<?php

namespace SwayCDN\Client\Twig\Exception\ClientExtension;

class AliasException extends \Exception
{
    /**
     * Throws an exception when alias name is missed
     * @param string $callString
     * @return \SwayCDN\Client\Twig\Exception\ClientExtension\AliasException
     */
    public static function emptyAliasName(string $callString) : AliasException
    {
        return (new AliasException(sprintf("Missed alias name in '%s'", $callString)));
    }
    
    /**
     * Throws an exception when alias is not exists
     * @param string $aliasName
     * @return \SwayCDN\Client\Twig\Exception\ClientExtension\AliasException
     */
    public static function aliasNotExists(string $aliasName) : AliasException
    {
        return (new AliasException(sprintf("Alias '%s' for twig extension is not exists", $aliasName)));
    }
}

?>