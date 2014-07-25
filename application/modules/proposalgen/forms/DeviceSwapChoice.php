<?php

/**
 * Class Proposalgen_Form_DeviceSwapChoice
 */
class Proposalgen_Form_DeviceSwapChoice extends Twitter_Bootstrap_Form
{
    const DEVICE_TYPE_MONO      = 0;
    const DEVICE_TYPE_MONO_MFP  = 1;
    const DEVICE_TYPE_COLOR     = 2;
    const DEVICE_TYPE_COLOR_MFP = 3;

    /**
     * @var Proposalgen_Model_DeviceInstance[]
     */
    protected $_devices;

    /**
     * @var int
     */
    protected $_dealerId;

    /**
     * @var int
     */
    protected $_hardwareOptimizationId;

    /**
     *
     * @var Hardwareoptimization_Model_Device_Swap[]
     */
    protected $blackReplacementDevices;

    /**
     *
     * @var Hardwareoptimization_Model_Device_Swap[]
     */
    protected $blackMfpReplacementDevices;

    /**
     *
     * @var Hardwareoptimization_Model_Device_Swap[]
     */
    protected $colorReplacementDevices;

    /**
     *
     * @var Hardwareoptimization_Model_Device_Swap[]
     */
    protected $colorMfpReplacementDevices;

    /**
     * @param Proposalgen_Model_DeviceInstance[] $devices
     * @param int                                $dealerId
     * @param int                                $hardwareOptimizationId
     * @param null|array                         $options
     */
    public function __construct ($devices, $dealerId, $hardwareOptimizationId, $options = null)
    {
        $this->_devices                = $devices;
        $this->_dealerId               = $dealerId;
        $this->_hardwareOptimizationId = $hardwareOptimizationId;
        parent::__construct($options);
    }

