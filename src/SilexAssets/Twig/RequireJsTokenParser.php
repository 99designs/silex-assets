<?php

namespace SilexAssets\Twig;

class RequireJsTokenParser extends \Twig_TokenParser
{
    private
        $_config=array()
        ;

    public function __construct($config)
    {
        $this->_config = $config;
    }

    public function parse(\Twig_Token $token)
    {
        $files = array();
        $stream = $this->parser->getStream();

        // parse string values until the tag end
        while (!$stream->test(\Twig_Token::BLOCK_END_TYPE)) {
            if ($stream->test(\Twig_Token::STRING_TYPE)) {
                // 'bundle/file', 'bundle/file2'
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

        return new RequireJsNode($this->_config, $files, $token->getLine(), $this->getTag());
    }

    public function getTag()
    {
        return "requirejs";
    }
}
