<?php

/**
 * The Heart
 *
 * @author      Bitcoding <bitcoding@bitcoding.eu>
 * @copyright   Copyright &copy; 2009-2014, Bitcoding
 * @link        http://www.lessphp.eu/
 * @link        http://www.bitcoding.eu/
 * @license     http://www.bitcoding.eu/license/
 * 
 * @version     0.1.0 (Breadcrumb): LessPHP.php
 * @package     LessPHP/LessPHP
 * @category    LessPHP
 */
interface BLessPHP {

    public function getTagHandlers();

    public function setAuto($bo);

    public function setReturn($bo);
}

class LessPHP {

    const INIT = 0;
    const RENDER = 1;
    const FINISH = 2;

    private $_parent = null;
    private $_handler = null;
    public $tags;

    function __construct(&$parent, $docblock, $run = true) {
        if ($parent instanceof BLessPHP) {
            $this->_parent = $parent;
            $this->_taghandler = $parent->getTagHandlers();

            if (method_exists($docblock, 'getDocComment')) {
                $docblock = $docblock->getDocComment();
            }
            $docblock = $this->cleanInput($docblock);

            list(,, $tags) = $this->splitDocBlock($docblock);
            $this->parseTags($tags);
            if ($run)
                $this->run();
        }
    }

    function run($state = self::INIT) {
        if (isset($this->tags[$state])) {
            foreach ($this->tags[$state] as $value) {
                $tag = $value->tag;
                $args = $value->args;
                if (isset($this->_taghandler[$state][$tag])) {
                    $_func = $this->_taghandler[$state][$tag];
                    $this->_parent->$_func($args);
                }
            }
        }
    }

    function getTags() {
        return $this->tags;
    }

    /**
     * Strips the asterisks from the DocBlock comment.
     *
     * @param string $comment String containing the comment text.
     *
     * @return string
     */
    protected function cleanInput($comment) {
        $comment = trim(preg_replace('#[ \t]*(?:\/\*\*|\*\/|\*)?[ \t]{0,1}(.*)?#u', '$1', $comment));

        // reg ex above is not able to remove */ from a single line docblock
        if (substr($comment, -2) == '*/') {
            $comment = trim(substr($comment, 0, -2));
        }

        // normalize strings
        $comment = str_replace(array("\r\n", "\r"), "\n", $comment);

        return $comment;
    }

    /**
     * Splits the DocBlock into a short description, long description and
     * block of tags.
     *
     * @param string $comment Comment to split into the sub-parts.
     *
     * @author RichardJ Special thanks to RichardJ for the regex responsible
     *     for the split.
     *
     * @return string[] containing the short-, long description and an element
     *     containing the tags.
     */
    protected function splitDocBlock($comment) {
        if (strpos($comment, '@') === 0) {
            $matches = array('', '', $comment);
        } else {
            // clears all extra horizontal whitespace from the line endings
            // to prevent parsing issues
            $comment = preg_replace('/\h*$/Sum', '', $comment);

            /*
             * Big thanks to RichardJ for contributing this Regular Expression
             */
            preg_match('/
            \A (
              [^\n.]+
              (?:
                (?! \. \n | \n{2} ) # disallow the first seperator here
                [\n.] (?! [ \t]* @\pL ) # disallow second seperator
                [^\n.]+
              )*
              \.?
            )
            (?:
              \s* # first seperator (actually newlines but it\'s all whitespace)
              (?! @\pL ) # disallow the rest, to make sure this one doesn\'t match,
              #if it doesn\'t exist
              (
                [^\n]+
                (?: \n+
                  (?! [ \t]* @\pL ) # disallow second seperator (@param)
                  [^\n]+
                )*
              )
            )?
            (\s+ [\s\S]*)? # everything that follows
            /ux', $comment, $matches);
            array_shift($matches);
        }

        while (count($matches) < 3) {
            $matches[] = '';
        }
        return $matches;
    }

    /**
     * Creates the tag objects.
     *
     * @param string $tags Tag block to parse.
     *
     * @return void
     */
    protected function parseTags($tags) {
        $_result = array();
        $result = array();
        $tags = trim($tags);
        if ('' !== $tags) {
            if ('@' !== $tags[0]) {
                throw new \LogicException('A tag block started with text instead of an actual tag,' . ' this makes the tag block invalid: ' . $tags);
            }
            foreach (explode("\n", $tags) as $tag_line) {
                if (trim($tag_line) === '') {
                    continue;
                }

                if (isset($tag_line[0]) && ($tag_line[0] === '@')) {
                    $_result[] = $tag_line;
                } else {
                    $_result[count($_result) - 1] .= PHP_EOL . $tag_line;
                }
            }

            //var_dump(implode('|', array_keys($this->_taghandler)));
            // create proper Tag objects
            foreach ($_result as $key => $tag_line) {
                $matches = null;

                if (!preg_match('/^@((.*)\s?)/us', trim($tag_line), $matches)) {
                    throw new \InvalidArgumentException('Invalid tag_line detected: ' . $tag_line);
                }
                if (!is_array($matches))
                    continue;
                list($tag, $args) = explode(' ', $matches[1], 2);
                $tag = strtolower($tag);
                $t = array('line' => $tag_line, 'tag' => $tag, 'args' => $args);
                if (isset($this->_taghandler[self::INIT][$tag]))
                    $result[self::INIT][$key] = (object) ($t);
                else if (isset($this->_taghandler[self::RENDER][$tag]))
                    $result[self::RENDER][$key] = (object) ($t);
                else if (isset($this->_taghandler[self::FINISH][$tag]))
                    $result[self::FINISH][$key] = (object) ($t);
                else
                    unset($result[$key]);
            }
        }

        $this->tags = $result;
    }

    private static $_varhelper = array('false' => false, 'true' => true, 'null' => null);

    static function GetArrayVar($command) {
        if (is_array($command))
            return $command;

        $array = array();
        $ret = array();

        $reg = ':\[(.*?)\]:sx';
        $test = preg_match_all($reg, $command, $array, PREG_SET_ORDER);

        if (!$test)
            return $command;

        $t = explode("|", $array[0][1]);
        foreach ($t as $value) {
            if (!$value)
                continue;

            list($key, $v) = explode(":", trim($value));
            $ret[$key] = isset(static::$_varhelper[strtolower($v)]) ? static::$_varhelper[strtolower($v)] : $v;
        }
        return $ret;
    }

}
