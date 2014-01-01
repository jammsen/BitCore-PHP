<?php

/**
 * Bit bootstrap file.
 *
 * This file is intended to be included in the entry script of Bit applications.
 * It defines Bit class by extending BitBase, a static class providing globally
 * available functionalities that enable Bit component model and error handling mechanism.
 *
 * By including this file, the PHP error and exception handlers are set as
 * Bit handlers, and an __autoload function is provided that automatically
 * loads a class file if the class is not defined.
 *
 * @author      Bitcoding <bitcoding@bitcoding.eu>
 * @copyright   Copyright &copy; 2009-2014, Bitcoding
 * @link        http://www.lessphp.eu/
 * @link        http://www.bitcoding.eu/
 * @license     http://www.bitcoding.eu/license/
 * 
 * @version     0.1.0 (Breadcrumb): Bit.php
 * @package     System
 */
/**
 * Defines a simple Directory Seperator
 */
if (!defined('DS'))
    define('DS', DIRECTORY_SEPARATOR);

/**
 * Defines the Bit framework installation path.
 */
if (!defined('BIT_DIR_BASE'))
    define('BIT_DIR_BASE', dirname(__FILE__));
/**
 * Defines the External path.
 */
if (!defined('BIT_DIR_EXTERNAL'))
    define('BIT_DIR_EXTERNAL', BIT_DIR_BASE . DS . "Externals");
/**
 * Defines the External path.
 */
if (!defined('BIT_DIR_EXCEPTION'))
    define('BIT_DIR_EXCEPTION', BIT_DIR_BASE . DS . "Exceptions");
/**
 * Defines a simple Directory Back 
 */
if (!defined('DB'))
    define('DB', '..' . DS);

include_once BIT_DIR_BASE . '/Interface.php'; //Include Simple Interfaces
// XXX: Would I use it? i would see 

abstract class BitwiseFlag {

    protected $flags;

    /**
     * Note: these functions are protected to prevent outside code
     * from falsely setting BITS. See how the extending class 'User'
     * handles this.
     *
     */
    protected function isFlagSet($flag) {
        return (($this->flags & $flag) == $flag);
    }

    protected function setFlag($flag, $value) {
        if ($value) {
            $this->flags |= $flag;
        } else {
            $this->flags &= ~$flag;
        }
    }

}

/**
 * BitBase class.
 *
 * BitBase implements a few fundamental static methods.
 *
 * To use the static methods, Use Bit as the class name rather than BitBase.
 * BitBase is meant to serve as the base class of Bit. The latter might be
 * rewritten for customization.
 *
 * Thank to Prado
 * 
 * @version 0.1.0 (Breadcrumb): Bit.php
 * @package System
 * @since   0.1.0
 */
class BitBase {

    /**
     * File extension for PHP files.
     */
    const PHP_EXT = 'php';

    /**
     * File extension for translations files.
     */
    const LANG_EXT = 'lng';

    /**
     * File extension for Template files.
     */
    const HTML_EXT = 'html';
    const BIT_RULE = '42495443-4f44-494e-475f-52554c450d0a';
    const BIT_CACHE = '42495443-4f44-494e-475f-52554c450d0a';
    const BIT_HMAC = '42495443-4f44-494e-475f-484d41430d0a';

    /**
     * PHP Run Level
     */
    const RING_0 = 0x0;

    /**
     * Kernel Mode
     */
    const RING_1 = 0x1;

    /**
     * Prepare Mode
     */
    const RING_2 = 0x2;

    /**
     * Render Mode
     */
    const RING_3 = 0x4;

    /**
     * Finish Mode
     */
    const RING_4 = 0x8;
    
