<?php

namespace SilexAssets;

class Manifest
{
    private $_outputDir, $_manifestFile, $_manifest;

    public function __construct($outputDir, $manifestFile)
    {
        $this->_outputDir = $outputDir;
        $this->_manifestFile = $manifestFile;
    }

    private function _parse()
    {
        $path = $this->_outputDir.'/'.$this->_manifestFile;
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

    public function getChecksum($file)
    {
        if(!isset($this->_manifest)) $this->_manifest = $this->_parse();

        if(isset($this->_manifest[$file])) {
            return $this->_manifest[$file];
        }

        return false;
    }

    /*

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

        */

}
