<?php

class My_Error_Handler
{
    static $errorNames = array (
            1 => "E_ERROR", 
            2 => "E_WARNING", 
            4 => "E_PARSE", 
            8 => "E_NOTICE", 
            16 => "E_CORE_ERROR", 
            32 => "E_CORE_WARNING", 
            64 => "E_COMPILE_ERROR", 
            128 => "E_COMPILE_WARNING", 
            256 => "E_USER_ERROR", 
            512 => "E_USER_WARNING", 
            1024 => "E_USER_NOTICE", 
            2048 => "E_STRICT", 
            4096 => "E_RECOVERABLE_ERROR", 
            6143 => "E_ALL" 
    );
    static $errorColors = array (
            1 => "alert-danger", 
            2 => "", 
            4 => "alert-danger", 
            8 => "alert-info", 
            16 => "alert-danger", 
            32 => "", 
            64 => "alert-danger", 
            128 => "", 
            256 => "alert-danger", 
            512 => "", 
            1024 => "alert-info", 
            2048 => "alert-danger", 
            4096 => "alert-danger", 
            6143 => "alert-danger" 
    );
    static $errors = array ();

    public static function handle ($errno, $errstr, $errfile, $errline)
    {
        if (! error_reporting() || $errno > error_reporting())
            return;
        
        $errorName = self::$errorNames [$errno];
        $fileName = basename($errfile);
        
        // Create an error object
        $error = new stdClass();
        $error->message = "{$errorName} : {$errstr} in {$fileName} on line {$errline}";
        $error->color = self::$errorColors [$errno];
        $error->number = $errno;
        self::$errors [] = $error;
    }

    public static function set ()
    {
        set_error_handler(array (
                __CLASS__, 
                'handle' 
        ));
    }
}
?>