    /**
     * Initializes the form
     */
    public function init ()
    {
        foreach ($this->_devices as $deviceInstance)
        {
            /**
             *
             * Get the proper replacement device list
             *
             */
            if ($deviceInstance->getMasterDevice()->tonerConfigId === Proposalgen_Model_TonerConfig::BLACK_ONLY)
            {
                if ($deviceInstance->getMasterDevice()->isMfp())
                {
                    $replacementDevices = $this->getBlackMfpReplacementDevices($deviceInstance->getPageCounts()->getCombinedPageCount()->getMonthly());
                }
                else
                {
                    $replacementDevices = $this->getBlackReplacementDevices($deviceInstance->getPageCounts()->getCombinedPageCount()->getMonthly());
                }
            }
            else
            {
                if ($deviceInstance->getMasterDevice()->isMfp())
                {
                    $replacementDevices = $this->getColorMfpReplacementDevices($deviceInstance->getPageCounts()->getCombinedPageCount()->getMonthly());
                }
                else
                {
                    $replacementDevices = $this->getColorReplacementDevices($deviceInstance->getPageCounts()->getCombinedPageCount()->getMonthly());
                }
            }


            /**
             *
             * Setup the dropdown for actions and replacements
             *
             */
            $hardwareOptimizationDeviceInstance = $deviceInstance->getHardwareOptimizationDeviceInstance($this->_hardwareOptimizationId);

            $replacementDeviceOptions = array(
                Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance::ACTION_KEEP   => Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance::ACTION_KEEP,
                Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance::ACTION_RETIRE => Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance::ACTION_RETIRE . " and Migrate",
                Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance::ACTION_DNR    => Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance::ACTION_DNR,
            );

            /**
             * Only display the replacement option if we have devices available.
             */
            if (count($replacementDevices) > 0)
            {
                $replacementDeviceOptions["Replace With:"] = $replacementDevices;
            }

            // Eligible replacement master devices changed from time to time. Old selections should still show up for the sake of usability.
            if ($hardwareOptimizationDeviceInstance->masterDeviceId > 0 && !array_key_exists($hardwareOptimizationDeviceInstance->masterDeviceId, $replacementDevices['Replace With:']))
            {
                $replacementMasterDevice = $hardwareOptimizationDeviceInstance->getMasterDevice();
                if ($replacementMasterDevice instanceof Proposalgen_Model_MasterDevice)
                {
                    $replacementDevices [$replacementMasterDevice->id] = $replacementMasterDevice->getManufacturer()->fullname . " " . $replacementMasterDevice->modelName;
                }
            }

            $this->addElement('select', 'deviceInstance_' . $deviceInstance->id, array(
                'label'        => 'Device: ',
                'attribs'      => array(
                    'style'                   => 'width: 100%',
                    'data-device-instance-id' => $deviceInstance->id,
                ),
                'value'        => ($hardwareOptimizationDeviceInstance->masterDeviceId > 0) ? $hardwareOptimizationDeviceInstance->masterDeviceId : $hardwareOptimizationDeviceInstance->action,
                'multiOptions' => $replacementDeviceOptions,
            ));


            /**
             *
             * Setup the dropdown the replacement reason
             *
             */
            $deviceSwapReason = $hardwareOptimizationDeviceInstance->getDeviceSwapReason();

            $reasons = false;

            if ($hardwareOptimizationDeviceInstance->action == Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance::ACTION_REPLACE && $hardwareOptimizationDeviceInstance->masterDeviceId > 0)
            {
                $reasons = $this->getDeviceSwapsByCategory(Hardwareoptimization_Model_Device_Swap_Reason_Category::HAS_REPLACEMENT);
            }
            else if ($hardwareOptimizationDeviceInstance->action == Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance::ACTION_DNR)
            {
                $reasons = $this->getDeviceSwapsByCategory(Hardwareoptimization_Model_Device_Swap_Reason_Category::FLAGGED);
            }

            if ($deviceSwapReason->id > 0 && !in_array($deviceSwapReason->id, $reasons))
            {
                $reasons[$deviceSwapReason->id] = $deviceSwapReason->reason;
            }

            if ($reasons !== false)
            {
                $this->addElement('select', 'deviceInstanceReason_' . $deviceInstance->id, array(
                    'label'        => ': ',
                    'attribs'      => array(
                        'style'                   => 'width: 100%',
                        'data-device-instance-id' => $deviceInstance->id,
                    ),
                    'multiOptions' => $reasons,
                    'value'        => ($deviceSwapReason->id) ? $deviceSwapReason->id : 0,
                ));
            }


        }
    }

    /**
     * Getter for $blackReplacementDevices
     *
     * @param $monthlyPageCounts
     *
     * @return Hardwareoptimization_Model_Device_Swap []
     */
    public function getBlackReplacementDevices ($monthlyPageCounts)
    {
        $deviceArray        = array();
        $replacementDevices = Hardwareoptimization_Model_Mapper_Device_Swap::getInstance()->getBlackReplacementDevices($this->_dealerId);
        foreach ($replacementDevices as $replacementDevice)
        {
            if ($monthlyPageCounts < $replacementDevice->maximumPageCount && $monthlyPageCounts > $replacementDevice->minimumPageCount)
            {
                $masterDevice                                            = Proposalgen_Model_Mapper_MasterDevice::getInstance()->find($replacementDevice->getMasterDevice()->id);
                $deviceArray [$replacementDevice->getMasterDevice()->id] = $masterDevice->getManufacturer()->fullname . ' ' . $masterDevice->modelName;
            }
        }

        $this->blackReplacementDevices = $deviceArray;

        return $this->blackReplacementDevices;
    }

    /**
     * Getter for $blackMfpReplacementDevices
     *
     * @param $monthlyPageCounts
     *
     * @return Hardwareoptimization_Model_Device_Swap []
     */
    public function getBlackMfpReplacementDevices ($monthlyPageCounts)
    {
        $deviceArray        = array();
        $replacementDevices = Hardwareoptimization_Model_Mapper_Device_Swap::getInstance()->getBlackMfpReplacementDevices($this->_dealerId);
        foreach ($replacementDevices as $replacementDevice)
        {
            if ($monthlyPageCounts < $replacementDevice->maximumPageCount && $monthlyPageCounts > $replacementDevice->minimumPageCount)
            {
                $masterDevice                                            = Proposalgen_Model_Mapper_MasterDevice::getInstance()->find($replacementDevice->getMasterDevice()->id);
                $deviceArray [$replacementDevice->getMasterDevice()->id] = $masterDevice->getManufacturer()->fullname . ' ' . $masterDevice->modelName;
            }
        }

        $this->blackMfpReplacementDevices = $deviceArray;


        return $this->blackMfpReplacementDevices;
    }

