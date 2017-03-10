<?php

namespace SwayCDN\Client\Service\Exception\Connector;

class ProtocolException extends \Exception
{
    /**
     * Throws an exception when parameter 'protocol' is missed
     * @return \SwayCDN\Client\Service\Exception\Connector\ProtocolException
     */
    public static function protocolDefinitionMissed() : ProtocolException
    {
        return (new ProtocolException("Parameter 'protocol' is missed! You must specify which protocol you want to use", 2494));
    }
    
    /**
     * Throws an exception when web service returned non-json response
     * @return \SwayCDN\Client\Service\Exception\Connector\ProtocolException
     */
    public static function invalidWebServiceResponseFormat() : ProtocolException
    {
        return (new ProtocolException("Web service returns non-json response", 9283));
    }
    
    /**
     * Throws an exception when web service returns invalid response
     * @param string $expectedKey
     * @return \SwayCDN\Client\Service\Exception\Connector\ProtocolException
     */
    public static function invalidWebServiceResponse(string $expectedKey) : ProtocolException
    {
        return (new ProtocolException(sprintf("Invalid web service response. Expected '%s' key", $expectedKey), 2159));
    }
    
    /**
     * Throws an exception when web service returned empty response
     * @return \SwayCDN\Client\Service\Exception\Connector\ProtocolException
     */
    public static function emptyWebServiceResponse() : ProtocolException
    {
        return (new ProtocolException("Web service returns empty response", 9284));
    }
    
    /**
     * Throws an exception when web service returns ..
     * @param string $packageName
     * @return \SwayCDN\Client\Service\Exception\Connector\ProtocolException
     */
    public static function invalidAccessToken(string $packageName) : ProtocolException
    {
        return (new ProtocolException(sprintf("Invalid access token for package '%s'", $packageName), 7548));
    }
}

?>

