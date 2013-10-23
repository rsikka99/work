<?php
/**
 * Class Bootstrap
 * This class prepares the application with all the needed settings before anything is routed
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    /**
     * Initializes the Zend_Config object into the registry
     *
     * @return Zend_Config
     */
    protected function _initConfig ()
    {
        $config = new Zend_Config($this->getOptions());
        if (!Zend_Registry::isRegistered("config"))
        {
            Zend_Registry::set("config", $config);
        }

        return $config;
    }

    protected function _initStatsd ()
    {
        $options                    = $this->getOptions();
        Tangent_Statsd::$rootBucket = $options['statsd']['rootBucket'];
        Tangent_Statsd::$enabled    = $options['statsd']['enabled'];
        Tangent_Statsd::$host       = $options['statsd']['host'];
        Tangent_Statsd::$port       = $options['statsd']['port'];
        Tangent_Statsd::increment('mpstoolbox.pageloads', 1);
    }

    /**
     * Start session
     */
    public function _initCoreSession ()
    {
        $this->bootstrap('db');
        $this->bootstrap('session');
        Zend_Session::start();

        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity())
        {
            if ($auth->getIdentity()->id > 0)
            {
                Application_Service_Navigation::$userId = $auth->getIdentity()->id;
            }
        }
    }

    /**
     * Initializes php runtime settings
     */
    protected function _initPhpSettings ()
    {
        $options = $this->getOptions();
        date_default_timezone_set($options ["phpSettings"] ["timezone"]);

        // Turn on the display of errors
        if (APPLICATION_ENV != 'production')
        {
            @ini_set("display_errors", 1);
        }
    }

    /**
     * Registers the error handler with php
     */
    protected function _initMyErrorHandler ()
    {
        My_Error_Handler::set();
    }

    /**
     * Registers the logger with Zend_Registry.
     * Under no circumstances should this be renamed to _initLog as it will
     * override ini settings
     */
    protected function _initLoggerToRegistry ()
    {
        $this->bootstrap('Log');
        if ($this->hasResource('Log'))
        {
            Zend_Registry::set('Zend_Log', $this->getResource('Log'));
        }
    }

    /**
     * Initializes settings for the view
     */
    protected function _initViewSettings ()
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');

        // Set the doctype to HTML5 so components know how to render items
        $view->doctype('HTML5');

        // Initialize the twitter bootstrap menu plugin
        $view->registerHelper(new My_View_Helper_Navigation_Menu(), 'menu');

        // Setup default styles

        $view->headLink()->prependStylesheet($view->theme("/css/styles.css"));
        $view->headLink()->prependStylesheet($view->baseUrl("/css/styles.css"));
        $view->headLink()->prependStylesheet($view->theme("/jquery/ui/grid/ui.jqgrid.css"));
        $view->headLink()->prependStylesheet($view->theme("/jquery/ui/jquery-ui-1.10.3.custom.min.css"));
        $view->headLink()->prependStylesheet($view->baseUrl("/css/bootstrap.css"));

        // Add default scripts
        $view->headScript()->prependFile($view->baseUrl("/js/script.js"));
        $view->headScript()->prependFile($view->baseUrl("/js/plugins.js"));
        $view->headScript()->prependFile($view->baseUrl("/js/libs/select2/select2.min.js"));
        $view->headScript()->prependFile($view->baseUrl("/js/libs/bootstrap-switch.js"));
        $view->headScript()->prependFile($view->baseUrl("/js/libs/bootstrap-modalmanager.js"));
        $view->headScript()->prependFile($view->baseUrl("/js/libs/bootstrap-modal.js"));
        $view->headScript()->prependFile($view->baseUrl("/js/libs/bootstrap.min.js"));

        $view->headScript()->prependFile($view->baseUrl("/js/libs/jqgrid/jquery.jqGrid.min.js"));
        $view->headScript()->prependFile($view->baseUrl("/js/libs/jqgrid/i18n/grid.locale-en.js"));
    }

    /**
     * Initializes the ACL for the project
     */
    protected function _initAcl ()
    {
        $acl = Application_Model_Acl::getInstance();
        Zend_Registry::set('Zend_Acl', $acl);

        return $acl;
    }

    /**
     * Takes initialized caches and puts them into the registry
     */
    protected function _initPutCachesIntoRegistry ()
    {
        $this->bootstrap('cachemanager');
        /* @var $cacheManager Zend_Cache_Manager */
        $cacheManager = $this->getResource('cachemanager');

        Zend_Registry::set('navigationCache', $cacheManager->getCache('navigation_cache'));
        Zend_Registry::set('aclCache', $cacheManager->getCache('acl_cache'));
    }

    /**
     * Loads our currency helper into the registry
     */
    protected function _initCurrency ()
    {
        $currency = new Zend_Currency('en_US');
        Zend_Registry::set('Zend_Currency', $currency);
    }
}