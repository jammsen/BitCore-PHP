<?php

/**
 *
 * @author      Bitcoding <bitcoding@bitcoding.eu>
 * 
 * @link        http://www.lessphp.eu/
 * @link        http://www.bitcoding.eu/
 * @license     http://www.bitcoding.eu/license/
 * 
 * @version     0.1.0 (Breadcrumb): index.php
 */

//Debug the Crumb
error_reporting(E_ALL);

define('ROOT', dirname(__FILE__));
$frameworkPath = ROOT.'/../../Bit.php';

if (!is_file($frameworkPath))
    die("Unable to find Bit framework path $frameworkPath.");

require_once $frameworkPath;
require_once ROOT.DS."/Init.php";

/**
 *Small Routes
 */
Map::attachRoutes('/', array(
    'name_prefix' => 'm_',
    // the routes to attach
    'routes' => array(
        'overview' => array('path' => '/', 'namespace' => 'PIndex'),
        'e404' => array('path' => '/404', 'namespace' => 'PIndex')
)));

if (!Bit::isSite())
    throw new BitException('site_unavailable');

$site->run();