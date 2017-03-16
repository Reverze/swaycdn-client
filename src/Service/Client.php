<?php

namespace SwayCDN\Client\Service;

use SwayCDN\Client\Service\ConnectorInterface;
use SwayCDN\Client\Utils\Transformer\ContainerTransformer;
use SwayCDN\Client\Utils\Container\PackageAliasContainer;
use SwayCDN\Client\Utils\Container\RepositoryAliasContainer;
use SwayCDN\Client\Utils\Container\TokenContainer;

class Client extends ContainerTransformer implements ClientInterface
{
    /**
     * Connector
     * @var \SwayCDN\Client\Service\ConnectorInterface
     */
    private $connector = null;
    
    /**
     * Package's alias container
     * @var \wayCDN\Client\Utils\Container\PackageAliasContainer
     */
    private $packageAliasContainer = null;
    
    /**
     * Repositories'a alias container
     * @var \SwayCDN\Client\Utils\Container\RepositoryAliasContainer
     */
    private $repositoryAliasContainer = null;
    
    /**
     * Package's token container
     * @var \SwayCDN\Client\Utils\Container\TokenContaine
     */
    private $tokenContainer = null;
    
    public function __construct(ConnectorInterface $connector, array $clientParameters)
    {
        if (empty($this->connector)){
            $this->connector = $connector;
        }
        
        $this->initializeClientParameters($clientParameters);
    }
    
    /**
     * Initializes client parameters
     * @param array $clientParameters
     */
    private function initializeClientParameters(array $clientParameters)
    {
        if (array_key_exists('token', $clientParameters)){
            $this->tokenContainer = $this->transformIntoContainer('token', $clientParameters['token']);
        }
        else{
            $this->tokenContainer = $this->createEmptyContainer('token');
        }
        
        if (array_key_exists('alias', $clientParameters)){
            if (array_key_exists('package', $clientParameters['alias'])){
                $this->packageAliasContainer = $this->transformIntoContainer('alias.package', 
                        $clientParameters['alias']['package']);
            }
            else{
                $this->packageAliasContainer = $this->createEmptyContainer('alias.package');
            }
            
            if (array_key_exists('repository', $clientParameters['alias'])){
                $this->repositoryAliasContainer = $this->transformIntoContainer('alias.repository', 
                        $clientParameters['alias']['repository']);
            }
            else{
                $this->repositoryAliasContainer = $this->createEmptyContainer('alias.repository');
            }
        }
        else{
            $this->packageAliasContainer = $this->createEmptyContainer('alias.package');
            $this->repositoryAliasContainer = $this->createEmptyContainer('alias.repository');
        }
    }
    
    /**
     * {@inheritdoc}
     * @return string
     */
    public function getPackageName(string $name) 
    {
        if ($this->packageAliasContainer->getSourceNameFor($name) !== false){
            return $this->packageAliasContainer->getSourceNameFor($name);
        }
        return $name;
    }
    
    /**
     * {@inheritdoc}
     * @return string
     */
    public function getRepositoryName(string $name) 
    {
        if ($this->repositoryAliasContainer->getSourceNameFor($name) !== false){
            return $this->repositoryAliasContainer->getSourceNameFor($name);
        }
        return $name;
    }
    
    /**
     * {@inheritdoc}
     */
    public function listResources(string $packageName, string $repositoryName) 
    {
        return $this->connector->listResources($packageName, $repositoryName);
    }
    
    
    public function generateRoute(string $packageName, string $repositoryName, string $resourceVirtualPath)
    {
        return $this->connector->generateRouteToResource($this->getPackageName($packageName), 
                $this->getRepositoryName($repositoryName), $resourceVirtualPath);    
    }
}


?>