<?php

// Define our environment
defined('BASE_PATH') || define('BASE_PATH', realpath(dirname(__FILE__) . '/../'));
defined('APPLICATION_PATH') || define('APPLICATION_PATH', BASE_PATH . '/application');

defined('DATA_PATH') || define('DATA_PATH', BASE_PATH . '/data');
defined('TEST_PATH') || define('TEST_PATH', BASE_PATH . '/tests');

// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'testing'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array (
        realpath(BASE_PATH . '/library'), 
        get_include_path() 
)));

error_reporting(E_ALL | E_STRICT);

//require_once 'Zend/Loader/Autoloader.php';
//Zend_Loader_Autoloader::getInstance();
require_once 'Zend/Application.php';
$application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
$application->bootstrap();
