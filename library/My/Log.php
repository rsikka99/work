<?php

/**
 * Class My_Log
 * A validator for DateTime strings.
 * Defaults to check the format of YYYY/MM/DD HH:SS and that the format will work with strtotime.
 *
 * @author "Lee Robert"
 */
class My_Log
{
    const SOURCE_ZENDLOG = 1;
    const SOURCE_LOGINATTEMPT = 2;
    const SOURCE_PROPOSAL = 3;
    const SOURCE_EMAIL = 4;
    const SOURCE_SECURITY = 5;
    
    /**
     *
     * @var Zend_Log
     */
    private static $logger;

    static function log ($message, $level = null, $source = null)
    {
        if (FALSE !== ($logger = self::getLogger()))
        {
            if ($level === null)
                $level = Zend_Log::INFO;
            
            if ($source === null)
                $source = self::SOURCE_ZENDLOG;
            
            $logger->setEventItem('logTypeId', $source);
            $logger->log($message, $level);
        }
    }

    static function info ($message, $source = null)
    {
        self::log($message, Zend_Log::INFO, $source);
    }

    /**
     *
     * @return Zend_Log
     */
    private static function getLogger ()
    {
        if (! isset(self::$logger))
        {
            if (Zend_Registry::isRegistered('Zend_Log'))
            {
                self::$logger = Zend_Registry::get('Zend_Log');
                $auth = Zend_Auth::getInstance();
                if ($auth->hasIdentity())
                {
                    self::$logger->setEventItem('userId', $auth->getIdentity()->id);
                }
            }
            else
            {
                return false;
            }
        }
        return self::$logger;
    }

    /**
     *
     * @return Zend_Log_Writer_Firebug
     */
    private static function getFirebugLogger ()
    {
        if (! isset(self::$firebugLogger))
        {
            if (Zend_Registry::isRegistered('Zend_Log_Writer_Firebug'))
            {
                self::$firebugLogger = Zend_Registry::get('Zend_Log_Writer_Firebug');
            }
            else
            {
                return false;
            }
        }
        return self::$firebugLogger;
    }

}