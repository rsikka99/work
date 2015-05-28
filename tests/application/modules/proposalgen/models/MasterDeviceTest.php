<?php

use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\MasterDeviceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\CostPerPageSettingModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel;

class Proposalgen_Model_MasterDeviceTest extends My_DatabaseTestCase
{

    public $fixtures = [];

    /**
     * @var CostPerPageSettingModel
     */
    protected $_costPerPageSetting;

    /**
     * @param bool $createNew
     *
     * @return CostPerPageSettingModel
     */
    public function getCostPerPageSetting ()
    {
        $this->_costPerPageSetting                         = new CostPerPageSettingModel();
        $this->_costPerPageSetting->adminCostPerPage       = 0.05;
        $this->_costPerPageSetting->monochromeLaborCostPerPage       = 0.06;
        $this->_costPerPageSetting->monochromePartsCostPerPage       = 0.07;
        $this->_costPerPageSetting->colorLaborCostPerPage  = 0.06;
        $this->_costPerPageSetting->colorPartsCostPerPage  = 0.07;
        $this->_costPerPageSetting->pageCoverageMonochrome = 4;
        $this->_costPerPageSetting->pageCoverageColor      = 18;
        return $this->_costPerPageSetting;
    }

    public function getMasterDevice ($id=1)
    {
        MasterDeviceMapper::getInstance()->clearItemCache();
        return MasterDeviceMapper::getInstance()->find($id);
    }

    public function test_getDealerAttributes() {

    }
    public function test_getMaximumMonthlyPageVolume() {

    }
    public function test_populate() {

    }
    public function test_toArray() {

    }
    public function test_getManufacturer() {

    }
    public function test_setManufacturer() {

    }
    public function test_getTonerConfig() {

    }
    public function test_setTonerConfig() {

    }
    public function test_getToners() {

    }
    public function test_setToners() {

    }
    public function test_getHasValidMonoGrossMarginToners() {

    }
    public function test_getHasValidColorGrossMarginToners() {

    }
    public function test_getTonersForAssessment() {

    }
    public function test_getTonersForGrossMargin() {

    }
    public function test_setTonersForAssessment() {

    }
    public function test_setTonersForGrossMargin() {

    }
    public function test_getRequiredTonerColors() {

    }
    public function test_setRequiredTonerColors() {

    }
    public function test_getFullDeviceName() {

    }
    public function test_isColor() {

    }

    /**
     * A quick test to test a master devices using non default cost per page settings.
     */
    public function testCalculateCostPerPage ()
    {
        $this->setup_fixtures(['dealers','toner_configs','manufacturers','users','master_devices','toner_colors','toners','device_toners']);
        Zend_Auth::getInstance()->getStorage()->write((object)array("dealerId" => 1));

        $masterDevice       = $this->getMasterDevice();
        $costPerPageSetting = $this->getCostPerPageSetting();

        $costPerPage = $masterDevice->calculateCostPerPage($costPerPageSetting)->getCostOfInkAndTonerPerPage();

        $this->assertSame($costPerPage->monochromeCostPerPage, 0.020727272727273);
        $this->assertSame($costPerPage->colorCostPerPage, 0.10092857142857);
    }

    public function test_getCheapestTonerSetByVendor() {

    }

    public function test_getCheapestTonerSetByVendorId() {

    }

    public function test_getDeviceType() {

    }

    public function test_getAge() {

    }

    public function test_isJitCompatible() {

    }

    public function test_calculateEstimatedMaxLifeCount() {

    }

    public function get_recalculateMaximumRecommendedMonthlyPageVolume() {
        return [
            [1,2200],
            [2,8000],
            [3,7500],
            [4,5000],
            [5,6000],
        ];
    }

    /**
     * @dataProvider get_recalculateMaximumRecommendedMonthlyPageVolume
     */
    public function test_recalculateMaximumRecommendedMonthlyPageVolume($masterDeviceId, $expected) {
        $this->setup_fixtures(['dealers','toner_configs','manufacturers','users','master_devices','toner_colors','toners','device_toners']);
        $device = $this->getMasterDevice($masterDeviceId);
        $device->maximumRecommendedMonthlyPageVolume = 0;
        $device->recalculateMaximumRecommendedMonthlyPageVolume();
        $this->assertEquals($expected,$device->maximumRecommendedMonthlyPageVolume);
    }


}