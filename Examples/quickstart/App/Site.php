<?php

Bit::using("Bit.Site.*");
Bit::using('Bit.Database.*');

class Site extends BSite{
    public  $_start = NULL;
    private $_route = NULL;
    private $_page  = NULL;
    private $_cache = NULL;
    private $_speak = NULL;
    private $_states = NULL;

    function onInit() {
        
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
     * XXX subPath
     */
    function doLoadPage() {
        $this->_route = ["namespace"=>Vars::get_getstring('page'),"values"=>['view'=>Vars::get_getstring('view')]];
    }

    /**
     * doRender()
     */
    function doRender() {
        Bit::using("Pages." . $this->_route['namespace'] , false);

        $_class_name = Bit::getClassOfNamespace(Vars::get_getstring('page'), false);
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

    public function getPage() {
        return $this->_page;
    }

    public function getRoute() {
        return $this->_route;
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