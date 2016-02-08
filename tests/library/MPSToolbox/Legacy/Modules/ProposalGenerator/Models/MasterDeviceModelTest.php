<?php

class MPSToolbox_Legacy_Modules_ProposalGenerator_Models_MasterDeviceModelTest extends My_DatabaseTestCase
{

    public $fixtures = [ 'toners', 'master_devices', 'device_toners', 'dealers', 'dealer_toner_attributes', 'dealer_master_device_attributes', 'toner_vendor_ranking_sets', 'toner_vendor_rankings' ];

    public function test_isLeased() {
        $model = new \MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel(['id'=>1]);
        $result = $model->isLeased(2);
        $this->assertFalse($result);
        $model = new \MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel(['id'=>2]);
        $result = $model->isLeased(2);
        $this->assertTrue($result);
    }

    public function test_getMaximumMonthlyPageVolume() {
        $model = new \MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel(['id'=>1]);
        $cpp = new \MPSToolbox\Legacy\Modules\ProposalGenerator\Models\CostPerPageSettingModel(['dealerId'=>2]);
        $result = $model->getMaximumMonthlyPageVolume($cpp);
        $this->assertEquals(1400, $result);

        $model = new \MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel(['id'=>2]);
        $cpp = new \MPSToolbox\Legacy\Modules\ProposalGenerator\Models\CostPerPageSettingModel(['dealerId'=>2]);
        $result = $model->getMaximumMonthlyPageVolume($cpp);
        $this->assertEquals(123, $result);
    }

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

    public function test_calculateCostPerPage1() {
        $this->user2();
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
        $this->assertEquals(15.0494481868998, $toner->calculatedCost);
        $this->assertEquals(0.15684065826677, $result->getCostPerPage()->monochromeCostPerPage);
    }

    public function test_calculateCostPerPage2() {
        $this->user2();
        $model = new \MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel(['id'=>19]);
        $cppSetting = new \MPSToolbox\Legacy\Modules\ProposalGenerator\Models\CostPerPageSettingModel(['dealerId'=>2, 'clientId'=>5]);
        $cppSetting->adminCostPerPage = 0;
        $cppSetting->colorLaborCostPerPage = 0;
        $cppSetting->colorPartsCostPerPage = 0;
        $cppSetting->monochromeLaborCostPerPage = 0;
        $cppSetting->monochromePartsCostPerPage = 0;
        $cppSetting->pageCoverageColor = 100;
        $cppSetting->pageCoverageMonochrome = 100;
        $cppSetting->monochromeTonerRankSet = $cppSetting->colorTonerRankSet = \MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerVendorRankingSetMapper::getInstance()->find(1108);

        $result = $model->calculateCostPerPage($cppSetting, true);
        $this->assertTrue(isset($result->toners[1]));
        $toner = $result->toners[1];
        $this->assertEquals(100.329654579332, $toner->calculatedCost);
        $this->assertEquals(0.20065930915866, $result->getCostPerPage()->monochromeCostPerPage);
    }

}