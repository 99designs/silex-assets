<?php

namespace Twig\Extensions\Assets;

class TokenParser extends \Twig_TokenParser
{
    private
        $_tag,
        $_config=array()
        ;

    public function __construct($tag, $config)
    {
        $this->_tag = $tag;
        $this->_config = $config;
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

        return $this->createNode($this->getTag(), $token->getLine(), $files);
    }

    private function createNode($tag, $line, $files)
    {
        switch($tag)
        {
            case 'stylesheet':
                return new StylesheetNode($this->_config, $files, $line, $this->getTag());
            case 'javascript':
                return new JavascriptNode($this->_config, $files, $line, $this->getTag());
            case 'requirejs':
                return new RequireJsNode($this->_config, $files, $line, $this->getTag());
            default:
                throw new \InvalidArgumentException("Unknown tag $tag");
        }
    }

    public function getTag()
    {
        return $this->_tag;
    }
}
