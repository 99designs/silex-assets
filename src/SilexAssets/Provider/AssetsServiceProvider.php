<?php

namespace SilexAssets\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use SilexAssets\Twig\AssetsExtension;
use SilexAssets\Manifest;

class AssetsServiceProvider implements ServiceProviderInterface
{
    private $_config;

    public function __construct($config=array())
    {
        $this->_config = array_merge(array(
            'manifest_file' => '.manifest',
        ),$config);
    }

    public function register(Application $app)
    {
        if(!isset($app['assets.manifest'])) {
            $app['assets.manifest'] = new Manifest(
                $this->_config['output_dir'],
                $this->_config['manifest_file']
            );
        }

        $config = $this->_config;
        $app['twig'] = $app->share($app->extend('twig', function($twig) use($app, $config) {
            $twig->addExtension(new AssetsExtension($app['assets.manifest'], $config));
            return $twig;
        }));
    }

    public function boot(Application $app)
    {
    }
}
