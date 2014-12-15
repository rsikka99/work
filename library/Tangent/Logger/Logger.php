<?php

namespace Tangent\Logger;

class Logger
{
    const SOURCE_ZEND_LOG      = 1;
    const SOURCE_LOGIN_ATTEMPT = 2;
    const SOURCE_PROPOSAL      = 3;
    const SOURCE_EMAIL         = 4;
    const SOURCE_SECURITY      = 5;

    /**
     * The logger
     *
     * @var \Zend_Log
     */
    private static $logger;

    /**
     * Gets the unique id for this session of logs
     *
     * @var string
     */
    private static $_uniqueId;

    /**
     * Gets a unique id for the current page session.
     * This can be provided to a user so that we can look at the exact error they received
     *
     * @return string
     */
    static function getUniqueId ()
    {
        if (!isset(self::$_uniqueId))
        {
            self::$_uniqueId = uniqid();
        }

        return self::$_uniqueId;
    }

    /**
     * Logs an exception
     *
     * @param \Exception $e
     */
    static function logException (\Exception $e)
    {
        $stackTrace = $e->getTraceAsString();

        self::error($e);
        self::error($stackTrace);
    }

    /**
     * Logs a message
     *
     * @param string $message
     * @param null   $level
     * @param null   $source
     */
    static function log ($message, $level = null, $source = null)
    {
        $uid = self::getUniqueId();

        if (false !== ($logger = self::getLogger()))
        {
            if ($level === null)
            {
                $level = \Zend_Log::INFO;
            }

            if ($source === null)
            {
                $source = self::SOURCE_ZEND_LOG;
            }

            $logger->setEventItem('logTypeId', $source);
            $logger->log("{$uid}: {$message}", $level);
        }
    }

    /**
     * A shortcut to logging a debug message
     *
     * @param      $message
     * @param null $source
     */
    static function debug ($message, $source = null)
    {
        self::log($message, \Zend_Log::DEBUG, $source);
    }

    /**
     * A shortcut to logging an info message
     *
     * @param      $message
     * @param null $source
     */
    static function info ($message, $source = null)
    {
        self::log($message, \Zend_Log::INFO, $source);
    }

    /**
     * A shortcut to logging a notice message
     *
     * @param      $message
     * @param null $source
     */
    static function notice ($message, $source = null)
    {
        self::log($message, \Zend_Log::NOTICE, $source);
    }

    /**
     * A shortcut to logging a warn message
     *
     * @param      $message
     * @param null $source
     */
    static function warn ($message, $source = null)
    {
        self::log($message, \Zend_Log::WARN, $source);
    }

    /**
     * A shortcut to logging an error message
     *
     * @param      $message
     * @param null $source
     */
    static function error ($message, $source = null)
    {
        self::log($message, \Zend_Log::ERR, $source);
    }

    /**
     * A shortcut to logging a critical message
     *
     * @param      $message
     * @param null $source
     */
    static function crit ($message, $source = null)
    {
        self::log($message, \Zend_Log::CRIT, $source);
    }

    /**
     * A shortcut to logging an alert message
     *
     * @param      $message
     * @param null $source
     */
    static function alert ($message, $source = null)
    {
        self::log($message, \Zend_Log::ALERT, $source);
    }

    /**
     * A shortcut to logging an emergency message
     *
     * @param      $message
     * @param null $source
     */
    static function emerg ($message, $source = null)
    {
        self::log($message, \Zend_Log::EMERG, $source);
    }

    /**
     * Gets an instance of the Zend Logger
     *
     * @return \Zend_Log
     */
    private static function getLogger ()
    {
        if (!isset(self::$logger))
        {
            if (\Zend_Registry::isRegistered('Zend_Log'))
            {
                self::$logger = \Zend_Registry::get('Zend_Log');
                $auth         = \Zend_Auth::getInstance();
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
}