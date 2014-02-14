<?php

use \Silex\Application;
use \SilexAssets\Provider\AssetsServiceProvider;
use \Symfony\Component\HttpFoundation\Request;

class AssetsServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testTwigAddExtension()
    {
        if (!class_exists('Twig_Environment')) {
            $this->markTestSkipped('Twig was not installed.');
        }

        $app = new Application();
        $app['twig'] = $app->share(function () {
            return new \Twig_Environment(new \Twig_Loader_String());
        });

        $app->register(new AssetsServiceProvider(null, array()));
        $this->assertInstanceOf('SilexAssets\\Twig\\AssetsExtension', $app['twig']->getExtension('assets'));
    }
}
