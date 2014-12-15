<?php
/**
 * NOTE from lrobert: This is in here to make sure our createBlankDatabase.php
 * file uses mysqli instead of pdo so that we can properly drop/create the
 * database
 */
return array(
    'resources' => array(
        'db' => array(
            'adapter' => 'mysqli',
        ),
    ),
);