    const RELEASE = 0x0;
    const SIMPLE = 0x1;
    const DEBUG = 0x2;
    /**
     * PHP-Runtime
     */
    const MODE_0 = 0x0;
    /**
     * Cli-Mode
     */
    const MODE_1 = 0x0;
    /**
     * Page-Mode
     */
    const MODE_2 = 0x1;
    /**
     * Custom Mode 1
     */
    const MODE_3 = 0x2;
    /**
     * Custom Mode 2
     */
    const MODE_4 = 0x2;
    
    protected static $state = 0x0000;

    /**
     * @var array list of path aliases
     */
    private static $_aliases = array(
        'Bit' => BIT_DIR_BASE,
        'Externals' => BIT_DIR_EXTERNAL,
        'Exception' => BIT_DIR_EXCEPTION
    );

    /**
     * @var array list of namespaces currently in use
     */
    private static $_usings = array();

    /**
     * @var BSite the Site instance
     */
    private static $_site = null;

    /**
     * @var BPage the Page instance need by Exception? 
     */
    private static $_page = null;

    /**
     * @return string the version of Bit framework
     */
    public static function getVersion() {
        return '0.1.0';
    }

    /**
     * @return bool is BitBase a site
     */
    public static function isSite() {
        if (self::$_site !== null && (self::$_site instanceof BSite))
            return true;
        return false;
    }

    /**
     * @var callable $jQuery Handler
     */
    public static $jQuery = null;

    /**
     * Stores the Site instance in the class static member.
     * This method helps implement a singleton pattern for BSite.
     * Repeated invocation of this method or the application constructor
     * will cause the throw of an exception.
     * This method should only be used by framework developers.
     * @param BSite $site the Site instance
     * @throws InvalidException if this method is invoked twice or more.
     */
    public static function setSite(BSite $site) {
        if (self::$_site !== null && !defined('BIT_TEST_RUN'))
            throw new InvalidException('bit_site_singelton');
        self::$_site = $site;
    }

    /**
     * @return BSite the Site singleton, null if the singleton has not be created yet.
     */
    public static function getSite() {
        return self::$_site;
    }

    /**
     * init the Base.
     * @param BSite $site the Site instance
     * @throws InvalidException if this method is invoked twice or more.
     */
    public static function init() {
        Bit::using('Bit.*'); // I ;)
        Bit::using('Bit.LessPHP.LessPHP'); // I am Lazy
        Bit::using('Externals.phpQuery.phpQuery'); // Later Rewrite

        self::$jQuery = function() {
            return call_user_func_array(array('phpQuery', 'pq'), func_get_args());
        };
        self::initErrorHandlers();
        BitHelper::SetByteValue(self::$state, 0, self::RING_0);
        BitHelper::SetByteValue(self::$state, 1, self::MODE_2); //TODO
        BitHelper::SetByteValue(self::$state, 2, self::DEBUG); //TODO    
        /**
         * Sets onShutdown handler to be BitBase::onShutdown
         */
        register_shutdown_function(array(
            'BitBase',
            'onShutdown'
        ));
    }

    /**
     * Initializes error handlers.
     * This method set error and exception handlers to be functions
     * defined in this class.
     */
    public static function initErrorHandlers() {
        Bit::using('Bit.Exceptions.*');
        /**
         * Sets error handler to be BitBase::phpErrorHandler
         */
        set_error_handler(array(
            'BitBase',
            'phpErrorHandler'
        ));
        /**
         * Sets exception handler to be BitBase::exceptionHandler
         */
        set_exception_handler(array(
            'BitBase',
            'exceptionHandler'
        ));
    }

    /**
     * PHP error handler.
     * This method should be registered as PHP error handler using
     * {@link set_error_handler}. The method throws an exception that
     * contains the error information.
     * @param integer the level of the error raised
     * @param string the error message
     * @param string the filename that the error was raised in
     * @param integer the line number the error was raised at
     */
    public static function phpErrorHandler($errno, $errstr, $errfile, $errline) {
        if (error_reporting() & $errno) {
            if (self::$state & self::RING_1) {
                throw new PhpErrorException($errno, $errstr, $errfile, $errline);
            } else {
                var_dump($errno, $errstr, $errfile, $errline);
                die('TODO: Ring 0');
            }
        }
        return true;
    }

