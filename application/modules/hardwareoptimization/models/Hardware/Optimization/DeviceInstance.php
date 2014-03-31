<?php

/**
 * Class Hardwareoptimization_Model_Hardware_Optimization_Quote
 */
class Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance extends My_Model_Abstract
{
    const ACTION_DNR     = 'Do Not Repair';
    const ACTION_KEEP    = 'Keep';
    const ACTION_REPLACE = 'Replace';
    const ACTION_RETIRE  = 'Retire';
    const ACTION_LEASED  = 'Leased';

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
     * @var Proposalgen_Model_DeviceInstance
     */
    protected $_deviceInstance;

    /**
     * @var Hardwareoptimization_Model_Hardware_Optimization
     */
    protected $_hardwareOptimization;

    /**
     * @var Proposalgen_Model_MasterDevice
     */
    protected $_masterDevice;

    /**
     * @var Hardwareoptimization_Model_Device_Swap_Reason
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
        return array(
            "deviceInstanceId"       => $this->deviceInstanceId,
            "hardwareOptimizationId" => $this->hardwareOptimizationId,
            "action"                 => $this->action,
            "masterDeviceId"         => $this->masterDeviceId,
            "deviceSwapReasonId"     => $this->deviceSwapReasonId,
        );
    }

    /**
     * Gets the associated hardware optimization
     *
     * @return Hardwareoptimization_Model_Hardware_Optimization
     */
    public function getHardwareOptimization ()
    {
        if (!isset($this->_hardwareOptimization) && $this->hardwareOptimizationId > 0)
        {
            $this->_hardwareOptimization = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->find($this->hardwareOptimizationId);
        }

        return $this->_hardwareOptimization;
    }

    /**
     * Sets the hardware optimization
     *
     * @param Hardwareoptimization_Model_Hardware_Optimization $hardwareOptimization
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
     * @return Proposalgen_Model_DeviceInstance
     */
    public function getDeviceInstance ()
    {
        if (!isset($this->_deviceInstance) && $this->deviceInstanceId > 0)
        {
            $this->_deviceInstance = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->find($this->deviceInstanceId);
        }

        return $this->_deviceInstance;
    }

    /**
     * Sets the device instance
     *
     * @param Proposalgen_Model_DeviceInstance $deviceInstance
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
     * @return Proposalgen_Model_MasterDevice
     */
    public function getMasterDevice ()
    {
        if (!isset($this->_masterDevice) && $this->masterDeviceId > 0)
        {
            $this->_masterDevice = Proposalgen_Model_Mapper_MasterDevice::getInstance()->find($this->masterDeviceId);
        }

        return $this->_masterDevice;
    }

    /**
     * Sets the master device
     *
     * @param Proposalgen_Model_MasterDevice $masterDevice
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
     * @return Hardwareoptimization_Model_Device_Swap_Reason
     */
    public function getDeviceSwapReason ()
    {
        if (!isset($this->_deviceSwapReason) && $this->deviceSwapReasonId > 0)
        {
            $this->_deviceSwapReason = Hardwareoptimization_Model_Mapper_Device_Swap_Reason::getInstance()->find($this->deviceSwapReasonId);
        }

        return $this->_deviceSwapReason;
    }

    /**
     * Sets the device swap reason
     *
     * @param Hardwareoptimization_Model_Device_Swap_Reason $deviceSwapReason
     *
     * @return $this
     */
    public function setDeviceSwapReason ($deviceSwapReason)
    {
        $this->_deviceSwapReason = $deviceSwapReason;

        return $this;
    }
}