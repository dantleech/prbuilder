<?php

namespace PrBuilder\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildCommand extends BaseCommand
{
    public function configure()
    {
        $this->setName('pr:build');
        $this->setDescription('Build a named PR');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
    }
}

