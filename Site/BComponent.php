<?php
/**
 * Represents an Master Class
 * 
 * @author      Bitcoding <bitcoding@bitcoding.eu>
 * 
 * @link        http://www.lessphp.eu/
 * @link        http://www.bitcoding.eu/
 * @license     http://www.bitcoding.eu/license/
 * 
 * @version     0.1.0 (Breadcrumb): BComponent.php
 * @package     Site/BComponent
 * @category    Site
 */
abstract class BComponent implements ICompomnent, BLessPHP, IDatabaseHolder {

    CONST AUTO = TRUE;
    CONST TYPE = 'void';
    CONST RENDER = 'View';
    CONST APPEND = '#content';
    CONST ATYPE  = 'append';
	
    protected $_root;
    public $_renderReturn;
    protected $_page;
    protected $_route;
    protected $_append;
    protected $_appendfunc;
    protected $_vars = array();
    protected static $_globalvars = array();
    protected $_less = null;
    protected $_init = array();
    protected $_templates = array();
    protected $_components = array();
    protected $_modules = array();
    
    
    /**
     * Standard constant for primary sql statement identifier
     */
    const STANDARD_PREPARE_STATEMENT = 'query';

    protected $_preparedStatements = array();

    //XXX: isinrole? other LessPHP tags
    protected $_starttaghandler = array('template' => 'LoadTemplate', 'auto' => 'setAuto', 'return' => 'setReturn', 'var' => 'setVar', 'header' => 'setHeader','prepare' => 'setPrepare');
    protected $_rendertaghandler = array('module' => 'LoadModule');
    protected $_finishtaghandler = array('component' => 'LoadComponent');

    public function setDB($key, Array $value) {
        throw new InvalidException('no_actic_function');
    }

    /**
     * @return PDO
     */
    public function getDB($key = null) {
        if (Bit::isSite())
            return Site::getDB($key);

        throw new SiteException('site_need_init');
    }

    /**
     * Sets a prepared statement
     * @param type $querykey Aliasname for query
     * @param type $databasekey Aliasname for database connection
     * @param type $statement Statement which should be executed
     * @return void
     */
    public function setPrepare($querykey = null, $databasekey = null, $statement = null) {
        $h = explode(" ", $querykey, 3);
        list($querykey, $databasekey, $statement) = $h;
        $this->_preparedStatements[$querykey] = array('0' => $databasekey, '1' => $statement);
    }

    /**
     * Aliasfunction for lazy programmers using executePreparedStatement
     * @see self::executePreparedStatement()
     * @param string $key Querykey
     * @param array $options Options
     * @return PDOStatement
     */
    public function ePS($key = null, $options = array()) {
        return $this->executePreparedStatement($key ? $key : static::STANDARD_PREPARE_STATEMENT, $options);
    }

    /**
     * Function for @prepare usage to execute a prepared statement
     * @param string $key Querykey
     * @param array $options Options
     * @return PDOStatement
     */
    public function executePreparedStatement($key = null, $options = array()) {
        $key = $key ? $key : static::STANDARD_PREPARE_STATEMENT;
        if (isset($this->_preparedStatements[$key])) {
            if (is_array($this->_preparedStatements[$key])) {
                list($databasekey, $statement) = $this->_preparedStatements[$key];
                $this->_preparedStatements[$key] = $this->getDB($databasekey)->prepare($statement);
                $this->_preparedStatements[$key]->setFetchMode(PDO::FETCH_OBJ);
            }
            $this->_preparedStatements[$key]->execute($options);
            return $this->_preparedStatements[$key];
        }
        return null;
    }
    /**
     *  implements BLessPHP
     */
    public function getTagHandlers() {
        return array($this->_starttaghandler, $this->_rendertaghandler, $this->_finishtaghandler);
    }

