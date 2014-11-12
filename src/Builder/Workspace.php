<?php

namespace PrBuilder\Builder;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use PrBuilder\Model\BuildRequest;
use Symfony\Component\Yaml\Yaml;
use PrBuilder\Util\OutputWriter;

class Workspace
{
    private $filesystem;
    private $basePath;
    private $buildRequest;
    private $workspacePath;
    private $output;

    public function __construct($basePath, BuildRequest $buildRequest, OutputWriter $output)
    {
        $this->basePath = $basePath;
        $this->filesystem = new Filesystem();
        $this->buildRequest = $buildRequest;
        $this->workspacePath = sprintf('%s/%s', $this->basePath, $this->buildRequest->getId());
        $this->output = $output;
    }

    public function checkout()
    {
        if (file_exists($this->workspacePath)) {
            $this->filesystem->remove($this->workspacePath);
        }

        $this->filesystem->mkdir($this->workspacePath);
        $this->cloneRepository($this->buildRequest, $this->workspacePath);
    }

    public function getDockerFiles()
    {
        $config = $this->parseConfig();
        $files = array();

        foreach ($config['dockerPath'] as $dockerFile) {
            $absPath = $this->workspacePath . '/' . $dockerFile;
            if (!file_exists($absPath)) {
                throw new \InvalidArgumentException(sprintf(
                    'Could not find referenced docker file "%s" at absolute path "%s"',
                    $dockerFile,
                    $absPath
                ));
            }

            $files[] = $absPath;
        }

        return $files;
    }

    private function parseConfig()
    {
        $prBuilderFile = 'prbuilder.yml';
        $prBuilderPath = $this->workspacePath . '/prbuilder.yml';

        if (!file_exists($prBuilderPath)) {
            throw new \Exception(sprintf(
                'No "%s" file found in "%s"', $prBuilderFile, $this->workspacePath
            ));
        }

        $config = Yaml::parse(file_get_contents($prBuilderPath));

        $config = array_merge(array(
            'dockerFiles' => array()
        ), $config);

        return $config;
    }

    private function cloneRepository()
    {
        $this->exec(sprintf('git clone %s %s', $this->buildRequest->getRepoUrl(), $this->workspacePath), $this->basePath);
        if ($this->buildRequest instanceof \PrBuilder\Model\PullRequest) {
            $this->exec(sprintf('git fetch refs/pull/%s/head:%s', $this->buildRequest->getNumber(), $this->buildRequest->getBranchName()));
        }

        $this->exec(sprintf('git checkout %s', $this->buildRequest->getBranchName()));
    }

    private function exec($cmd)
    {
        $process = new Process($cmd);
        $process->setWorkingDirectory($this->workspacePath);
        $output = $this->output;
        $output->write('<info>Executing: </info>' . $cmd);

        $process->run(function ($type, $buffer) use ($output) {
            if (Process::ERR === $type) {
                $output->write('ERR: ' . $buffer);
            } else {
                $output->write('OUT: ' . $buffer);
            }
        });
    }
}
