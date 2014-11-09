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
            print_r($_POST);
            $app['manager.pull_request'];
            return new Response('ok');
        });
    }
}
