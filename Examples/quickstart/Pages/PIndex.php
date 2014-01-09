<?php
/**
 * @Title HelloWorld
 */
final class PIndex extends Page{
    /**
     * @Title Index
     */
    final protected function ViewIndex(){
        $js = Bit::$jQuery;
        $js('body')->html('Hello World');
        $js('body')->append('<br>');
        $js('body')->append(Vars::get_getInt('test'));
        $js('body')->append('<br>');
        $js('body')->append(Vars::getInt('sdf45345sdf'));
    }
}
