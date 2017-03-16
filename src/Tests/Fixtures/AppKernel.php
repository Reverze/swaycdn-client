<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * Kernel to simulate an application to make some tests
 */
class AppKernel extends Kernel
{
    public function registerBundles() 
    {
        $bundles = [];
        
        if (in_array($this->getEnvironment(), array('test'))){
            $bundles[] = new Symfony\Bundle\FrameworkBundle\FrameworkBundle();
            $bundles[] = new Circle\RestClientBundle\CircleRestClientBundle();
            $bundles[] = new SwayCDN\Client\ClientBundle();  
            $bundles[] = new Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle();
        }
        
        return $bundles;
    }
    
    public function registerContainerConfiguration(LoaderInterface $loader) 
    {
        $loader->load(__DIR__ . '/config.yml');
    }
    
}


?>