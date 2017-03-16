<?php

namespace SwayCDN\Client\Service\Exception\Router;

class RouterException extends \Exception
{
    /**
     * Throws an exception when router parameters are not defined
     * @return \SwayCDN\Client\Service\Exception\Router\RouterException
     */
    public static function emptyRouterParameters() : RouterException
    {
        return (new RouterException("Router's parameters are not defined", 4523));
    }
}


?>
