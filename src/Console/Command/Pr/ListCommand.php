<?php

namespace PrBuilder\Console\Command\Pr;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use PHPCR\Util\QOM\QueryBuilder;
use PrBuilder\Console\Command\BaseCommand;
use Symfony\Component\Console\Helper\Table;
use PrBuilder\Phpcr\PullRequestManager;

class ListCommand extends BaseCommand
{
    public function configure()
    {
        $this->setName('pr:list');
        $this->setDescription('List PRs in the database');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $prManager = $this->get('manager.pull_request');
        $pullRequests = $prManager->getPullRequests();

        $table = new Table($output);
        $table->setHeaders(array('#', 'Title', 'State', 'Build'));
        foreach ($pullRequests as $pullRequest) {
            $table->addRow(array(
                $pullRequest->getNumber(),
                $pullRequest->getTitle(),
                $pullRequest->getState(),
                $pullRequest->getBuildStatus(),
            ));
        }

        $table->render();
    }
}

