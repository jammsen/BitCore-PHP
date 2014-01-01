<?php
/**
 *
 * @author      Bitcoding <bitcoding@bitcoding.eu>
 * @copyright   Copyright &copy; 2009-2014, Bitcoding
 * @link        http://www.lessphp.eu/
 * @link        http://www.bitcoding.eu/
 * @license     http://www.bitcoding.eu/license/
 * 
 * @version     0.1.0 (Breadcrumb): BitHelper.php
 * @since       0.1.0
 * @package     System/Helper/BitHelper
 * @category    Helper
 */
class ByteHelper {
    /* Return true or false, depending on if the bit is set */
    static function iSet(&$bf, $n) {$n = pow(2, $n);
        return ($bf & $n);
    }

    /* Force a specific bit to ON */
    static function setOn(&$bf, $n) {
        $bf |= pow(2, $n);
    }

    /* Force a specific bit to be OFF */
    static function setOff(&$bf, $n) {
        $bf &= ~(pow(2, $n));
    }

    /* Toggle a bit, so bits that are on are turned off, and bits that are off are turned on. */
    static function Toogle(&$bf, $n) {
        $bf^= pow(2, $n);
    }

    static function SetByteValue(&$bf, $offset, $value) {

        if ($bf>>($offset * 8) != $value) {
            $bf &= ~(0xFF<<($offset * 8));
            $bf |= $value<<($offset * 8);
        }
    }

}