<?php

namespace SwayCDN\Client\Tests\Unit\Routing;

use SwayCDN\Client\Service\Router;
use SwayCDN\Client\Service\Exception;

class RouterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Example parameters for tests
     * @var array
     */
    private $exampleParameters = [
        'routes' => [
            'list_packages' => [
                'path' => '/ls-content/deliver/package/{packageName}/{repositoryName}'
            ],
            'example' => [
                'path' => '/ls-content/deliver'
            ]
        ]    
    ];
    
    /**
     * Tests Router behavior when empty parameters array was passed
     */
    public function testEmptyParameters()
    {
        try {
            $router = new Router([]);
            $this->fail('Router should thrown an exception when empty parameters array was passed');
        } 
        catch (Exception\Router\RouterException $ex) {
            $this->assertEquals(4523, $ex->getCode());
        }
    }
    
    /**
     * Tests Router behaviour when router parameters were passed without defined routes
     */
    public function testWithoutGivenRoutes()
    {
        try {
            $router = new Router([
                'query' => [
                    
                ]
            ]);
        } 
        catch (Exception\Router\RouteException $ex) {
            $this->assertEquals(7263, $ex->getCode());
        }
    }
    
    /**
     * Tests Router behaviour on non-existing route.
     * Router should throw an exception
     */
    public function testGeneratorOnFakeRoute()
    {
        /**
         * Initialize Router with example parameters
         */
        $router = new Router($this->exampleParameters);
        
        /**
         * Checking behaviour for non-existing route
         */
        try {
            $router->generateRoute('fake_route');
        } 
        catch (Exception\Router\RouteException $ex) {
            $this->assertEquals(2154, $ex->getCode());
        }
        
    }
    
    /**
     * Tests behaviour when route's parameters are required, but that parameters were not passed.
     * Router should throw an exception
     */
    public function testGeneratorWithoutRequiredRouteParameters()
    {
        /**
         * Initialize Router with example parameters
         */
        $router = new Router($this->exampleParameters);
        
        /**
         * Checking behaviour when route's parameters are required and that parameters were not passed
         */
        try {
            $router->generateRoute('list_packages', [], []);
        } 
        catch (Exception\Router\RouteException $ex) {
            $this->assertEquals(785487, $ex->getCode());
        }
        
    }
    
    public function testRouteGenerate()
    {
        /**
         * Initialize Router with example parameters
         */
        $router = new Router($this->exampleParameters);
        
        
        /**
         * Checks route generator for route without parameters
         */
        $generatedRoute = $router->generateRoute('example');
        $this->assertEquals('/ls-content/deliver', $generatedRoute);
        
        /**
         * Checks route generator for route with parameters
         */
        $generatedRoute = $router->generateRoute('list_packages', [], [
            'packageName' => 'testPackage',
            'repositoryName' => 'testRepository'
        ]);
        $this->assertEquals('/ls-content/deliver/package/testPackage/testRepository', $generatedRoute);
    }
}


?>