<?php

namespace SwayCDN\Client\Utils\Container;

class ResourceContainer extends Container
{
    /**
     * Contains resources
     * ResourceIdentifier => resourceVirtualPath
     * @var array
     */
    private $resources = array();
    
    /**
     * Initialize container with predefined values
     * @param array $resources
     */
    public function __construct(array $resources = array())
    {
        if (empty($this->resources) && !empty($resources)){
            $this->resources = $resources;
        }
    }
    
    /**
     * Gets resource's identifier by virtual path
     * @param string $virtualPath
     * @return string or false if resource was not found
     */
    public function getIdentifier(string $virtualPath)
    { 
        foreach ($this->resources as $resourceIdentifier => $resourceVirtualPath){
            if ($resourceVirtualPath === $virtualPath){
                return $resourceIdentifier;
            }
        }
        
        return false;       
    }
    
    /**
     * Gets resource's virtual path by resource's identifier
     * @param string $resourceIdenitifier
     * @return string or false if resource was not found
     */
    public function getVirtualPath(string $resourceIdenitifier)
    {
        if (isset($this->resources[$resourceIdenitifier])){
            return $this->resources[$resourceIdenitifier];
        }
        
        return false;
    }
    
    /**
     * Stores or override existing resource
     * @param string $resourceIdentifier
     * @param string $resourceVirtualPath
     */
    public function save(string $resourceIdentifier, string $resourceVirtualPath)
    {
        $this->resources[$resourceIdentifier] = $resourceVirtualPath;
    }
    
    /**
     * Drops resource (if exists) from container
     * @param string $resourceIdentifier
     */
    public function drop(string $resourceIdentifier)
    {
        if (isset($this->resources[$resourceIdentifier])){
            unset($this->resources[$resourceIdentifier]);
        }
    }
    
    /**
     * Gets array with only all resources identifiers
     * @return array
     */
    public function getIdentifiers() : array
    {
        $matchedIdentifiers = array();
        foreach ($this->resources as $resourceIdentifier => $resourceVirtualPath){
            array_push($matchedIdentifiers, $resourceIdentifier);
        }
        return $matchedIdentifiers;
    }
    
    /**
     * Gets array with only all resource virtual paths
     * @return array
     */
    public function getVirtualPaths() : array
    {
        $storedVirtualPaths = array();
        foreach ($this->resources as $resourceIdentifier => $resourceVirtualPath){
            array_push($storedVirtualPaths, $resourceVirtualPath);
        }
        return $storedVirtualPaths;
    }
}


?>
