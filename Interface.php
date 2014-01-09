<?php

/**
 * HEADER
 * */
interface IVar {

    public static function isBase64($var);

    public static function isInt($var, $min = null, $max = null, $options = array());

    public static function isBool($var, $options = array());

    public static function isFloat($var, $sep = null, $options = array());

    public static function isURL($var, $flags = null);

    public static function isIP($var, $flags = null);

    public static function isEmail($var);

    public static function getInt($var);

    public static function getFloat($var, $flags = array());

    public static function getURL($var);

    public static function getEmail($var);

    public static function getBlank($var, $filter, $option = null);

    //public static function    getSafeString($key,$flags = null);
    public static function getString($key, $flags = null);

    public static function valCallback($var, $callback);

    public static function valRegEx($var, $regex, $options = array());
}

interface IDebuggable {

    public static function isDebuggable();

    public static function setDebuggable($bool);
}

interface IInput extends IVar {

    public static function iSet($key);

    public static function getRaw($key);
}

interface IConfig {

    public static function setConfig($key, $value);

    public static function getConfig($key);
}

interface IConfigurabel {

    public function setConfig($key, $value);

    public function getConfig($key);
}

interface IDatabaseSHolder {

    public static function setDB($key, Array $value);

    public static function getDB($key);
}

interface IDatabaseHolder {

    public function setDB($key, Array $value);

    public function getDB($key);
}

interface ICompomnent {

    public function getPage();

    public function getRoute();

    public static function getSite();
}

interface IPage {
    
}

interface IModule extends IConfig {
    function _init();
}


interface IClass extends IConfigurabel {
    
}

interface IDatabase {
    /* public function query($resource);		
      //public function __construct($host,$user, $pass, $db);
      public function __destruct();

      public function isConnected();
      public function getClient();
      /*public function getLastError();
      public function getInsertID();
      public function getSqlEscape($string);
      public function getVersion();
      public function getServerVersion();
      public function getDatabaseType();
      public function getIterator(); */
}

interface ISite extends IConfig, ICompomnent, IDatabaseSHolder {
    
}

interface IException {
    /* Protected methods inherited from Exception class */

    public function getMessage();                 // Exception message

    public function getCode();                    // User-defined Exception code

    public function getFile();                    // Source filename

    public function getLine();                    // Source line

    public function getTrace();                   // An array of the backtrace()

    public function getTraceAsString();           // Formated string of trace

    /* Overrideable methods inherited from Exception class */

    public function __toString();                 // formated string for display
}

interface IAuthManager
{
	public function getGuestName();
	public function getUser($username=null);
	public function getUserFromCookie($cookie);
	public function saveUserToCookie($cookie);
	public function validateUser($username,$password);
}

interface IUserManager
{
	public function getGuestName();
	public function getUser($username=null);
	public function getUserFromCookie($cookie);
	public function saveUserToCookie($cookie);
	public function validateUser($username,$password);
}

interface IUser
{
	public function getName();
	public function setName($value);
	public function getIsGuest();
	public function setIsGuest($value);
	public function getRoles();
	public function setRoles($value);
	public function isInRole($role);
	public function saveToString();
	public function loadFromString($string);
}