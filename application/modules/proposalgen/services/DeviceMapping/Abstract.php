<?php
abstract class Proposalgen_Service_DeviceMapping_Abstract
{
    /**
     * @var Proposalgen_Model_Mapper_Device_Instance_Master_Device
     */
    protected $_deviceInstanceMasterDeviceMapper;

    public function __construct()
    {
        $this->_deviceInstanceMasterDeviceMapper = Proposalgen_Model_Mapper_Device_Instance_Master_Device::getInstance();
    }
}