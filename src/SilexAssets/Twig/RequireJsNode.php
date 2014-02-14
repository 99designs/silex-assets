<?php

namespace SilexAssets\Twig;

class RequireJsNode extends \Twig_Node
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
        $compiler->addDebugInfo($this);

        foreach($this->_files as $file) {
            if($this->_config['requirejs_compiled']) {
                $webPath = $this->_config['requirejs_web_path'] . '/' . $file. '.js';
                $filePath = realpath($this->_config['requirejs_output_dir'] . '/' . $file . '.js');

                if(!is_file($filePath)) {
                    throw new \Exception("Failed to find ".
                        ($this->_config['requirejs_output_dir'] . '/' . $file . '.js'));
                }

                $compiler
                    ->write(sprintf(
                        'echo "<script src=\"%s?%s\"></script>"', $webPath, $buster
                    ))
                    ->raw(";\n")
                ;
            }

            $compiler
                ->write(sprintf(
                    'echo "<script>require([\"%s\"]);</script>"', $file
                ))
                ->raw(";\n")
            ;
        }
    }
}
