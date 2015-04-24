<?php

/**
 * Class My_Error_Handler
 */
class My_Error_Handler
{
    static $errorCounts = [
        "E_ERROR"      => 0,
        "E_WARNING"    => 0,
        "E_NOTICE"     => 0,
        "E_STRICT"     => 0,
        "E_PARSE"      => 0,
        "E_DEPRECATED" => 0,
        "E_OTHER"      => 0,
    ];

    static $errorNames  = [
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
        32767 => "E_ALL",
    ];
    static $errorColors = [
        1     => "danger",
        2     => "",
        4     => "danger",
        8     => "info",
        16    => "danger",
        32    => "",
        64    => "danger",
        128   => "",
        256   => "danger",
        512   => "",
        1024  => "info",
        2048  => "danger",
        4096  => "danger",
        8192  => "warning",
        16384 => "warning",
        32767 => "danger",
    ];
    static $errors      = [];

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
        $error->trace    = self::generateCallTrace();
        $error->number   = $errorNumber;
        self::$errors [] = $error;
    }

    /**
     * Set the error handler as the default error handler in php
     */
    public static function set ()
    {
        set_error_handler([__CLASS__, 'handle']);
    }

    /**
     * @return string
     */
    protected static function generateCallTrace ()
    {
        $e     = new Exception();
        $trace = explode("\n", $e->getTraceAsString());
        // reverse array to make steps line up chronologically
        $trace = array_reverse($trace);
        array_shift($trace); // remove {main}
        array_pop($trace); // remove call to this method
        array_pop($trace); // remove call to previous method
        $trace  = array_reverse($trace);
        $length = count($trace);
        $result = [];

        for ($i = 0; $i < $length; $i++)
        {
            $result[] = ($i + 1) . ')' . substr($trace[$i], strpos($trace[$i], ' ')); // replace '#someNum' with '$i)', set the right ordering
        }

        return "\t" . implode("\n\t", $result);
    }
}