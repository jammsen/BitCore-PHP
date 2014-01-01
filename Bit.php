<?php

/**
 * BitBase class file.
 * 
 * This is the file that establishes the BitCore
 * and error handling mechanism.
 * 
 * @author      Bitcoding <bitcoding@bitcoding.eu>
 * @copyright   Copyright &copy; 2009-2014, Bitcoding
 * @link        http://www.lessphp.eu/
 * @link        http://www.bitcoding.eu/
 * @license     http://www.bitcoding.eu/license/
 * 
 * @version     0.1.0 (Breadcrumb): BitBase.php
 * @package     System
 */
require_once (__DIR__ . '/BitBase.php');

class Bit extends BitBase {
}

spl_autoload_register(array('Bit', 'autoload'));
Bit::init();