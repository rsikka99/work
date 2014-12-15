<?php

/**
 * App_View_Helper_Version
 *
 * @author Lee Robert
 *
 */
class App_View_Helper_Version extends Zend_View_Helper_Abstract
{
    static $versionString;

    /**
     * @return string
     */
    public function Version ()
    {
        if (!isset(self::$versionString))
        {
            // Set version string
            $configPath = APPLICATION_PATH . '/configs/version.ini';
            $config     = false;
            if (file_exists($configPath))
            {
                try
                {
                    $config = new Zend_Config_Ini($configPath, 'production');
                }
                catch (Exception $e)
                {
                }
            }

            if ($config)
            {
                self::$versionString = "{$config->version} - {$config->versionType}";
            }
            else
            {
                self::$versionString = "Not Available";
            }
        }

        return self::$versionString;
    }
}
