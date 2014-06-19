<?php

/**
 * Class My_Error_Handler
 */
class My_Error_Handler
{
    static $errorCounts = array(
        "E_ERROR"      => 0,
        "E_WARNING"    => 0,
        "E_NOTICE"     => 0,
        "E_STRICT"     => 0,
        "E_PARSE"      => 0,
        "E_DEPRECATED" => 0,
        "E_OTHER"      => 0,
    );

    static $errorNames = array(
        1     => "E_ERROR",
        2     => "E_WARNING",
        4     => "E_PARSE",
        8     => "E_NOTICE",
        16    => "E_CORE_ERROR",
        32    => "E_CORE_WARNING",
        64    => "E_COMPILE_ERROR",
        128   => "E_COMPILE_WARNING",
        256   => "E_USER_ERROR",
        512   => "E_USER_WARNING",
        1024  => "E_USER_NOTICE",
        2048  => "E_STRICT",
        4096  => "E_RECOVERABLE_ERROR",
        8192  => "E_DEPRECATED",
        16384 => "E_USER_DEPRECATED",
        32767 => "E_ALL"
    );
    static $errorColors = array(
        1     => "alert-danger",
        2     => "",
        4     => "alert-danger",
        8     => "alert-info",
        16    => "alert-danger",
        32    => "",
        64    => "alert-danger",
        128   => "",
        256   => "alert-danger",
        512   => "",
        1024  => "alert-info",
        2048  => "alert-danger",
        4096  => "alert-danger",
        8192  => "alert-warning",
        16384 => "alert-warning",
        32767 => "alert-danger"
    );
    static $errors = array();

    /**
     * Handles php errors
     *
     * @param int    $errorNumber
     * @param string $errorString
     * @param string $errorFile
     * @param int    $errorLineNumber
     */
    public static function handle ($errorNumber, $errorString, $errorFile, $errorLineNumber)
    {
        switch ($errorNumber)
        {
            case E_STRICT:
                self::$errorCounts["E_STRICT"]++;
                break;
            case E_ERROR:
            case E_USER_ERROR:
                self::$errorCounts["E_ERROR"]++;
                break;
            case E_WARNING:
            case E_USER_WARNING:
                self::$errorCounts["E_WARNING"]++;
                break;
            case E_NOTICE:
            case E_USER_NOTICE:
                self::$errorCounts["E_NOTICE"]++;
                break;
            case E_PARSE:
                self::$errorCounts["E_PARSE"]++;
                break;
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                self::$errorCounts["E_DEPRECATED"]++;
                break;
            default:
                self::$errorCounts["E_OTHER"]++;
                break;
        }

        if (!error_reporting() || $errorNumber > error_reporting())
        {
            return;
        }

        $errorName = (array_key_exists($errorNumber, self::$errorNames)) ? self::$errorNames [$errorNumber] : '';
        $fileName  = basename($errorFile);

        $shortPath = sprintf('<br>"%s"', str_replace(APPLICATION_BASE_PATH, '', $errorFile));

        // Create an error object
        $error           = new stdClass();
        $error->message  = "{$errorName} : {$errorString} in {$fileName} on line {$errorLineNumber}${shortPath}";
        $error->color    = (array_key_exists($errorNumber, self::$errorColors)) ? self::$errorColors [$errorNumber] : '';
        $error->number   = $errorNumber;
        self::$errors [] = $error;
    }

    /**
     * Set the error handler as the default error handler in php
     */
    public static function set ()
    {
        set_error_handler(array(__CLASS__, 'handle'));
    }
}