<?php

namespace PrBuilder\Model;

use PHPCR\NodeInterface;

class PullRequest extends BuildRequest
{
    private $number;
    private $title;
    private $state;

    public function fromNode(NodeInterface $node)
    {
        $this->number = $node->getPropertyValue('number');
        $this->title = $node->getPropertyValue('title');
        $this->state =  $node->getPropertyValue('state');
        $this->status = $node->getPropertyValue('prbuilder-build-status');
        $this->repoUrl =  $node->getNode('head')->getNode('repo')->getPropertyValue('clone_url');
        $this->branchName = $node->getNode('head')->getPropertyValue('ref');
    }

    public function toNode(NodeInterface $node)
    {
        $node->setProperty('prbuilder-build-status', $this->status);
    }

    public function getNumber() 
    {
        return $this->number;
    }

    public function getTitle() 
    {
        return $this->title;
    }

    public function getState() 
    {
        return $this->state;
    }

    public function getBuildStatus() 
    {
        return $this->status;
    }

    public function setBuildStatus($status)
    {
        $this->status = $status;
    }
}
