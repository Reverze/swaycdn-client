<?php

namespace SwayCDN\Client\Service\Exception\Router;

class RouteException extends \Exception
{
    /**
     * Throws an exception when none routes were defined
     * @return \SwayCDN\Client\Service\Exception\Router\RouteException
     */
    public static function routesNotDefined() : RouteException
    {
        return (new RouteException("None routes were defined", 7263));
    }
    
    /**
     * Throws an exception when route definition is invalid
     * @param string $routeName
     * @return \SwayCDN\Client\Service\Exception\Router\RouteException
     */
    public static function invalidRouteDefinition(string $routeName) : RouteException
    {
        return (new RouteException(sprintf("Invalid definition of route '%s'", $routeName)));
    }
    
    /**
     * Throws an exception when route's path is invalid
     * @param string $routeName
     * @return \SwayCDN\Client\Service\Exception\Router\RouteException
     */
    public static function invalidRoutePath(string $routeName) : RouteException
    {
        return (new RouteException(sprintf("Invalid path of route '%s'", $routeName)));
    }
    
    /**
     * Throws an exception when route was not found
     * @param string $routeName
     * @return \SwayCDN\Client\Service\Exception\Router\RouteException
     */
    public static function routeNotFound(string $routeName) : RouteException
    {
        return (new RouteException(sprintf("Route '%s' was not found", $routeName), 2154));
    }
    
    /**
     * Throws an excepton when required route's parameter was not passed
     * @param string $routeName
     * @param string $parameterName
     * @return \SwayCDN\Client\Service\Exception\Router\RouteException
     */
    public static function routeParameterRequire(string $routeName, string $parameterName) : RouteException
    {
        return (new RouteException(sprintf("Parameter '%s' is required for route '%s'", $parameterName, $routeName), 785487));
    }
}



?>