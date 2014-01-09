<?php
/**
 * Represents an Master Class
 * 
 * @author      Bitcoding <bitcoding@bitcoding.eu>
 * 
 * @link        http://www.lessphp.eu/
 * @link        http://www.bitcoding.eu/
 * @license     http://www.bitcoding.eu/license/
 * 
 * @version     0.1.0 (Breadcrumb): BModule.php
 * @package     Site/BModule
 * @category    Site
 */

abstract class BModule implements IModule {
    
    /**
     *
     * @var BSite|Site Site Controller 
     */
    protected $Site = null;
    
    /**
     * init The Modil.
     * @param BSite $site the Site instance
     * @throws InvalidException if this method is invoked twice or more.
     */
    function __construct() {
        $this->Site = Site::getSite();
        $this->_init();
    }
}