<?php

namespace SwayCDN\Client\Utils\Container;


class PackageAliasContainer extends Container
{
    /**
     * Stores package aliases
     * @var array
     */
    private $aliases = array();
    
    public function __construct(array $aliases = array())
    {
        if (empty($this->aliases) && !empty($aliases)){
            $this->aliases = $aliases;
        }
    }
    
    /**
     * Gets alias for package name
     * @param string $sourceName
     * @return mixed
     */
    public function getAliasFor(string $sourceName)
    {
        if (isset($this->aliases[$sourceName])){
            return $this->aliases[$sourceName];
        }
    }
    
    /**
     * Gets source name by alias
     * @param string $aliasName
     * @return mixed
     */
    public function getSourceNameFor(string $aliasName)
    {
        foreach ($this->aliases as $sourceName => $alias){
            if ($alias === $aliasName){
                return $sourceName;
            }
        }
        return false;
    }
    
    /**
     * Saves alias 
     * @param string $sourceName
     * @param string $aliasName
     */
    public function save(string $sourceName, string $aliasName)
    {
        $this->aliases[$sourceName] = $aliasName;
    }
    
    /**
     * Drops alias from container
     * @param string $sourceName
     */
    public function drop(string $sourceName)
    {
        if (isset($this->aliases[$sourceName])){
            unset($this->aliases[$sourceName]);
        }
    }
}


?>