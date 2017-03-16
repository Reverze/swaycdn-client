<?php

namespace SwayCDN\Client\Service\Exception\Connector;

class NetworkConnectionException extends \Exception
{
    /**
     * Throws an exception when parameter 'network' is missed
     * @return \SwayCDN\Client\Service\Exception\Connector\NetworkConnectionException
     */
    public static function missedNetworkParameter() : NetworkConnectionException
    {
        return (new NetworkConnectionException("Parameter 'network' is missed. This parameter defines which CDN you want to use."));
    }
    
}


?>
