<?php

namespace MPSToolbox\Legacy\Modules\HardwareOptimization\Models;

use ArrayObject;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers\DeviceSwapReasonMapper;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers\HardwareOptimizationMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DeviceInstanceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\MasterDeviceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel;
use My_Model_Abstract;

/**
 * Class HardwareOptimizationDeviceInstanceModel
 *
 * @package MPSToolbox\Legacy\Modules\HardwareOptimization\Models
 */
class HardwareOptimizationDeviceInstanceModel extends My_Model_Abstract
{
    const ACTION_DNR     = 'Do Not Repair';
    const ACTION_KEEP    = 'Keep';
    const ACTION_REPLACE = 'Replace';
    const ACTION_RETIRE  = 'Retire';
    const ACTION_UPGRADE = 'Upgrade';

    /**
     * @var int
     */
    public $deviceInstanceId;

    /**
     * @var int
     */
    public $hardwareOptimizationId;

    /**
     * @var string
     */
    public $action;

    /**
     * @var int
     */
    public $masterDeviceId;

    /**
     * @var int
     */
    public $deviceSwapReasonId;

    /**
     * @var DeviceInstanceModel
     */
    protected $_deviceInstance;

    /**
     * @var HardwareOptimizationModel
     */
    protected $_hardwareOptimization;

    /**
     * @var MasterDeviceModel
     */
    protected $_masterDevice;

    /**
     * @var DeviceSwapReasonModel
     */
    protected $_deviceSwapReason;


    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->deviceInstanceId) && !is_null($params->deviceInstanceId))
        {
            $this->deviceInstanceId = $params->deviceInstanceId;
        }

        if (isset($params->hardwareOptimizationId) && !is_null($params->hardwareOptimizationId))
        {
            $this->hardwareOptimizationId = $params->hardwareOptimizationId;
        }

        if (isset($params->action) && !is_null($params->action))
        {
            $this->action = $params->action;
        }

        if (isset($params->masterDeviceId) && !is_null($params->masterDeviceId))
        {
            $this->masterDeviceId = $params->masterDeviceId;
        }

        if (isset($params->deviceSwapReasonId) && !is_null($params->deviceSwapReasonId))
        {
            $this->deviceSwapReasonId = $params->deviceSwapReasonId;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return [
            "deviceInstanceId"       => $this->deviceInstanceId,
            "hardwareOptimizationId" => $this->hardwareOptimizationId,
            "action"                 => $this->action,
            "masterDeviceId"         => $this->masterDeviceId,
            "deviceSwapReasonId"     => $this->deviceSwapReasonId,
        ];
    }

    /**
     * Gets the associated hardware optimization
     *
     * @return HardwareOptimizationModel
     */
    public function getHardwareOptimization ()
    {
        if (!isset($this->_hardwareOptimization) && $this->hardwareOptimizationId > 0)
        {
            $this->_hardwareOptimization = HardwareOptimizationMapper::getInstance()->find($this->hardwareOptimizationId);
        }

        return $this->_hardwareOptimization;
    }

    /**
     * Sets the hardware optimization
     *
     * @param HardwareOptimizationModel $hardwareOptimization
     *
     * @return $this
     */
    public function setHardwareOptimization ($hardwareOptimization)
    {
        $this->_hardwareOptimization = $hardwareOptimization;

        return $this;
    }

    /**
     * Gets the associated device instance
     *
     * @return DeviceInstanceModel
     */
    public function getDeviceInstance ()
    {
        if (!isset($this->_deviceInstance) && $this->deviceInstanceId > 0)
        {
            $this->_deviceInstance = DeviceInstanceMapper::getInstance()->find($this->deviceInstanceId);
        }

        return $this->_deviceInstance;
    }

    /**
     * Sets the device instance
     *
     * @param DeviceInstanceModel $deviceInstance
     *
     * @return $this
     */
    public function setDeviceInstance ($deviceInstance)
    {
        $this->_deviceInstance = $deviceInstance;

        return $this;
    }

    /**
     * Gets the associated master device
     *
     * @return MasterDeviceModel
     */
    public function getMasterDevice ()
    {
        if (!isset($this->_masterDevice) && $this->masterDeviceId > 0)
        {
            $this->_masterDevice = MasterDeviceMapper::getInstance()->findForReports($this->masterDeviceId,
                $this->getHardwareOptimization()->dealerId,
                $this->getHardwareOptimization()->getClient()->getClientSettings()->proposedFleetSettings->defaultMonochromeLaborCostPerPage,
                $this->getHardwareOptimization()->getClient()->getClientSettings()->proposedFleetSettings->defaultMonochromePartsCostPerPage);
        }

        return $this->_masterDevice;
    }

    /**
     * Sets the master device
     *
     * @param MasterDeviceModel $masterDevice
     *
     * @return $this
     */
    public function setMasterDevice ($masterDevice)
    {
        $this->_masterDevice = $masterDevice;

        return $this;
    }

    /**
     * Gets the associated device swap reason
     *
     * @return DeviceSwapReasonModel
     */
    public function getDeviceSwapReason ()
    {
        if (!isset($this->_deviceSwapReason))
        {
            if ($this->deviceSwapReasonId > 0)
            {
                $this->_deviceSwapReason = DeviceSwapReasonMapper::getInstance()->find($this->deviceSwapReasonId);
            }

            if (!$this->_deviceSwapReason instanceof DeviceSwapReasonModel)
            {
                $this->_deviceSwapReason         = new DeviceSwapReasonModel();
                $this->_deviceSwapReason->reason = 'Please make sure you have at least 1 default reason per category and re-analyze your fleet.';
            }
        }

        return $this->_deviceSwapReason;
    }

    /**
     * Sets the device swap reason
     *
     * @param DeviceSwapReasonModel $deviceSwapReason
     *
     * @return $this
     */
    public function setDeviceSwapReason ($deviceSwapReason)
    {
        $this->_deviceSwapReason = $deviceSwapReason;

        return $this;
    }
}