    /**
     * Default exception handler.
     * This method should be registered as default exception handler using
     * {@link set_exception_handler}. The method tries to use the errorhandler
     * module of the Bit site to handle the exception.
     * If the site or the module does not exist, it simply echoes the
     * exception.
     * @param Exception exception that is not caught
     */
    static function exceptionHandler($exception) {
        if (self::$_site !== null) {
            self::$_site->doException($exception);
        } else {
            echo $exception;
        }
        exit(1);
    }

    /**
     * Default Shutdown.
     * This method should be registered as default exception handler using
     * {@link set_exception_handler}. The method tries to use the errorhandler
     * module of the Bit site to handle the exception.
     * If the site or the module does not exist, it simply echoes the
     * exception.
     * @param Exception exception that is not caught
     */
    static function onShutdown() {
        if (self::$_site !== null) {
            self::$_site->onDestroy();
        }
    }

    /**
     * Class autoload loader.
     * This method is provided to be invoked within an __autoload() magic method.
     * @param string class name
     */
    static function autoload($className) {
        include_once (str_replace('\\', '/', $className) . '.' . static::PHP_EXT);

        if (strpos('\\', $className) && (!class_exists($className, false) && !interface_exists($className, false)))
            self::fatalError("Class file for '$className' cannot be found.");
    }

    /**
     * Uses a namespace.
     * A namespace ending with an asterisk '*' refers to a directory, otherwise it represents a PHP file.
     * If the namespace corresponds to a directory, the directory will be appended
     * to the include path. If the namespace corresponds to a file, it will be included (include_once).
     * @param string namespace to be used
     * @param boolean whether to check the existence of the class after the class file is included
     * @throws TInvalidDataValueException if the namespace is invalid
     */
    public static function using($namespace, $checkExistence = true) {
        if (isset(self::$_usings[$namespace]) || class_exists($namespace, false))
            return;

        $_path = self::getPathOfNamespace($namespace);
        $path = $_path['dirname'] . DS . $_path['basename'];

        if (isset($_path['extension']) && $_path['extension'] === self::PHP_EXT) {
            self::$_usings[$namespace] = $path;

            include_once ($path);
            set_include_path(get_include_path() . PATH_SEPARATOR . $_path['dirname']);

            if ($checkExistence && (!class_exists($_path['filename'], false) && !interface_exists($_path['filename'], false)))
                throw new BitException('class_unknown', $_path['filename']);
        }
        elseif (is_array($_path)) {
            $_stdDir = array(
                $path . DS . 'Exceptions',
                $path . DS . 'Const',
                $path . DS . 'Helper',
                $path . DS . 'Type',
                $path . DS /* WildCardHelper */
            );
            set_include_path(get_include_path() . PATH_SEPARATOR . implode(PATH_SEPARATOR, $_stdDir));
        } else
            throw new InvalidException('bit_using', $namespace);
    }

    /**
     * Translates a namespace into a file path.
     * The first segment of the namespace is considered as a path alias
     * which is replaced with the actual path. The rest segments are
     * subdirectory names appended to the aliased path.
     * If the namespace ends with an asterisk '*', it represents a directory;
     * Otherwise it represents a file whose extension name is specified by the second parameter (defaults to empty).
     * Note, this method does not ensure the existence of the resulting file path.
     * @param string namespace
     * @param string extension to be appended if the namespace refers to a file
     * @return string file path corresponding to the namespace, null if namespace is invalid
     */
    public static function getPathOfNamespace($namespace, $ext = Bit::PHP_EXT) {

        if (isset(self::$_usings[$namespace]))
            return self::$_usings[$namespace];

        if (isset(self::$_aliases[$namespace]))
            return self::$_aliases[$namespace];

        $segs = explode('.', $namespace);

        $alias = self::getPathOfAlias(array_shift($segs));
        if (!$alias)
            return null;

        $base = array_pop($segs);
        $path = implode(DS, $segs);
        $root = rtrim($alias . DS . $path, '/\\');

        return pathinfo($root . (($base === '*') ? '' : DS . $base . "." . $ext));
    }

