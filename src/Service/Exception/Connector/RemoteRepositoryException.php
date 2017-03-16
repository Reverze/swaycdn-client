<?php

namespace SwayCDN\Client\Service\Exception\Connector;

class RemoteRepositoryException extends \Exception
{
    /**
     * Throws an exception when remote repository was not found
     * @param string $remoteRepositoryName
     * @return \SwayCDN\Client\Service\Exception\Connector\RemoteRepositoryException
     */
    public static function remoteRepositoryNotFound(string $remoteRepositoryName) : RemoteRepositoryException
    {
        return (new RemoteRepositoryException(sprintf("Remote repository '%s' was not found by web service", $remoteRepositoryName), 4415));
    }
}


?>
