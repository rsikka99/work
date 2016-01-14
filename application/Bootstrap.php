<?php
use MPSToolbox\Legacy\Models\Acl\AppAclModel;
use MPSToolbox\Legacy\Services\NavigationService;
use MPSToolbox\Legacy\Services\LessCssService;
use Tangent\Statsd;


/**
 * Class Bootstrap
 * This class prepares the application with all the needed settings before anything is routed
 */
class Bootstrap extends Tangent\Bootstrap
{

    /**
     * Initializes the Zend_Config object into the registry
     *
     * @return Zend_Config
     */
    protected function _initConfig ()
    {
        $config = new Zend_Config($this->getOptions());
        if (!Zend_Registry::isRegistered('config'))
        {
            Zend_Registry::set('config', $config);
        }

        return $config;
    }

    /**
     * Initializes Laravel's database layer
     */
    protected function _initEloquent ()
    {
        $options = $this->getOptions();

        $capsule = new Illuminate\Database\Capsule\Manager();
        Zend_Registry::set('Illuminate\Database\Capsule\Manager', $capsule);

        $capsule->addConnection([
            'driver'    => 'mysql',
            'host'      => $options['resources']['db']['params']['host'],
            'database'  => $options['resources']['db']['params']['dbname'],
            'username'  => $options['resources']['db']['params']['username'],
            'password'  => $options['resources']['db']['params']['password'],
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]);
        $capsule->bootEloquent();

    }

    /**
     * Initializes Doctrine ORM
     */
    protected function _initDoctrine()
    {
        $options = $this->getOptions();

        $paths = array(APPLICATION_BASE_PATH.'/library/MPSToolbox/Entities');
        $isDevMode = true;

        $dbParams = array(
            'driver'   => 'pdo_mysql',
            'host'     => $options['resources']['db']['params']['host'],
            'user'     => $options['resources']['db']['params']['username'],
            'password' => $options['resources']['db']['params']['password'],
            'dbname'   => $options['resources']['db']['params']['dbname'],
        );

        $config = Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
        $entityManager = Doctrine\ORM\EntityManager::create($dbParams, $config);

        Zend_Registry::set('Doctrine\ORM\EntityManager', $entityManager);
    }

    /**
     * Initializes the routing
     */
    protected function _initRoutes ()
    {
        include APPLICATION_PATH . '/configs/routes.php';
    }

    protected function _initBrand ()
    {
        $options = $this->getOptions();

        if (isset($options['app']['theme']))
        {
            $includeBrandFile = PUBLIC_PATH . '/themes/' . $options['app']['theme'] . '/brand.php';
            if (file_exists($includeBrandFile))
            {
                $brandVariables = include($includeBrandFile);
                My_Brand::populate($brandVariables);
            }
        }
    }

    protected function _initStatsd ()
    {
        $options = $this->getOptions();
        if (isset($options['statsd']))
        {
            Statsd::$rootBucket = $options['statsd']['rootBucket'];
            Statsd::$enabled    = $options['statsd']['enabled'];
            Statsd::$host       = $options['statsd']['host'];
            Statsd::$port       = $options['statsd']['port'];
            Statsd::increment('mpstoolbox.pageloads', 1);
        }
    }

