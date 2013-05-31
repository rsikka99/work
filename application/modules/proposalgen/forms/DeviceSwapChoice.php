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
     * @var Proposalgen_Model_DeviceInstance[]
     */
    protected $blackReplacementDevices;

    /**
     *
     * @var Proposalgen_Model_DeviceInstance[]
     */
    protected $blackMfpReplacementDevices;

    /**
     *
     * @var Proposalgen_Model_DeviceInstance[]
     */
    protected $colorReplacementDevices;

    /**
     *
     * @var Proposalgen_Model_DeviceInstance[]
     */
    protected $colorMfpReplacementDevices;

    /**
     * @param null $devices
     * @param      $dealerId               int
     * @param      $hardwareOptimizationId int
     * @param null $options
     */
    public function __construct ($devices, $dealerId, $hardwareOptimizationId, $options = null)
    {
        $this->_devices                = $devices;
        $this->_dealerId               = $dealerId;
        $this->_hardwareOptimizationId = $hardwareOptimizationId;
        parent::__construct($options);
    }

    public function init ()
    {

        /* @var $deviceInstance Proposalgen_Model_DeviceInstance */
        foreach ($this->_devices as $deviceInstance)
        {
            // Get replacement devices for each type of device
            if ($deviceInstance->getAction() !== 'Retire')
            {
                if ($deviceInstance->getMasterDevice()->tonerConfigId === Proposalgen_Model_TonerConfig::BLACK_ONLY)
                {
                    if ($deviceInstance->getMasterDevice()->isCopier)
                    {
                        $replacementDevices = $this->getBlackMfpReplacementDevices($deviceInstance->getPageCounts()->getCombined()->getMonthly());
                    }
                    else
                    {
                        $replacementDevices = $this->getBlackReplacementDevices($deviceInstance->getPageCounts()->getCombined()->getMonthly());
                    }
                }
                else
                {
                    if ($deviceInstance->getMasterDevice()->isCopier)
                    {
                        $replacementDevices = $this->getColorMfpReplacementDevices($deviceInstance->getPageCounts()->getCombined()->getMonthly());
                    }
                    else
                    {
                        $replacementDevices = $this->getColorReplacementDevices($deviceInstance->getPageCounts()->getCombined()->getMonthly());
                    }
                }
                $deviceInstanceReplacementMasterDevice = $deviceInstance->getReplacementMasterDeviceForHardwareOptimization($this->_hardwareOptimizationId);
            }
            else
            {
                $replacementDevices                    = array();
                $deviceInstanceReplacementMasterDevice = null;
            }

            $elementType = 'deviceInstance_';

            $replacementDevices[0] = $deviceInstance->getAction();
            // Create an element for each device Device list per manufacturer
            $deviceElement = $this->createElement('select', $elementType . $deviceInstance->id, array(
                                                                                                     'label'   => 'Device: ',
                                                                                                     'attribs' => array(
                                                                                                         'style' => 'width: 100%'
                                                                                                     ),
                                                                                                     'value'   => ($deviceInstanceReplacementMasterDevice) ? $deviceInstanceReplacementMasterDevice->id : 0
                                                                                                ));

            $this->addElement($deviceElement);

            $elementType = 'deviceInstanceReason_';

            if ($deviceInstance->getReplacementMasterDevice() instanceof Proposalgen_Model_MasterDevice)
            {
                $deviceReasonElement = $this->createElement('select', $elementType . $deviceInstance->id, array(
                                                                                                               'label'   => ': ',
                                                                                                               'attribs' => array(
                                                                                                                   'style' => 'width: 100%'
                                                                                                               ),
                                                                                                          ));
                $this->addElement($deviceReasonElement);
                $deviceReasonElement->setMultiOptions($this->getDeviceSwapsByCategory(Hardwareoptimization_Model_Device_Swap_Reason_Category::HAS_REPLACEMENT));
            }
            else if ($deviceInstance->getAction() === Proposalgen_Model_DeviceInstance::ACTION_REPLACE)
            {
                $deviceReasonElement = $this->createElement('select', $elementType . $deviceInstance->id, array(
                                                                                                               'label'   => ': ',
                                                                                                               'attribs' => array(
                                                                                                                   'style' => 'width: 100%'
                                                                                                               ),
                                                                                                          ));
                $this->addElement($deviceReasonElement);
                $deviceReasonElement->setMultiOptions($this->getDeviceSwapsByCategory(Hardwareoptimization_Model_Device_Swap_Reason_Category::FLAGGED));
            }

            /*
             * If the master device device does not exist in our array we need to add it as it is replaced anyways....
             * o.O
             */
            if ($deviceInstanceReplacementMasterDevice && !array_key_exists($deviceInstanceReplacementMasterDevice->id, $replacementDevices))
            {
                $replacementDevices [$deviceInstanceReplacementMasterDevice->id] = $deviceInstanceReplacementMasterDevice->getManufacturer()->fullname . " " . $deviceInstanceReplacementMasterDevice->modelName;
            }
            $deviceElement->setMultiOptions($replacementDevices);
        }
    }

    /**
     * Getter for $blackReplacementDevices
     *
     * @param $monthlyPageCounts
     *
     * @return Proposalgen_Model_DeviceInstance []
     */
    public function getBlackReplacementDevices ($monthlyPageCounts)
    {
        $deviceArray        = array();
        $deviceArray [0]    = 'Keep';
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
     * @return Proposalgen_Model_DeviceInstance []
     */
    public function getBlackMfpReplacementDevices ($monthlyPageCounts)
    {
        $deviceArray        = array();
        $deviceArray [0]    = 'Keep';
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
     * @return Proposalgen_Model_DeviceInstance []
     */
    public function getColorReplacementDevices ($monthlyPageCounts)
    {
        $deviceArray        = array();
        $deviceArray [0]    = 'Keep';
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
     * @return Proposalgen_Model_DeviceInstance []
     */
    public function getColorMfpReplacementDevices ($monthlyPageCounts)
    {
        $deviceArray        = array();
        $deviceArray [0]    = 'Keep';
        $replacementDevices = Hardwareoptimization_Model_Mapper_Device_Swap::getInstance()->getColorMfpReplacementDevices($this->_dealerId);
        foreach ($replacementDevices as $replacementDevice)
        {
            if ($monthlyPageCounts < $replacementDevice->maximumPageCount && $monthlyPageCounts > $replacementDevice->minimumPageCount)
            {
                $masterDevice = Proposalgen_Model_Mapper_MasterDevice::getInstance()->find($replacementDevice->getMasterDevice()->id);
                if ($masterDevice->tonerConfigId !== Proposalgen_Model_TonerConfig::BLACK_ONLY && $masterDevice->isCopier)
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
            if ($reason->id !== $defaultReason->deviceSwapReasonId)
            {

                $reasonArray[$reason->id] = $reason->reason;
            }
        }

        return $reasonArray;
    }
}