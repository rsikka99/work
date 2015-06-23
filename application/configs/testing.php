<?php
return array(
    'app'         => array(
        'title'          => 'MPS Toolbox',
        'theme'          => 'default',
        'useAnalytics'   => false,
        'useFeedbackTab' => true,
        'useChatTab'     => false,
        'copyright'      => 'Tangent MTW',
        'deviceLimit'    => 6000,
        'supportEmail'   => 'support@mpstoolbox.com',
        'locale'         => 'en_US',
    ),
    'email'       => array(
        'host'     => 'smtp.googlemail.com',
        'ssl'      => 'SSL',
        'port'     => '465',
        'username' => 'noreply@tangentmtw.com',
        'password' => 'rammishryous',
    ),
    'phpSettings' => array(
        'display_errors'    => true,
        'displayExceptions' => true,
    ),
    'resources'   => array(
        'db'              => array(
            'params' => array(
                'host'     => 'localhost',
                'username' => 'develop',
                'password' => 'tmtwdev',
                'dbname'   => 'mpstoolbox_develop',
            ),
        ),
        'frontController' => array(
            'params' => array(
                'displayExceptions' => true,
            ),
        ),
    ),
    'statsd'      => array(
        'rootBucket' => 'dev.lrobert',
        'enabled'    => true,
        'host'       => '162.243.39.39',
        'port'       => 8125,
    ),
    'mandrill'    => array(
        'enabled' => true,
        'api_key' => '3HPeFdoClDJrOs7ZMLhItA',
    ),
);
