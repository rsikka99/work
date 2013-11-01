<?php
/**
 * Class Admin_ViewModel_MemjetDeviceSwap
 */
class Admin_ViewModel_MemjetDeviceSwap
{
    /**
     * @var array
     */
    protected $_replacementModelsByType;

    /**
     * @var Proposalgen_Model_CostPerPageSetting
     */
    protected $_costPerPageSetting;

    /**
     * @var Admin_Model_Memjet_Device_Swap[]
     */
    protected $_replacementDevices;

    /**
     * Getter for _replacementModelsByType
     *
     * @return array
     */
    public function getReplacementModelsByType ()
    {
        if (!isset($this->_replacementModelsByType))
        {
            $this->_replacementModelsByType[Proposalgen_Model_MasterDevice::$TonerConfigNames[Proposalgen_Model_MasterDevice::DEVICE_TYPE_MONO]]      = array();
            $this->_replacementModelsByType[Proposalgen_Model_MasterDevice::$TonerConfigNames[Proposalgen_Model_MasterDevice::DEVICE_TYPE_MONO_MFP]]  = array();
            $this->_replacementModelsByType[Proposalgen_Model_MasterDevice::$TonerConfigNames[Proposalgen_Model_MasterDevice::DEVICE_TYPE_COLOR]]     = array();
            $this->_replacementModelsByType[Proposalgen_Model_MasterDevice::$TonerConfigNames[Proposalgen_Model_MasterDevice::DEVICE_TYPE_COLOR_MFP]] = array();

            $costPerPageSetting                         = new Proposalgen_Model_CostPerPageSetting();
            $costPerPageSetting->adminCostPerPage       = 0;
            $oemTonerRankingSet                         = new Proposalgen_Model_Toner_Vendor_Ranking_Set();
            $costPerPageSetting->monochromeTonerRankSet = $oemTonerRankingSet;
            $costPerPageSetting->colorTonerRankSet      = $oemTonerRankingSet;

            foreach ($this->getReplacementDevices() as $replacementDevice)
            {
                $data         = array();
                $masterDevice = $replacementDevice->getMasterDevice();

                $data['isColor']       = $masterDevice->isColor();
                $data['manufacturer']  = $masterDevice->getManufacturer()->fullname;
                $data['deviceName']    = $masterDevice->modelName;
                $data['monochromeCPP'] = $masterDevice->calculateCostPerPage($costPerPageSetting)->monochromeCostPerPage;
                $data['colorCPP']      = $masterDevice->calculateCostPerPage($costPerPageSetting)->colorCostPerPage;
                $data['minPageCount']  = $replacementDevice->minimumPageCount;
                $data['maxPageCount']  = $replacementDevice->maximumPageCount;

                $this->_replacementModelsByType[Proposalgen_Model_MasterDevice::$TonerConfigNames[$replacementDevice->getMasterDevice()->getDeviceType()]] [] = $data;
            }
        }

        return $this->_replacementModelsByType;
    }


    /**
     * @return Proposalgen_Model_CostPerPageSetting
     */
    public function getCostPerPageSetting ()
    {
        if (!isset($this->_costPerPageSetting))
        {
            $this->_costPerPageSetting                         = new Proposalgen_Model_CostPerPageSetting();
            $this->_costPerPageSetting->adminCostPerPage       = 0;
            $oemTonerRankSet                                   = new Proposalgen_Model_Toner_Vendor_Ranking_Set();
            $this->_costPerPageSetting->monochromeTonerRankSet = $oemTonerRankSet;
            $this->_costPerPageSetting->colorTonerRankSet      = $oemTonerRankSet;
        }

        return $this->_costPerPageSetting;
    }

    /**
     * @return Admin_Model_Memjet_Device_Swap[]
     */
    public function getReplacementDevices ()
    {
        if (!isset($this->_replacementDevices))
        {
            $this->_replacementDevices = Admin_Model_Mapper_Memjet_Device_Swap::getInstance()->fetchAll();
        }

        return $this->_replacementDevices;
    }


}