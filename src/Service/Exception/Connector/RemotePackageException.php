<?php

namespace SwayCDN\Client\Service\Exception\Connector;

class RemotePackageException extends \Exception
{
    /**
     * Throws an exception when remote package was not found
     * @param string $packageName
     * @return \SwayCDN\Client\Service\Exception\Connector\RemotePackageException
     */
    public static function remotePackageNotFound(string $packageName) : RemotePackageException
    {
        return (new RemotePackageException(sprintf("Given package '%s' was not found by CDN web service", $packageName), 8751));
    }
}

?>