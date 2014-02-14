<?php

namespace SilexAssets\Twig;

class AssetTokenParser extends \Twig_TokenParser
{
    private
        $_tag,
        $_manifest,
        $_config=array()
        ;

    public function __construct($tag, $manifest, $config)
    {
        $this->_tag = $tag;
        $this->_config = $config;
        $this->_manifest = $manifest;
    }

    public function parse(\Twig_Token $token)
    {
        $files = array();
        $stream = $this->parser->getStream();

        // parse string values until the tag end
        while (!$stream->test(\Twig_Token::BLOCK_END_TYPE)) {
            if ($stream->test(\Twig_Token::STRING_TYPE)) {
                // 'css/myfile.css', 'css/llamas.css'
                $files[] = $stream->next()->getValue();
            } else {
                $token = $stream->getCurrent();
                throw new \Twig_Error_Syntax(sprintf(
                    'Unexpected token "%s" of value "%s"',
                    \Twig_Token::typeToEnglish($token->getType(), $token->getLine()), $token->getValue()),
                    $token->getLine());
            }
        }

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        $body = $this->parser->subparse(array($this, 'testEndTag'), true);

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        return $this->createNode($body, $this->getTag(), $token->getLine(), $files);
    }

    public function testEndTag(\Twig_Token $token)
    {
        return $token->test(array('end'.$this->getTag()));
    }

    private function createNode($body, $tag, $line, $files)
    {
        return new AssetNode($this->_manifest, $this->_config, $body, $files, $line, $this->getTag());
    }

    public function getTag()
    {
        return $this->_tag;
    }
}
