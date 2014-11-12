<?php

namespace PrBuilder\Model;

use PHPCR\NodeInterface;

class BuildRequest
{
    protected $repoUrl;
    protected $branchName;

    public function getId()
    {
        return md5($this->repoUrl . $this->branchName);
    }

    public function setPrNumber($prNumber)
    {
        $this->prNumber = $prNumber;
    }

    public function setRepoUrl($repoUrl)
    {
        $this->repoUrl = $repoUrl;
    }

    public function setBranchName($branchName)
    {
        $this->branchName = $branchName;
    }

    public function getRepoUrl()
    {
        return $this->repoUrl;
    }

    public function getBranchName()
    {
        return $this->branchName;
    }
}
