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
$application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');

// Initialize and retrieve DB resource
$bootstrap = $application->getBootstrap();
$bootstrap->bootstrap('db');
/* @var $dbAdapter Zend_Db_Adapter_Mysqli */
$dbAdapter = $bootstrap->getResource('db');

// let the user know whats going on (we are actually creating a
// database here)

echo '<pre>';
echo 'Database Script running. Timestamp:' . date('Y-m-d H:i:s') . PHP_EOL;

// if ('testing' != APPLICATION_ENV)
// {
//     echo 'Writing Database in (control-c to cancel): ' . PHP_EOL;
//     for($x = 5; $x > 0; $x --)
//     {
//         echo $x . "\r";
//         sleep(1);
//     }
// }


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
        
        runSQLFile(dirname(__FILE__) . '/schema.base.sql', $conn);
        runSQLFile(dirname(__FILE__) . '/schema.proposalgenerator.sql', $conn);
        
        if ('testing' != APPLICATION_ENV)
        {
            echo PHP_EOL;
            echo 'Database Created';
            echo PHP_EOL;
        }
        if ($withData)
        {
            
            runSQLFile(dirname(__FILE__) . '/data.base.sql', $conn);
            runSQLFile(dirname(__FILE__) . '/data.proposalgenerator.sql', $conn);
            
            if ('testing' != APPLICATION_ENV)
            {
                echo 'Data Loaded.';
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
