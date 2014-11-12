<?php

namespace PrBuilder\Builder;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use PrBuilder\Model\BuildRequest;
use PrBuilder\Util\OutputWriter;

class WorkspaceFactory
{
    private $path;
    private $output;

    public function __construct($path, OutputWriter $output)
    {
        $this->path = $path;
        $this->output = $output;
    }

    public function getWorkspace(BuildRequest $pullRequest)
    {
        return new Workspace($this->path, $pullRequest, $this->output);
    }
}

