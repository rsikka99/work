<?php

#define('UNIT_TESTING',true);

parse_str(implode('&', array_slice($argv, 1)), $_GET);
if (!isset($_GET['table'])) die('table not defined');

#--
defined('APPLICATION_BASE_PATH') || define('APPLICATION_BASE_PATH', realpath(dirname(__FILE__) . '/..'));

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

//
ini_set("display_errors", 1);
error_reporting(E_ALL);

// Define application environment.
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Create application, bootstrap, and run
$application = new Zend_Application('production', array(
    'config' => array(
        APPLICATION_PATH . '/configs/global.php',
        APPLICATION_PATH . '/configs/local.php',
    )));
$application->bootstrap();

$adapter = Zend_Db_Table::getDefaultAdapter();
$select = $adapter->select()->from($_GET['table']);
$rows = $adapter->fetchAll($select);

$path=dirname(__FILE__).($fn='/fixtures/'.$_GET['table'].'.yml');
$result=[];
foreach ($rows as $i=>$row) {
    $line=[];
    foreach ($row as $col => $value) {
        $line[$col] = $value;
    }
    $result[]=$line;
    if ($i>=100) break;
}
file_put_contents($path, Symfony\Component\Yaml\Yaml::dump([$_GET['table']=>$result]));
echo 'done writing '.count($rows).' rows to '.$fn;




