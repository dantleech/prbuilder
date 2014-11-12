<?php

namespace PrBuilder\Console\Command\Pr;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use PrBuilder\Console\Command\BaseCommand;

class BuildCommand extends BaseCommand
{
    public function configure()
    {
        $this->setName('pr:build');
        $this->addArgument('pr-number', InputArgument::REQUIRED);
        $this->setDescription('Build a named PR');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $prNumber = $input->getArgument('pr-number');
        $builder = $this->get('builder');

        $builder->setOutputCallback(function ($line) use ($output) {
            $output->writeln($line);
        });
        $builder->setLoggerCallback(function ($type, $message) use($output) {
            $output->writeln('<' . $type . '>' . $message . '</' . $type .'>');
        });

        $pullRequest = $this->get('manager.pull_request')->getPullRequest($prNumber);
        $builder->build($pullRequest);
    }
}

