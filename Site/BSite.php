<?php

/**
 * 
 * @author      Bitcoding <bitcoding@bitcoding.eu>
 * 
 * @link        http://www.lessphp.eu/
 * @link        http://www.bitcoding.eu/
 * @license     http://www.bitcoding.eu/license/
 * 
 * @version     0.1.0 (Breadcrumb): BSite.php
 * @package     Site/BSite
 * @category    Site
 */

Bit::using("Bit.Site.*");
Bit::using("Bit.Site.Router.*");

abstract class BSite implements ISite {

    protected static $_loggedIn = false;
    protected static $_user = NULL;
    protected static $_modules = array();

    public static function setModule($namespace) {
        //self::$_modules = 
    }

    public static function getModule($namespace) {
        
    }

    public static function isLoggedIn($server = null, $matches = null) {
        return self::$_loggedIn;
    }

    protected $_data = array();
    protected static $call = null;
    
    function __construct() {
        BitBase::setSite($this);
        self::$call = $this;
        $this->onInit();
    }

    function __destroy() {
        
    }

    public static function run() {
        $cls = get_called_class();
        $staticCall = !(isset($this)) ? new $cls() : $this;
        
        $staticCall->onRequest();
        $staticCall->doLoadPage();
        $staticCall->doRender();
        $staticCall->onDestroy();
    }

    abstract public function onInit();
    abstract public function onRequest();
    abstract public function onDestroy();
    abstract public function doLoadPage();
    abstract public function doRender();
    
    function doException(Exception $ex) {
        if (!($ex instanceof BException))
            return;
        
        Bit::using('Bit.Exceptions.DocBlock.DocBlock');
        
        $js = Bit::$jQuery;
        $doc = $ex->getTemplate();
        
        $content = $js('div#content'           ,$doc);
        $js('<h1 class="border-left: 1px #fff dotted;">' . get_class($ex) . '</h1>')->appendTo($js('div.page-header-content',$doc));
        
        $trace = $ex->getTrace();
        if (isset($trace[0]['class']))
        {
            $reflection = new ReflectionMethod($trace[0]['class'], $trace[0]['function']);
            //$function =
            $content->append('<h5 class="border-left: 1px #fff dotted;"><b>Function : ' . $trace[0]['class'] . $trace[0]['type'] . $trace[0]['function'] . '()</h5>');
        }
        elseif (isset($trace[0]['function']))
        {
            $reflection = new ReflectionFunction($trace[0]['function']);
            $content->append('<h5 class="border-left: 1px #fff dotted;"><b>Function : ' . $trace[0]['function'] . '()</h5>');
        }
        $content->append('<h5 class="border-left: 1px #fff dotted;"><b>In File   : ' . $ex->getFile() . '@' . $ex->getLine() . '</h5>');
               
	if(isset($reflection) &&  $object = new DocBlock($reflection))
        {
            $content->append('<div class="docblock">'.$object.'</div>');
            
        }
        
        if ($ex instanceof PrintNiceException)
            $content->append($ex->getErrorMessage());
        else
            $content->append($ex->getErrorMessage() . '');

        if (isset($trace[0])) {
            $content->append('<h2 class="border-left: 1px #fff dotted;"><b>Backtrace: ' . '</h3>');
            $content->append('<ol id="trace"></ol>');
            $ol = $js("ol#trace");
            foreach ($trace as $value) {
                if (isset($value['line'])) {
                    if (isset($value['class']))
                        $ol->append('<li>' . $value['class'] . $value['type'] . $value['function'] . '<br> <ul><li>in ' . (isset($value['file']) ? $value['file'] : __FILE__) . '@' . $value['line'] . '</li></ul></li>');
                    else
                        $ol->append('<li>' . $value['function'] . '<br> <ul><li>in ' . (isset($value['file']) ? $value['file'] : __FILE__) . '@' . $value['line'] . '</li></ul></li>');
                }
            }
        }
        
        return $doc->htmlOuter();
    }

    /* Interface ArrayAccess */

    /**
     * @return Self 
     */
    public static function getSite(){
        return self::$call;
    }
    
    /* Interface IConfig */

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
