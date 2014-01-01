<?php

/**
 *
 * @author      Bitcoding <bitcoding@bitcoding.eu>
 * @copyright   Copyright &copy; 2009-2014, Bitcoding
 * @link        http://www.lessphp.eu/
 * @link        http://www.bitcoding.eu/
 * @license     http://www.bitcoding.eu/license/
 * 
 * @version     0.1.0 (Breadcrumb): SiteException.php
 * @since       0.1.0
 * @package     System/Exception/SiteException
 * @category    Exception
 */
class SiteException extends BitException {

    protected function getErrorMessageFile($dir = __DIR__) {
        return parent::getErrorMessageFile($dir);
    }

}