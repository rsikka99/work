<?php
/**
 * Class Hardwareoptimization_Model_Optimization_Abstract
 */
abstract class Hardwareoptimization_Model_Optimization_Abstract
{
    // Used to calculate the average supply count for a fleet
    const SUPPLY_TYPE_THRESHOLD = 0.30;
    /**
     * @var Hardwareoptimization_ViewModel_Optimization
     */
    protected $_optimization;
    /**
     * @var Hardwareoptimization_Model_Hardware_Optimization|int
     */
    protected $_hardwareOptimization;
    /**
     * Devices that have replacement devices attached to them
     *
     * @var Proposalgen_Model_DeviceInstance []
     */
    public $replaced;
    /**
     * Deices that have their action flagged as retire
     *
     * @var Proposalgen_Model_DeviceInstance []
     */
    public $retired;
    /**
     * Devices that are either replacement devices that haven't been replacement, or good devices that haven't been
     * replaced
     *
     * @var Proposalgen_Model_DeviceInstance []
     */
    public $kept;
    /**
     * Devices that have been swapped out
     *
     * @var Proposalgen_Model_DeviceInstance []
     */
    public $excess;
    /**
     * Devices that have been excluded from the original reporting
     *
     * @var Proposalgen_Model_DeviceInstance []
     */
    public $excluded;
    /**
     * Devices that are leased
     *
     * @var Proposalgen_Model_DeviceInstance  []
     */
    public $leased;
    /**
     * Devices that action is replace, with no replacement devices assigned
     *
     * @var Proposalgen_Model_DeviceInstance  []
     */
    public $flagged;
    /**
     * The count of devices that's action is Keep
     *
     * @var number
     */
    public $actionKeepCount;
    /**
     * The count of devices that's action is Replace
     *
     * @var number
     */
    public $actionReplaceCount;
    /**
     * The count of devices that's action is Retire
     *
     * @var number
     */
    public $actionRetireCount;
    /**
     * Gets the amount of purchased devices that are JIT Compatible
     *
     * @var int
     */
    public $jitCompatibleCount;
    /**
     * Gets the amount of replacement devices that are JIT compatible
     *
     * @var int
     */
    public $replacementJitCompatibleCount;
    /**
     * Stores count of devices based on age ranking
     *
     * @var array
     */
    public $deviceAges = array();
    /**
     * Stores count of devices based on age ranking
     *
     * @var array
     */
    public $deviceAgesOptimized = array();
    /**
     * Stores the count of devices based on categories required
     *
     * @var array
     */
    public $deviceCategories = array();
    /**
     * What a client should be a for average supplies
     *
     * @var int
     */
    public $averageSupplyType;
    /**
     * The number of supply types used the clients fleet
     *
     * @var int
     */
    public $supplyTypeCount;
    /**
     * The number of supply types used in a fleet after optimized
     *
     * @var int
     */
    public $optimizedSupplyType;
    /**
     * The number of the devices that are used in hardware optimization
     *
     * @var int
     */
    protected $_deviceCount;
    /**
     * The ages that are shown inside the age graph inside the customer facing report.
     *
     * @var array
     */
    public static $ageRanks = array(
        8 => "8+ Years",
        4 => "4-8 Years",
        2 => "2-4 Years",
        0 => "0-2 Years",
    );
    /**
     * The ages that are shown inside the age graph inside the customer facing report.
     *
     * @var array
     */
    public static $ageRankTable = array(
        8 => 8,
        4 => 4,
        2 => 2,
        0 => 0,
    );

