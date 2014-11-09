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
        $this->post('/pr', function (Request $request) use ($app) {
            $prManager = $app->get('manager.pull_request');
            return new Response('ok');
        });
    }

    public function get($name)
    {
        return $this->prcontainer->get($name);
    }
}
