<?php

namespace MPSToolbox\Services;

use MPSToolbox\Api\Cloudinary;

class ImageService {

    const LOCAL_DEVICES_DIR = '/public/img/devices';
    const LOCAL_TONER_DIR = '/public/img/toners';
    const LOCAL_SKU_DIR = '/public/img/sku';

    const TAG_DEVICE = 'Device';
    const TAG_TONER = 'Toner';
    const TAG_SKU = 'SKU';

    public $lastError='';

    public function getImageUrls($baseProductId) {
        $db = \Zend_Db_Table::getDefaultAdapter();
        $result = [];
        foreach($db->query("select * from cloud_file where `type`='image' and baseProductId=".intval($baseProductId).' order by orderBy')->fetchAll() as $line) {
            $result[$line['id']] = $line['url'];
        }
        return $result;
    }

    public function addImage($baseProductId, $imageDescription, $local_dir=ImageService::LOCAL_DEVICES_DIR, $tag=ImageService::TAG_DEVICE, $orderBy=1) {
        $tmpfname = APPLICATION_BASE_PATH.'/data/temp/'.uniqid(time(),true);
        $this->lastError = $tmpfname;
        if (is_string($imageDescription)) { //eg URL
            file_put_contents(
                $tmpfname,
                file_get_contents($imageDescription)
            );

        } else if (!empty($imageDescription['tmp_name'])) { //eg _FILES['..']
            move_uploaded_file($imageDescription['tmp_name'], $tmpfname);
        }

        $image_info = @getimagesize($tmpfname);
        if (!$image_info || ($image_info[0]<1)) {
            $this->lastError = "cannot get image size from {$tmpfname}";
            return false;
        }

        $ext=null;
        switch ($image_info['mime']) {
            case 'image/jpeg' :
                $ext='jpg';
                break;
            case 'image/png' :
                $ext='png';
                break;
            case 'image/gif' :
                $ext='gif';
                break;
        }
        if (!$ext) {
            $this->lastError = "unknown mime type: {$image_info['mime']}";
            return false;
        }

        $filename = $local_dir.'/'.$baseProductId.'_'.time().'.'.$ext;
        rename($tmpfname, APPLICATION_BASE_PATH.$filename);

        $db = \Zend_Db_Table::getDefaultAdapter();
        $mfg = $db->query('select fullname from manufacturers m where m.id=(select manufacturerId from base_product p where p.id='.intval($baseProductId).')')->fetchColumn(0);
        $sku = $db->query('select sku from base_product where id='.intval($baseProductId))->fetchColumn(0);

        $cloud = Cloudinary::getInstance();
        $cloud_result = $cloud->upload(APPLICATION_BASE_PATH.$filename, ['tags'=>"{$tag},{$mfg},{$baseProductId},{$sku}"]);

        if (!empty($cloud_result['public_id'])) {

            $st1 = $db->prepare("
INSERT INTO cloud_file SET
  `type`='image',
  `orderBy`=?,
  `localFile`=?,
  `baseProductId`=?,
  `handle`=?,
  `format`=?,
  `url`=?,
  `size`=?,
  `width`=?,
  `height`=?
");
            $st1->execute([
                $orderBy,
                $filename,
                $baseProductId,
                $cloud_result['public_id'],
                $cloud_result['format'],
                $cloud_result['secure_url'],
                $cloud_result['bytes'],
                $cloud_result['width'],
                $cloud_result['height'],
            ]);
        }

        return $cloud_result['secure_url'];
    }

    public function deleteImageById($id) {
        $db = \Zend_Db_Table::getDefaultAdapter();
        $line = $db->query('select * from cloud_file where id='.intval($id))->fetch();
        if (empty($line)) return;

        $cloud = Cloudinary::getInstance();
        $cloud->getApi()->delete_resources($line['handle']);

        $filename = APPLICATION_BASE_PATH.$line['localFile'];
        if (file_exists($filename) && is_file($filename)) {
            unlink($filename);
        }

        $db->query('delete from cloud_file where id='.intval($id));
    }


}