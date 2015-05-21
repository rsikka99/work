<?php

use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\OptimizationStandardDeviceReplacementModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\MasterDeviceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\CostPerPageSettingModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DeviceInstanceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerConfigModel;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\DeviceSwapModel;

/**
 * Class MPSToolbox_Settings_Form_AllSettingsTest
 * @property OptimizationStandardDeviceReplacementModel $model
 */
class MPSToolbox_Legacy_Modules_HardwareOptimization_Models_OptimizationStandardDeviceReplacementModelTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $replacementDevices = [];
        $dealerId = 1;
        $costThreshold = 1;
        $dealerCostPerPageSetting = new CostPerPageSettingModel([
            'dealerId'=>1,
        ]);
        $replacementsCostPerPageSetting = new CostPerPageSettingModel([
            'dealerId'=>1,
        ]);
        $reportLaborCostPerPage = new CostPerPageSettingModel([
            'dealerId'=>1,
        ]);
        $reportPartsCostPerPage = new CostPerPageSettingModel([
            'dealerId'=>1,
        ]);
        $this->model = new OptimizationStandardDeviceReplacementModel(
            $replacementDevices,
            $dealerId,
            $costThreshold,
            $dealerCostPerPageSetting,
            $replacementsCostPerPageSetting,
            $reportLaborCostPerPage,
            $reportPartsCostPerPage
        );
    }

    protected function tearDown()
    {
    }

    public function testFindReplacement() {
        $device = new DeviceInstanceModel([
            'id'=>1,
            'rmsUploadId'=>1,
            'rmsUploadRowId'=>1,
            'ipAddress'=>'',
            'isExcluded'=>0,
            'mpsDiscoveryDate'=>'',
            'reportsTonerLevels'=>'',
            'serialNumber'=>'',
            'useUserData'=>'',
            'isManaged'=>'',
            'assetId'=>'',
            'pageCoverageMonochrome'=>'',
            'pageCoverageCyan'=>'',
            'pageCoverageMagenta'=>'',
            'pageCoverageYellow'=>'',
            'isLeased'=>'',
            'rawDeviceName'=>'',
            'compatibleWithJitProgram'=>'',
            'pageCoverageColor'=>'',
            'location'=>'',
        ]);
        $device->setDeviceAction(DeviceInstanceModel::ACTION_REPLACE);

        $masterDevice = new MasterDeviceModel();
        $masterDevice->setTonerConfig(new TonerConfigModel(['id'=>TonerConfigModel::BLACK_ONLY]));
        $masterDevice->setCopier(false);
        $device->setMasterDevice($masterDevice);

        $this->model->setBlackReplacementDevices([
            new DeviceSwapModel(['masterDeviceId'=>2]),
            new DeviceSwapModel(['masterDeviceId'=>3]),
        ]);

        $result = $this->model->findReplacement($device);
        $this->assertFalse(empty($result));
    }


}