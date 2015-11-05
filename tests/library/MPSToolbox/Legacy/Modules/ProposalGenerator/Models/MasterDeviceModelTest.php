<?php

class MPSToolbox_Legacy_Modules_ProposalGenerator_Models_MasterDeviceModelTest extends My_DatabaseTestCase
{

    public $fixtures = [ 'toners', 'master_devices', 'device_toners', 'dealers', 'dealer_toner_attributes' ];

    public function test_getToners() {
        $model = new \MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel(['id'=>1]);
        $toners = $model->getToners(2,5,'level1');
        $this->assertEquals(2, count($toners));
    }

    public function test_getCheapestTonerSetByVendor() {
        $model = new \MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel(['id'=>1]);
        $cppSetting = new \MPSToolbox\Legacy\Modules\ProposalGenerator\Models\CostPerPageSettingModel(['dealerId'=>2, 'clientId'=>5]);
        $cppSetting->level = 'level1';
        $result = $model->getCheapestTonerSetByVendor($cppSetting);
        $this->assertTrue(isset($result[1]));
        $toner = $result[1];
        $this->assertEquals(21, $toner->calculatedCost);
    }

    public function test_calculateCostPerPage() {
        $model = new \MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel(['id'=>1]);
        $cppSetting = new \MPSToolbox\Legacy\Modules\ProposalGenerator\Models\CostPerPageSettingModel(['dealerId'=>2, 'clientId'=>5]);
        $cppSetting->adminCostPerPage = 0.05;
        $cppSetting->colorLaborCostPerPage = 0.05;
        $cppSetting->colorPartsCostPerPage = 0.05;
        $cppSetting->monochromeLaborCostPerPage = 0.05;
        $cppSetting->monochromePartsCostPerPage = 0.05;
        $cppSetting->pageCoverageColor = 20;
        $cppSetting->pageCoverageMonochrome = 5;
        $cppSetting->level = 'level1';

        $result = $model->calculateCostPerPage($cppSetting, true);
        $this->assertTrue(isset($result->toners[1]));
        $toner = $result->toners[1];
        $this->assertEquals(21, $toner->calculatedCost);
        $this->assertEquals(0.15954545454545, $result->getCostPerPage()->monochromeCostPerPage);
    }

}