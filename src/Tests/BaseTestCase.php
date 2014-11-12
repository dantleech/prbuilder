<?php

namespace PrBuilder\Tests;

use Symfony\Component\Filesystem\Filesystem;
use PrBuilder\DI\PrBuilderContainer;
use Prophecy\PhpUnit\ProphecyTestCase;

class BaseTestCase extends ProphecyTestCase
{
    private $container;

    public function setUp()
    {
        $this->container = new PrBuilderContainer();
        $config = $this->container->getConfig();
        $filesystem = new Filesystem();
        $filesystem->remove($config['workspace.path']);
    }

    protected function get($serviceId)
    {
        return $this->container->get($serviceId);
    }
}
