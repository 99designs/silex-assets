<?php

namespace Twig\Extensions\Assets;

class JavascriptNode extends \Twig_Node
{
    private
        $_config=array(),
        $_files=array()
        ;

    public function __construct($config, $files, $line, $tag = null)
    {
        parent::__construct(array(), array(), $line, $tag);

        $this->_config = $config;
        $this->_files = $files;
    }

    public function compile(\Twig_Compiler $compiler)
    {
        $manifest = $this->_config['manifest'];
        $root = $this->_config['manifest_root'];

        $compiler->addDebugInfo($this);

        foreach($this->_files as $file) {
            $webPath = $this->_config['js_web_path'] . '/' . $file;
            $filePath = realpath($this->_config['js_output_dir'] . '/' . $file);

            if(!is_file($filePath)) {
                throw new \Exception("Failed to find ".
                    ($this->_config['js_output_dir'] . '/' . $file));
            }

            if(isset($this->_config['manifest_files'][$filePath])) {
                $buster = '?'.$this->_config['manifest_files'][$filePath];
            } else {
                // write cachebuster hash from asset manifest
                $buster = "?".time();
            }

            $compiler
                ->write(sprintf(
                    'echo "<script src=\"%s%s\"></script>"', $webPath, $buster
                ))
                ->raw(";\n")
            ;
        }
    }
}
