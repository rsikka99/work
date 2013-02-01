<?php
if (file_exists('vendor/autoload.php'))
{
    $loader = include 'vendor/autoload.php';
}

$zendFrameworkPath = false;

if (getenv('ZF_PATH'))
{
    // Support for ZF_PATH environment variable or git submodule
    $zendFrameworkPath = getenv('ZF_PATH');
}
elseif (get_cfg_var('zf_path'))
{
    // Support for zf2_path directive value
    $zendFrameworkPath = get_cfg_var('zf_path');
}
elseif (is_dir('vendor/zendframework/zendframework1/library'))
{
    $zendFrameworkPath = 'vendor/zendframework/zendframework1/library';
}

if ($zendFrameworkPath)
{
    if (isset($loader))
    {
        $loader->add('Zend', $zendFrameworkPath);
    }
    else
    {
        include $zendFrameworkPath . '/Zend/Loader/Autoloader.php';
        $autoloader = Zend_Loader_Autoloader::getInstance();
    }
}
else
{
    include ('Zend/Loader/Autoloader.php');
    $autoloader = Zend_Loader_Autoloader::getInstance();
}

if (!class_exists('Zend_Application'))
{
//    throw new RuntimeException('Unable to load Zend Framework. Run `php composer.phar install` or define a ZF_PATH environment variable.');
}