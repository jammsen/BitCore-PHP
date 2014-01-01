<?php

/**
 * BException class
 *
 * BException is the base class for all BIT exceptions.
 *
 * BException provides the functionality of translating an error code
 * into a descriptive error message in a language that is preferred
 * by user browser. Additional parameters may be passed together with
 * the error code so that the translated message contains more detailed
 * information.
 *
 * By default, BException looks for a message file by calling
 * {@link getErrorMessageFile()} method, which uses Bit::CLASS_HTML_EXT
 * 
 * file located under "self::LANG_DIR" folder
 * @author      Bitcoding <bitcoding@bitcoding.eu>
 * @copyright   Copyright &copy; 2009-2014, Bitcoding
 * @link        http://www.lessphp.eu/
 * @link        http://www.bitcoding.eu/
 * @license     http://www.bitcoding.eu/license/
 * 
 * @version     0.1.0 (Breadcrumb): BitException.php
 * @since       0.1.0
 * @package     System/Exception/BException
 * @category    Exception
 */
abstract class BException extends Exception implements IException {

    CONST LANG_DIR = __DIR__;
    CONST TEMPLATE_DIR = __DIR__;

    private $_errorCode = '';
    static $_messageCache = array();

    /**
     * Constructor.
     * @param string error message. 
     */
    public function __construct($errorMessage) {
        $this->_errorCode = $errorMessage;
        $errorMessage = $this->translateErrorMessage($errorMessage);
        $args = func_get_args();

        array_shift($args);
        parent::__construct(vsprintf($errorMessage, $args));
    }

    /**
     * Translates an error code into an error message.
     * @param string error code that is passed in the exception constructor.
     * @return string the translated error message
     */
    protected function translateErrorMessage($key) {
        $file = $this->getErrorMessageFile();
        $uFile = Bit::getUUIDv5($file);

        if (!isset(self::$_messageCache[$uFile]))
            self::$_messageCache[$uFile] = parse_ini_file($file, TRUE);

        $lang = Bit::getPreferredLanguage();
        $c = &self::$_messageCache[$uFile];

        if (isset($c[$lang][$key]))
            return $c[$lang][$key];
        else if (isset($c[Agent::LANG_EN][$key]))
            return $c[Agent::LANG_EN][$key];

        return $key;
    }

    /**
     * @return string path to the error message file
     */
    protected function getErrorMessageFile() {
        $dir = constant(get_called_class() . '::LANG_DIR');
        $file = $dir . DS . get_called_class() . "." . Bit::LANG_EXT;
        if (!is_file($file))
            $file = __DIR__ . DS . "BitException" . "." . Bit::LANG_EXT;

        if (!is_file($file))
            Bit::fatalError("No Exception Lang file Found");

        return $file;
    }

    /**
     * @return string error code
     */
    public function getErrorCode() {
        return $this->_errorCode;
    }

    /**
     * @param string error code
     */
    public function setErrorCode($code) {
        $this->_errorCode = $code;
    }

    /**
     * @return string error message
     */
    public function getErrorMessage() {
        return $this->getMessage();
    }

    /**
     * @param string error message
     */
    protected function setErrorMessage($message) {
        $this->message = $message;
    }

    /**
     * Standart Error
     * @param string to Header
     */
    public function __toString() {
        $ret = '<h1>Kernel - Error</h1>';
        $ret .= '<p>' . $this->getMessage() . '</p>';

        if (!function_exists('debug_backtrace'))
            return;

        echo '<h2>Debug Backtrace</h2>';
        echo '<pre>';
        $index = -1;
        foreach (debug_backtrace() as $t) {
            $index++;
            if ($index == 0)// hide the backtrace of this function
                continue;
            echo '#' . $index . ' ';
            if (isset($t['file']))
                echo basename($t['file']) . ':' . $t['line'];
            else
                echo '<PHP inner-code>';
            echo ' -- ';
            if (isset($t['class']))
                echo $t['class'] . $t['type'];
            echo $t['function'] . '(';
            if (isset($t['args']) && sizeof($t['args']) > 0) {
                $count = 0;
                foreach ($t['args'] as $item) {
                    if (is_string($item)) {
                        $str = htmlentities(str_replace("\r\n", "", $item), ENT_QUOTES);
                        if (strlen($item) > 70)
                            echo "'" . substr($str, 0, 70) . "...'";
                        else
                            echo "'" . $str . "'";
                    }
                    else if (is_int($item) || is_float($item))
                        echo $item;
                    else if (is_object($item))
                        echo get_class($item);
                    else if (is_array($item))
                        echo 'array(' . count($item) . ')';
                    else if (is_bool($item))
                        echo $item ? 'true' : 'false';
                    else if ($item === null)
                        echo 'NULL';
                    else if (is_resource($item))
                        echo get_resource_type($item);
                    $count++;
                    if (count($t['args']) > $count)
                        echo ', ';
                }
            }
            echo ")\n";
        }
        echo '</pre>';
        exit(1);
    }

    public function getTemplate() {
        $dir = constant(get_called_class() . '::TEMPLATE_DIR') . DS;
        $file = $dir . get_called_class() . '.' . Bit::HTML_EXT;
        if (is_file($file) === false) {
            $file = $dir . 'BException' . '.' . Bit::HTML_EXT;
            if (is_file($file) === false) {
                $file = (self::TEMPLATE_DIR) . DS . 'BException' . '.tpl';
            }
        }
        return isset($file) ? pq(phpQuery::newDocumentFile($file)) : NULL;
    }

}