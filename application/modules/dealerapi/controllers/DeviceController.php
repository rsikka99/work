<?php

require 'IndexController.php';

use MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers\DeviceSwapMapper;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\OptimizationStandardDeviceReplacementModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceMeterModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerVendorRankingSetModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\MasterDeviceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerVendorRankingSetMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\CostPerPageSettingModel;

/**
 * Class Api_IndexController
 */
class Dealerapi_DeviceController extends Dealerapi_IndexController
{
    /**
     * @var int
     */
    public $dealerId;

    public function indexAction() {
    }

    public function getDealerId() {
        if (empty($this->dealerId)) {
            $auth   = Zend_Auth::getInstance();
            $identity = $auth->getIdentity();
            if (empty($identity->dealerId)) throw new RuntimeException('dealer auth not found');
            $this->dealerId = $identity->dealerId;
        }
        return $this->dealerId;
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
        $dealerId = $this->getDealerId();

        $id = intval($this->getParam('id'));
        if (!$id) {
            $this->outputJson(['error'=>'id not provided']);
            return;
        }

        $ampv = max(100,intval($this->getParam('ampv')));

        $masterDevice = MasterDeviceMapper::getInstance()->find($id);
        if (!$masterDevice) {
            $this->outputJson(['error'=>"device {$id} not found"]);
            return;
        }

        $service = new \MPSToolbox\Settings\Service\DealerSettingsService();
        $settings = $service->getDealerSettings($dealerId);

        $costThreshold = 0;
        $dealerCostPerPageSetting = new CostPerPageSettingModel([
            "adminCostPerPage"              => $settings->currentFleetSettings->adminCostPerPage,
            "monochromePartsCostPerPage"    => $settings->currentFleetSettings->defaultMonochromePartsCostPerPage,
            "monochromeLaborCostPerPage"    => $settings->currentFleetSettings->defaultMonochromeLaborCostPerPage,
            "colorPartsCostPerPage"         => $settings->currentFleetSettings->defaultColorPartsCostPerPage,
            "colorLaborCostPerPage"         => $settings->currentFleetSettings->defaultColorLaborCostPerPage,
            "pageCoverageMonochrome"        => $settings->currentFleetSettings->defaultMonochromeCoverage,
            "pageCoverageColor"             => $settings->currentFleetSettings->defaultColorCoverage,
            "monochromeTonerRankSet"        => TonerVendorRankingSetMapper::getInstance()->find(1),
            "colorTonerRankSet"             => TonerVendorRankingSetMapper::getInstance()->find(2),
            "useDevicePageCoverages"        => 0,
            "customerMonochromeCostPerPage" => 0,
            "customerColorCostPerPage"      => 0,
            "clientId"                      => 1,
            "dealerId"                      => $dealerId,
            "pricingMargin"                 => $settings->currentFleetSettings->tonerPricingMargin,
        ]);

        $replacementsCostPerPageSetting = new CostPerPageSettingModel([
            "adminCostPerPage"              => $settings->proposedFleetSettings->adminCostPerPage,
            "monochromePartsCostPerPage"    => $settings->proposedFleetSettings->defaultMonochromePartsCostPerPage,
            "monochromeLaborCostPerPage"    => $settings->proposedFleetSettings->defaultMonochromeLaborCostPerPage,
            "colorPartsCostPerPage"         => $settings->proposedFleetSettings->defaultColorPartsCostPerPage,
            "colorLaborCostPerPage"         => $settings->proposedFleetSettings->defaultColorLaborCostPerPage,
            "pageCoverageMonochrome"        => $settings->proposedFleetSettings->defaultMonochromeCoverage,
            "pageCoverageColor"             => $settings->proposedFleetSettings->defaultColorCoverage,
            "monochromeTonerRankSet"        => TonerVendorRankingSetMapper::getInstance()->find(1),
            "colorTonerRankSet"             => TonerVendorRankingSetMapper::getInstance()->find(2),
            "useDevicePageCoverages"        => 0,
            "customerMonochromeCostPerPage" => 0,
            "customerColorCostPerPage"      => 0,
            "clientId"                      => 1,
            "dealerId"                      => $dealerId,
            "pricingMargin"                 => $settings->proposedFleetSettings->tonerPricingMargin,
        ]);

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
            0,
            0
        );

        $deviceInstance = new DeviceInstanceModel();
        $deviceInstance->setMasterDevice($masterDevice);
        $deviceInstance->setDeviceAction(DeviceInstanceModel::ACTION_REPLACE);
        $meter = new DeviceInstanceMeterModel();
        $deviceInstance->setMeter($meter);
        if ($masterDevice->isColor()) {
            $deviceInstance->setCombinedMonthlyPageCount($ampv);
            $deviceInstance->setBlackMonthlyPageCount($ampv/2);
            $deviceInstance->setColorMonthlyPageCount($ampv/2);
        } else {
            $deviceInstance->setCombinedMonthlyPageCount($ampv);
            $deviceInstance->setBlackMonthlyPageCount($ampv);
            $deviceInstance->setColorMonthlyPageCount(0);
        }
        $model->findReplacement($deviceInstance);

        $options = $model->getAllReplacementOptions();
        usort($options, function($a,$b) {
            if ($a['deviceReplacementCost']>$b['deviceReplacementCost']) return 1;
            if ($a['deviceReplacementCost']<$b['deviceReplacementCost']) return -1;
            return 0;
        });

        $result = [];
        foreach($options as $option) {
            if (count($result)>=3) break;
            $replacementDevice = $option['replacementDevice'];
            $deviceReplacementCost = $option['deviceReplacementCost'];
            $costDelta = $option['costDelta'];
            $item = [
                'id'=>$replacementDevice->id,
                'monthly'=>number_format($deviceReplacementCost,2),
                'delta'=>number_format($costDelta,2),
            ];
            $result[] = $item;
        }
        $this->outputJson(['result'=>$result]);
    }
}

