<?php
if (file_exists('vendor/autoload.php'))
{
    $loader = include 'vendor/autoload.php';
}

// We only really need to do this if composer hasn't setup an autoloader
if (!$loader)
{
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
    elseif (is_dir(APPLICATION_BASE_PATH . '/vendor/webino/zf1/src'))
    {
        $zendFrameworkPath = APPLICATION_BASE_PATH . '/vendor/webino/zf1/src';
    }

    if ($zendFrameworkPath)
    {
        if (isset($loader))
        {
            $loader->add('Zend', $zendFrameworkPath);
        }
        else
        {
            include ($zendFrameworkPath . '/Zend/Loader/Autoloader.php');
            $autoLoader = Zend_Loader_Autoloader::getInstance();
        }
    }
    else
    {
        include ('Zend/Loader/Autoloader.php');
        $autoLoader = Zend_Loader_Autoloader::getInstance();
    }
}

// Make sure our class exists
if (!class_exists('Zend_Application'))
{
    throw new RuntimeException('Unable to load Zend Framework. Run `php composer.phar install` or define a ZF_PATH environment variable.');
}