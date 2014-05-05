<?php

namespace SilexAssets\Twig;

class RequireJsNode extends \Twig_Node
{
    private $config;
    private $manifest;
    private $files = array();

    public function __construct($manifest, $config, $files, $line, $tag = null)
    {
        parent::__construct(array(), array(), $line, $tag);

        $this->manifest = $manifest;
        $this->config = $config;
        $this->files = $files;
    }

    public function compile(\Twig_Compiler $compiler)
    {
        $compiler->addDebugInfo($this);

        foreach($this->files as $file) {
            if($this->config['requirejs_compiled']) {
                $filePath = realpath($this->config['requirejs_output_dir'] . '/' . $file . '.js');

                if(!is_file($filePath)) {
                    throw new \Exception("Failed to find ".
                        ($this->config['requirejs_output_dir'] . '/' . $file . '.js'));
                }

                $compiler
                    ->write(sprintf(
                        'echo "<script src=\"%s\"></script>"', $this->getAssetUrl($file)
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

    private function getAssetUrl($asset)
    {
        if($checksum = $this->manifest->getChecksum("js/" . $asset . ".js")) {
            $buster = "?".$checksum;
        } else {
            $buster = "?t".time();
        }
        return $this->config['requirejs_web_path'] . '/' . $asset .  ".js" . $buster;
    }
}
