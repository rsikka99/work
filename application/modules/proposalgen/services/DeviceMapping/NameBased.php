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
     * MAP IT™ attempts to map a device instance to a master device by using name matching techniques with the manufacturer and modelName. If it
     * cannot find a match it will return FALSE®
     *
     * @param Proposalgen_Model_DeviceInstance $deviceInstance
     *
     * @return bool
     */
    public function mapIt (Proposalgen_Model_DeviceInstance $deviceInstance)
    {
        $isMapped = false;

        $manufacturers = $this->_manufacturerMapper->searchByName($deviceInstance->getRmsUploadRow()->manufacturer);

        $manufacturer = null;
        if (count($manufacturers) === 1)
        {
            $manufacturer = $manufacturers[0];
        }

        $masterDevices = $this->_masterDeviceMapper->searchByModelName($deviceInstance->getRmsUploadRow()->modelName, $manufacturer);
        if (count($masterDevices) === 1)
        {
            $masterDevice                                 = $masterDevices[0];
            $deviceInstanceMasterDevice                   = new Proposalgen_Model_Device_Instance_Master_Device();
            $deviceInstanceMasterDevice->deviceInstanceId = $deviceInstance->id;
            $deviceInstanceMasterDevice->masterDeviceId   = $masterDevice->id;

            $this->_deviceInstanceMasterDeviceMapper->insert($deviceInstanceMasterDevice);

            // TODO: Create user matchup?

            $isMapped = true;
        }

        return $isMapped;
    }
}