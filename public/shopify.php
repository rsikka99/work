<?php

define('IS_DEV', file_exists("c:"));
define('IS_PROD', !IS_DEV);
define('IS_SANDBOX', true);

function _proxy_get_db()
{
    global $_proxy_db;
    if (empty($_proxy_db)) {

        $settings = include('../application/configs/local.php');
        $_proxy_db = new PDO('mysql:host='.$settings['resources']['db']['params']['host'].';dbname='.$settings['resources']['db']['params']['dbname'], $settings['resources']['db']['params']['username'], $settings['resources']['db']['params']['password']);
    }
    return $_proxy_db;
}

require_once '../vendor/autoload.php';

if (!isset($_GET['shop'])) die('shop?');

$config=json_decode(file_get_contents('../application/configs/shopify.json'));
$config->shopname = str_replace('.myshopify.com','',$_GET['shop']);
$storage = new \cdyweb\Shopify\OAuth\PDOTokenStorage(_proxy_get_db(), 'shopify_');
$client = new \cdyweb\Shopify\Shopify($config, $storage);

if (isset($_GET['code'])) {
    $client->authorizeCallback();
    header('HTTP/1.1 302 Found');
    header('Location: '.$config->redirect_uri);
    exit;
}

if (!$client->hasAccessToken()) {
    header('HTTP/1.1 302 Found');
    header('Location: '.$client->getAuthorizeUri());
    exit;
}

try {
    $result = $client->getPages();
} catch (\ActiveResource\Exceptions\UnauthorizedAccess $ex) {
    header('HTTP/1.1 302 Found');
    header('Location: '.$client->getAuthorizeUri());
    exit;
}

echo 'ok!';

/**
chdir('script/ccms/');
require 'ccms.inc.php';

$user = getOneRow('select * from ccms_user where id=2');
$_SESSION['user'] = $user;
session_write_close();

header('Location: /ccms/', true, 303);
 **/