    /**
     * Getter for $colorReplacementDevices
     *
     * @param $monthlyPageCounts
     *
     * @return Hardwareoptimization_Model_Device_Swap []
     */
    public function getColorReplacementDevices ($monthlyPageCounts)
    {
        $deviceArray        = array();
        $replacementDevices = Hardwareoptimization_Model_Mapper_Device_Swap::getInstance()->getColorReplacementDevices($this->_dealerId);
        foreach ($replacementDevices as $replacementDevice)
        {
            if ($monthlyPageCounts < $replacementDevice->maximumPageCount && $monthlyPageCounts > $replacementDevice->minimumPageCount)
            {
                $masterDevice = Proposalgen_Model_Mapper_MasterDevice::getInstance()->find($replacementDevice->getMasterDevice()->id);
                if ($masterDevice->tonerConfigId !== Proposalgen_Model_TonerConfig::BLACK_ONLY)
                {
                    $deviceArray [$replacementDevice->getMasterDevice()->id] = $masterDevice->getManufacturer()->fullname . ' ' . $masterDevice->modelName;
                }
            }
        }
        $this->colorReplacementDevices = $deviceArray;


        return $this->colorReplacementDevices;
    }

    /**
     * Getter for $colorMfpReplacementDevices
     *
     * @param $monthlyPageCounts
     *
     * @return Hardwareoptimization_Model_Device_Swap []
     */
    public function getColorMfpReplacementDevices ($monthlyPageCounts)
    {
        $deviceArray        = array();
        $replacementDevices = Hardwareoptimization_Model_Mapper_Device_Swap::getInstance()->getColorMfpReplacementDevices($this->_dealerId);
        foreach ($replacementDevices as $replacementDevice)
        {
            if ($monthlyPageCounts < $replacementDevice->maximumPageCount && $monthlyPageCounts > $replacementDevice->minimumPageCount)
            {
                $masterDevice = Proposalgen_Model_Mapper_MasterDevice::getInstance()->find($replacementDevice->getMasterDevice()->id);
                if ($masterDevice->tonerConfigId !== Proposalgen_Model_TonerConfig::BLACK_ONLY && $masterDevice->isMfp())
                {
                    $deviceArray [$replacementDevice->getMasterDevice()->id] = $masterDevice->getManufacturer()->fullname . ' ' . $masterDevice->modelName;
                }
            }
        }

        $this->colorMfpReplacementDevices = $deviceArray;


        return $this->colorMfpReplacementDevices;
    }

    /**
     * Gets all reasons by the dealer id on the form
     *
     * @param $categoryId
     *
     * @return array
     */
    public function getDeviceSwapsByCategory ($categoryId)
    {
        $reasonArray = array();
        // Get the default reason
        $defaultReason                                    = Hardwareoptimization_Model_Mapper_Device_Swap_Reason_Default::getInstance()->findDefaultByDealerId($categoryId, $this->_dealerId);
        $reasonArray [$defaultReason->deviceSwapReasonId] = Hardwareoptimization_Model_Mapper_Device_Swap_Reason::getInstance()->find($defaultReason->deviceSwapReasonId)->reason;

        foreach (Hardwareoptimization_Model_Mapper_Device_Swap_Reason::getInstance()->fetchAllByCategoryId($categoryId, $this->_dealerId) as $reason)
        {
            // Add the element to the array as long as it's not the default since that is already added
            if (!array_key_exists($reason->id, $reasonArray))
            {
                $reasonArray[$reason->id] = $reason->reason;
            }
        }

        return $reasonArray;
    }
}