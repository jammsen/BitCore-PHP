<?php

/**
 *
 * @author      Bitcoding <bitcoding@bitcoding.eu>
 * @copyright   Copyright &copy; 2009-2014, Bitcoding
 * @link        http://www.lessphp.eu/
 * @link        http://www.bitcoding.eu/
 * @license     http://www.bitcoding.eu/license/
 * 
 * @version     0.1.0 (Breadcrumb): Vars.php
 * @since       0.1.0
 * @package     System/Helper/Vars
 * @category    Type
 * 
 * 
 * @method static valregex(string $key)
 * @method static valcallback(string $key) 
 * @method static isbase64(string $key)
 * @method static isint(string $key)
 * @method static isbool(string $key)
 * @method static isfloat(string $key) 
 * @method static isurl(string $key) 
 * @method static isip(string $key) 
 * @method static isemail(string $key) 
 * @method static getint(string $key)
 * @method static getfloat(string $key)
 * @method static getstring(string $key) 
 * @method static geturl(string $key) 
 * @method static getemail(string $key)
 */
class Vars {

    private static $lastVar = null;
    private static $error = [['Vars', '__error'], null, []];
    private static $calls = array(
        'valregex' => [['Vars', '_tcall'], FILTER_VALIDATE_REGEXP, []],
        'valcallback' => [['Vars', '_tcall'], FILTER_CALLBACK, []],
        'isbase64' => [['Vars', '_tcall'], FILTER_VALIDATE_REGEXP, ['regexp' => '/^[a-zA-Z0-9\/\r\n+]*={0,2}$/']],
        'isint' => [['Vars', '_tcall'], FILTER_VALIDATE_INT, []],
        'isbool' => [['Vars', '_tcall'], FILTER_VALIDATE_BOOLEAN, []],
        'isfloat' => [['Vars', '_tcall'], FILTER_VALIDATE_FLOAT, []],
        'isurl' => [['Vars', '_tcall'], FILTER_VALIDATE_URL, []],
        'isip' => [['Vars', '_tcall'], FILTER_VALIDATE_IP, []],
        'isemail' => [['Vars', '_tcall'], FILTER_VALIDATE_EMAIL, []],
        'getint' => [['Vars', '_tcall'], FILTER_SANITIZE_NUMBER_INT, []],
        'getfloat' => [['Vars', '_tcall'], FILTER_SANITIZE_NUMBER_FLOAT, []],
        'getstring' => [['Vars', '_tcall'], FILTER_SANITIZE_STRING, []],
        'geturl' => [['Vars', '_tcall'], FILTER_SANITIZE_URL, []],
        'getemail' => [['Vars', '_tcall'], FILTER_SANITIZE_EMAIL, []]
    );
    private static function __error() {
        throw new ErrorException();
    }
    
    private static function _tcall($filter, $var, $options = array()) {
        return filter_var($var, $filter, $options);
    }
    
    public static function registerCall($key,$func,$filter = FILTER_CALLBACK,$opt = array()){
        self::$calls[$key] = [$func,$filter,$opt];
    }
    
    private static $callTable = array('session'=> '_SESSION','post'=>'_POST','get'=>'_GET','request'=>'_REQUEST','env'=>'_ENV','file'=>'_FILES','arg'=>'argv');
    
    public static function __callStatic($name, $arguments) {
        $name = explode('_',$name);
        if (!isset($arguments[0]))
            throw new UnexpectedType(($name> 2 ) ? 'var_empty': 'key_empty');
        
        if(count($name) == 1){
            $func = $name[0];
            $name = null;
        }
        else 
            list($name,$func) = $name; //First Ignore Overhead
        
        if(count($arguments) == 1)
            $arguments[] = array();
        
        list($var,$options) = $arguments;
        
        $name = strtolower($name);
        $func = strtolower($func);
        
        if($func == 'iset')
            return isset(self::$callTable[$name]) ? isset($GLOBALS[self::$callTable[$name]][$var]) : null;
        
        $var = isset(self::$callTable[$name]) ? (isset($GLOBALS[self::$callTable[$name]][$var]) ? $GLOBALS[self::$callTable[$name]][$var] : false) : $var;
        list($f,$filter,$stdOptions)  = isset(self::$calls[$func]) ? self::$calls[$func] : self::$error;
        
        $t = $f($filter,$var,array_merge($stdOptions,$options));
        return $t;
    }

}