<?php

namespace SwayCDN\Client\Service;

use SwayCDN\Client\Utils\Http\QueryAliasContainer;

class Router
{
    /**
     * Defined routes
     * @var array
     */
    private $routes = null;
      
    /**
     * Query parameters container (aliases)
     * @var \SwayCDN\Client\Utils\Http\QueryAliasContainer
     */
    private $queryAlias = null;
    
    public function __construct(array $routerParameters)
    {
        $this->initializeParameters($routerParameters);
    }
    
    /**
     * Initializes router from parameters
     * @param array $routerParameters
     * @throws \SwayCDN\Client\Service\Exception\RouterException
     * @throws \SwayCND\Client\Service\Exception\RouteException
     */
    private function initializeParameters(array $routerParameters)
    {
        /**
         * Router's parameters must be defined
         */
        if (empty($routerParameters)){
            throw Exception\Router\RouterException::emptyRouterParameters();
        }
        
        /**
         * If routes are not defined
         */
        if (!array_key_exists('routes', $routerParameters)){
            throw Exception\Router\RouteException::routesNotDefined();
        }
        
        /**
         * If routes array is empty
         */
        if (empty($routerParameters['routes'])){
            throw Exception\Router\RouteException::routesNotDefined();
        }
        
        foreach ($routerParameters['routes'] as $routeName => $routeParameters){
            $this->routes[$routeName] = $routeParameters['path'];
        }
        
        /**
         * Stores query aliases if defined
         */
        if (array_key_exists('query', $routerParameters)){
            if (array_key_exists('alias', $routerParameters['query'])){
                $this->queryAlias = new QueryAliasContainer($routerParameters['query']['alias']);
            }
        }
        
    }
    
    /**
     * Initialize routes (store)
     * @param array $routes
     */
    private function initializeRoutes(array $routes)
    {
        $this->routes = $routes;
    }
    
    /**
     * Checks if route is defined
     * @param string $routeName
     * @return bool
     */
    private function isRouteExists(string $routeName) : bool
    {
        return isset($this->routes[$routeName]);
    }
    
    /**
     * Checks if route has parameters
     * @param string $routeName
     * @return bool
     */
    private function isRouteHaveParameters(string $routeName) : bool
    {
        return (bool) preg_match(sprintf('/%s/', '\{([a-zA-Z0-9\-\_]+)\}'), $this->routes[$routeName]);
    }
    
    /**
     * Gets parameters at route
     * @param string $routeName
     * @return array
     */
    private function getRouteParameters(string $routeName) : array
    {
        /**
         * Matched parameter at route
         */
        $matchedParameters = array();
        
        preg_match_all(sprintf('/%s/', '\{([a-zA-Z0-9\-\_]+)\}'), $this->routes[$routeName], $matchedParameters);
        
        return (isset($matchedParameters[1]) ? $matchedParameters[1] : array());
    }
    
    /**
     * Joins http query parameters with route path
     * @param string $routePath
     * @param string $queryParameters
     * @return string
     */
    private function joinQueryParameters(string $routePath, array $queryParameters) : string
    {
        /**
         * An array contaning qeuery properties
         */
        $queryData = array();
        
        
        /**
         * Translates aliases into source parameter name
         */
        foreach ($queryParameters as $queryParameter => $parameterValue){        
            if (is_string($this->queryAlias->getSourceQueryParameter($queryParameter)) &&
                    strlen(strval($parameterValue))){
                $queryData[$this->queryAlias->getSourceQueryParameter($queryParameter)] = strval($parameterValue);
            }
        }
        
        /**
         * Contains ready http query
         */
        $httpQuery = http_build_query($queryData);
        
        
        if (strlen($httpQuery)){
            return sprintf("%s?%s", $routePath, $httpQuery);
        }
        
        return $routePath;
    }
    
    /**
     * 
     * @param string $routeName
     * @param array $parameters
     * @return string
     * @throws \SwayCDN\Client\Service\Exception\Router\RouteException
     */
    public function generateRoute(string $routeName, array $queryParameters = array(), array $parameters = array()) : string
    {
        /**
         * If route is not defined, throws an exception
         */
        if (!$this->isRouteExists($routeName)){
            throw Exception\Router\RouteException::routeNotFound($routeName);
        }
        
        /**
         * If route doesnt have parameters, returns route path
         */
        if (!$this->isRouteHaveParameters($routeName)){
            return $this->joinQueryParameters($this->routes[$routeName], $queryParameters);
        }
        
        /**
         * Gets defined parameters at route
         */
        $routeParameters = $this->getRouteParameters($routeName);
        
        $routePath = $this->routes[$routeName];
        
        foreach ($routeParameters as $routeParameter){
            /**
             * If route parameter was not passeds
             */
            if (!array_key_exists($routeParameter, $parameters)){
                throw Exception\Router\RouteException::routeParameterRequire($routeName, $routeParameter);
            }
            
            $routePath = str_replace('{' . $routeParameter . '}', (string) $parameters[$routeParameter], $routePath);
            
        }
        
        return $this->joinQueryParameters($routePath, $queryParameters);
    }
    
    
}


?>
