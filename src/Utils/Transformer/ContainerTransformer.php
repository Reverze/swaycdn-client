<?php

namespace SwayCDN\Client\Utils\Transformer;

use SwayCDN\Client\Utils\Container;

abstract class ContainerTransformer
{
    /**
     * Transformers array into selected container object
     * @param string $containerType
     * @param string $inputArray
     * @return \SwayCDN\Client\Utils\Container\Container
     */
    protected function transformIntoContainer(string $containerType, array $inputArray) : Container\Container
    {
        /**
         * Transforms into ResourceContainer
         */
        if (strtolower($containerType) === 'resource'){
            $container = new Container\ResourceContainer($inputArray);
            return $container;
        }    
        else if (strtolower($containerType) === 'alias.package'){
            $container = new Container\PackageAliasContainer($inputArray);
            return $container;
        }
        else if (strtolower($containerType) === 'alias.repository'){
            $container = new Container\RepositoryAliasContainer($inputArray);
            return $container;
        }
        else if (strtolower($containerType) === 'token'){
            $container = new Container\TokenContainer($inputArray);
            return $container;
        }
        else {
            $container = new Container\DefaultContainer();
            return $container;
        }
    }
    
    /**
     * Creates empty container
     * @param string $containerType
     * @return \SwayCDN\Client\Utils\Container\Container
     */
    protected function createEmptyContainer(string $containerType) : Container\Container
    {
        return $this->transformIntoContainer($containerType, []);
    }
}


?>