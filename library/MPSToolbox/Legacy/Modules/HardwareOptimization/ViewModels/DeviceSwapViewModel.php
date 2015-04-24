<?php

namespace MPSToolbox\Legacy\Modules\HardwareOptimization\ViewModels;

use MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers\DeviceSwapMapper;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\DeviceSwapModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\CostPerPageSettingModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerVendorRankingSetModel;

/**
 * Class DeviceSwapViewModel
 *
 * @package MPSToolbox\Legacy\Modules\HardwareOptimization\ViewModels
 */
class DeviceSwapViewModel
{
    /**
     * @var array
     */
    protected $_replacementModelsByType;

    /**
     * @var CostPerPageSettingModel
     */
    protected $_costPerPageSetting;

    /**
     * @var DeviceSwapModel[]
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
            $this->_replacementModelsByType[MasterDeviceModel::$TonerConfigNames[MasterDeviceModel::DEVICE_TYPE_MONO]]      = [];
            $this->_replacementModelsByType[MasterDeviceModel::$TonerConfigNames[MasterDeviceModel::DEVICE_TYPE_MONO_MFP]]  = [];
            $this->_replacementModelsByType[MasterDeviceModel::$TonerConfigNames[MasterDeviceModel::DEVICE_TYPE_COLOR]]     = [];
            $this->_replacementModelsByType[MasterDeviceModel::$TonerConfigNames[MasterDeviceModel::DEVICE_TYPE_COLOR_MFP]] = [];

            $costPerPageSetting                         = new CostPerPageSettingModel();
            $costPerPageSetting->adminCostPerPage       = 0;
            $oemTonerRankingSet                         = new TonerVendorRankingSetModel();
            $costPerPageSetting->monochromeTonerRankSet = $oemTonerRankingSet;
            $costPerPageSetting->colorTonerRankSet      = $oemTonerRankingSet;

            foreach ($this->getReplacementDevices() as $replacementDevice)
            {
                $data         = [];
                $masterDevice = $replacementDevice->getMasterDevice();

                $data['isColor']       = $masterDevice->isColor();
                $data['manufacturer']  = $masterDevice->getManufacturer()->fullname;
                $data['deviceName']    = $masterDevice->modelName;
                $data['monochromeCPP'] = $masterDevice->calculateCostPerPage($costPerPageSetting)->getCostOfInkAndTonerPerPage()->monochromeCostPerPage;
                $data['colorCPP']      = $masterDevice->calculateCostPerPage($costPerPageSetting)->getCostOfInkAndTonerPerPage()->colorCostPerPage;
                $data['minPageCount']  = $replacementDevice->minimumPageCount;
                $data['maxPageCount']  = $replacementDevice->maximumPageCount;

                $this->_replacementModelsByType[MasterDeviceModel::$TonerConfigNames[$replacementDevice->getMasterDevice()->getDeviceType()]] [] = $data;
            }
        }

        return $this->_replacementModelsByType;
    }


    /**
     * @return CostPerPageSettingModel
     */
    public function getCostPerPageSetting ()
    {
        if (!isset($this->_costPerPageSetting))
        {
            $this->_costPerPageSetting                         = new CostPerPageSettingModel();
            $this->_costPerPageSetting->adminCostPerPage       = 0;
            $oemTonerRankSet                                   = new TonerVendorRankingSetModel();
            $this->_costPerPageSetting->monochromeTonerRankSet = $oemTonerRankSet;
            $this->_costPerPageSetting->colorTonerRankSet      = $oemTonerRankSet;
        }

        return $this->_costPerPageSetting;
    }

    /**
     * @return DeviceSwapModel[]
     */
    public function getReplacementDevices ()
    {
        if (!isset($this->_replacementDevices))
        {
            $this->_replacementDevices = DeviceSwapMapper::getInstance()->fetchAll();
        }

        return $this->_replacementDevices;
    }
}