    /**
     * Start session
     */
    public function _initCoreSession ()
    {
        $this->bootstrap('session');
        Zend_Session::start();

        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity())
        {
            if ($auth->getIdentity()->id > 0)
            {
                NavigationService::$userId = $auth->getIdentity()->id;
            }
        }
    }

    /**
     * Initializes php runtime settings
     */
    protected function _initPhpSettings ()
    {
        $options = $this->getOptions();
        date_default_timezone_set($options ['phpSettings'] ['timezone']);

        // Turn on the display of errors
        if (APPLICATION_ENV != 'production')
        {
            @ini_set('display_errors', 1);
        }
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
        $dealerId = (Zend_Auth::getInstance()->hasIdentity()) ? Zend_Auth::getInstance()->getIdentity()->dealerId : 0;
        $this->bootstrap('view');
        $this->bootstrap('brand');
        $this->bootstrap('db');
        $view = $this->getResource('view');

        // Set the doctype to HTML5 so components know how to render items
        $view->doctype('HTML5');

        // Initialize the twitter bootstrap menu plugin
        $view->registerHelper(new My_View_Helper_Navigation_Menu(), 'menu');


        $forceRecompile = (isset($_REQUEST['recompile_less']));

        if ($forceRecompile || !file_exists(PUBLIC_PATH . '/css/site/styles.css')) {
            LessCssService::compileSiteStyles($forceRecompile);
            LessCssService::compileSiteThemeStyles($forceRecompile);
            LessCssService::compileReportStyles($forceRecompile);
        }

        /**
         * CSS Styles
         */
        $view->headLink()->prependStylesheet($view->baseUrl(sprintf('css/reports/reports_dealer_%s.css', $dealerId)));
        $view->headLink()->prependStylesheet($view->theme('/css/site/styles.css'));
        $view->headLink()->prependStylesheet($view->baseUrl('/css/site/styles.css'));

        /**
         * Common.js used for require.js setup
         */
        $view->headScript()->prependFile($view->baseUrl('/js/common.js?_='.date('dmY')));
    }

    /**
     * Initializes the ACL for the project
     */
    protected function _initAcl ()
    {
        $acl = AppAclModel::getInstance();
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

        $acl_cache = $cacheManager->getCache('acl_cache');
        $navigation_cache = $cacheManager->getCache('navigation_cache');

        if (extension_loaded('memcache')) {
            $acl_cache->setBackend(new Zend_Cache_Backend_Memcached());
            $navigation_cache->setBackend(new Zend_Cache_Backend_Memcached());
        } else if (extension_loaded('memcached')) {
            $acl_cache->setBackend(new Zend_Cache_Backend_Libmemcached());
            $navigation_cache->setBackend(new Zend_Cache_Backend_Libmemcached());
        }

        Zend_Registry::set('navigationCache', $navigation_cache);
        Zend_Registry::set('aclCache', $acl_cache);
    }

    /**
     * Loads our currency helper into the registry
     */
    protected function _initCurrency ()
    {
        //Zend_Locale_Data::disableCache(true);
        if (extension_loaded('memcache')) {
            $core = new Zend_Cache_Core();
            $core->setBackend(new Zend_Cache_Backend_Memcached());
            Zend_Locale_Data::setCache($core);
        } else if (extension_loaded('memcached')) {
            $core = new Zend_Cache_Core();
            $core->setBackend(new Zend_Cache_Backend_Libmemcached());
            Zend_Locale_Data::setCache($core);
        }

        $currency = new Zend_Currency(['currency' => 'EUR'], 'en_US');
        Zend_Registry::set('Zend_Currency', $currency);
    }

    /**
     * Loads our My_Feature settings
     */
    protected function _initFeature ()
    {
        $options = $this->getOptions();

        if (array_key_exists('feature', $options))
        {
            $adapter = new $options['feature']['adapter']($options['feature']['options']);
            My_Feature::setAdapter($adapter);
        }
    }

    protected function _initMailTransport()
    {
        $config = Zend_Registry::get('config');
        Zend_Mail::setDefaultFrom($config->app->supportEmail, $config->app->name);

        //if (APPLICATION_ENV <> 'production') {
        if (file_exists('c:')) {
            $callback = function() {
                return 'ZendMail_' . microtime(true) .'.tmp';
            };
            $fileTransport = new Zend_Mail_Transport_File([
                'path' => APPLICATION_BASE_PATH . '/data/temp',
                'callback'=>$callback
            ]);
            Zend_Mail::setDefaultTransport($fileTransport);
        } else {
            if ($config->email->type=='smtp') {
                $emailConfig = [
                    'auth' => 'login',
                    'username' => $config->email->username,
                    'password' => $config->email->password,
                    'ssl' => $config->email->ssl,
                    'port' => $config->email->port,
                    'host' => $config->email->host
                ];
                Zend_Mail::setDefaultTransport(new Zend_Mail_Transport_Smtp($emailConfig['host'], $emailConfig));
            }
        }
    }
}
