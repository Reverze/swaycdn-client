<?php

namespace SwayCDN\Client\Tests\Utils;

use Symfony\Component\DependencyInjection\ContainerInterface;

class WebTestCase extends \Symfony\Bundle\FrameworkBundle\Test\WebTestCase
{
    /**
     * Container
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container = null;
    
    
    public function setUp() 
    {
        require_once __DIR__.'/../Fixtures/AppKernel.php';

        $kernel = new \AppKernel('test', true);
        $kernel->boot();
        $container = $kernel->getContainer();
        $this->container = $container;
    }
    
}


?>

