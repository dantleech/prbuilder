<?php

namespace PrBuilder\Phpcr;

use PHPCR\SessionInterface;

class PullRequestManager
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    private function getRootNode($name)
    {
        $root = $this->session->getRootNode();
        if (!$root->hasNode($name)) {
            $root->addNode($name);
        }
    }

    public function registerPullRequest(array $json)
    {
        var_dump($json);die();;
    }
}