    /**
     * 
     * @return Self
     */
    public function setVar($key, $value = null) {
        if (strpos($key, '$') === 0)
            $key = substr($key, 1);
        
        if (strpos($key, ' ') !== FALSE) {
            list($key, $value) = explode(' ', $key, 2);
        }
        $this->_vars[$key] = LessPHP::GetArrayVar($value);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getVar($key, $ret = false) {
        $key = strpos($key, '$') === 0 ? substr($key, 1) : $key;
        return isset($this->_vars[$key]) ? $this->_vars[$key] : ($ret ? $key : null);
    }
    
    /**
     * 
     * @return Self
     */
    public function setGlobalVar($key, $value = null) {
        if (strpos($key, '$') === 0)
            $key = substr($key, 1);
        
        if (strpos($key, ' ') !== FALSE) {
            list($key, $value) = explode(' ', $key, 2);
        }
        static::$_globalvars[$key] = LessPHP::GetArrayVar($value);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGlobalVar($key, $ret = false) {
        $key = strpos($key, '$') === 0 ? substr($key, 1) : $key;
        return isset(static::$_globalvars[$key]) ? static::$_globalvars[$key] : ($ret ? $key : null);
    }
    
    /**
     * return from Rendered FUNC
     */
    public function getReturn() {
        return $this->_renderReturn;
    }

    /**
     * 
     */
    public function setPage(&$page) {
        $this->_page = $page;
    }

    /**
     * 
     */
    public function setHeader($header) {
        header($header);
    }

    /**
     * 
     */
    public function getPage() {
        return $this->_page;
    }

    /**
     * Return a LoadedPage
     * @see phpQuery::pq()
     * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery|NULL|Array
     */
    public function getTemplate($key = null, $nice = true) {
        return isset($this->_templates[$key]) ? ($nice ? $this->_templates[$key][2] : $this->_templates[$key]) : null;
    }

    /**
     * 
     */
    public function getComponent($key = null, $nice = true) {
        return isset($this->_components[$key]) ? ($nice ? $this->_components[$key][2] : $this->_components[$key]) : null;
    }

    /**
     * 
     */
    public function getRoute() {
        return $this->_route;
    }

    /**
     * 
     */
    public function getRoot() {
        return $this->_root;
    }

    /**
     * 
     */
    public function __construct(ICompomnent &$root, $init = array()) {
        $this->_root = $root;
        $this->_init = $init;
        $this->_vars = array_merge($this->_init,$this->_vars);
        $this->_append = isset($init['appendTo']) ? $init['appendTo'] : static::APPEND;
        $this->_appendfunc = isset($init['appendFunc']) ? $init['appendFunc'] : static::ATYPE;
        $this->_page = $root->getPage();
        $this->_route = $root->getRoute();
        $this->Init();
        $oRefl = new ReflectionClass($this);
        $this->_less = new LessPHP($this, $oRefl);
    }

    /**
     * 
     */
    public function Init() {
        $this->_auto = static::AUTO;
        $this->_type = static::TYPE;
        $this->_renderReturn = true;
    }

    /**
     * 
     */
    public $_auto = null;

    /**
     * 
     */
    public function setAuto($bo) {
        $this->_auto = $bo;
    }

    /**
     * 
     */
    public $_type = null;

    /**
     * @return Self
     */
    public function setReturn($bo) {
        $this->_type = strtolower($bo);
        return $this;
    }
    /**
     * 
     */
    public function getReturnType() {
        return $this->_type;
    }
    /**
     * 
     */
    public function using($namspace, $check = true) {
        Bit::using($namspace, $check);
    }

    /**
     * 
     */
    public static function _getComponent($namespace, $init = true) {
        Bit::using("App." . $namespace, false);
        $dir = Bit::getClassOfNamespace("App." . $namespace, false);
        //$lang = Bit::getPreferredLanguage();
        return $dir;
    }

    /**
     * @see phpQuery::pq()
     * @return phpQueryObject
     */
    public static function _getTemplate($namespace) {
        $dir = Bit::getPathOfNamespace("Themes.Html." . $namespace, 'html');
        $lang = Bit::getPreferredLanguage();

        if (!is_file($file = $dir['dirname'] . DS . $lang . DS . $dir['basename']))
            $file = $dir['dirname'] . DS . $dir['basename'];

        return isset($file) ? pq(phpQuery::newDocumentFile($file)) : NULL;
    }

    /**
     *  @return Self
     */
    public function LoadComponent($namespace) {
        $h = explode(" ", $namespace, 4);
        if (count($h) == 1)
            $h[] = $this->_append;
        if (count($h) == 2)
            $h[] = static::ATYPE;
        
        if (count($h) == 3) {
            
        }
        list($namespaces, $append, $func) = $h;
        $this->_components[$namespaces] = array('0' => $append, '1' => $func, '2' => $namespaces);
    
        return $this;
    }

    /**
     * @return Self
     */
    public function LoadTemplate($namespace) {
        $h = explode(" ", $namespace, 3);
        if (count($h) == 1)
            $h[] = $this->_append;
        if (count($h) == 2)
            $h[] = $this->_appendfunc;
        
        list($namespace, $append, $func) = $h;
        $t = $this->_getTemplate($namespace);
        if ($t) {
            $this->_templates[$namespace] = array('0' => $append, '1' => $func, '2' => $t);
        } else
            throw new ToDoException("Load Template");

        return $this;
    }

    /**
     * 
     */
    public function Render() {
        $func = static::RENDER . 'Index';
        $this->_less->run(LessPHP::RENDER);
        $this->$func();
        $this->_less->run(LessPHP::FINISH);
        //XXX:IMPLEMENT CACHE + ActionsFunctions

        $this->Finish();
    }

    /**
     * 
     */
    public function Finish() {
        if ($this->_auto && $this->_renderReturn) {
            foreach ($this->_templates as $key => $v)
                $this->beforeRenderTemplate($key);
            foreach ($this->_components as $key => $v)
                $this->beforeComponent($key);
        }
    }

    /*
     * 
     */
    public function beforeRenderTemplate($key = '') {
        list($append, $func, $t) = $this->getTemplate($key, false);
        pq($this->getVar($append, true), $this->_page)->$func($t);
    }

    /**
     * Standart Rendering
     */
    public function beforeComponent($key = '') {
        list($append, $func, $ts) = $this->getComponent($key, false);

        $_t = $this->_getComponent($ts);
        $init = isset($this->_vars[$ts]) ? $this->_vars[$ts] : array();
        if (!isset($init['appendTo']))
            $init['appendTo'] = $this->getVar($append, true);
        if (!isset($init['appendFunc']))
            $init['appendFunc'] = $this->getVar($func, true);
        $t = new $_t($this, $init);
        $t->Render();
    }
    
    public function getSite(){
        throw new ToDoException('Hmm');
    }
    
}
