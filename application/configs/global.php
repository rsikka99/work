<?php
return array(
    'appnamespace'         => 'Application',
    'autoloadernamespaces' => array(
        'my'               => 'My_',
        'easybib'          => 'EasyBib_',
        'phpexcel'         => 'PHPExcel',
        'twitterbootstrap' => 'Twitter_Bootstrap_',
    ),
    'app'                  => array(
        'uploadPath' => DATA_PATH . '/uploads',
    ),
    'bootstrap'            => array(
        'path'  => APPLICATION_PATH . '/Bootstrap.php',
        'class' => 'Bootstrap',
    ),
    'feature'              => array(
        'adapter' => 'My_Feature_DbTableAdapter',
        'options' => array(
            'className' => 'Application_Model_DbTable_Dealer_Feature',
        ),
    ),
    'includePaths'         => array(
        'library' => APPLICATION_BASE_PATH . '/library',
    ),
    'phpSettings'          => array(
        'timezone'          => 'America/New_York',
        'display_errors'    => false,
        'displayExceptions' => false,
    ),
    'pluginPaths'          => array(
        'ZendX_Application_Resource' => 'ZendX/Application/Resource',
    ),
    'resources'            => array(
        'cachemanager'    => array(
            'acl_cache'        => array(
                'frontend' => array(
                    'name'                 => 'Core',
                    'customFrontendNaming' => false,
                    'options'              => array(
                        'lifetime'                => 7200,
                        'caching'                 => true,
                        'automatic_serialization' => true,
                    ),
                ),
                'backend'  => array(
                    'name'    => 'File',
                    'options' => array(
                        'cache_dir' => DATA_PATH . '/cache/acl/',
                    ),
                ),
            ),
            'navigation_cache' => array(
                'frontend' => array(
                    'name'                 => 'File',
                    'customFrontendNaming' => false,
                    'options'              => array(
                        'master_files'            => array(
                            APPLICATION_PATH . '/configs/navigation.xml',
                        ),
                        'caching'                 => true,
                        'automatic_serialization' => true,
                    ),
                ),
                'backend'  => array(
                    'name'    => 'File',
                    'options' => array(
                        'cache_dir' => DATA_PATH . '/cache/navigation/',
                    ),
                ),
            ),
        ),
        'db'              => array(
            'adapter'               => 'mysqli',
            'isDefaultTableAdapter' => true,
        ),
        'frontController' => array(
            'controllerDirectory' => APPLICATION_PATH . '/controllers',
            'moduleDirectory'     => APPLICATION_PATH . '/modules',
            'params'              => array(
                'displayExceptions'   => false,
                'prefixDefaultModule' => true,
            ),
            'plugins'             => array(
                'My_Controller_Plugin_Acl',
                'My_Controller_Plugin_ForceUserAction',
                'My_Controller_Plugin_UserActivity',
            ),
        ),
        'jquery'          => array(
            'version'    => '1.10.2',
            'ui_enable'  => true,
            'ui_version' => '1.10.3',
        ),
        'layout'          => array(
            'layoutPath' => APPLICATION_PATH . '/layouts/scripts'
        ),
        'log'             => array(
            'stream' => array(
                'filterName'   => 'Priority',
                'filterParams' => array(
                    'priority' => 4,
                ),
                'writerName'   => 'Stream',
                'writerParams' => array(
                    'stream' => DATA_PATH . '/logs/application.log',
                    'mode'   => 'a',
                ),
            ),
        ),
        'modules'         => array(
            'Admin',
            'Assessment',
            'DealerManagement',
            'Default',
            'HardwareLibrary',
            'HardwareOptimization',
            'Healthcheck',
            'Preferences',
            'Proposalgen',
            'Quotegen',
        ),
        'session'         => array(
            'name'             => 'MPSToolbox',
            'save_path'        => DATA_PATH . '/sessions',
            'use_only_cookies' => 'on',
            'cookie_lifetime'  => 43200,
            'gc_maxlifetime'   => '43200',
        ),
        'view'            => array(
            'helperPath' => array(
                'My_View_Helper'            => 'My/View/Helper',
                'My_View_Helper_Navigation' => 'My/View/Helper/Navigation',
                'Tangent_View_Helper'       => 'Tangent/View/Helper',
                'Application_View_Helper'   => APPLICATION_PATH . '/layouts/helpers',
            ),
        ),
    ),
);