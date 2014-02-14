<?php

namespace SilexAssets\Twig;

class AssetNode extends \Twig_Node
{
    private
        $_config,
        $_manifest,
        $_files=array()
        ;

    public function __construct($manifest, $config, \Twig_NodeInterface $body, $files, $line, $tag = null)
    {
        $nodes = array('body' => $body);
        $attributes = array('var_name'=>'asset_url');

        $this->_manifest = $manifest;
        $this->_config = $config;
        $this->_files = $files;

        parent::__construct($nodes, $attributes, $line, $tag);
    }

    public function compile(\Twig_Compiler $compiler)
    {
        $compiler->addDebugInfo($this);

        foreach($this->_files as $file) {
            $compiler
                ->write("// asset \"$file\"\n")
                ->write('$context[')
                ->repr($this->getAttribute('var_name'))
                ->raw('] = ')
                ->repr($this->getAssetUrl($file))
            ;

            $compiler
                ->raw(";\n")
                ->subcompile($this->getNode('body'))
            ;

            $compiler
                ->write('unset($context[')
                ->repr($this->getAttribute('var_name'))
                ->raw("]);\n")
            ;
        }
    }

    public function getAssetUrl($asset)
    {
        if($checksum = $this->_manifest->getChecksum($asset)) {
            $buster = "?".$checksum;
        } else {
            $buster = "?t".time();
        }
        return $this->_config['web_path'] . '/' . $asset . $buster;
    }
}
