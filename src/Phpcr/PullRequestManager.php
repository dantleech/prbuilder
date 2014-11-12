<?php

namespace PrBuilder\Phpcr;

use PHPCR\SessionInterface;
use PHPCR\NodeInterface;
use PrBuilder\Phpcr\QueueManager;
use PrBuilder\Model\PullRequest;

class PullRequestManager
{
    const PR_NODENAME_FORMAT = 'pr-%s';
    const PROPNAME_STATUS = 'pr-builder-status';
    const STATUS_PENDING = 'pending';

    private $session;
    private $queueManager;

    /**
     * @param SessionInterface $session
     * @param AMQPConnection $amqpConnection
     */
    public function __construct(SessionInterface $session, QueueManager $queueManager)
    {
        $this->session = $session;
        $this->queueManager = $queueManager;
    }

    public function registerPayload(array $payload)
    {
        $rootNode = $this->getRootNode('pr');
        $number = $payload['number'];
        $nodeName = sprintf(self::PR_NODENAME_FORMAT, $number);

        if (!$rootNode->hasNode($nodeName)) {
            $pullRequestNode = $rootNode->addNode($nodeName);
        } else {
            $pullRequestNode = $rootNode->getNode($nodeName);
        }

        $pullRequest = $payload['pull_request'];

        $this->serializePayload($pullRequestNode, $pullRequest);
        $this->queueManager->queueMessage(QueueManager::BUILD_QUEUE, $pullRequest);
        $pullRequest = new PullRequest();
        $pullRequest->fromNode($pullRequestNode);
        $pullRequest->setBuildStatus(self::STATUS_PENDING);
        $this->session->save();
    }

    public function getPullRequests()
    {
        $prNode = $this->getRootNode('pr');
        $nodes =  $prNode->getNodes();
        $pullRequests = array();

        foreach ($nodes as $node) {
            $pullRequest = new PullRequest($node);
            $pullRequest->fromNode($prNode);
            $pullRequests[] = $pullRequest;
        }

        return $pullRequests;
    }

    public function getPullRequest($prNumber)
    {
        $prNode = $this->getRootNode('pr');
        $pullRequest = $prNode->getNode(sprintf(self::PR_NODENAME_FORMAT, $prNumber));

        return new PullRequest($pullRequest);
    }

    private function serializePayload(NodeInterface $node, array $pullRequest)
    {
        foreach ($pullRequest as $key => $value) {
            if (is_array($value)) {
                if ($node->hasNode($key)) {
                    $child = $node->getNode($key);
                } else {
                    $child = $node->addNode($key);
                }

                $this->serializePayload($child, $value);
                continue;
            }

            $node->setProperty($key, $value);
        }
    }

    private function getRootNode($name)
    {
        $root = $this->session->getRootNode();
        if (!$root->hasNode($name)) {
            $node = $root->addNode($name);
        } else {
            $node = $root->getNode($name);
        }

        return $node;
    }
}
