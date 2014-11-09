<?php

namespace PrBuilder\Web;

use PrBuilder\DI\PrBuilderContainer;
use Silex\Application as SilexApplication;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class Application extends SilexApplication
{
    private $prcontainer;

    public function __construct()
    {
        parent::__construct();
        $this->prcontainer = new PrBuilderContainer($this);
        $this->setup();
    }

    public function setup()
    {
        $app = $this;
        $app['debug'] = true;
        $this->post('/pr', function () use ($app) {
            $payload = json_decode(file_get_contents('php://input'), true);

            if (!$payload) {
                throw new \Exception('Could not json_decode the payload');
            }

            $prManager = $app['manager.pull_request'];
            $prManager->registerPayload($payload);
            return new Response('ok');
        });
    }
}
