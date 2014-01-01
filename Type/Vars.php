<?php



define('FILTER_OBJ',2048);
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
 * @method static Post(string $key)
 * @method static Get(string $key) 
 * @method static Session(string $key)
 * @method static File(string $key)
 * @method static Cookie(string $key)
 * @method static Request(string $key) 
 */
class Vars {

    private static $lastVar = null;
    private static $calls = array(
        'valRegEx' => [['static', '_tcall'], FILTER_VALIDATE_REGEXP, []],
        'valCallback' => [['static', '_tcall'], FILTER_CALLBACK, []],
        'isBase64' => [['static', '_tcall'], FILTER_VALIDATE_REGEXP, ['regexp' => '/^[a-zA-Z0-9\/\r\n+]*={0,2}$/']],
        'isInt' => [['static', '_tcall'], FILTER_VALIDATE_INT, []],
        'isBool' => [['static', '_tcall'], FILTER_VALIDATE_BOOLEAN, []],
        'isFloat' => [['static', '_tcall'], FILTER_VALIDATE_FLOAT, []],
        'isURL' => [['static', '_tcall'], FILTER_VALIDATE_URL, []],
        'isIP' => [['static', '_tcall'], FILTER_VALIDATE_IP, []],
        'isEmail' => [['static', '_tcall'], FILTER_VALIDATE_EMAIL, []],
        'getInt' => [['static', '_tcall'], FILTER_SANITIZE_NUMBER_INT, []],
        'getFloat' => [['static', '_tcall'], FILTER_SANITIZE_NUMBER_FLOAT, []],
        'getString' => [['static', '_tcall'], FILTER_SANITIZE_STRING, []],
        'getURL' => [['static', '_tcall'], FILTER_SANITIZE_URL, []],
        'getEmail' => [['static', '_tcall'], FILTER_SANITIZE_EMAIL, []],
        
        'VarObj' => [['static', 'VarObj'], FILTER_OBJ, []],
    );

    private static function __tcall($filter, $var, $options = array()) {
        return filter_var($var, $filter, $options);
    }

    public static function call($filter, $var, $options = null) {
        return self::__tcall($filter, $var, $options);
    }
    public static function VarObj($class, $var, $option = null) {
        return new VarObject();
        return self::__tcall($filter, $var, $option);
    }
    public static function __callStatic($name, $arguments) {
        static $_Vars;

        if (!$_Vars)
            $_Vars = new Self();

        if (!isset($arguments[0]))
            throw new UnexpectedType('key_empty');

//        $var =
//        return function() use (&$_Vars,) { $_Vars-> };
        var_dump($_Vars);
        var_dump($name, $arguments);
        die('hmm');
        throw new ToDoException('Call Static Vars Like Post , GET ..');
    }

}

class VarObject{

    private $var = null;

    function __construct($_var) {
        $this->var = $_var;
    }
    
    public static function __callStatic($name, $arguments) {
        if (!isset($arguments[0]))
            throw new UnexpectedType('key_empty');
        
        return call_user_func_array(['Vars', '__callStatic', [$name, array_unshift(array_values($arguments) , $this->var)]]);
    }
}
