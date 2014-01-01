<?php

/**
 *
 * @author      Bitcoding <bitcoding@bitcoding.eu>
 * @copyright   Copyright &copy; 2009-2014, Bitcoding
 * @link        http://www.lessphp.eu/
 * @link        http://www.bitcoding.eu/
 * @license     http://www.bitcoding.eu/license/
 * 
 * @version     0.1.0 (Breadcrumb): PrintNiceException.php
 * @since       0.1.0
 * @package     System/Exception/PrintNiceException
 * @category    Exception
 */
class PrintNiceException extends BitException {

    private $_var = NULL;
    private $_fontsize = NULL;

    public function __construct(&$var, $fontsize = 11) {
        $this->_var = $var;
        $this->_fontsize = $fontsize;
        parent::__construct('temp');
    }

    public function getErrorMessage() {
        return '<pre style="font-size: ' . $this->_fontsize . 'px; line-height: ' . $this->_fontsize . 'px;">' . Bit::_debug($this->_var) . '</pre>';
    }

}
