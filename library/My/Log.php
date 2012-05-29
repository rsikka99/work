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
    
    /**
     * Gets the unique id for this session of logs
     *
     * @var unknown_type
     */
    private static $_uniqueId;

    public static function getUniqueId ()
    {
        if (! isset(self::$_uniqueId))
        {
            self::$_uniqueId = uniqid();
        }
        return self::$_uniqueId;
    }

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

    static function debug ($message, $source = null)
    {
        self::log($message, Zend_Log::DEBUG, $source);
    }

    static function info ($message, $source = null)
    {
        self::log($message, Zend_Log::INFO, $source);
    }

    static function notice ($message, $source = null)
    {
        self::log($message, Zend_Log::NOTICE, $source);
    }

    static function warn ($message, $source = null)
    {
        self::log($message, Zend_Log::WARN, $source);
    }

    static function error ($message, $source = null)
    {
        self::log($message, Zend_Log::ERR, $source);
    }

    static function crit ($message, $source = null)
    {
        self::log($message, Zend_Log::CRIT, $source);
    }

    static function alert ($message, $source = null)
    {
        self::log($message, Zend_Log::ALERT, $source);
    }

    static function emerg ($message, $source = null)
    {
        self::log($message, Zend_Log::EMERG, $source);
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
                else
                {
                    self::$logger->setEventItem('userId', null);
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