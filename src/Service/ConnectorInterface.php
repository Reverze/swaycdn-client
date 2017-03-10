<?php

namespace SwayCDN\Client\Service;

interface ConnectorInterface
{
    /**
     * Generates uri path to specified resource
     * @param string $packageName
     * @param string $repositoryName
     * @param string $resourceVirtualPath
     * @param array $parameters
     */
    public function generateRouteToResource(string $packageName, string $repositoryName,
            string $resourceVirtualPath, array $parameters = array());
    
    /**
     * List resources
     * @param string $packageName
     * @param string $repositoryName
     * @param array $parameters
     */
    public function listResources(string $packageName, string $repositoryName, array $parameters = array());
    
}


?>

