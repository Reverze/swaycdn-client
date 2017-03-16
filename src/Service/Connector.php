<?php

namespace SwayCDN\Client\Service;

use Circle\RestClientBundle\Services\RestClient;
use SwayCDN\Client\Service\Cache;
use SwayCDN\Client\Utils\Transformer\ContainerTransformer;

class Connector extends ContainerTransformer implements ConnectorInterface
{
    /**
     * Content delivery network URI
     * @var string
     */
    private $network = null;
    
    /**
     * Determines if secure HTTP connection is enabled
     * @var boolean
     */
    private $secureHttp = false;
    
    /**
     * Name of protocol to connect
     * @var string
     */
    private $protocol = 'swaycdn';
    
    /**
     *
     * @var array
     */
    private $defaults = array();
    
    /**
     *
     * @var \Circle\RestClientBundle\Services\RestClient
     */
    private $restClient = null;
    
    /**
     * Cache manager
     * @var \SwayCDN\Client\Service\Cache
     */
    private $cache = null;
    
    /**
     *
     * @var \SwayCDN\Client\Service\Router
     */
    private $router = null;
    
    public function __construct(RestClient $restClient, Cache $cache, Router $router, array $connectorParameters)
    {
        if (empty($this->restClient)){
            $this->restClient = $restClient;
        }
        
        if (empty($this->cache)){
            $this->cache = $cache;
        }
        
        if (empty($this->router)){
            $this->router = $router;
        }
        
        $this->unConnectorParameters($connectorParameters);
        
    }
    
    private function unConnectorParameters(array $connectorParameters)
    {
        /**
         * Parameter 'network' is required. If parameters is not defiend,
         * throws an exception
         */
        if (!array_key_exists('network', $connectorParameters)){
            throw Exception\Connector\NetworkConnectionException::missedNetworkParameter();
        }
        
        $this->network = $connectorParameters['network'];
        
        /**
         * Parameter 'secureHttp' is required. 
         */
        if (!array_key_exists('secureHttp', $connectorParameters)){
            throw Exception\Connector\HttpConnectionException::missedSecureParameter();
        }
        
        $this->secureHttp = $connectorParameters['secureHttp'];
        
        /**
         * Parameter 'protocol' is required
         */
        if (!array_key_exists('protocol', $connectorParameters)){
            throw Exception\Connector\ProtocolException::protocolDefinitionMissed();
        }   
        
        $this->protocol = $connectorParameters['protocol'];
        
        $this->defaults = $connectorParameters['defaults'] ?? array();
    }
    
    /**
     * Generates full uri
     * @param string $generatedRoutePath
     * @return string
     */
    private function generateUri(string $generatedRoutePath) : string
    {
        /**
         * Network provider
         */
        $networkProvider = $this->network;
        
        /**
         * Gets network provider http protocol
         */
        $httpProtocol = ($this->secureHttp ? "https" : "http");
        
        return sprintf("%s://%s%s", $httpProtocol, $networkProvider, $generatedRoutePath);
    }
    
    /**
     * Queries CDN web service for check remote package existing
     * @param string $packageName Remote package's name
     * @param string $accessToken Remote package's access token
     * @return bool
     * @throws \SwayCDN\Client\Service\Exception\Connector\ProtocolException
     */
    private function isRemotePackageExists(string $packageName, string $accessToken) : bool
    {
        $response = $this->restClient->get($this->generateUri($this->router->generateRoute('is_package_exists', [
            'accessToken' => $accessToken], [ 'packageName' => $packageName]))
        );
        
        /**
         * Gets response's content from webservice
         */
        $responseContent = $response->getContent();
        
        
        if (empty($responseContent) || !strlen($responseContent)){
            throw Exception\Connector\ProtocolException::emptyWebServiceResponse();
        }
        
        $responseJson = json_decode($responseContent, true);
        
        if (!is_array($responseJson)){
            throw Exception\Connector\ProtocolException::invalidWebServiceResponseFormat();
        }
        
        if (!array_key_exists('infoContent', $responseJson)){
            throw Exception\Connector\ProtocolException::invalidWebServiceResponse('infoContent');
        }
        
        /**
         * If access token is invalid
         */
        if ($responseJson['infoContent'] === 'invalid-token'){
            throw Exception\Connector\ProtocolException::invalidAccessToken($packageName);
        }
        
        if ($responseJson['infoContent'] === 'package-not-found'){
            return false;
        }
        
        if ($responseJson['infoContent'] === 'package-found'){
            return true;
        }
    }
    
