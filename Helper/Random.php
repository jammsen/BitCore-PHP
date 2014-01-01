<?php

/**
 *
 * @author      Bitcoding <bitcoding@bitcoding.eu>
 * @copyright   Copyright &copy; 2009-2014, Bitcoding
 * @link        http://www.lessphp.eu/
 * @link        http://www.bitcoding.eu/
 * @license     http://www.bitcoding.eu/license/
 * 
 * @version     0.1.0 (Breadcrumb): Random.php
 * @since       0.1.0
 * @package     System/Helper/Random
 * @category    Helper
 */
class Random {

    CONST UPPER = 0x0001;
    CONST LOWER = 0x0002;
    CONST NUMERIC = 0x0004;
    CONST SPECIAL = 0x0008;
    CONST ALL = 0x000F;
    CONST TMP_UPPER = 'WERTZUPLKJHGFDSAYXCVBNM';
    CONST TMP_LOWER = 'qwertzupasdfghkyxcvbnm';
    CONST TMP_NUMERIC = '1234567890';

    static function String($l = 12, $mode = self::ALL) {
        $key = '';
        $pool = '';
        if ($mode & self::LOWER)
            $pool .= self::TMP_LOWER;
        if ($mode & self::UPPER)
            $pool .= self::TMP_UPPER;
        if ($mode & self::NUMERIC)
            $pool .= self::TMP_NUMERIC;

        srand((double) microtime() * 1000000);
        for ($index = 0; $index < $l; $index++)
            $key .= substr($pool, (rand() % (strlen($pool))), 1);
        return $key;
    }

}
