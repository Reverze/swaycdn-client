<?php

namespace SwayCDN\Client\Twig;

use SwayCDN\Client\Service\ClientInterface;

class ClientExtension extends \Twig_Extension
{
    /**
     * Client service
     * @var \SwayCDN\Client\Service\ClientInterface
     */
    private $client = null;
    
    /**
     * Contains aliases for packageName:repositoryName
     * @var array
     */
    private $aliases = array();
    
    /**
     * Stores default file extension type if resource type is not defined
     * @var string
     */
    private $defaultExtension = null;
    
    public function __construct(ClientInterface $client, array $parameters)
    {
        if (empty($this->client)){
            $this->client = $client;
        }
        
        if (!empty($parameters)){
            $this->initializeParameters($parameters);
        }
    }
    
    /**
     * Initializes extension parameters (parameters are optional)
     * @param array $parameters
     */
    protected function initializeParameters(array $parameters)
    {
        /**
         * Stores invoke aliases
         */
        if (array_key_exists('alias', $parameters)){
            foreach($parameters['alias'] as $aliasName => $aliasValue){
                $this->aliases[strtolower($aliasName)] = $aliasValue;
            }
        }
        
        if (array_key_exists('defaults', $parameters)){
            if (isset($parameters['defaults']['extension'])){
                $this->defaultExtension = (string) $parameters['defaults']['extension'];
            }
        }
    }
    

    /**
     * Checks if alias invoke is exists
     * @param string $aliasName
     * @return bool
     */
    protected function isAliasExists(string $aliasName) : bool
    {
        return isset($this->aliases[strtolower($aliasName)]);
    }
    
    /**
     * Gets alias value
     * @param string $aliasName
     * @return string
     */
    protected function getAlias(string $aliasName) : string
    {
        return $this->aliases[strtolower($aliasName)];
    }
    
    /**
     * Checks if expression has alias call
     * @param string $potentialCall
     * @return bool
     */
    protected function hasInvokeCall(string $potentialCall) : bool
    {
        /**
         * Regular expression pattern to detect alias call
         */
        $regexPattern = '/^\@[a-zA-Z0-9\_\.]+:.*$/';
        
        return (bool) preg_match($regexPattern, $potentialCall);
    }
    
    /**
     * Checks if resource virtual path begins with slash
     * @param string $virtualPath
     * @return bool
     */
    protected function isResourceVirtualPathBeginsWithSlash(string $virtualPath) : bool
    {
        $regexPattern = '/^\/.*/';
        
        return (bool) preg_match($regexPattern, $virtualPath);
    }
    
    /**
     * Checks if resource virtual path has defined resource extension
     * @param string $virtualPath
     * @return bool
     */
    protected function isResourceVirtualPathHasExtension(string $virtualPath) : bool
    {
        $regexPattern = '/^\/.*\.[a-zA-Z0-9]+$/';
        return (bool) preg_match($regexPattern, $virtualPath);
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('cdn', array($this, 'putResourceUri'))
        ];
        
    }
    
    
    
    
    public function putResourceUri($resourceAbsoluteVirtualPath)
    {
        if ($this->hasInvokeCall($resourceAbsoluteVirtualPath)){
            return $this->serveAliasCall($resourceAbsoluteVirtualPath);           
        }
        /**
         * Exploded virtual path
         */
        $explodedVirtualPath = explode(":", $resourceAbsoluteVirtualPath);
        
        /**
         * Gets package's name
         */
        $packageName = $explodedVirtualPath[0] ?? null;
        /**
         * Gets repository's name
         */
        $repositoryName = $explodedVirtualPath[1] ?? null;
        /**
         * Gets resource's virtual path
         */
        $resourceVirtualPath = $explodedVirtualPath[2] ?? null;
        
        if (empty($packageName)){
            throw Exception\ClientExtension\VirtualPathException::missedPackageNameIn($resourceAbsoluteVirtualPath);
        }
        
        if (empty($repositoryName)){
            throw Exception\ClientExtension\VirtualPathException::missedRepositoryNameIn($resourceAbsoluteVirtualPath);
        }
        
        if (empty($resourceVirtualPath)){
            throw Exception\ClientExtension\VirtualPathException::missedResourceVirtualPathIn($resourceAbsoluteVirtualPath);
        }
        
        
        return $this->client->generateRoute($packageName, $repositoryName, $resourceVirtualPath);
    }
    
    protected function serveAliasCall($sourceInvokeCall)
    {
        /**
         * Remove alias call synonim
         */
        $invokeCall = str_replace("@", "", $sourceInvokeCall);
        
        $exploded = explode(":", $invokeCall);
        
        /**
         * Gets alias name
         */
        $aliasName = $exploded[0] ?? null;
        
        $resourceVirtualPath = $exploded[1] ?? null;
        
        if (empty($resourceVirtualPath)){
            throw Exception\ClientExtension\VirtualPathException::missedResourceVirtualPathIn($resourceAbsoluteVirtualPath);
        }
        
        /**
         * Gets virtual path
         */
        $resourceVirtualPath = strtolower($exploded[1]) ?? null;
        
        if (empty($aliasName)){
            throw Exception\ClientExtension\AliasException::emptyAliasName($sourceInvokeCall);
        }
        
        /**
         * Throws an exception if alias is not exists
         */
        if (!$this->isAliasExists($aliasName)){
            throw Exception\ClientExtension\AliasException::aliasNotExists($aliasName);
        }
        
        /**
         * Gets alias value
         */
        $aliasValue = $this->getAlias($aliasName);
        
        $explodedAliasValue = explode(":", $aliasValue);
        
        $packageName = $explodedAliasValue[0] ?? null;
        $repositoryName = $explodedAliasValue[1] ?? null;
        
        if (empty($packageName)){
            throw Exception\ClientExtension\VirtualPathException::missedPackageNameIn($resourceAbsoluteVirtualPath);
        }
        
        if (empty($repositoryName)){
            throw Exception\ClientExtension\VirtualPathException::missedRepositoryNameIn($resourceAbsoluteVirtualPath);
        }
        
        
        $resourceVirtualPath = $this->prepareResourceVirtualPath($resourceVirtualPath);
        
        
        return $this->client->generateRoute($packageName, $repositoryName, $resourceVirtualPath);    
    }
    
    protected function prepareResourceVirtualPath(string $virtualPath) : string
    {       
        if (!$this->isResourceVirtualPathBeginsWithSlash($virtualPath)){
            $virtualPath = '/' . $virtualPath;
        }       
        
        if (!$this->isResourceVirtualPathHasExtension($virtualPath)){      
            $virtualPath .= '.' . $this->defaultExtension;
        }
            
        return $virtualPath;         
    }
   
    
}


?>
