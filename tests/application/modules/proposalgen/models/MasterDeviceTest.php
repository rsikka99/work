<?php

use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\MasterDeviceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\CostPerPageSettingModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel;

class Proposalgen_Model_MasterDeviceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var CostPerPageSettingModel
     */
    protected $_costPerPageSetting;

    /**
     * @var MasterDeviceModel
     */
    protected $_masterDevice;

    /**
     * @param bool $createNew
     *
     * @return CostPerPageSettingModel
     */
    public function getCostPerPageSetting ($createNew = false)
    {
        if (!isset($this->_costPerPageSetting) || $createNew)
        {
            $this->_costPerPageSetting                         = new CostPerPageSettingModel();
            $this->_costPerPageSetting->adminCostPerPage       = 0.05;
            $this->_costPerPageSetting->laborCostPerPage       = 0.06;
            $this->_costPerPageSetting->partsCostPerPage       = 0.07;
            $this->_costPerPageSetting->pageCoverageMonochrome = 4;
            $this->_costPerPageSetting->pageCoverageColor      = 18;
        }

        return $this->_costPerPageSetting;
    }

    /**
     * @param bool $createNew
     *
     * @return MasterDeviceModel
     */
    public function getMasterDevice ($createNew = false)
    {
        if (!isset($this->_masterDevice) || $createNew)
        {
            $this->_masterDevice = MasterDeviceMapper::getInstance()->find(1);
        }

        return $this->_masterDevice;
    }

    /**
     * A quick test to test a master devices using non default cost per page settings.
     */
    public function testCalculateCostPerPage ()
    {
        Zend_Auth::getInstance()->getStorage()->write((object)array("dealerId" => 1));

        $masterDevice       = $this->getMasterDevice();
        $costPerPageSetting = $this->getCostPerPageSetting();

        $costPerPage = $masterDevice->calculateCostPerPage($costPerPageSetting)->getCostOfInkAndTonerPerPage();

        $this->assertSame($costPerPage->monochromeCostPerPage, 0.020727272727273);
        $this->assertSame($costPerPage->colorCostPerPage, 0.10092857142857);
    }


}