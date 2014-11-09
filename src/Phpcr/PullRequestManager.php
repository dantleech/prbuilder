<?php

namespace PrBuilder\Phpcr;

use PHPCR\SessionInterface;
use PHPCR\NodeInterface;

class PullRequestManager
{
    const PR_NODENAME_FORMAT = 'pr-%s';

    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
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

        $this->session->save();
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
}
