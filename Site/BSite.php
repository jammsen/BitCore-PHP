<?php
/**
 *
 * @author      Bitcoding <bitcoding@bitcoding.eu>
 * @link        http://www.bitcoding.eu/
 * @copyright   Copyright &copy; 2009-2013, Bitcoding
 * @license     http://www.bitcoding.eu/license/
 * @version     3.1.0: BSite.php
 * @package     System/Site
 * @category    Site
 * @since       3.1.0
 */
 
//Bit::using("Bit.Site.BitQuery.BitQuery");
 
 
Bit::using("Bit.Site.*");
Bit::using("Bit.Site.Router.*");

abstract class BSite implements ArrayAccess,ISite {
    protected static $_loggedIn = false;
    protected static $_user     = NULL;
	protected static $_modules 	= array();
	
	public static function setModule($namespace){
		
	}
	
	public static function getModule($namespace){	
		
	}
	
    public static function isLoggedIn($server = null, $matches = null)
    {
        return self::$_loggedIn;
    }
    
	
    protected $_data = array();
    
    function __construct() {
        BitBase::setSite($this);
        $this -> onInit();
    }

    function __destroy() {

    }

    function __set($name, $value) {
        $this -> _data[$name] = $value;
    }

    public function __get($name) {
        if (isset($this -> _data[$name]))
            return $this -> _data[$name];
        else
            return null;
    }

    public function run() {
        $init = false;
        $this -> onRequest();
        $this -> doLoadPage();
        $this -> doRender();
        $this -> onDestroy();

        /*try{

         }
         throw($e)
         {

         }*/
    }

    abstract public function onInit();
    abstract public function doFatalError(Exception $e);
    abstract public function onRequest();
    abstract public function onDestroy();

    abstract public function doException(Exception $e);
    abstract public function doLoadPage();
    abstract public function doRender();

    /*Interface ArrayAccess*/

    /**
     * @param mixed the key
     * @return boolean whether the request contains an item with the specified key
     */
    public function contains($key) {
        return isset($this -> _data[$key]) || array_key_exists($key, $this -> _data);
    }

    /**
     * @return array the list of data in array
     */
    public function toArray() {
        return $this -> _data;
    }

    /**
     * Returns whether there is an element at the specified offset.
     * This method is required by the interface ArrayAccess.
     * @param mixed the offset to check on
     * @return boolean
     */
    public function offsetExists($offset) {
        return $this -> contains($offset);
    }

    /**
     * Returns the element at the specified offset.
     * This method is required by the interface ArrayAccess.
     * @param integer the offset to retrieve element.
     * @return mixed the element at the offset, null if no element is found at the offset
     */
    public function offsetGet($offset) {
        return $this -> _data[$offset];
    }

    /**
     * Sets the element at the specified offset.
     * This method is required by the interface ArrayAccess.
     * @param integer the offset to set element
     * @param mixed the element value
     */
    public function offsetSet($offset, $item) {
        $this -> _data[$offset] = $item;
    }

    /**
     * Unsets the element at the specified offset.
     * This method is required by the interface ArrayAccess.
     * @param mixed the offset to unset element
     */
    public function offsetUnset($offset) {
        unset($this -> _data[$offset]);
    }

    /*Interface IConfig*/

    protected static $_config = array();

    public static function setConfig($key, $value) {
        self::$_config[$key] = $value;

    }

    public static function getConfig($key) {
        if (isset(self::$_config[$key]))
            return self::$_config[$key];
        else
            return null;
    }

    public static $database = array();
    protected static $_database = array();

    public static function setDB($key, Array $value) {
        self::$_database[$key] = $value;

    }

}
?>
