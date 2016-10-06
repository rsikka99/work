<?php

ob_implicit_flush();
set_time_limit(0);

ini_set("auto_detect_line_endings", true);

defined('APPLICATION_BASE_PATH') || define('APPLICATION_BASE_PATH', realpath(dirname(__FILE__)));

// Define the paths
defined('APPLICATION_PATH') || define('APPLICATION_PATH', APPLICATION_BASE_PATH . '/application');
defined('DATA_PATH') || define('DATA_PATH', APPLICATION_BASE_PATH . '/data');
defined('PUBLIC_PATH') || define('PUBLIC_PATH', APPLICATION_BASE_PATH . '/public');
defined('ASSETS_PATH') || define('ASSETS_PATH', APPLICATION_BASE_PATH . '/assets');

/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(APPLICATION_BASE_PATH);

// Setup autoloading
require 'init_autoloader.php';
My_Error_Handler::set();
//
//ini_set("display_errors", 1);
//error_reporting(E_ALL);

// Define application environment.
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (isset($_SERVER['php_environment_mode']) ? $_SERVER['php_environment_mode'] : 'production'));

// Create application, bootstrap, and run
$application = new Zend_Application('production', array(
    'config' => array(
        APPLICATION_PATH . '/configs/global.php',
        APPLICATION_PATH . '/configs/local.php',
    )));
$application->bootstrap();

//##########################################################################################################

$auth   = Zend_Auth::getInstance();
$user = json_decode(json_encode(['id'=>1, 'eulaAccepted'=>true, 'firstname'=>'unit', 'lastname'=>'testing', 'dealerId'=>1, 'resetPasswordOnNextLogin'=>false, 'email'=>'it@tangentmtw.com']));
$auth->getStorage()->write($user);
$mpsSession = new Zend_Session_Namespace('mps-tools');
$mpsSession->selectedClientId = 1;
$mpsSession->selectedRmsUploadId=1;

try {

    $cloudinary = \MPSToolbox\Api\Cloudinary::getInstance();
    $api = $cloudinary->getApi();
    #$result = $cloudinary->upload(APPLICATION_BASE_PATH.'/public/img/loading.gif');
    #var_dump($result);
    #$result = $api->update($result['public_id'],['tags'=>'abc,def']);
    #var_dump($result);
    #$result = $api->resources_by_tag('abc');
    #var_dump($result['resources']);
    //$api->delete_resources($result['public_id']);

    $db = Zend_Db_Table::getDefaultAdapter();

    $man = [];
    foreach ($db->query('select * from manufacturers')->fetchAll() as $line) {
        $man[$line['id']] = $line['fullname'];
    }

    $st1 = $db->prepare("
insert into cloud_file set
  `type`='image',
  `localFile`=?,
  `baseProductId`=?,
  `handle`=?,
  `format`=?,
  `url`=?,
  `size`=?,
  `width`=?,
  `height`=?
");

    $st2 = $db->prepare("
update cloud_file set
  `type`='image',
  `localFile`=?,
  `handle`=?,
  `format`=?,
  `url`=?,
  `size`=?,
  `width`=?,
  `height`=?
where id=?
");

    $exists = [];
    foreach ($db->query('select id, baseProductId, handle from cloud_file')->fetchAll() as $line) {
        $exists[$line['baseProductId']] = $line;
    }

    foreach ($db->query('select id, base_type, manufacturerId, imageFile from base_product')->fetchAll() as $line) {
        if (isset($exists[$line['id']])) {
            continue;
        }
        $mfg = $man[$line['manufacturerId']];
        if (empty($line['imageFile'])) continue;

        $tag=false;
        $filename = false;
        if (file_exists($fn = APPLICATION_BASE_PATH . '/public/img/devices/'.$line['imageFile']) && is_file($fn) && filesize($fn)>0) {
            $filename = $fn;
            $tag = 'Device';
        }
        if (file_exists($fn = APPLICATION_BASE_PATH . '/public/img/toners/'.$line['imageFile']) && is_file($fn) && filesize($fn)>0) {
            $filename = $fn;
            $tag = 'Toner';
        }
        if (file_exists($fn = APPLICATION_BASE_PATH . '/public/img/sku/'.$line['imageFile']) && is_file($fn) && filesize($fn)>0) {
            $filename = $fn;
            $tag = 'SKU';
        }

        if ($tag && $filename) {
            $localFile = str_replace(APPLICATION_BASE_PATH,'',$fn);
            $i = getimagesize($filename);
            if ($i[0] && $i[1]) {
                echo "processing id {$line['id']} > {$filename}\n";

                if (isset($exists[$line['id']])) {
                    echo "deleting existing cloud image: {$exists[$line['id']]['handle']}\n";
                    $api->delete_resources($exists[$line['id']]['handle']);
                }

                echo "uploading: {$filename}\n";
                $upload_result = $cloudinary->upload($filename);
                if (!empty($upload_result['public_id'])) {
                    $handle = $upload_result['public_id'];
                    $update_result = $api->update($handle, ['tags',"{$tag},{$mfg}"]);
                    if ($update_result->rate_limit_remaining<10) {
                        echo "rate_limit low: {$update_result->rate_limit_remaining}";
                        sleep(1);
                    }
                    if (!isset($exists[$line['id']])) {
                        echo "insert into cloud_file: {$handle}\n";
                        $st1->execute([
                            $localFile,
                            $line['id'],
                            $handle,
                            $upload_result['format'],
                            $upload_result['secure_url'],
                            $upload_result['bytes'],
                            $upload_result['width'],
                            $upload_result['height'],
                        ]);
                    } else {
                        echo "update cloud_file {$exists[$line['id']]['id']}: {$handle}\n";
                        $st2->execute([
                            $localFile,
                            $handle,
                            $upload_result['format'],
                            $upload_result['secure_url'],
                            $upload_result['bytes'],
                            $upload_result['width'],
                            $upload_result['height'],
                            $exists[$line['id']]['id']
                        ]);
                    }
                } else {
                    echo "uploading failed! {$filename}\n";
                }
            }
        }
    }

} catch (Exception $ex) {
    var_dump($ex);
}
