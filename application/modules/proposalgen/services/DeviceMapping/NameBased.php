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

        $manufacturer = $this->_manufacturerMapper->searchManufacturersByName();

        // TODO: Implement named based mapping


        return $isMapped;
    }
}