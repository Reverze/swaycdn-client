<?php

namespace SwayCDN\Client\Service;


interface ClientInterface
{
    public function generateRoute(string $packageName, string $repositoryName, string $resource);
    
    /**
     * Gets package's name
     * @param string $potentialAlias Package's name or Package's alias
     */
    public function getPackageName(string $name);
    
    /**
     * Gets repository's name
     * @param string $name Repositorys name or repository's alias
     */
    public function getRepositoryName(string $name);
    
    /**
     * List array with resources
     * @param string $packageName
     * @param string $repositoryName
     * @param array $parameters
     */
    public function listResources(string $packageName, string $repositoryName);
}

?>

