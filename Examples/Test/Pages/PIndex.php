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
    }
}