    /**
     * Constructor
     *
     * @param int|Hardwareoptimization_Model_Hardware_Optimization $hardwareOptimization
     */
    public function __construct ($hardwareOptimization)
    {
        $this->_optimization         = new Hardwareoptimization_ViewModel_Optimization($hardwareOptimization);
        $this->_hardwareOptimization = $hardwareOptimization;

        // Set up the arrays of devices to be produced
        $retiredDevices                      = array();
        $replacedDevices                     = array();
        $keepDevices                         = array();
        $excessDevices                       = array();
        $flaggedDevices                      = array();
        $actionKeep                          = 0;
        $actionReplace                       = 0;
        $actionRetire                        = 0;
        $this->replacementJitCompatibleCount = 0;
        $this->jitCompatibleCount            = 0;


        // Initialize the values for each age rank
        foreach (self::$ageRanks as $ageRank => $ageRankName)
        {
            $this->deviceAges[$ageRank]          = 0;
            $this->deviceAgesOptimized[$ageRank] = 0;
        }

        // Initialize the categories variables count to 0
        $this->deviceCategories["current"]["copy"]     = 0;
        $this->deviceCategories["current"]["color"]    = 0;
        $this->deviceCategories["current"]["duplex"]   = 0;
        $this->deviceCategories["optimized"]["copy"]   = 0;
        $this->deviceCategories["optimized"]["color"]  = 0;
        $this->deviceCategories["optimized"]["duplex"] = 0;

        // Go through each purchase devices that rank it's classifications
        /* @var $deviceInstance Proposalgen_Model_DeviceInstance */
        foreach ($this->_optimization->getMonthlyHighCostPurchasedDevice($this->_optimization->getCostPerPageSettingForDealer()) as $deviceInstance)
        {
            if ($deviceInstance->getMasterDevice()->isCopier)
            {
                $this->deviceCategories["current"]["copy"]++;
            }
            if ($deviceInstance->getMasterDevice()->isColor())
            {
                $this->deviceCategories["current"]["color"]++;
            }
            if ($deviceInstance->getMasterDevice()->isDuplex)
            {
                $this->deviceCategories["current"]["duplex"]++;
            }

            // Checks to see if the device is JIT Compatible
            if ($deviceInstance->getMasterDevice()->reportsTonerLevels)
            {
                $this->jitCompatibleCount++;
            }

            // Check the action status first, then check the replacement status
            if ($deviceInstance->getAction() === Proposalgen_Model_DeviceInstance::ACTION_REPLACE)
            {
                $actionReplace++;
            }
            else if ($deviceInstance->getAction() === Proposalgen_Model_DeviceInstance::ACTION_RETIRE)
            {
                $actionRetire++;
            }
            else
            {
                $actionKeep++;
            }

            // Get the age rank of the device instance
            $ageRank = Tangent_Functions::getValueFromRangeStepTable($deviceInstance->getMasterDevice()->getAge(), self::$ageRankTable, false);
            // Get the replacement device of the device instance if there is one
            $replacementDevice = $deviceInstance->getReplacementMasterDeviceForHardwareOptimization(($hardwareOptimization->id));
            if ($deviceInstance->getAction() !== Proposalgen_Model_DeviceInstance::ACTION_RETIRE)
            {
                // Assigned the optimized age rank if replacement device exists
                if ($replacementDevice instanceof Proposalgen_Model_MasterDevice)
                {
                    $optimizedAgeRank = Tangent_Functions::getValueFromRangeStepTable($replacementDevice->getAge(), self::$ageRankTable, false);
                    if ($replacementDevice->isCopier)
                    {
                        $this->deviceCategories["optimized"]["copy"]++;
                    }
                    if ($replacementDevice->isColor())
                    {
                        $this->deviceCategories["optimized"]["color"]++;
                    }
                    if ($replacementDevice->isDuplex)
                    {
                        $this->deviceCategories["optimized"]["duplex"]++;
                    }
                }
                else
                {
                    $optimizedAgeRank = $ageRank;
                    if ($deviceInstance->getMasterDevice()->isCopier)
                    {
                        $this->deviceCategories["optimized"]["copy"]++;
                    }
                    if ($deviceInstance->getMasterDevice()->isColor())
                    {
                        $this->deviceCategories["optimized"]["color"]++;
                    }
                    if ($deviceInstance->getMasterDevice()->isDuplex)
                    {
                        $this->deviceCategories["optimized"]["duplex"]++;
                    }
                }
                $this->deviceAgesOptimized[$optimizedAgeRank]++;
            }
            $this->deviceAges[$ageRank]++;


            if ($replacementDevice instanceof Proposalgen_Model_MasterDevice)
            {
                $excessDevices []   = $deviceInstance;
                $replacedDevices [] = $deviceInstance;

                if ($replacementDevice->reportsTonerLevels)
                {
                    $this->replacementJitCompatibleCount++;
                }
            }
            else if ($deviceInstance->getAction() === Proposalgen_Model_DeviceInstance::ACTION_RETIRE)
            {
                $retiredDevices [] = $deviceInstance;
            }
            else if ($deviceInstance->getAction() === Proposalgen_Model_DeviceInstance::ACTION_REPLACE)
            {
                $flaggedDevices [] = $deviceInstance;
            }
            else
            {
                $keepDevices [] = $deviceInstance;
            }
        }

//        $this->averageSupplyCount   = '';
//        $this->supplyTypeCount      = count($this->getUniqueTonerList($this->getUniquePurchasedMasterDevices($proposal->getPurchasedDevices())));
//        $this->optimizedSupplyCount = count($this->getUniqueTonerList($this->getUniquePurchasedMasterDevices($this->getAllMasterDevicesWithReplacements())));

        $excludedDevices = $this->_optimization->getExcludedDevices();
        $leasedDevices   = $this->_optimization->getLeasedDevices();

        $this->actionKeepCount    = $actionKeep;
        $this->actionReplaceCount = $actionReplace;
        $this->actionRetireCount  = $actionRetire;
        $this->excess             = $excessDevices;
        $this->flagged            = $flaggedDevices;
        $this->excluded           = $excludedDevices;
        $this->kept               = $keepDevices;
        $this->leased             = $leasedDevices;
        $this->replaced           = $replacedDevices;
        $this->retired            = $retiredDevices;
    }


