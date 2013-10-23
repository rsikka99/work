<?php

class Proposalgen_Model_MasterDeviceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Proposalgen_Model_CostPerPageSetting
     */
    protected $_costPerPageSetting;

    /**
     * @var Proposalgen_Model_MasterDevice
     */
    protected $_masterDevice;

    /**
     * @param bool $createNew
     *
     * @return Proposalgen_Model_CostPerPageSetting
     */
    public function getCostPerPageSetting ($createNew = false)
    {
        if (!isset($this->_costPerPageSetting) || $createNew)
        {
            $this->_costPerPageSetting                         = new Proposalgen_Model_CostPerPageSetting();
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
     * @return Proposalgen_Model_MasterDevice
     */
    public function getMasterDevice ($createNew = false)
    {
        if (!isset($this->_masterDevice) || $createNew)
        {
            $this->_masterDevice = Proposalgen_Model_Mapper_MasterDevice::getInstance()->find(1);
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

        $costPerPage = $masterDevice->calculateCostPerPage($costPerPageSetting);

        $this->assertSame($costPerPage->monochromeCostPerPage, 0.020727272727273);
        $this->assertSame($costPerPage->colorCostPerPage, 0.10092857142857);
    }


}