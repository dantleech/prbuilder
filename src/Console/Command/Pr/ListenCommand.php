<?php

namespace PrBuilder\Console\Command\Pr;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PrBuilder\Phpcr\QueueManager;
use PrBuilder\Console\Command\BaseCommand;

class ListenCommand extends BaseCommand
{
    public function configure()
    {
        $this->setName('pr:listen');
        $this->setDescription('Listen for incoming build requests from RabbitMQ');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $queueManager = $this->get('manager.queue');
        $queueManager->consume(QueueManager::BUILD_QUEUE, array($this, 'dispatchBuild'));
    }

    public function dispatchBuild($pullRequest)
    {
        $builder = $this->get('builder');
        $builder->buildPr($pullRequest['number']);
    }
}
