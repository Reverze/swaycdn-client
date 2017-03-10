<?php

namespace SwayCDN\Client\Utils\Container;

class DefaultContainer extends Container
{
    /**
     * 
     * @var array
     */
    private $container = null;
    
    public function __construct(array $values = array())
    {
        if (empty($this->container) && !empty($values)){
            $this->container = $values;
        }
    }
    
    public function getValueFor(string $keyName)
    {
        if (isset($this->container[$keyName])){
            return $this->container[$keyName];
        }
        return false;
    }
    
    public function getKeyByValue($searchValue)
    {
        foreach ($this->container as $keyName => $value){
            if ($value === $searchValue){
                return $keyName;
            }
        }
        return false;
    }
    
    public function store(string $keyName, $value)
    {
        $this->container[$keyName] = $value;
    }
    
    public function drop(string $keyName)
    {
        if (isset($this->container[$keyName])){
            unset($this->container[$keyName]);
        }
    }
}


?>