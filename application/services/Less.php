<?php

/**
 * Class Application_Service_Less
 */
class Application_Service_Less
{
    private static $initialized = false;

    /**
     * Handles initializing the LESS variables
     */
    public static function initialize ($reinitialize = false)
    {
        if (!self::$initialized || $reinitialize)
        {
            /**
             * Setup LESS variables
             */
            My_Less::setLessVariables(My_Brand::getColorVariablesAsArray());
            self::$initialized = true;
        }
    }

    /**
     * Gets the name of the current theme
     *
     * @return mixed
     */
    public static function getTheme ()
    {
        $config = Zend_Registry::get('config');

        return $config->app->theme;
    }

    /**
     * Compiles the styles for the site itself.
     *
     * @param bool $forceRecompile
     */
    public static function compileSiteStyles ($forceRecompile = false)
    {
        self::initialize($forceRecompile);
        My_Less::autoCompileLess(PUBLIC_PATH . '/less/site/bootstrap/bootstrap.less', PUBLIC_PATH . '/css/site/bootstrap.css', self::getTheme(), $forceRecompile);
        My_Less::autoCompileLess(PUBLIC_PATH . '/less/site/styles.less', PUBLIC_PATH . '/css/site/styles.css', self::getTheme(), $forceRecompile);
    }

    /**
     * Compiles theme specific styles
     *
     * @param bool $forceRecompile
     */
    public static function compileSiteThemeStyles ($forceRecompile = false)
    {
        self::initialize($forceRecompile);
        My_Less::autoCompileLess(PUBLIC_PATH . '/themes/' . self::getTheme() . '/less/site/styles.less', PUBLIC_PATH . '/themes/' . self::getTheme() . '/css/site/styles.css', self::getTheme(), true);
    }

    /**
     * Compiles the report styles
     *
     * @param bool $forceRecompile
     */
    public static function compileReportStyles ($forceRecompile = false)
    {
        if (Zend_Auth::getInstance()->hasIdentity())
        {
            $dealerId = Zend_Auth::getInstance()->getIdentity()->dealerId;
            self::initialize($forceRecompile);
            My_Less::autoCompileLess(PUBLIC_PATH . '/less/reports/reports.less', PUBLIC_PATH . "/css/reports/reports_dealer_$dealerId.css", self::getTheme(), $forceRecompile);
        }
    }
}