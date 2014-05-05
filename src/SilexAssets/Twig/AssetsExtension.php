<?php

namespace SilexAssets\Twig;

class AssetsExtension extends \Twig_Extension
{
    private $config;
    private $manifest;

    public function __construct($manifest, $config=array())
    {
        $this->manifest = $manifest;
        $this->config = $config;
    }

    public function getName()
    {
        return 'silex_assets_extension';
    }

    function getTokenParsers()
    {
      return array(
          new AssetTokenParser('stylesheets', $this->manifest, $this->config),
          new AssetTokenParser('javascripts', $this->manifest, $this->config),
          new RequireJsTokenParser($this->manifest, $this->config),
      );
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('css', array($this, 'css'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('js', array($this, 'js'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('image_url', array($this, 'imageUrl'))
        );
    }

    public function imageUrl($path)
    {
        return sprintf("/img/%s", $path);
    }

    public function css($assetUrl)
    {
        return '<link href="/assets/'.$assetUrl.'" type="text/css" rel="stylesheet" />';
    }

    public function js($assetUrl)
    {
        return '<script src="/assets/'.$assetUrl.'"></script>';
    }
}