    /**
     * Queries a web service to get list (dictionary) with all available resources in remote repository.
     * IMPORTANT!: Before use this function, ensure that package is cached in local cache
     * @param string $packageName Remote package's name
     * @param string $repositoryName Remote repository's name
     * @return array
     * @throws \SwayCDN\Client\Service\Exception\Connector\ProtocolException
     * @throws \SwayCDN\Client\Service\Exception\Connector\RemotePackageException
     * @throws \SwayCDN\Client\Service\Exception\Connector\RemoteRepositoryException
     */
    private function getRemoteRepositoryResources(string $packageName, string $repositoryName) : array
    {
        $response = $this->restClient->get($this->generateUri($this->router->generateRoute('list_packages', [
            'accessToken' => $this->cache->getAccessToken($packageName) ], [ 
                'packageName' => $packageName, 'repositoryName' => $repositoryName 
            ]))
        );
        
        $responseContent = $response->getContent();
        
        if (empty($responseContent) || !strlen($responseContent)){
            throw Exception\Connector\ProtocolException::emptyWebServiceResponse();
        }
        
        $responseJson = json_decode($responseContent, true);
        
        if (!is_array($responseJson)){
            throw Exception\Connector\ProtocolException::invalidWebServiceResponseFormat();
        }
        
        if (!array_key_exists('lsContent', $responseJson)){
            throw Exception\Connector\ProtocolException::invalidWebServiceResponse('lsContent');
        }
        
        if ($responseJson['lsContent'] === 'invalid-token'){
            throw Exception\Connector\ProtocolException::invalidAccessToken($packageName);
        }
        
        if ($responseJson['lsContent'] === 'package-not-found'){
            throw Exception\Connector\RemotePackageException::remotePackageNotFound($packageName);
        }
        
        if ($responseJson['lsContent'] === 'repository-not-found'){
            throw Exception\Connector\RemoteRepositoryException::remoteRepositoryNotFound($repositoryName);
        }
        
        if (!array_key_exists('resources', $responseJson['lsContent'])){
            throw Exception\Connector\ProtocolException::invalidWebServiceResponse('resources');
        }
        
        return $responseJson['lsContent']['resources'];      
    }
    
    /**
     * {@inheritdoc}
     * @param string $packageName
     * @param string $repositoryName
     * @param array $parameters
     * @return boolean
     */
    public function listResources(string $packageName, string $repositoryName, array $parameters = array())
    {
        try {
            /**
             * Gets all available resources in remote repository
             */
            $resources = $this->getRemoteRepositoryResources($packageName, $repositoryName);
            
            return $resources;
        } 
        catch (Exception $ex) {
            return false;
        }
    }
    
   /**
    * {@inheritdoc}
    * @throws \SwayCDN\Client\Service\Exception\Connector\RemotePackageException
    */
    public function generateRouteToResource(string $packageName, string $repositoryName,
            string $resourceVirtualPath, array $parameters = array())
    {
        /**
         * At first, we must check if package is cached.
         */
        if (!$this->cache->isPackageCached($packageName)){
            
            /**
             * Queries web service if package is exists
             */
            if (!$this->isRemotePackageExists($packageName, $parameters['accessToken'] ?? "")){
                throw Exception\Connector\RemotePackageException::remotePackageNotFound($packageName);
            }
            
            /**
             * If remote package is exists, creates local cache for that package
             */
            $this->cache->createPackageCacheProfile($packageName, [
                'accessToken' => $parameters['accessToken'] ?? null
            ]);
            
            
            /**
             * Gets all available resources in remote repository
             */
            $resources = $this->getRemoteRepositoryResources($packageName, $repositoryName);
            
            /**
             * Stores remote repository into local cache
             */
            $this->cache->cacheRepository($packageName, $repositoryName, $resources);     
        }
        
        /**
         * If repository not exists in local cache
         */
        if (!$this->cache->isCachedFor($packageName, $repositoryName)){
            $resources = $this->getRemoteRepositoryResources($packageName, $repositoryName);
            $this->cache->cacheRepository($packageName, $repositoryName, $resources);
        }
        
        /**
         * Gets repository's resources from local cache
         */
        $repositoryResources = $this->cache->getResourcesFromRepository($packageName, $repositoryName);
        
        
        /**
         * Transform array into container
         */
        $resourceContainer = $this->transformIntoContainer('resource', $repositoryResources);
        
        
        /**
         * Gets resource's identifier
         */
        $resourceIdentifier = $resourceContainer->getIdentifier($resourceVirtualPath);
        
        if (!$resourceIdentifier && isset($this->defaults['resourceNotFound'])){
            $resourceIdentifier = $this->defaults['resourceNotFound'];
        }
        
        return $this->generateUri($this->router->generateRoute('get_resource_package', 
                [ 'accessToken' => $parameters['accessToken'] ?? "" ], [
                    'packageName' => $packageName,
                    'repositoryName' => $repositoryName,
                    'resourceIdentifier' => $resourceIdentifier
                ]));
    }
    

    
}


?>