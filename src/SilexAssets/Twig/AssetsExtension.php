<?php

namespace SilexAssets\Twig;

class AssetsExtension extends \Twig_Extension
{
    private $_config, $_manifest;

    public function __construct($manifest, $config=array())
    {
        $this->_manifest = $manifest;
        $this->_config = $config;
    }

    public function getName()
    {
        return 'assets';
    }

    function getTokenParsers()
    {
    	return array(
    		new AssetTokenParser('stylesheets', $this->_manifest, $this->_config),
            new AssetTokenParser('javascripts', $this->_manifest, $this->_config),
    		new RequireJsTokenParser($this->_config, $this->_manifest),
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
