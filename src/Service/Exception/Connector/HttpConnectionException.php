<?php

namespace SwayCDN\Client\Service\Exception\Connector;

class HttpConnectionException extends \Exception
{
    /**
     * Throws an exception when parameter 'secureHttp' is missed
     * @return \SwayCDN\Client\Service\Exception\Connector\HttpConnectionException
     */
    public static function missedSecureParameter() : HttpConnectionException
    {
        return (new HttpConnectionException("Parameter 'secureHttp' is missed. This parameter defines if secured http is used"));
    }
}

?>

