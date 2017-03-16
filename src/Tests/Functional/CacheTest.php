<?php

namespace SwayCDN\Client\Tests\Functional;

use SwayCDN\Client\Tests\Utils\WebTestCase;
use SwayCDN\Client\Service\Cache;
use SwayCDN\Client\Service\Exception;

class CacheTest extends WebTestCase
{
    /**
     * Example parameters for Cache (tests)
     * @var array
     */
    private $exampleParameters = [
        'lifetime' => 3600,
        'doctrine_cache_service' => [
            'type' => 'apcu',
            'name' => 'apcu_cache'
        ]
    ];
    
    /**
     * Example resources
     * @var array
     */
    private $exampleResources = [
        '23djnas72bdja' => 'images/image1.png',
        '82nvausSDjsj2' => 'images/image2.png',
        'd72SYSn2kdsjs' => 'images/image3.png'
    ];
    
    /**
     * Fake package name
     * @var string
     */
    private $testPackageName = 'testPackage';
    
    /**
     * Fake repository name 
     * @var string 
     */
    private $testRepositoryName = 'repositoryName';
    
    /**
     * Fake package's access toke 
     * @var string
     */
    private $testAccessToken = 'doksnii222';
    
    /**
     * Tests behaviour when array with no parameters was passed
     */
    public function testOnEmptyParameters()
    {
        try {
            $cacheDriver = new Cache([], $this->container);
        } 
        catch (Exception\Cache\CacheException $ex) {
            $this->assertEquals(1, 1);
        }
        
    }
    
    /**
     * Tests behaviour wheb parameters array doesnt contains information about cache lifetime
     */
    public function testWithoutGivenLifetime()
    {
        try {
            $cacheDriver = new Cache(['doctrine_cache_service' => []], $this->container);
        } 
        catch (Exception\Cache\CacheLifetimeException $ex) {
            $this->assertEquals(1, 1);
        }
    }
    
    /**
     * Tests behaviour when given lifetime is invalid
     */
    public function testWithInvalidLifetime()
    {
        try {
            $cacheDriver = new Cache(['lifetime' => -500, 'doctrine_cache_service' => []], $this->container);
        } 
        catch (Exception\Cache\CacheLifetimeException $ex) {
            $this->assertEquals(1, 1);
        }
        
        try {
            $cacheDriver = new Cache(['lifetime' => new \Exception("dasdas"), 'doctrine_cache_service' => []], $this->container);
        } 
        catch (Exception\Cache\CacheLifetimeException $ex) {
            $this->assertEquals(1, 1);
        }
    }
    
    /**
     * Tests without given 'doctrine_cache_service' parameter
     */
    public function testWithoutGivenDoctrineService()
    {
        try {
            $cacheDriver = new Cache([ 'lifetime' => 3600 ], $this->container);
        } 
        catch (Exception\Cache\DoctrineCacheRequiredException $ex) {
            $this->assertEquals(1,1);
        }
    }
    
       
    /**
     * Tests behaviour when package is not cached
     */
    public function testIsCached()
    {
        $cacheDriver = new Cache($this->exampleParameters, $this->container);
        /**
         * We want to make sure that isCachedFor returns false when package is not cached,
         * so we clear cache
         */
        $cacheDriver->clearCache();
        
        /**
         * Checks if package is cached
         */
        $isCached = $cacheDriver->isCachedFor($this->testPackageName, $this->testRepositoryName);
        
        $this->assertEquals(false, $isCached);
    
    }
    
    /**
     * 
     */
    public function testCacheRepositoryWithoutSavedPackage()
    {
        try {
            $cacheProvider = $this->createMock(\Doctrine\Common\Cache\CacheProvider::class);
            $cacheProvider->method('fetch')->will($this->returnValue([
                Cache::CACHE_PACKAGE_PROFILE_NAME => [
                    
                ]
            ]));
            
            $this->container->set('apcu_cache', $cacheProvider);
            $cacheDriver = new Cache($this->exampleParameters, $this->container);
            $cacheDriver->cacheRepository($this->testPackageName, $this->testRepositoryName, $this->exampleResources);
            $this->fail('Try to cache repository but package is not cached before. Expected exception throw');
            
        } 
        catch (Exception\Cache\PackageCacheException $ex) {
            $this->assertEquals(1, 1);
        }
        
    }
    
