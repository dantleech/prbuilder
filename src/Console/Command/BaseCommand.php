<?php

namespace PrBuilder\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Application;

class BaseCommand extends Command
{
    private $container;

    public function setApplication(Application $application)
    {
        parent::setApplication($application);
        $this->container = $application->getContainer();
    }

    protected function get($serviceId)
    {
        return $this->container->get($serviceId);
    }
}
