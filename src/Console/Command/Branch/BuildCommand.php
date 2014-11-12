<?php

namespace PrBuilder\Console\Command\Branch;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use PrBuilder\Console\Command\BaseCommand;
use PrBuilder\Model\BuildRequest;

class BuildCommand extends BaseCommand
{
    public function configure()
    {
        $this->setName('branch:build');
        $this->addArgument('repoUrl', InputArgument::REQUIRED);
        $this->addArgument('branchName', InputArgument::REQUIRED);
        $this->setDescription('Reposutory URL');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $repoUrl = $input->getArgument('repoUrl');
        $branchName = $input->getArgument('branchName');
        $buildRequest = new BuildRequest();
        $buildRequest->setRepoUrl($repoUrl);
        $buildRequest->setBranchName($branchName);

        $outputWriter = $this->get('util.output');
        $outputWriter->setOutputClosure(function ($message) use ($output) {
            $output->writeln($message);
        });

        $builder = $this->get('builder');

        $builder->setLoggerCallback(function ($type, $message) use($output) {
            $output->writeln('<' . $type . '>' . $message . '</' . $type .'>');
        });

        $builder->build($buildRequest);
    }
}


