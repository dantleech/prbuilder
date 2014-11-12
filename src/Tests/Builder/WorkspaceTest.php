<?php

namespace PrBuilder\Tests\Builder;

use PrBuilder\Tests\BaseTestCase;

class WorkspaceTest extends BaseTestCase
{
    public function testWorkspace()
    {
        $pullRequest = $this->prophesize('PrBuilder\Model\PullRequest');
        $pullRequest->getRepoUrl()->willReturn('https://github.com/dantleech/dtlweb');
        $pullRequest->getId()->willReturn('thisistest');
        $pullRequest->getBranchName()->willReturn('docker');
        $pullRequest->getNumber()->willReturn(53);

        $workspace = $this->get('builder.workspace');
        $workspace->checkoutPr($pullRequest->reveal());
    }
}
