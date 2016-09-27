<?php

#--
parse_str(implode('&', array_slice($argv, 1)), $_GET);
if (empty($_GET['uri'])) die('uri?');
if (empty($_GET['user'])) die('user?');
if (empty($_GET['pass'])) die('pass?');
unset($_GET['action']);
#--

ini_set("auto_detect_line_endings", true);

defined('APPLICATION_BASE_PATH') || define('APPLICATION_BASE_PATH', realpath(dirname(__FILE__)));

// Define the paths
defined('APPLICATION_PATH') || define('APPLICATION_PATH', APPLICATION_BASE_PATH . '/application');
defined('DATA_PATH') || define('DATA_PATH', APPLICATION_BASE_PATH . '/data');
defined('PUBLIC_PATH') || define('PUBLIC_PATH', APPLICATION_BASE_PATH . '/public');
defined('ASSETS_PATH') || define('ASSETS_PATH', APPLICATION_BASE_PATH . '/assets');

/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(APPLICATION_BASE_PATH);

// Setup autoloading
require 'init_autoloader.php';
My_Error_Handler::set();
//
//ini_set("display_errors", 1);
//error_reporting(E_ALL);

// Define application environment.
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (isset($_SERVER['php_environment_mode']) ? $_SERVER['php_environment_mode'] : 'production'));

// Create application, bootstrap, and run
$application = new Zend_Application('production', array(
    'config' => array(
        APPLICATION_PATH . '/configs/global.php',
        APPLICATION_PATH . '/configs/local.php',
    )));
$application->bootstrap();

//##########################################################################################################

$auth   = Zend_Auth::getInstance();
$user = json_decode(json_encode(['id'=>1, 'eulaAccepted'=>true, 'firstname'=>'unit', 'lastname'=>'testing', 'dealerId'=>1, 'resetPasswordOnNextLogin'=>false, 'email'=>'it@tangentmtw.com']));
$auth->getStorage()->write($user);
$mpsSession = new Zend_Session_Namespace('mps-tools');
$mpsSession->selectedClientId = 1;
$mpsSession->selectedRmsUploadId=1;

try {

    $fmaudit = new \MPSToolbox\Api\FMAudit($_GET['uri']);
    if (!$fmaudit->login($_GET['user'], $_GET['pass'])) die('Login failed');

    $html = $fmaudit->get('/AccountSelector/ListAccountsDetailedAsList?startsWith=Z');
    die($html->getBody()->getContents());

} catch (Exception $ex) {
    var_dump($ex);
}
