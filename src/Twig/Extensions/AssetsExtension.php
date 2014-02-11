<?php

namespace Twig\Extensions;

class AssetsExtension extends \Twig_Extension
{
    private $_config;

    public function __construct($config)
    {
        $this->_config = $config;

        if(isset($this->_config['manifest'])) {
            $this->_config['manifest_files'] = 
                $this->_parseManifest($this->_config['manifest']);
        }

        // a manifest root means manifest contains relative paths
        if(isset($this->_config['manifest_root'])) {
            $root = realpath($this->_config['manifest_root']);

            // make paths unrelative
            foreach($this->_config['manifest_files'] as $key=>$val) {
                unset($this->_config['manifest_files'][$key]);
                $this->_config['manifest_files'][$root . '/' . $key] = $val;
            }
        }
    }

    private function _parseManifest($path)
    {
        $handle = @fopen($path, "r");
        $manifest = array();

        if ($handle) {
            while (($buffer = fgets($handle, 4096)) !== false) {
                list($hash, $filename) = explode(' ', $buffer);
                $manifest[trim($filename)] = $hash;
            }
            if (!feof($handle)) {
                throw new \Exception("Unexpected fgets() fail\n");
            }
            fclose($handle);
        } else {
            throw new \Exception("Failed to open $path, run make manifest\n");
        }

        return $manifest;
    } 

    public function getName()
    {
        return 'assets';
    }

    function getTokenParsers()
    {
    	return array(
    		new TokenParser('stylesheet', $this->_config),
            new TokenParser('javascript', $this->_config),
    		new TokenParser('requirejs', $this->_config),
    	);
    }
}
