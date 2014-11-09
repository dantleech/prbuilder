<?php

namespace PrBuilder\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListenCommand extends BaseCommand
{
    public function configure()
    {
        $this->setName('pr:listen');
        $this->setDescription('Listen for incoming build requests from RabbitMQ');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
    }
}
