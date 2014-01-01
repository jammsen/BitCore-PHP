<?php
/**
 * Simple Sqlite Wrapper
 * @author      Bitcoding <bitcoding@bitcoding.eu>
 * @copyright   Copyright &copy; 2009-2014, Bitcoding
 * @link        http://www.lessphp.eu/
 * @link        http://www.bitcoding.eu/
 * @license     http://www.bitcoding.eu/license/
 * 
 * @version     0.1.0 (Breadcrumb): Agent.php
 * @since       0.1.0
 * @package     System/Database/Sqlite
 * @category    Database
 */
class BSqlite extends BDatabase {  
    /**
     * Construct Simple Sqlite Connection
     * 
     * @var string $file Sqlite file
     * @see parent::__construct()
     */
    function __construct($file) {    
        parent::__construct('sqlite:'.$file);
    }
}


?>