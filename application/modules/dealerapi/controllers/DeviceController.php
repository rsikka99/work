<?php

require 'IndexController.php';

use MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers\DeviceSwapMapper;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\OptimizationStandardDeviceReplacementModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceMeterModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\MasterDeviceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\CostPerPageSettingModel;

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

    public function swapAction() {
        $id = intval($this->getParam('id'));
        if (!$id) {
            $this->outputJson(['error'=>'id not provided']);
            return;
        }
        $masterDevice = MasterDeviceMapper::getInstance()->find($id);
        if (!$masterDevice) {
            $this->outputJson(['error'=>"device {$id} not found"]);
            return;
        }

        $result=array();

        $replacementDevices = [];
        $dealerId = 2;
        $costThreshold = 0;
        $dealerCostPerPageSetting = new CostPerPageSettingModel(['dealerId'=>$dealerId]);
        $replacementsCostPerPageSetting = new CostPerPageSettingModel(['dealerId'=>$dealerId]);
        $reportLaborCostPerPage = 0;
        $reportPartsCostPerPage = 0;

        $doFunctionalityUpgrade = false;

        $blackReplacementDevices    = DeviceSwapMapper::getInstance()->getBlackReplacementDevices($dealerId, true, $doFunctionalityUpgrade);
        $blackMfpReplacementDevices = DeviceSwapMapper::getInstance()->getBlackMfpReplacementDevices($dealerId, $doFunctionalityUpgrade);
        $colorReplacementDevices    = DeviceSwapMapper::getInstance()->getColorReplacementDevices($dealerId, true);
        $colorMfpReplacementDevices = DeviceSwapMapper::getInstance()->getColorMfpReplacementDevices($dealerId);


        $model = new OptimizationStandardDeviceReplacementModel(
            [
                'black'    => $blackReplacementDevices,
                'blackmfp' => $blackMfpReplacementDevices,
                'color'    => $colorReplacementDevices,
                'colormfp' => $colorMfpReplacementDevices
            ],
            $dealerId,
            $costThreshold,
            $dealerCostPerPageSetting,
            $replacementsCostPerPageSetting,
            $reportLaborCostPerPage,
            $reportPartsCostPerPage
        );

        $deviceInstance = new DeviceInstanceModel();
        $deviceInstance->setMasterDevice($masterDevice);
        $deviceInstance->setDeviceAction(DeviceInstanceModel::ACTION_REPLACE);
        $meter = new DeviceInstanceMeterModel();
        $deviceInstance->setMeter($meter);
        $result = $model->findReplacement($deviceInstance);

        $this->outputJson(['result'=>$result]);
    }
}