    /* @param string namespace
     * @param boolean Init to be appended if the namespace refers to a file
     * @return string|object file path corresponding to the namespace, null if namespace is invalid
     */

    public static function getClassOfNamespace($namespace, $init = true) {
        $class_name = explode('.', $namespace);
        $class_name = array_pop($class_name);
        if ($class_name !== '*' && $class_name !== '')
            return $init ? new $class_name : $class_name;
        return null;
    }

    /**
     * @param string alias to the path
     * @return string the path corresponding to the alias, null if alias not defined.
     */
    public static function getPathOfAlias($alias) {
        return isset(self::$_aliases[$alias]) ? self::$_aliases[$alias] : null;
    }

    protected static function getPathAliases() {
        return self::$_aliases;
    }

    /**
     * @param string alias to the path
     * @param string the path corresponding to the alias
     * @throws RedefinedException if the alias is already defined
     */
    public static function setPathOfAlias($alias, $path) {
        if (isset(self::$_aliases[$alias]) && !defined('BIT_TEST_RUN'))
            throw new RedefinedException('alias', $alias);
        else if (($rp = realpath($path)) !== false && is_dir($rp)) {
            if (strpos($alias, '.') === false)
                self::$_aliases[$alias] = $rp;
            else
                throw new InvalidException('aliasname', $alias);
        } else
            throw new InvalidException('alias_path', $alias, $path);
    }

    public static function IncludeAll($namespace) {
        if (($path = self::getPathOfNamespace($namespace)) !== null) {
            foreach (glob($path['dirname'] . DS . $path['basename'] . DS . "*.php") as $filename) {
                require_once $filename;
            }
        } else
            throw new InvalidException('bit_using', $namespace);
    }

    /**
     * Returns a list of user preferred languages.
     * The languages are returned as an array. Each array element
     * represents a single language preference. The languages are ordered
     * according to user preferences. The first language is the most preferred.
     * @return array list of user preferred languages.
     */
    public static function getUserLanguages() {
        static $languages = null;
        if ($languages === null) {
            if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
                $languages[0] = Agent::LANG_EN;
            else {
                $languages = array();
                foreach (explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']) as $language) {
                    $array = explode(';q=', trim($language));
                    $languages[trim($array[0])] = isset($array[1]) ? (float) $array[1] : 1.0;
                }
                arsort($languages);
                $languages = array_keys($languages);
                if (empty($languages))
                    $languages[0] = Agent::LANG_EN;
            }
        }
        return $languages;
    }

    /**
     * Returns the most preferred language by the client user.
     * @return string the most preferred language by the client user, defaults to English.
     */
    public static function getPreferredLanguage() {
        static $language = null;
        if ($language === null) {
            $langs = Bit::getUserLanguages();
            $lang = explode('-', $langs[0]);
            if (empty($lang[0]) || !ctype_alpha($lang[0]))
                $language = Agent::LANG_EN;
            else
                $language = $lang[0];
        }
        return $language;
    }

    /**
     *
     */
    public static function getUUIDv5($string, $base = self::BIT_RULE) {
        return UUID::v5($base, $string);
    }

    public static function getSysTemp() {
        return sys_get_temp_dir();
    }

    public static function getFileHash($file, $mode = 'md5', $secret = self::BIT_HMAC) {
        return hash_hmac_file($mode, $file, $secret);
    }

    public static function fatalError($msg) {
        $_msg = $msg;

        $msg = '<h1>Fatal Error</h1>';
        $msg .= '<p>' . $msg . '</p>';
        static::_Error($msg);
        return $msg;
    }

