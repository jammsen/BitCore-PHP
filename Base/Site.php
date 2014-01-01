<?php

Bit::using("Bit.Site.*");
Bit::using('Bit.Database.*');

class Site extends BSite{
    public  $_start = NULL;
    private $_route = NULL;
    private $_page  = NULL;
    private $_cache = NULL;
    private $_speak = NULL;
    
    public static $Config = array("tools" => array("ex_trans" => 1), "expansion" => 3, "max_accounts" => 2, "charid" => "", "ip-pin" => 0000, "agb_version" => 0, "recent_post" => array("opt" => 'none', "val" => 0), "site_tag" => "newbe");

    function writeConfig($config) {
        $this->Config = Matrix::MergeRecursiveDistinct(self::$Config, $config);
    }

    private $_states = NULL;

    function onInit() {
        $protocol = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on') ? 'https://' : 'http://';
        $host = $_SERVER["HTTP_HOST"];
        $key = ($_SERVER['SERVER_SOFTWARE'] === 'nginx') ? 'REQUEST_URI' : 'REDIRECT_URL';
        $uri = explode('?', isset($_SERVER[$key]) ? $_SERVER[$key] : '/');
        $uri = array_shift($uri);
        
        define('SITE_URI'     , $uri);
        define('SITE_URI_HOST', $protocol.$host);
        define('SITE_URI_FULL', SITE_URI_HOST.$uri);
    }
    /**
     * onDestroy()
     * called by PageShutdown
     *
     * print BPage
     *
     */
    function onDestroy() {

    }

    function onRequest() {
        
    }

    /**
     * doLoadPage()
     * called by $this->run();
     *
     * Found Route From Redirect
     *
     * XXX subPath
     */
    function doLoadPage() {
        $this->_route = Map::matchRoute(SITE_URI, $_SERVER, 'm_e404');

        if (!$this->_route)
            throw new RouteNotFound('');
    }

    /**
     * doRender()
     */
    function doRender() {
        Bit::using("Pages." . $this->_route['namespace'], false);

        $_class_name = Bit::getClassOfNamespace($this->_route['namespace'], false);
        if ($_class_name) {
            $page_class = new $_class_name($this);
            $wrapper = $page_class->getWrapper();
            $_class_name = Bit::getClassOfNamespace($wrapper, false);
            Bit::using("Wrapper." . $wrapper, false);
            
            $wrapper_class = new $_class_name($this);
            $render = $wrapper_class->Render($page_class);
            echo $render;
        }
    }

    function doException(Exception $ex) {
        if ($ex instanceof PageException)
            return;
        var_dump($ex);
        echo (isset($ex->xdebug_message)) ? $ex->xdebug_message : $ex;
        exit(1);
    }

    function doFatalError(Exception $e) {
        var_dump($e);
        die("Fatal_Error");
    }

    public function getPage() {
        return $this->_page;
    }

    public function getRoute() {
        return $this->_route;
    }

    public function getRoot() {
        return $this;
    }
    /**
     * @return Self 
     */
    public function getSite(){
        return $this;
    }
    /**
     * @return PDO
     */
    public static function getDB($key) {
        if (isset(self::$_database[$key])) {
            if (!isset(self::$database[$key]) || !(self::$database[$key] instanceof BDatabase)) {
                $con = &self::$_database[$key];

                if (isset($con['file']))
                    return self::$database[$key] = new BSqlite($con['file']);
                elseif (isset($con['host'])) {
                    return self::$database[$key] = new BMysql($con['host'], $con['user'], $con['pass'], $con['db']);
                }
                else
                    throw new DatabaseException("unknow_connectiontyp", $key);
            }
            else
                return self::$database[$key];
        }
        else {
            throw new DatabaseException("get_connection", $key);
        }
    }
}