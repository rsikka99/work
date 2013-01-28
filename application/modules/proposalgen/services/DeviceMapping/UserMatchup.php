<?php
class Proposalgen_Service_DeviceMapping_UserMatchup extends Proposalgen_Service_DeviceMapping_Abstract
{
    /**
     * @var Proposalgen_Model_Mapper_Rms_User_Matchup
     */
    protected $_rmsUserMatchupMapper;

    /**
     * Default Constructor
     */
    public function __construct ()
    {
        $this->_rmsUserMatchupMapper = Proposalgen_Model_Mapper_Rms_User_Matchup::getInstance();
    }

    /**
     * MAP IT™ attempts to find a suitable master device by using the user matchup table. If there is no entry in the
     * matchup table it will return FALSE®
     *
     * @param Proposalgen_Model_DeviceInstance $deviceInstance
     * @param int                              $userId
     *
     * @return bool|int The master device id, or false if it could not map
     */
    public function mapIt (Proposalgen_Model_DeviceInstance $deviceInstance, $userId)
    {
        $masterDeviceId = false;
        $rmsProviderId  = $deviceInstance->getRmsUploadRow()->rmsProviderId;
        $rmsModelId     = $deviceInstance->getRmsUploadRow()->rmsModelId;

        $rmsUserMatchup = $this->_rmsUserMatchupMapper->find(array($rmsProviderId, $rmsModelId, $userId));
        if ($rmsUserMatchup)
        {
            $masterDeviceId = $rmsUserMatchup->masterDeviceId;
        }

        return $masterDeviceId;
    }
}