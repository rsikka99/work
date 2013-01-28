<?php
class Proposalgen_Service_DeviceMapping_NameBased extends Proposalgen_Service_DeviceMapping_Abstract
{
    /**
     * @var Proposalgen_Model_Mapper_Manufacturer
     */
    protected $_manufacturerMapper;

    /**
     * Default Constructor
     */
    public function __construct ()
    {
        $this->_manufacturerMapper = Proposalgen_Model_Mapper_Manufacturer::getInstance();
        $this->_masterDeviceMapper = Proposalgen_Model_Mapper_MasterDevice::getInstance();

        parent::__construct();
    }

    /**
     * MAP IT™ attempts to find a suitable master device by using name matching techniques with the manufacturer and modelName. If it
     * cannot find a match it will return FALSE®
     *
     * @param Proposalgen_Model_DeviceInstance $deviceInstance
     *
     * @return bool|int The master device id, or false if it could not map
     */
    public function mapIt (Proposalgen_Model_DeviceInstance $deviceInstance)
    {
        $masterDeviceId = false;

        $manufacturers = $this->_manufacturerMapper->searchByName($deviceInstance->getRmsUploadRow()->manufacturer);

        $manufacturerId = null;
        if (count($manufacturers) === 1)
        {
            $manufacturerId = $manufacturers[0]->id;
        }

        $masterDevices = $this->_masterDeviceMapper->searchByModelName($deviceInstance->getRmsUploadRow()->modelName, $manufacturerId);
        if (count($masterDevices) === 1)
        {
            $masterDeviceId = $masterDevices[0]->id;
        }

        return $masterDeviceId;
    }
}