    public static function _Error(&$msg) {
        if (!function_exists('debug_backtrace'))
            return;

        $msg .= '<h2>Debug Backtrace</h2>';
        $msg .= '<pre>';
        $index = -1;
        foreach (debug_backtrace() as $t) {
            $index++;
            if ($index == 0)// hide the backtrace of this function
                continue;
            echo '#' . $index . ' ';
            if (isset($t['file']))
                $msg .= basename($t['file']) . ':' . $t['line'];
            else
                $msg .= '<PHP inner-code>';
            echo ' -- ';
            if (isset($t['class']))
                $msg .= $t['class'] . $t['type'];
            echo $t['function'] . '(';
            if (isset($t['args']) && sizeof($t['args']) > 0) {
                $count = 0;
                foreach ($t['args'] as $item) {
                    if (is_string($item)) {
                        $str = htmlentities(str_replace("\r\n", "", $item), ENT_QUOTES);
                        if (strlen($item) > 70)
                            $msg .= "'" . substr($str, 0, 70) . "...'";
                        else
                            $msg .= "'" . $str . "'";
                    }
                    else if (is_int($item) || is_float($item))
                        $msg .= $item;
                    else if (is_object($item))
                        $msg .= get_class($item);
                    else if (is_array($item))
                        $msg .= 'array(' . count($item) . ')';
                    else if (is_bool($item))
                        $msg .= $item ? 'true' : 'false';
                    else if ($item === null)
                        $msg .= 'NULL';
                    else if (is_resource($item))
                        $msg .= get_resource_type($item);
                    $count++;
                    if (count($t['args']) > $count)
                        $msg .= ', ';
                }
            }
            $msg .= ")\n";
        }
        $msg .= '</pre>';
    }

    /**
     * @see phpQuery::pq()
     * @return phpQueryObject
     */
    public static function getTemplate($namespace) {
        $dir = Bit::getPathOfNamespace("Themes.Html." . $namespace, 'html');
        $lang = Bit::getPreferredLanguage();

        if (!is_file($file = $dir['dirname'] . DIR_SEP . $lang . DIR_SEP . $dir['basename']))
            $file = $dir['dirname'] . DIR_SEP . $dir['basename'];

        return isset($file) ? pq(phpQuery::newDocumentFile($file)) : NULL;
    }

    private static $lessCache = array();

    public static function callLess() {
        $cache = static::$lessCache;
    }

    public static function debug(&$val) {
        $res = "<pre>" . self::_debug($val) . "</pre>";
        pq('body', self::$_page)->append($res);
    }

    public static function _debug(&$val) {
        $text = print_r($val, true);

        $objectName = 'BMysql|BSqlite|phpQueryObject'; // TODO:

        $text = preg_replace('#(' . $objectName . ') Object\n(\s+)\(.*?\n\2\)\n#s', "$1 Object(<span style=\"color: #FF9900;\">--&gt; HIDDEN - courtesy of wtf() &lt;--</span>)", $text);
        $text = preg_replace('#(\w+)(\s+Object\s+\()#s', '<span style="color: #079700;">$1</span>$2', $text);
        $text = preg_replace('#(\w+)(\s+Object\()#s', '<span style="color: #FF0011;">$1</span>$2', $text);
        $text = preg_replace('#\[(\w+)\]#', '[<span style="color: #000099;">$1</span>]', $text);
        $text = preg_replace('#\[(\w+)\:(public|private|protected)\]#', '[<span style="color: #000099;">$1</span>:<span style="color: #009999;">$2</span>]', $text);
        $text = preg_replace('#\[(\w+)\:(\w+)\:(public|private|protected)\]#', '[<span style="color: #000099;">$1</span>:<span style="color: #990099;">$2</span>:<span style="color: #009999;">$3</span>]', $text);

        return $text;
    }

}
