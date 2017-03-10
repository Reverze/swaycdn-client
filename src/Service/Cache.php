<?php

namespace SwayCDN\Client\Service;

use Doctrine\Common\Cache\CacheProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Cache
{
    /**
     * Aliases
     */
    const CACHE_PACKAGE_PROFILE_NAME = 'swaycdn_packages_cache_profile';
    const CACHE_PACKAGE_ACCESS_TOKEN = 'access_token';
    const CACHE_PACKAGE_REPOSITORIES_CONTAINER = 'package_repositories_container';
    const CACHE_PACKAGE_REPOSITORY_RESOURCES = 'repository_resources';
    /**
     * Cache's lifetime
     * @var int
     */
    private $cacheLifetime = null;
    
    /**
     * Doctrine cache type
     * @var string
     */
    private $doctrineCacheType = null;
    
    /**
     * Doctrine cache service name
     * @var string
     */
    private $doctrineCacheName = null;
    
    /**
     * Doctrine cache provider
     * @var \Doctrine\Common\Cache\CacheProvider
     */
    private $cacheProvider = null;
    
    /**
     * Service container
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container = null;
    
    public function __construct(array $parameters, ContainerInterface $container)
    {
        if (empty($this->container)){
            $this->container = $container;
        }
        
        $this->initializeParameters($parameters);
    }
    
    /**
     * Initialzie parameters for cache manager
     * @param array $parameters
     * @throws \SwayCDN\Client\Server\Exception\Cache\CacheLifetimeException
     */
    private function initializeParameters(array $parameters)
    {
        
        if (empty($parameters)){
            throw Exception\Cache\CacheException::emptyParameters();
        }
        
        
        if (!array_key_exists('lifetime', $parameters)){
            throw Exception\Cache\CacheLifetimeException::cacheLifetimeNotSpecified();
        }
        
        if (!is_numeric($parameters['lifetime'])){
            throw Exception\Cache\CacheLifetimeException::invalidCacheLifetime();
        }
        
        if ((int) $parameters['lifetime'] < 0){
            throw Exception\Cache\CacheLifetimeException::invalidCacheLifetime();
        }
        
        $this->cacheLifetime = (int) $parameters['lifetime'];
        
        if (!array_key_exists('doctrine_cache_service', $parameters)){
            throw Exception\Cache\DoctrineCacheRequiredException::doctrineCacheDriverRequired();
        }
        
        if (!array_key_exists('type', $parameters['doctrine_cache_service'])){
            throw Exception\Cache\DoctrineCacheTypeException::cacheTypeNotDefined();
        }
        
        $this->doctrineCacheType = $parameters['doctrine_cache_service']['type'];
        
        if (!array_key_exists('name', $parameters['doctrine_cache_service'])){
            throw Exception\Cache\DoctrineCacheException::doctrineServiceNameNotDefined();
        }
        
        $this->doctrineCacheName = $parameters['doctrine_cache_service']['name'];
        
        if (!$this->container->has($this->doctrineCacheName)){
            throw Exception\Cache\DoctrineCacheException::doctrineServiceNotFound($this->doctrineCacheName);
        }
        
        if (!$this->container->get($this->doctrineCacheName) instanceof CacheProvider){
            throw Exception\Cache\DoctrineCacheException::invalidDoctrineService($this->doctrineCacheName);
        }
        
        
        $this->cacheProvider = $this->container->get($this->doctrineCacheName);
        
        
        
        if (!$this->isCacheProfileExists()){
            $this->cacheProvider->save('swaycdn_packages_cache_profile', []);      
        }
                
    }
    
    /**
     * Checks if package cache profile exists
     * @return bool
     */
    private function isCacheProfileExists() : bool
    {  
        if ($this->cacheProvider->fetch('swaycdn_packages_cache_profile') === false){
            return false;
        }
        else{
            return true;
        }
    }
    
    /**
     * Checks if package is cached
     * @param string $packageName
     * @return bool
     */
    public function isPackageCached(string $packageName) : bool
    {
        return isset($this->cacheProvider->fetch('swaycdn_packages_cache_profile')[$packageName]);
    }
    
    /**
     * Creates package cache profile
     * @param string $packageName
     * @param array $packageParameters
     */
    public function createPackageCacheProfile(string $packageName, array $packageParameters = array())
    {   
        $array = $this->cacheProvider->fetch('swaycdn_packages_cache_profile');
        $array[$packageName] = [
                    'access_token' => $packageParameters['accessToken'] ?? null,
                    'package_repositories_container' => array()
                ];
        $this->cacheProvider->save('swaycdn_packages_cache_profile', $array);
    }
    
    /**
     * Updates package cache profile
     * @param string $packageName
     * @param array $packageProfile
     */
    private function updatePackageCacheProfile(string $packageName, array $packageProfile)
    {   
        $array = $this->cacheProvider->fetch(self::CACHE_PACKAGE_ACCESS_TOKEN);
        $array[$packageName] = $packageProfile;
        $this->cacheProvider->save('swaycdn_packages_cache_profile', $array);
    }
    
    /**
     * Gets package cache prolfile
     * @param string $packageName
     * @return array
     */
    private function getPackageCacheProfile(string $packageName) : array
    {
        return $this->cacheProvider->fetch('swaycdn_packages_cache_profile')[$packageName];
    }
    
    /**
     * Gets repository from package cache profile
     * @param string $packageName
     * @param string $repositoryName
     * @return array
     */
    private function getRepositoryFromPackageProfile(string $packageName, string $repositoryName) : array
    {
        return $this->cacheProvider->fetch('swaycdn_packages_cache_profile')[$packageName]['package_repositories_container'][$repositoryName];
    }
    
    /**
     * Checks if repository is cached in package
     * @param string $packageName
     * @param string $repositoryName
     * @return bool
     */
    private function isRepositoryCached(string $packageName, string $repositoryName) : bool
    {
        return isset($this->cacheProvider->fetch('swaycdn_packages_cache_profile')[$packageName]['package_repositories_container'][$repositoryName]);
    }
    
    /**
     * Caches repository
     * @param string $packageName
     * @param string $repositoryName
     * @param array $resources
     * @throws \SwayCDN\Client\Service\Exception\Cache\PackageCacheException
     */
    public function cacheRepository(string $packageName, string $repositoryName, array $resources)
    {
        /**
         * Package cache profile must exists, otherwise throws an exception
         */
        if (!$this->isPackageCached($packageName)){
            throw Exception\Cache\PackageCacheException::packageCacheProfileNotExists($packageName);
        }
         
        /**
         * Update package cache
         */
        $packageProfile = $this->getPackageCacheProfile($packageName);
        $packageProfile['package_repositories_container'][$repositoryName] = [
            'repository_resources' => $resources
        ];
        $this->updatePackageCacheProfile($packageName, $packageProfile);
    }
    
    /**
     * Gets resources from repository as array [ resourceIdentifier => resourcePath ]
     * @param string $packageName
     * @param string $repositoryName
     * @return array
     */
    public function getResourcesFromRepository(string $packageName, string $repositoryName) : array
    {
        /**
         * Repository must be cached
         */
        if (!$this->isRepositoryCached($packageName, $repositoryName)){
            throw Exception\Cache\RepositoryCacheException::repositoryNotFound($packageName, $repositoryName);
        }
        
        /**
         * Return array resourceIdentifier => resourcePath
         */
        return $this->getRepositoryFromPackageProfile($packageName, $repositoryName)['repository_resources'];
    }
    
    /**
     * Checks if repository in package in cached
     * @param string $packageName
     * @param string $repositoryName
     * @return bool
     */
    public function isCachedFor(string $packageName, string $repositoryName) : bool
    {
        /**
         * If cache profile not exists, it means that no datas were cached
         */
        if (!$this->isCacheProfileExists()){
            return false;
        }
        
        /**
         * If package is not cached, returns false
         */
        if (!$this->isPackageCached($packageName)){
            return false;
        }
        
        /**
         * If repository is not cached in package, returns false.
         * If repository is cached return true.
         */
        return $this->isRepositoryCached($packageName, $repositoryName);
    }
    
    /**
     * Gets access token to package
     * @param string $packageName
     * @return string
     */
    public function getAccessToken(string $packageName)
    {
        if (!$this->isPackageCached($packageName)){
            return Exception\Cache\PackageCacheException::packageCacheProfileNotExists($packageName);
        }
        return $this->getPackageCacheProfile($packageName)['access_token'];
    }
    
    public function clearCache()
    {
        $this->cacheProvider->delete('swaycdn_packages_cache_profile');
    }

    
}


?>