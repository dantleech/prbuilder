<?php

namespace PrBuilder\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use PHPCR\Util\QOM\QueryBuilder;
use PrBuilder\Console\Command\BaseCommand;

class ListCommand extends BaseCommand
{
    public function configure()
    {
        $this->setName('pr:list');
        $this->setDescription('List PRs in the database');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $qb = new QueryBuilder($this->get('phpcr.query_manager')->getQOMFactory());
        $qb->from($qb->qomf()->selector('nt:unstructured', 'a'));
    }
}

