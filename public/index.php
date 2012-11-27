<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));


/** Zend_Application */
require_once 'Zend/Application.php';
require_once 'ImportExport.php';
require_once 'Utils.php';


// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

Zend_Registry::set("config", new Zend_Config_Ini(APPLICATION_PATH . "/configs/config.ini", APPLICATION_ENV));

//configuro il logger
$writer = new Zend_Log_Writer_Stream(APPLICATION_PATH . "/../logs/" . date("Ymd") . ".log");
Zend_Registry::set("log", new Zend_Log($writer));

$application->bootstrap()
            ->run();

require_once 'DbupdaterController.php';
DbupdaterController::UpdateDBToCurrentVersion();
