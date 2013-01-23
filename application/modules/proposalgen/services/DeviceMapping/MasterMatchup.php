<?php
class Proposalgen_Service_DeviceMapping_MasterMatchup extends Proposalgen_Service_DeviceMapping_Abstract
{
    /**
     * @var Proposalgen_Model_Mapper_Rms_Master_Matchup
     */
    protected $_rmsMasterMatchupMapper;

    /**
     * Default Constructor
     */
    public function __construct ()
    {
        $this->_rmsMasterMatchupMapper = Proposalgen_Model_Mapper_Rms_Master_Matchup::getInstance();

        parent::__construct();
    }

    /**
     * MAP IT™ attempts to map a device instance to a master device by using the master matchup table. If there is no entry in the
     * matchup table it will return FALSE®
     *
     * @param Proposalgen_Model_DeviceInstance $deviceInstance
     *
     * @return bool
     */
    public function mapIt (Proposalgen_Model_DeviceInstance $deviceInstance)
    {
        $isMapped      = false;
        $rmsProviderId = $deviceInstance->getRmsUploadRow()->rmsProviderId;
        $rmsModelId    = $deviceInstance->getRmsUploadRow()->rmsModelId;

        $rmsMasterMatchup = $this->_rmsMasterMatchupMapper->find(array($rmsProviderId, $rmsModelId));
        if ($rmsMasterMatchup)
        {
            $deviceInstanceMasterDevice                   = new Proposalgen_Model_Device_Instance_Master_Device();
            $deviceInstanceMasterDevice->deviceInstanceId = $deviceInstance->id;
            $deviceInstanceMasterDevice->masterDeviceId   = $rmsMasterMatchup->masterDeviceId;

            $this->_deviceInstanceMasterDeviceMapper->insert($deviceInstanceMasterDevice);

            $isMapped = true;
        }

        return $isMapped;
    }
}