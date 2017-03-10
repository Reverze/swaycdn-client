<?php

namespace SwayCDN\Client\Utils\Container;

class TokenContainer extends Container
{
    /**
     * Stores tokens for packages
     * @var array
     */
    private $tokens = array();
    
    public function __construct (array $tokens = array())
    {
        if (empty($this->tokens) && !empty($tokens)){
            $this->tokens = $tokens;
        }
    }
    
    /**
     * Gets stored token for package
     * @param string $packageName
     * @return mixed
     */
    public function getTokenFor(string $packageName)
    {
        if (isset($this->tokens[$packageName])){
            return $this->tokens[$packageName];
        }
        return false;
    }
    
    /**
     * Stores package's token into container
     * @param string $packageName
     * @param string $token
     */
    public function save(string $packageName, string $token)
    {
        $this->tokens[$packageName] = $token;
    }
    
    /**
     * Drop package's token from container
     * @param string $packageName
     */
    public function drop(string $packageName)
    {
        if (isset($this->tokens[$packageName])){
            unset($this->tokens[$packageName]);
        }
    }
}


?>
