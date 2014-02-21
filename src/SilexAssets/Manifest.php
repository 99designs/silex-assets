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
}
