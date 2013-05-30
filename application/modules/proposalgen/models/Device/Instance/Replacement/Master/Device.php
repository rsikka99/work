<?php
/**
 * Class Proposalgen_Model_Device_Instance_Replacement_Master_Device
 */
class Proposalgen_Model_Device_Instance_Replacement_Master_Device extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $deviceInstanceId;

    /**
     * @var int
     */
    public $masterDeviceId;

    /**
     * @var int
     */
    public $hardwareOptimizationId;

    /**
     * @var Proposalgen_Model_DeviceInstance
     */
    protected $_deviceInstance;

    /**
     * @var Proposalgen_Model_MasterDevice
     */
    protected $_masterDevice;

    /**
     * @var Proposalgen_Model_MasterDevice
     */
    protected $_reportMasterDevice;


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

        if (isset($params->masterDeviceId) && !is_null($params->masterDeviceId))
        {
            $this->masterDeviceId = $params->masterDeviceId;
        }

        if (isset($params->hardwareOptimizationId) && !is_null($params->hardwareOptimizationId))
        {
            $this->hardwareOptimizationId = $params->hardwareOptimizationId;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "deviceInstanceId"       => $this->deviceInstanceId,
            "masterDeviceId"         => $this->masterDeviceId,
            "hardwareOptimizationId" => $this->hardwareOptimizationId,
        );
    }

    /**
     * Gets the device instance
     *
     * @return Proposalgen_Model_DeviceInstance
     */
    public function getDeviceInstance ()
    {
        if (!isset($this->_deviceInstance))
        {
            $this->_deviceInstance = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->find($this->deviceInstanceId);
        }

        return $this->_deviceInstance;
    }

    /**
     *  Sets the device instance
     *
     * @param Proposalgen_Model_DeviceInstance $deviceInstance
     *
     * @return Proposalgen_Model_Device_Instance_Master_Device
     */
    public function setDeviceInstance ($deviceInstance)
    {
        $this->_deviceInstance = $deviceInstance;

        return $this;
    }

    /**
     * Gets the master device
     *
     * @return Proposalgen_Model_MasterDevice
     */
    public function getMasterDevice ()
    {
        if (!isset($this->_masterDevice))
        {
            $this->_masterDevice = Proposalgen_Model_Mapper_MasterDevice::getInstance()->find($this->masterDeviceId);
        }

        return $this->_masterDevice;
    }

    /**
     *  Sets the master device
     *
     * @param Proposalgen_Model_MasterDevice $masterDevice
     *
     * @return Proposalgen_Model_Device_Instance_Master_Device
     */
    public function setMasterDevice ($masterDevice)
    {
        $this->_masterDevice = $masterDevice;

        return $this;
    }

    public function getMasterDeviceForReports ($dealerId)
    {
        if (!isset($this->_reportMasterDevice))
        {
            $this->_reportMasterDevice = Proposalgen_Model_Mapper_MasterDevice::getInstance()->findForReports($this->masterDeviceId, $dealerId);
        }

        return $this->_reportMasterDevice;
    }
}