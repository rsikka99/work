<?php
/**
 * Class Proposalgen_Service_DeviceMapping_MasterMatchup
 */
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
    }

    /**
     * MAP IT™ attempts to find a suitable master device by using the master matchup table. If there is no entry in the
     * matchup table it will return FALSE®
     *
     * @param Proposalgen_Model_DeviceInstance $deviceInstance
     *
     * @return bool|int The master device id, or false if it could not map
     */
    public function mapIt (Proposalgen_Model_DeviceInstance $deviceInstance)
    {
        $masterDeviceId = false;
        $rmsProviderId  = $deviceInstance->getRmsUploadRow()->rmsProviderId;
        $rmsModelId     = $deviceInstance->getRmsUploadRow()->rmsModelId;

        $rmsMasterMatchup = $this->_rmsMasterMatchupMapper->find(array($rmsProviderId, $rmsModelId));
        if ($rmsMasterMatchup)
        {
            $masterDeviceId = $rmsMasterMatchup->masterDeviceId;
        }

        return $masterDeviceId;
    }
}