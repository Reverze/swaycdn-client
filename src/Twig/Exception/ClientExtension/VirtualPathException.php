<?php

namespace SwayCDN\Client\Twig\Exception\ClientExtension;

class VirtualPathException extends \Exception
{
    /**
     * Throws an exception when package's name is missed in absolute virtual path
     * @param string $absoluteVirtualPath
     * @return \SwayCDN\Client\Twig\Exception\ClientExtension\VirtualPathException
     */
    public static function missedPackageNameIn(string $absoluteVirtualPath) : VirtualPathException
    {
        return (new VirtualPathException(sprintf("Package's name is missed in '%s'", $absoluteVirtualPath)));
    }
    
    /**
     * Throws an exception when repository's name is missed in absolute virtual path
     * @param string $absoluteVirtualPath
     * @return \SwayCDN\Client\Twig\Exception\ClientExtension\VirtualPathException
     */
    public static function missedRepositoryNameIn(string $absoluteVirtualPath) : VirtualPathException
    {
        return (new VirualPathException(sprintf("Repository's name is missed in '%s'", $absoluteVirtualPath)));
    }
    
    /**
     * Throws an exception when resource virtual path is missed in absolute virtual path
     * @param string $absoluteVirtualPath
     * @return \SwayCDN\Client\Twig\Exception\ClientExtension\VirtualPathException
     */
    public static function missedResourceVirtualPathIn(string $absoluteVirtualPath) : VirtualPathException
    {
        return (new VirtualPathException(sprintf("Resource virtual path is missed '%s'", $absoluteVirtualPath)));
    }
    
}


?>
