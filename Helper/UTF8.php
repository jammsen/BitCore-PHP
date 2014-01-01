<?php

/**
 *
 * @author      Bitcoding <bitcoding@bitcoding.eu>
 * @copyright   Copyright &copy; 2009-2014, Bitcoding
 * @link        http://www.lessphp.eu/
 * @link        http://www.bitcoding.eu/
 * @license     http://www.bitcoding.eu/license/
 * 
 * @version     0.1.0 (Breadcrumb): UTF8.php
 * @since       0.1.0
 * @package     System/Helper/UTF8
 * @category    Helper
 */
class UTF8 {

    /**
     * @link http://php.net/manual/de/function.chr.php
     */
    static function HtmlChar($u) {
        return mb_convert_encoding('&#' . intval($u) . ';', 'UTF-8', 'HTML-ENTITIES');
    }

}

?>