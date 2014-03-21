<?php

/**
 * Class Memjetoptimization_Form_DeviceSwapChoice
 */
class Memjetoptimization_Form_DeviceSwapChoice extends Twitter_Bootstrap_Form
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
    protected $_memjetOptimizationId;

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
     *
     * @var Proposalgen_Model_DeviceInstance[]
     */
    protected $memjetBlackReplacementDevices;

    /**
     *
     * @var Proposalgen_Model_DeviceInstance[]
     */
    protected $memjetBlackMfpReplacementDevices;

    /**
     *
     * @var Proposalgen_Model_DeviceInstance[]
     */
    protected $memjetColorReplacementDevices;

    /**
     *
     * @var Proposalgen_Model_DeviceInstance[]
     */
    protected $memjetColorMfpReplacementDevices;

    /**
     * @var Proposalgen_Model_CostPerPageSetting
     */
    protected $_costPerPageSetting;

    /**
     * @param null                                 $devices
     * @param                                      $dealerId               int
     * @param                                      $memjetOptimizationId   int
     * @param Proposalgen_Model_CostPerPageSetting $costPerPageSetting
     * @param null                                 $options
     */
    public function __construct ($devices, $dealerId, $memjetOptimizationId, $costPerPageSetting, $options = null)
    {
        $this->_devices              = $devices;
        $this->_dealerId             = $dealerId;
        $this->_memjetOptimizationId = $memjetOptimizationId;
        $this->_costPerPageSetting   = $costPerPageSetting;
        parent::__construct($options);
    }

    public function init ()
    {

        /* @var $deviceInstance Proposalgen_Model_DeviceInstance */
        foreach ($this->_devices as $deviceInstance)
        {
            // Get replacement devices for each type of device
            if ($deviceInstance->getAction($this->_costPerPageSetting) !== 'Retire')
            {
                $memjetReplacementDevices              = $this->getAllMemjetReplacementDevices($deviceInstance->getPageCounts()->getCombinedPageCount()->getMonthly(), $deviceInstance);
                $deviceInstanceReplacementMasterDevice = $deviceInstance->getReplacementMasterDeviceForMemjetOptimization($this->_memjetOptimizationId);
            }
            else
            {
                $memjetReplacementDevices              = array();
                $deviceInstanceReplacementMasterDevice = null;
            }

            $elementType = 'deviceInstance_';

            $memjetReplacementDevices[0] = $deviceInstance->getAction($this->_costPerPageSetting);
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

            $deviceInstanceDeviceSwapReason = Memjetoptimization_Model_Mapper_Device_Instance_Device_Swap_Reason::getInstance()->find(array($this->_memjetOptimizationId, $deviceInstance->id));

            if ($deviceInstance->getMemjetReplacementMasterDevice($this->_memjetOptimizationId) instanceof Proposalgen_Model_MasterDevice)
            {
                $memjetReason   = $deviceInstance->getDefaultMemjetDeviceSwapReasonCategoryId($this->_memjetOptimizationId, $this->_costPerPageSetting);
                $reasonCategory = null;
                if ($memjetReason == Memjetoptimization_Model_Device_Swap_Reason_Category::FUNCTIONALITY_UPGRADE)
                {
                    $reasonCategory = Memjetoptimization_Model_Device_Swap_Reason_Category::FUNCTIONALITY_UPGRADE;
                }
                else
                {
                    $reasonCategory = Memjetoptimization_Model_Device_Swap_Reason_Category::HAS_REPLACEMENT;
                }

                $deviceReasonElement = $this->createElement('select', $elementType . $deviceInstance->id, array(
                    'label'   => ': ',
                    'attribs' => array(
                        'style' => 'width: 100%'
                    ),
                    'value'   => ($deviceInstanceDeviceSwapReason->deviceSwapReasonId) ? $deviceInstanceDeviceSwapReason->deviceSwapReasonId : 0
                ));
                $this->addElement($deviceReasonElement);
                $deviceReasonElement->setMultiOptions($this->getDeviceSwapsByCategory($reasonCategory));
            }
            else if ($deviceInstance->getAction($this->_costPerPageSetting) === Proposalgen_Model_DeviceInstance::ACTION_REPLACE)
            {
                $deviceReasonElement = $this->createElement('select', $elementType . $deviceInstance->id, array(
                    'label'   => ': ',
                    'attribs' => array(
                        'style' => 'width: 100%'
                    ),
                    'value'   => ($deviceInstanceDeviceSwapReason->deviceSwapReasonId) ? $deviceInstanceDeviceSwapReason->deviceSwapReasonId : 0
                ));
                $this->addElement($deviceReasonElement);
                $deviceReasonElement->setMultiOptions($this->getDeviceSwapsByCategory(Memjetoptimization_Model_Device_Swap_Reason_Category::FLAGGED));
            }

            /*
             * If the master device device does not exist in our array we need to add it as it is replaced anyways....
             * o.O
             */
            if ($deviceInstanceReplacementMasterDevice && !array_key_exists($deviceInstanceReplacementMasterDevice->id, $memjetReplacementDevices))
            {
                $replacementDevices [$deviceInstanceReplacementMasterDevice->id] = $deviceInstanceReplacementMasterDevice->getManufacturer()->fullname . " " . $deviceInstanceReplacementMasterDevice->modelName;
            }

            $deviceElement->setMultiOptions($memjetReplacementDevices);

        }
    }

    /**
     * Getter for $blackReplacementDevices
     *
     * @param float                            $monthlyPageCounts
     *
     * @param Proposalgen_Model_DeviceInstance $deviceInstance
     *
     * @return Proposalgen_Model_DeviceInstance []
     */
    public function getAllMemjetReplacementDevices ($monthlyPageCounts, $deviceInstance)
    {
        $deviceArray        = array();
        $deviceArray [0]    = 'Keep';
        $replacementDevices = Admin_Model_Mapper_Memjet_Device_Swap::getInstance()->fetchAllReplacements();
        foreach ($replacementDevices as $replacementDevice)
        {
            if ($monthlyPageCounts <= $replacementDevice->getDealerMaximumPageCount($this->_dealerId) && $monthlyPageCounts >= $replacementDevice->getDealerMinimumPageCount($this->_dealerId)
                && !($deviceInstance->getMasterDevice()->isColor() && $replacementDevice->getMasterDevice()->isColor() == false) // Do not allow trades where you lose color
                && !($deviceInstance->getMasterDevice()->isMfp() && $replacementDevice->getMasterDevice()->isMfp() == false)
                // Do not allow trades where you lose MFP
            )
            {
                $masterDevice                                            = Proposalgen_Model_Mapper_MasterDevice::getInstance()->find($replacementDevice->getMasterDevice()->id);
                $deviceArray [$replacementDevice->getMasterDevice()->id] = $masterDevice->getManufacturer()->fullname . ' ' . $masterDevice->modelName;
            }
        }

        $this->memjetBlackReplacementDevices = $deviceArray;

        return $this->memjetBlackReplacementDevices;
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
        $defaultReason                                    = Memjetoptimization_Model_Mapper_Device_Swap_Reason_Default::getInstance()->findDefaultByDealerId($categoryId, $this->_dealerId);
        $reasonArray [$defaultReason->deviceSwapReasonId] = Memjetoptimization_Model_Mapper_Device_Swap_Reason::getInstance()->find($defaultReason->deviceSwapReasonId)->reason;

        foreach (Memjetoptimization_Model_Mapper_Device_Swap_Reason::getInstance()->fetchAllByCategoryId($categoryId, $this->_dealerId) as $reason)
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