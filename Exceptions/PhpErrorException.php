<?php

/**
 *
 * @author      Bitcoding <bitcoding@bitcoding.eu>
 * @copyright   Copyright &copy; 2009-2014, Bitcoding
 * @link        http://www.lessphp.eu/
 * @link        http://www.bitcoding.eu/
 * @license     http://www.bitcoding.eu/license/
 * 
 * @version     0.1.0 (Breadcrumb): PhpErrorException.php
 * @since       0.1.0
 * @package     System/Exception/PhpErrorException
 * @category    Exception
 */
class PhpErrorException extends BitException {

    /**
     * Constructor.
     * @param integer error number
     * @param string error string
     * @param string error file
     * @param integer error line number
     */
    public function __construct($errno, $errstr, $errfile, $errline) {
        
        static $errorTypes = array(
            E_ERROR => "Error",
            E_WARNING => "Warning",
            E_PARSE => "Parsing Error",
            E_NOTICE => "Notice",
            E_CORE_ERROR => "Core Error",
            E_CORE_WARNING => "Core Warning",
            E_COMPILE_ERROR => "Compile Error",
            E_COMPILE_WARNING => "Compile Warning",
            E_USER_ERROR => "User Error",
            E_USER_WARNING => "User Warning",
            E_USER_NOTICE => "User Notice",
            E_STRICT => "Runtime Notice"
        );
        $errorType = isset($errorTypes[$errno]) ? $errorTypes[$errno] : 'Unknown Error';
        parent::__construct("[$errorType] $errstr (@line $errline in file $errfile).");
    }

}

?>