<?php

namespace SwayCDN\Client\Tests\Functional;

use SwayCDN\Client\Tests\Utils\WebTestCase;
use Circle\RestClientBundle\Services\RestClient;
use SwayCDN\Client\Service\Cache;
use SwayCDN\Client\Service\Router;
use SwayCDN\Client\Service\Connector;
use SwayCDN\Client\Service\Exception;

class ConnectorTest extends WebTestCase
{
    /**
     *
     * @var \Circle\RestClientBundle\Services\RestClient
     */
    private $restClient = null;
    
    /**
     *
     * @var \SwayCDN\Client\Service\Cache
     */
    private $cacheService = null;
    
    /**
     *
     * @var \SwayCDN\Client\Service\Router
     */
    private $routerService = null;
    
    /**
     *
     * @var object
     */
    private $cacheServiceMocked = null;
    
    private $routerServiceMocked = null;
    
    private $exampleInitParameters = [
        'network' => 'cdn.swayware.eu/app_dev.php', //fake network provider
        'secureHttp' => false,
        'protocol' => 'swaycdn'
    ];
    
    private $testPackageName = 'hbplay';
    private $testRepositoryName = 'repositoryName';
    private $testResourceVirtualPath = '/img/image1.png';
    
    
    public function setUp() 
    {
        parent::setUp();
        $this->restClient = $this->container->get('circle.restclient');
        $this->cacheService = $this->container->get('swaycdn.cache');
        $this->routerService = $this->container->get('swaycdn.router');
        $this->createMockedCache();
    }
    
    
    protected function createMockedCache()
    {
        $cacheMock = $this->createMock(Cache::class);
        $cacheMock->method('isPackageCached')->will($this->returnValue(false));
        
        $this->cacheServiceMocked = $cacheMock;
        
        $routerMock = $this->createMock(Router::class);
        $routerMock->expects($this->once())
                ->method('generateRoute')
                ->with($this->equalTo('is_package_exists'))
                ->will($this->returnValue(sprintf('/info-content/deliver/is-package/%s', $this->testPackageName)));
        $routerMock->expects($this->once())
                ->method('generateRoute')
                ->with($this->equalTo('list_packages'))
                ->will($this->returnValue(sprintf('/ls-content/deliver/package/%s/%s',
                        $this->testPackageName, $this->testRepositoryName)));
        
        $this->routerServiceMocked = $routerMock;
    }
    
    public function testInitConnector()
    {
        /**
         * Without given network provider
         */
        try {
            $connector = new Connector($this->restClient, $this->cacheService, $this->routerService, [
                'fakeParameter' => 'fakeValue'
            ]);
        } 
        catch (Exception\Connector\NetworkConnectionException $ex) {
            $this->assertEquals(1, 1);
        }
        
        /**
         * Without given secureHttp
         */
        try {
            $connector = new Connector($this->restClient, $this->cacheService, $this->routerService, [
                'network' => 'https://fakenetworkprovider.io'
            ]);
        } 
        catch (Exception\Connector\HttpConnectionException $ex) {
            $this->assertEquals(1, 1);
        }
        
        try {
            $connector = new Connector($this->restClient, $this->cacheService, $this->routerService, [
                'network' => 'https://fakenetworkprovider.io',
                'secureHttp' => false
            ]);
        } 
        catch (Exception\Connector\ProtocolException $ex) {
            $this->assertEquals(1, 1);
        }
    }
    
    
    public function testGenerateRoute()
    {
        try {
            $connector = new Connector($this->restClient, $this->cacheServiceMocked, $this->routerService, 
                    $this->exampleInitParameters);
            $connector->generateRouteToResource('fakePackageName', $this->testRepositoryName, $this->testResourceVirtualPath);
        } 
        catch (Exception\Connector\RemotePackageException $ex) {
            $this->assertEquals(1, 1);
        }
        
        try {
            $connector = new Connector($this->restClient, $this->cacheServiceMocked, $this->routerService,
                    $this->exampleInitParameters);
            $connector->generateRouteToResource($this->testPackageName, 'fakeRepositoryName', $this->testResourceVirtualPath);
            
        } 
        catch (Exception\Connector\RemoteRepositoryException $ex) {
            $this->assertEquals(1, 1);
        }
        
        
        try {
            $connector = new Connector($this->restClient, $this->cacheServiceMocked, $this->routerServiceMocked,
                    $this->exampleInitParameters);
            $connector->generateRouteToResource($this->testPackageName, $this->testRepositoryName, $this->testResourceVirtualPath);
        } 
        catch (Exception $ex) {

        }
        
         
    }
    
    
    
}


?>