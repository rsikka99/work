<?php

require 'IndexController.php';

/**
 * Class Api_IndexController
 */
class Dealerapi_SupplyController extends Dealerapi_IndexController
{
    public function indexAction() {
    }

    public function searchAction() {
        $keyword = $this->getParam('keyword');
        if (strlen($keyword)<3) {
            $this->outputJson(['error'=>'keyword not provided or too short (minimal 3 characters)']);
            return;
        }

        $db = Zend_Db_Table::getDefaultAdapter();
        $zendDbSelect =
            $db->select()
                ->from('toners',['id','sku','imageFile'])
                ->joinLeft('manufacturers','toners.manufacturerId=manufacturers.id',['fullname'])
                ->where('sku like ?',"%$keyword%");
        $zendDbStatement = $db->query($zendDbSelect);
        $result = [];
        foreach ($zendDbStatement->fetchAll() as $line) {
            $image_url='';
            if ($line['imageFile']) $image_url="http://{$_SERVER['HTTP_HOST']}/img/devices/{$line['imageFile']}";
            $result[]=[
                'type'=>'supply',
                'id'=>$line['id'],
                'manufacturer'=>$line['fullname'],
                'sku'=>$line['sku'],
                'name'=>$line['fullname'].' '.$line['sku'],
                'image'=>$image_url,
            ];
        }
        $zendDbSelect =
            $db->select()
                ->from('master_devices',['id','modelName','imageFile'])
                ->joinLeft('manufacturers','master_devices.manufacturerId=manufacturers.id',['fullname'])
                ->where('modelName like ?',"%$keyword%");
        $zendDbStatement = $db->query($zendDbSelect);
        foreach ($zendDbStatement->fetchAll() as $line) {
            $image_url='';
            if ($line['imageFile']) $image_url="http://{$_SERVER['HTTP_HOST']}/img/devices/{$line['imageFile']}";
            $result[]=[
                'type'=>'device',
                'id'=>$line['id'],
                'manufacturer'=>$line['fullname'],
                'model'=>$line['modelName'],
                'name'=>$line['fullname'].' '.$line['modelName'],
                'image'=>$image_url,
            ];
        }
        $this->outputJson(['result'=>$result]);
    }

}

