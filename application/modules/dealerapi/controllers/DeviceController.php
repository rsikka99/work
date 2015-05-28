<?php

require 'IndexController.php';

/**
 * Class Api_IndexController
 */
class Dealerapi_DeviceController extends Dealerapi_IndexController
{
    public function indexAction() {
    }

    public function manufacturersAction() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $zendDbSelect = $db->select()->from('manufacturers',['id','fullname'])->where('isDeleted=0');
        $zendDbStatement = $db->query($zendDbSelect);
        $result = [];
        foreach ($zendDbStatement->fetchAll() as $line) {
            $result[]=[
                'id'=>$line['id'],
                'name'=>$line['fullname'],
            ];
        }
        $this->outputJson(['manufacturers'=>$result]);
    }

    public function modelsAction() {
        $manufacturer = $this->getParam('manufacturer');
        $color = $this->getParam('color');
        $mfp = $this->getParam('mfp');
        if (($manufacturer<1) || ($color=='') || ($mfp=='')) {
            $this->outputJson(['error'=>'parameter missing']);
            return;
        }
        $db = Zend_Db_Table::getDefaultAdapter();
        $zendDbSelect = $db->select()->from('manufacturers',['id','fullname'])->where('id=?',intval($manufacturer));
        $manufacturer_row = $db->query($zendDbSelect)->fetch();
        if (!$manufacturer_row) {
            $this->outputJson(['error'=>'invalid manufacturer ID']);
            return;
        }

        $zendDbSelect = $db->select()->from('master_devices',['id','modelName','imageFile'])->where('manufacturerId=?',intval($manufacturer))->where('isCopier'.($mfp?'=1':'=0'))->where('tonerConfigId'.($color?'<>1':'=1'));
        $zendDbStatement = $db->query($zendDbSelect);
        $result = [];
        foreach ($zendDbStatement->fetchAll() as $line) {
            $image_url='';
            if ($line['imageFile']) $image_url="http://{$_SERVER['HTTP_HOST']}/img/devices/{$line['imageFile']}";
            $result[]=[
                'id'=>$line['id'],
                'name'=>$line['modelName'],
                'image'=>$image_url,
            ];
        }
        $this->outputJson(['manufacturer'=>$manufacturer_row['fullname'],'models'=>$result]);
    }
}

