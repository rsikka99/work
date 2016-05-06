<?php
return [
    'appnamespace'         => 'App',
    'autoloadernamespaces' => [
        'twitterbootstrap' => 'Twitter_Bootstrap_',
    ],
    'app'                  => [
        'uploadPath'     => DATA_PATH . '/uploads',
        'copyright'      => 'Tangent MTW',
        'useAnalytics'   => false,
        'useFeedbackTab' => false,
        'useChatTab'     => false,
        'supportEmail'   => 'support@tangentmtw.com',
        'locale'         => 'en_GB',
    ],
    'bootstrap'            => [
        'path'  => APPLICATION_PATH . '/Bootstrap.php',
        'class' => 'Bootstrap',
    ],
    'feature'              => [
        'adapter' => 'My_Feature_DbTableAdapter',
        'options' => [
            'className' => 'MPSToolbox\Legacy\DbTables\DealerFeatureDbTable',
        ],
    ],
    'includePaths'         => [
        'library' => APPLICATION_BASE_PATH . '/library',
    ],
    'phpSettings'          => [
        'timezone'          => 'America/New_York',
        'display_errors'    => false,
        'displayExceptions' => false,
    ],
    'pluginPaths'          => [
        'ZendX_Application_Resource' => 'ZendX/Application/Resource',
    ],
    'resources'            => [
        'cachemanager'    => [
            'acl_cache'        => [
                'frontend' => [
                    'name'                 => 'Core',
                    'customFrontendNaming' => false,
                    'options'              => [
                        'lifetime'                => 7200,
                        'caching'                 => true,
                        'automatic_serialization' => true,
                    ],
                ],
                'backend'  => [
                    'name'    => 'File',
                    'options' => [
                        'cache_dir' => DATA_PATH . '/cache/acl/',
                    ],
                ],
            ],
            'navigation_cache' => [
                'frontend' => [
                    'name'                 => 'File',
                    'customFrontendNaming' => false,
                    'options'              => [
                        'master_files'            => [
                            APPLICATION_PATH . '/configs/navigation.xml',
                        ],
                        'caching'                 => true,
                        'automatic_serialization' => true,
                    ],
                ],
                'backend'  => [
                    'name'    => 'File',
                    'options' => [
                        'cache_dir' => DATA_PATH . '/cache/navigation/',
                    ],
                ],
            ],
        ],
        'db'              => [
            'adapter'               => 'Pdo_Mysql',
            'isDefaultTableAdapter' => true,
        ],
        'frontController' => [
            'controllerDirectory' => APPLICATION_PATH . '/controllers',
            'moduleDirectory'     => APPLICATION_PATH . '/modules',
            'params'              => [
                'displayExceptions'   => false,
                'prefixDefaultModule' => true,
            ],
            'plugins'             => [
                'My_Controller_Plugin_Acl',
                'My_Controller_Plugin_ForceUserAction',
                'My_Controller_Plugin_UserActivity',
                '\\MPSToolbox\\Legacy\\Controllers\\Plugins\\AddScriptPath',
            ],
        ],
        'jquery'          => [
            'version'    => '1.11.1',
            'ui_enable'  => true,
            'ui_version' => '1.10.3',
        ],
        'layout'          => [
            'layoutPath' => APPLICATION_PATH . '/layouts/scripts'
        ],
        'log'             => [
            'stream' => [
                'filterName'   => 'Priority',
                'filterParams' => [
                    'priority' => 4,
                ],
                'writerName'   => 'Stream',
                'writerParams' => [
                    'stream' => DATA_PATH . '/logs/application.log',
                    'mode'   => 'a',
                ],
            ],
        ],
        'modules'         => [
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
        ],
        'session'         => [
            'name'             => 'MPSToolbox',
            'save_path'        => DATA_PATH . '/sessions',
            'use_only_cookies' => 'on',
            'cookie_lifetime'  => 43200,
            'gc_maxlifetime'   => '43200',
        ],
        'view'            => [
            'helperPath' => [
                'My_View_Helper'             => 'My/View/Helper',
                'My_View_Helper_Navigation'  => 'My/View/Helper/Navigation',
                'Tangent\\View\\Helper\\'    => 'Tangent/View/Helper',
                'Bootstrap3\\View\\Helper\\' => 'Bootstrap3/View/Helper',
                'App_View_Helper'            => APPLICATION_PATH . '/layouts/helpers',
            ],
        ],
    ],
];