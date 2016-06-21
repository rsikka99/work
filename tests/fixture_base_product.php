<?php

#define('UNIT_TESTING',true);

$_GET['table'] = 'base_product';

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

$path=dirname(__FILE__).($fn='/fixtures/'.$_GET['table'].'.yml');
$result=[];

$adapter = Zend_Db_Table::getDefaultAdapter();

$rows = $adapter->query("select * from base_product where base_type='printer' limit 100")->fetchAll();
foreach ($rows as $i=>$row) {
    $line=[];
    foreach ($row as $col => $value) {
        $line[$col] = $value;
    }
    $result[]=$line;
}

$rows = $adapter->query("select * from base_product where base_type='printer_cartridge' limit 100")->fetchAll();
foreach ($rows as $i=>$row) {
    $line=[];
    foreach ($row as $col => $value) {
        $line[$col] = $value;
    }
    $result[]=$line;
}

file_put_contents($path, Symfony\Component\Yaml\Yaml::dump([$_GET['table']=>$result]));
echo 'done writing '.count($rows).' rows to '.$fn;




