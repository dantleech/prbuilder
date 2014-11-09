<?php

namespace PrBuilder\DI;

use PHPCR\SimpleCredentials;
use Jackalope\RepositoryFactoryFilesystem;
use PrBuilder\Phpcr\PullRequestManager;

class PrBuilderContainer
{
    private $pimple;
    private $defaultConfig = array(
        'phpcr.data_path' => __DIR__ . '/../../phpcr-data'
    );

    public function __construct(\Pimple $pimple = null)
    {
        $this->pimple = $pimple ? : new \Pimple();
        $this->configure(array());
    }

    private function configure($config)
    {
        $config = array_merge(
            $this->defaultConfig,
            $config
        );

        $this->pimple['phpcr.factory'] = function ($c) {
            return new RepositoryFactoryFilesystem();
        };

        $this->pimple['phpcr.repository'] = function ($c) use ($config) {
            return $c['phpcr.factory']->getRepository(array(
                'path' => $config['phpcr.data_path'],
            ));
        };

        $this->pimple['phpcr.session'] = function ($c) {
            $credentials = new SimpleCredentials('admin', 'admin');
            return $c['phpcr.repository']->login($credentials);
        };

        $this->pimple['phpcr.workspace'] = function ($c) {
            $credentials = new SimpleCredentials('admin', 'admin');
            return $c['phpcr.session']->getWorkspace();
        };

        $this->pimple['phpcr.query_manager'] = function ($c) {
            return $c['phpcr.workspace']->getQueryManager();
        };

        $this->pimple['manager.pull_request'] = function ($c) {
            return new PullRequestManager($c['phpcr.session']);
        };
    }


    public function get($name)
    {
        return $this->pimple[$name];
    }
}
