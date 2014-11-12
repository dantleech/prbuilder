<?php

namespace PrBuilder\Console;

use Symfony\Component\Console\Application as BaseApplication;
use PrBuilder\DI\PrBuilderContainer;
use PrBuilder\Console\Command\ListCommand;
use PrBuilder\Console\Command;

class Application extends BaseApplication
{
    const VERSION = '0.0.1';

    public function __construct()
    {
        parent::__construct('PR Builder', self::VERSION);

        $this->container = new PrBuilderContainer();
        $this->registerCommands();
    }

    private function registerCommands()
    {
        $this->add(new Command\Pr\ListCommand());
        $this->add(new Command\Pr\BuildCommand());
        $this->add(new Command\Pr\ListenCommand());
        $this->add(new Command\Pr\ServeCommand());
        $this->add(new Command\Branch\BuildCommand());
    }

    public function getContainer()
    {
        return $this->container;
    }
}