    /**
     * Returns the amount of devices that are considered to be used in this report
     *
     * @return int
     */
    public function getDeviceCount ()
    {
        if (!isset($this->_deviceCount))
        {
            $this->_deviceCount = count($this->kept) + count($this->replaced) + count($this->flagged);
        }

        return $this->_deviceCount;
    }

    /**
     * Get unique purchased master devices for the fleet
     *
     *
     * @param $devices Proposalgen_Model_MasterDevice [] | Proposalgen_Model_DeviceInstance []
     *
     * @return Proposalgen_Model_MasterDevice [] | bool
     */
    protected function getUniquePurchasedMasterDevices ($devices)
    {
        $masterDeviceList = array();

        if ($devices[0] instanceof Proposalgen_Model_DeviceInstance)
        {
            foreach ($devices as $deviceInstance)
            {
                $masterDeviceModel = $deviceInstance->getMasterDevice();
                // Does the master device exist in the array
                // if not add in it.
                if (!in_array($masterDeviceModel, $masterDeviceList))
                {
                    $masterDeviceList [] = $masterDeviceModel;
                }

            }
        }
        else if ($devices[0] instanceof Proposalgen_Model_MasterDevice)
        {
            foreach ($devices as $masterDevice)
            {
                // Does the master device exist in the array
                // if not add in it.
                if (!in_array($masterDevice, $masterDeviceList))
                {
                    $masterDeviceList [] = $masterDevice;
                }
            }
        }
        else
        {
            $masterDeviceList = false;
        }

        return $masterDeviceList;
    }

    /**
     * Gets a list of all master devices and replacement devices
     *
     * @return Proposalgen_Model_MasterDevice []
     */
    protected function getAllMasterDevicesWithReplacements ()
    {
        $masterDevices = array();
        /* @var $deviceInstance Proposalgen_Model_DeviceInstance */
        foreach ($this->_optimization->getPurchasedDevices() as $deviceInstance)
        {
            if ($deviceInstance->getAction() !== Proposalgen_Model_DeviceInstance::ACTION_RETIRE)
            {
                $replacementDevice = $deviceInstance->getReplacementMasterDeviceForHardwareOptimization($this->_hardwareOptimization->id);
                if ($replacementDevice instanceof Proposalgen_Model_MasterDevice)
                {
                    $masterDevices [] = $replacementDevice;
                }
                else
                {
                    $masterDevices [] = $deviceInstance->getMasterDevice();
                }
            }
        }

        return $masterDevices;
    }


    /**
     * Gets a list of unique toners for a list of master devices
     * Toners users are toners that will be used in the assessment
     *
     * @param $masterDevices Proposalgen_Model_MasterDevice []
     *
     * @return Proposalgen_Model_Toner []
     */
    protected function getUniqueTonerList ($masterDevices)
    {
        $uniqueTonerList = array();

        foreach ($masterDevices as $masterDevice)
        {
            $toners = $masterDevice->getTonersForAssessment($this->_optimization->getCostPerPageSettingForDealer());

            foreach ($toners as $toner)
            {
                if (!in_array($toner, $uniqueTonerList))
                {
                    $uniqueTonerList [] = $toner;
                }
            }

        }

        return $uniqueTonerList;
    }


    /**
     * Gets the maximum supply devices for the list of master devices provided.
     *
     * @param Proposalgen_Model_MasterDevice [] $masterDevices
     *
     * @return int
     */
    protected function getMaximumSupplyCount ($masterDevices)
    {

        $maximumSupplyCount = 0;
        foreach ($masterDevices as $masterDevice)
        {
            switch ($masterDevice->tonerConfigId)
            {
                case Proposalgen_Model_TonerConfig::BLACK_ONLY:
                    $maximumSupplyCount += 1;
                    break;
                case Proposalgen_Model_TonerConfig::THREE_COLOR_SEPARATED:
                    $maximumSupplyCount += 4;
                    break;
                case Proposalgen_Model_TonerConfig::THREE_COLOR_COMBINED:
                    $maximumSupplyCount += 2;
                    break;
                case Proposalgen_Model_TonerConfig::FOUR_COLOR_COMBINED:
                    $maximumSupplyCount += 1;
                    break;
            }
        }

        return $maximumSupplyCount;
    }
}
