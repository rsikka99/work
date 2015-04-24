<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Models;

use ArrayObject;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DeviceInstanceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\MasterDeviceMapper;
use My_Model_Abstract;

/**
 * Class DeviceInstanceMasterDeviceModel
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Models
 */
class DeviceInstanceMasterDeviceModel extends My_Model_Abstract
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
     * @var DeviceInstanceModel
     */
    protected $_deviceInstance;

    /**
     * @var MasterDeviceModel
     */
    protected $_masterDevice;


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

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return [
            "deviceInstanceId" => $this->deviceInstanceId,
            "masterDeviceId"   => $this->masterDeviceId,
        ];
    }

    /**
     * Gets the device instance
     *
     * @return DeviceInstanceModel
     */
    public function getDeviceInstance ()
    {
        if (!isset($this->_deviceInstance))
        {
            $this->_deviceInstance = DeviceInstanceMapper::getInstance()->find($this->deviceInstanceId);
        }

        return $this->_deviceInstance;
    }

    /**
     *  Sets the device instance
     *
     * @param DeviceInstanceModel $deviceInstance
     *
     * @return DeviceInstanceMasterDeviceModel
     */
    public function setDeviceInstance ($deviceInstance)
    {
        $this->_deviceInstance = $deviceInstance;

        return $this;
    }

    /**
     * Gets the master device
     *
     * @return MasterDeviceModel
     */
    public function getMasterDevice ()
    {
        if (!isset($this->_masterDevice))
        {
            $this->_masterDevice = MasterDeviceMapper::getInstance()->find($this->masterDeviceId);
        }

        return $this->_masterDevice;
    }

    /**
     *  Sets the master device
     *
     * @param MasterDeviceModel $masterDevice
     *
     * @return DeviceInstanceMasterDeviceModel
     */
    public function setMasterDevice ($masterDevice)
    {
        $this->_masterDevice = $masterDevice;

        return $this;
    }
}