    /**
     * Tests with stores package
     */
    public function testCacheRepository()
    {
        try
        {
            $cacheProviderMocked = $this->createMock(\Doctrine\Common\Cache\CacheProvider::class);
            $cacheProviderMocked
                    ->method('fetch')
                    ->will($this->returnValue([
                        Cache::CACHE_PACKAGE_PROFILE_NAME => [
                            "$this->testPackageName" => [
                                Cache::CACHE_PACKAGE_ACCESS_TOKEN => $this->testAccessToken,
                                Cache::CACHE_PACKAGE_REPOSITORIES_CONTAINER => array()
                            ]
                        ]
                    ]));
            
            $this->container->set('apcu_cache', $cacheProviderMocked);        
            $cacheDriver = new Cache($this->exampleParameters, $this->container);
            $cacheDriver->cacheRepository($this->testPackageName, $this->testRepositoryName, $this->exampleResources);
            $this->assertEquals(1, 1);
        } 
        catch (Exception\Cache\PackageCacheException $ex) {
            $this->fail("Exception with undefiend package cached but package has been created");
        }
    }
    
    /**
     * Tests behaviour on getting package access token
     */
    public function testGetPackageAccessToken()
    {
        try {
            $cacheProviderMocked = $this->createMock(\Doctrine\Common\Cache\CacheProvider::class);
            $cacheProviderMocked
                    ->method('fetch')
                    ->will($this->returnValue([
                        Cache::CACHE_PACKAGE_PROFILE_NAME => [ /* Test package is cached */
                            "$this->testPackageName" => [
                                Cache::CACHE_PACKAGE_ACCESS_TOKEN => $this->testAccessToken,
                                Cache::CACHE_PACKAGE_REPOSITORIES_CONTAINER => array()
                            ]
                        ]
                    ]));
            
            $this->container->set('apcu_cache', $cacheProviderMocked);        
            $cacheDriver = new Cache($this->exampleParameters, $this->container);
            
            $accessToken = $cacheDriver->getAccessToken($this->testPackageName);
            
            $this->assertEquals($this->testAccessToken, $accessToken);
            
        } 
        catch (Exception\Cache\PackageCacheException $ex) {
            $this->fail("Package is cached but exception 'package not found' was thrown");
        }
    }
    
    /**
     * Tests behaviour on getting resources from repository
     */
    public function testGetResourcesFromRepository()
    {
        $cacheProviderMocked = $this->createMock(\Doctrine\Common\Cache\CacheProvider::class);
        $cacheProviderMocked
                ->method('fetch')
                ->will($this->returnValue([
                            Cache::CACHE_PACKAGE_PROFILE_NAME => [/* Test package and repository are cached */
                                "$this->testPackageName" => [
                                    Cache::CACHE_PACKAGE_ACCESS_TOKEN => $this->testAccessToken,
                                    Cache::CACHE_PACKAGE_REPOSITORIES_CONTAINER => [
                                        "$this->testRepositoryName" => [
                                            Cache::CACHE_PACKAGE_REPOSITORY_RESOURCES => array()
                                        ]
                                    ]
                                ]
                            ]
        ]));

        $this->container->set('apcu_cache', $cacheProviderMocked);
        $cacheDriver = new Cache($this->exampleParameters, $this->container);
        $resources = $cacheDriver->getResourcesFromRepository($this->testPackageName, $this->testRepositoryName);


        if (empty($resources) && is_array($resources)) {
            $this->assertEquals(1, 1);
        } else {
            $this->fail('Expected to return empty array');
        }
    }
}


?>

