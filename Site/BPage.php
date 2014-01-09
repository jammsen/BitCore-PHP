<?php

abstract class BPage extends Component implements IPage {

    private $_viewport = null;
    private $_lessmethod = null;
    protected $_wrapper = 'HTML';
    protected $_t = 'main';

    public function getWrapper() {
        return $this->_wrapper;
    }

    public function setWrapper($bo) {
        $this->_wrapper = $bo;
    }

    public function getWrapperTemplate() {
        return $this->_t;
    }

    public function setWrapperTemplate($bo) {
        $this->_t = $bo;
    }

    public function Init() {
        parent::Init();

        $this->_starttaghandler = array_merge($this->_starttaghandler, array('wrapper' => 'setWrapper', 'wtemplate' => 'setWrapperTemplate'));
        $this->_rendertaghandler = array_merge($this->_rendertaghandler, array('title' => 'appendTitle'));
        $this->_finishtaghandler = array_merge($this->_finishtaghandler, array('css' => 'addCss'));

        $p = &$this->_page;
        $r = &$this->_route;

        $func = isset($r["values"]['view']) ? static::RENDER . $r["values"]['view'] : static::RENDER . 'Index';
        
        try {
            $viewport = new ReflectionMethod(get_class($this), $func);
        } catch (Exception $r) {
            $viewport = null;
        }
        if (is_object($viewport))
            if ($viewport->isFinal() && $viewport->isProtected() && $viewport->getDocComment() !== false) {
                $this->_lessmethod = new LessPHP($this, $viewport);
                $this->_viewport = $func;
            } else {
                throw new SecurityException('View Function');
                $this->_viewport = null;
            }
    }

    /**
     * @return string page title.
     */
    public function getTitle() {
        throw new ToDoException("");
    }

    /**
     * Clear Title and Set new One Be CareFull
     * @param string the new Title
     * @return void
     */
    public function setTitle($title) {
        pq('title', $this->_page)->html($title);
    }

    /**
     * Append Title at the End
     * @param string $title the new Title
     * @param string $key Delemiter default  »
     * @return void
     */
    public function appendTitle($title, $key = ' » ') {
        pq('title', $this->_page)->append($key . $title);
    }

    /**
     * Returns the head Reference
     * Nice to use to Manipulate the Meta Tags
     * @return phpQueryObject(HEAD)
     */
    public function Head() {
        return pq('head', $this->_page);
    }

    /**
     * Append Css or CssLink at the End of Header
     * @param string $css simpel css or
     * @return void
     */
    public function addCss($css) {
        if (strpos($css, '{') === false)
            $this->Head()->append('<link rel="stylesheet" href="' . $css . '" />');
        else
            $this->Head()->append('<style>' . $css . '</style>');
    }

    /* Standart Page Rendering */

    public function Render() {
        $this->_less->run(LessPHP::RENDER);
        if (!is_null($this->_viewport)) {
            $func = $this->_viewport;
            if ($this->_lessmethod instanceof LessPHP) {
                $this->_lessmethod->run(LessPHP::RENDER);
                if ($this->_type != 'void')
                    $this->_renderReturn = $this->$func();
                else
                    $this->$func();

                $this->_lessmethod->run(LessPHP::FINISH);
            }
            $this->_less->run(LessPHP::FINISH);
            $this->Finish();
        } else {
            $this->ViewError();
        }
    }

    
    
    function ViewError() {
        throw new ToDoException("Make a ViewError or a ViewIndex Handler You Idiot. Maybe Check your route");
    }

}