<?php

namespace PrBuilder\Builder;

use Docker\Docker;
use PrBuilder\Phpcr\BuildRequestManager;
use Docker\Exception\ImageNotFoundException;
use Docker\Container;
use Docker\Exception\APIException;
use PrBuilder\Model\BuildRequest;
use PrBuilder\Util\OutputWriter;
use PrBuilder\Builder\Docker\Context;

class Builder
{
    const IMAGE = 'tutum/lamp';

    private $docker;
    private $loggerCallback;
    private $workspaceFactory;
    private $output;

    public function __construct(Docker $docker, WorkspaceFactory $workspaceFactory, OutputWriter $output)
    {
        $this->docker = $docker;
        $this->containerManager = $this->docker->getContainerManager();
        $this->imageManager = $this->docker->getImageManager();
        $this->output = $output;
        $this->loggerCallback = function () {};
        $this->workspaceFactory = $workspaceFactory;
    }

    public function build(BuildRequest $buildRequest)
    {
        $imageNames = $this->prepareImages($buildRequest);
        foreach ($imageNames as $imageName) {
            $this->runContainer($imageName, $buildRequest);
        }
    }

    public function setLoggerCallback(\Closure $callback)
    {
        $this->loggerCallback = $callback;
    }

    private function log($type, $message)
    {
        $logger = $this->loggerCallback;
        $logger($type, $message);
    }

    private function prepareImages(BuildRequest $buildRequest)
    {
        $workspace = $this->workspaceFactory->getWorkspace($buildRequest);
        $workspace->checkout();
        $dockerFiles = $workspace->getDockerFiles();
        $imageNames = array();

        foreach ($dockerFiles as $name => $dockerFile) {
            $imageName = sprintf('prbuilder-%s-%s', $name, $buildRequest->getId());

            try {
                $image = $this->imageManager->find($imageName);
                $this->imageManager->delete($image);
            } catch (APIException $e) {
            }

            $context = new Context($dockerFile);
            $context->setEnv(array(
                'REPO_URL' => $buildRequest->getRepoUrl(),
            ));

            $this->docker->build($context, $imageName, $this->output->getClosure());

            $imageNames[] = $imageName;
        }

        return $imageNames;
    }

    private function runContainer($imageName, BuildRequest $buildRequest)
    {
        $containerName = 'prbuilder-' . $buildRequest->getId();
        $container = $this->getContainer($imageName, $containerName);

        $outputCallback = $this->output->getClosure();
        $this->log('info', 'Running container');

        $this->containerManager->run($container, function ($output, $type) use ($outputCallback) {
            $outputCallback(sprintf('[%s] %s', $type, $output));
        });
        $container->setCmd(array('run.sh'));
        $this->containerManager->run($container, function ($output, $type) use ($outputCallback) {
            $outputCallback(sprintf('[%s] %s', $type, $output));
        });
    }

    private function getContainer($imageName, $containerName)
    {
        // ContainerManager should return null, but it doesn't...
        try {
            $container = $this->containerManager->find($containerName);
            $this->log('info', 'Found existing container, removing it');

            try {
                $this->log('comment', 'Stopping container');
                $this->containerManager->stop($container);
            } catch (\Exception $e) {
                $this->log('error', $e->getMessage());
            }

            $this->log('comment', 'Removing container');
            $this->containerManager->remove($container);
        } catch (APIException $e) {
        }

        $container = new Container(array(
            'Image' => $imageName
        ));
        $container->setName($containerName);

        return $container;
    }

}
