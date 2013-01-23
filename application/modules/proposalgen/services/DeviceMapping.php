<?php
class Proposalgen_Service_DeviceMapping
{
    /**
     * @var Proposalgen_Service_DeviceMapping_UserMatchup
     */
    protected $_userMatchupBasedMappingService;

    /**
     * @var Proposalgen_Service_DeviceMapping_MasterMatchup
     */
    protected $_masterMatchupBasedMappingService;

    /**
     * @var Proposalgen_Service_DeviceMapping_NameBased
     */
    protected $_nameBasedMappingService;


    /**
     * The default constructor. Initializes the various mapping services
     */
    public function __construct ()
    {
        $this->_userMatchupBasedMappingService   = new Proposalgen_Service_DeviceMapping_UserMatchup();
        $this->_masterMatchupBasedMappingService = new Proposalgen_Service_DeviceMapping_MasterMatchup();
        $this->_nameBasedMappingService          = new Proposalgen_Service_DeviceMapping_NameBased();
    }

    /**
     * Loops through the given Proposalgen_Model_DeviceInstance[] and attempts to MAP IT™ if the device is not already mapped.
     *
     * @param Proposalgen_Model_DeviceInstance[] $deviceInstances
     * @param int                                $userId
     * @param bool                               $useNameBasedMapping
     */
    public function mapDevices ($deviceInstances, $userId, $useNameBasedMapping = false)
    {
        foreach ($deviceInstances as $deviceInstance)
        {
            if (!$deviceInstance->useUserData)
            {
                if (!$deviceInstance->getIsMappedToMasterDevice())
                {
                    /*
                     * No Mapping, lets MAP IT™!
                     */
                    $this->_mapIt($deviceInstance, $userId, $useNameBasedMapping);
                }
            }
        }
    }

    /**
     * MAP IT™ takes a Proposalgen_Model_DeviceInstance and try to MAP IT™ to a Proposalgen_Model_MasterDevice. A Proposalgen_Model_DeviceInstance
     * must have have a Proposalgen_Model_Rms_Upload_Row attached to it that has an rmsModelId in order for User or Master matching to work.
     * If a Proposalgen_Model_DeviceInstance does not have a rmsModelId and TRUE® was passed to $useNamedBasedMapping it will use the
     * manufacturer and modelName to attempt mapping the Proposalgen_Model_DeviceInstance. Please note that named based mapping requires there to be only a
     * single Proposalgen_Model_MasterDevice result when we look it up, otherwise it will be up to the user to MAP IT™ themselves.
     *
     * @param Proposalgen_Model_DeviceInstance $deviceInstance
     * @param int                              $userId
     * @param bool                             $useNameBasedMapping
     *
     * @return bool Returns whether or not the device got mapped
     */
    protected function _mapIt (Proposalgen_Model_DeviceInstance $deviceInstance, $userId, $useNameBasedMapping)
    {
        $isMapped = false;
        $deviceInstance->id;
        $rmsUploadRow = $deviceInstance->getRmsUploadRow();

        $hasRmsModelId = ($rmsUploadRow->rmsModelId > 0);

        if ($hasRmsModelId)
        {
            // User Based
            $isMapped = $this->_userMatchupBasedMappingService->mapIt($deviceInstance, $userId);


            if (!$isMapped)
            {
                // Master Based
                $isMapped = $this->_masterMatchupBasedMappingService->mapIt($deviceInstance);
            }
        }

        if (!$isMapped && $useNameBasedMapping)
        {
            // Named based
            $isMapped = $this->_nameBasedMappingService->mapIt($deviceInstance);
        }

        return $isMapped;
    }
}