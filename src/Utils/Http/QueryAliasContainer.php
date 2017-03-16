<?php

namespace SwayCDN\Client\Utils\Http;

class QueryAliasContainer
{
    /**
     * Array which contains aliases for query (GET) parameters.
     * They are stored like that way:
     *      aliasName => sourceQueryParameter
     * @var array
     */
    private $aliases = array();
    
    public function __construct(array $predefinedAliases = array())
    {
        /**
         * If predefined aliases werent predefined before
         */
        if (empty($this->aliases)){
            $this->aliases = $predefinedAliases;
        }
        
    }
    
    /**
     * Adds alias into container. Overriding is enabled
     * @param string $aliasName
     * @param string $sourceQueryParameter
     */
    public function addAlias(string $aliasName, string $sourceQueryParameter)
    {
        $this->aliases[$aliasName] = $sourceQueryParameter;
    }
    
    /**
     * Gets source query parameter name.
     * If alias is not defined, returns false
     * @param string $aliasName
     * @return mixed
     */
    public function getSourceQueryParameter(string $aliasName)
    {
        if (isset($this->aliases[$aliasName])){
            return $this->aliases[$aliasName];
        }
        else{
            return false;
        }
    }
    
    /**
     * Drop alias from container
     * @param string $aliasName
     */
    public function dropAlias(string $aliasName)
    {
        if (isset($this->aliases[$aliasName])){
            unset($this->aliases[$aliasName]);
        }
    }
    
    /**
     * Gets list with defined aliases (only aliases names)
     * @return array
     */
    public function getAliasList() : array
    {
        $list = array();
        foreach ($this->aliases as $aliasName => $aliasSource){
            array_push($list, $aliasName);
        }
        return $list;
    }
    
    /**
     * Gets array with defined aliases (and sources)
     * @return array
     */
    public function getAliases() : array
    {
        return $this->aliases;
    }
}

?>