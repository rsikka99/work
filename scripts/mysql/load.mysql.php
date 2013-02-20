<?php
include ('includes/functions.php');
/**
 * Script for creating and loading database
 */

// Initialize the application path and autoloading
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../application'));

set_include_path(implode(PATH_SEPARATOR, array (
        APPLICATION_PATH . '/../../library',
        get_include_path()
)));

defined('APPLICATION_BASE_PATH') || define('APPLICATION_BASE_PATH', realpath(dirname(__FILE__) . '/../..'));

// Define the paths
defined('APPLICATION_PATH') || define('APPLICATION_PATH', APPLICATION_BASE_PATH . '/application');
defined('DATA_PATH') || define('DATA_PATH', APPLICATION_BASE_PATH . '/data');
defined('PUBLIC_PATH') || define('PUBLIC_PATH', APPLICATION_BASE_PATH . '/public');
defined('ASSETS_PATH') || define('ASSETS_PATH', APPLICATION_BASE_PATH . '/assets');
defined('MYSQL_FILES_PATH') || define('MYSQL_FILES_PATH', APPLICATION_BASE_PATH . '/scripts/mysql');

/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(APPLICATION_BASE_PATH);

// Setup autoloading
require 'init_autoloader.php';

//
//ini_set("display_errors", 1);
//error_reporting(E_ALL);

// Define application environment.
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

// Create application, bootstrap, and run
$application = new Zend_Application(APPLICATION_ENV, array(
                                                          'config' => array(
                                                              APPLICATION_PATH . '/configs/global.ini',
                                                              APPLICATION_PATH . '/configs/application.ini',
                                                          )));


require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();

// Define some CLI options
$getopt = new Zend_Console_Getopt(array (
        'withdata|w' => 'Load database with sample data',
        'env|e-s' => 'Application environment for which to create database (defaults to development)',
        'help|h' => 'Help -- usage message'
));
try
{
    $getopt->parse();
}
catch ( Zend_Console_Getopt_Exception $e )
{
    // Bad options passed: report usage
    echo $e->getUsageMessage();
    return false;
}

// If help requested, report usage message
if ($getopt->getOption('h'))
{
    echo $getopt->getUsageMessage();
    return true;
}

// Initialize values based on presence or absence of CLI options
$withData = $getopt->getOption('w');
$env = $getopt->getOption('e');
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (null === $env) ? 'development' : $env);

// Initialize Zend_Application
$application = new Zend_Application(APPLICATION_ENV, array(
                                                          'config' => array(
                                                              APPLICATION_PATH . '/configs/global.ini',
                                                              APPLICATION_PATH . '/configs/application.ini',
                                                          )));

// Initialize and retrieve DB resource
$bootstrap = $application->getBootstrap();
$bootstrap->bootstrap('db');
/* @var $dbAdapter Zend_Db_Adapter_Mysqli */
$dbAdapter = $bootstrap->getResource('db');

// let the user know whats going on (we are actually creating a
// database here)


echo 'Database Script running. Timestamp: ' . date('r') . PHP_EOL;
echo 'This script will attempt to create the database';
if ($withData)
{
    echo ' and load data';
}
echo '.' . PHP_EOL;

// Check to see if we have a database file already
$options = $bootstrap->getOption('resources');

// this block executes the actual statements that were loaded from
// the schema file.
try
{
    $conn = $dbAdapter->getConnection();
    if ($conn instanceof mysqli)
    {
        $conn->query('DROP DATABASE IF EXISTS `' . $options ['db'] ['params'] ['dbname'] . '`;');
        $conn->query('CREATE DATABASE `' . $options ['db'] ['params'] ['dbname'] . '`;');
        $conn->select_db($options ['db'] ['params'] ['dbname']);

        echo "Loading Schema...";
        runSQLFile(MYSQL_FILES_PATH . '/schema.sql', $conn);
        runSQLFile(MYSQL_FILES_PATH . '/requiredData.base.sql', $conn);
        runSQLFile(MYSQL_FILES_PATH . '/requiredData.proposalgenerator.sql', $conn);
        runSQLFile(MYSQL_FILES_PATH . '/requiredData.quotegenerator.sql', $conn);

        if ('testing' != APPLICATION_ENV)
        {
            echo 'DONE!' . PHP_EOL;
        }

        if ($withData)
        {
            echo "Loading Data...";
            runSQLFile(MYSQL_FILES_PATH . '/data.proposalgenerator.sql', $conn);
            runSQLFile(MYSQL_FILES_PATH . '/data.quotegen.sql', $conn);

            if ('testing' != APPLICATION_ENV)
            {
                echo 'DONE!';
                echo PHP_EOL;
            }
        }
    }
    else
    {
        throw new Exception("Connection is not a mysqli connection!");
    }
}
catch ( Exception $e )
{
    echo 'AN ERROR HAS OCCURED:' . PHP_EOL;
    echo $e->getMessage() . PHP_EOL;
    return false;
}

// generally speaking, this script will be run from the command line
return true;
