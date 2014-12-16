<?php

namespace MPSToolbox\Legacy\Modules\HardwareOptimization\Models;

use MPSToolbox\Legacy\Modules\HardwareOptimization\ViewModels\OptimizationViewModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerConfigModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerModel;
use Tangent\Functions;

/**
 * Class OptimizationAbstractModel
 *
 * @package MPSToolbox\Legacy\Modules\HardwareOptimization\Models
 */
abstract class OptimizationAbstractModel
{
    // Used to calculate the average supply count for a fleet
    const SUPPLY_TYPE_THRESHOLD = 0.30;

    /**
     * @var OptimizationViewModel
     */
    protected $_optimization;

    /**
     * @var HardwareOptimizationModel|int
     */
    protected $_hardwareOptimization;

    /**
     * Devices that have replacement devices attached to them
     *
     * @var DeviceInstanceModel []
     */
    public $replaced;

    /**
     * Devices that have their action flagged as retire
     *
     * @var DeviceInstanceModel []
     */
    public $retired;

    /**
     * Devices that are either replacement devices that haven't been replacement, or good devices that haven't been
     * replaced
     *
     * @var DeviceInstanceModel []
     */
    public $kept;

    /**
     * Devices that have been swapped out
     *
     * @var DeviceInstanceModel []
     */
    public $excess;

    /**
     * Devices that have been excluded from the original reporting
     *
     * @var DeviceInstanceModel []
     */
    public $excluded;

    /**
     * Devices that are leased
     *
     * @var DeviceInstanceModel  []
     */
    public $leased;

    /**
     * Devices that action is replace, with no replacement devices assigned
     *
     * @var DeviceInstanceModel  []
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
     * What a client should have for average supplies
     *
     * @var int
     */
    public $averageSupplyType;

    /**
     * The number of supply types used in the client's fleet
     *
     * @var int
     */
    public $supplyTypeCount;

    /**
     * The number of supply types used in a fleet after optimization
     *
     * @var int
     */
    public $optimizedSupplyType;

    /**
     * The number of devices that are used in hardware optimization
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
     * @param int|HardwareOptimizationModel $hardwareOptimization
     */
    public function __construct ($hardwareOptimization)
    {
        $this->_optimization         = new OptimizationViewModel($hardwareOptimization);
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
        /* @var $deviceInstance DeviceInstanceModel */
        foreach ($this->_optimization->getMonthlyHighCostPurchasedDevice($this->_optimization->getCostPerPageSettingForDealer()) as $deviceInstance)
        {
            $hardwareOptimizationDeviceInstance = $deviceInstance->getHardwareOptimizationDeviceInstance($hardwareOptimization->id);

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
            if ($deviceInstance->getMasterDevice()->isCapableOfReportingTonerLevels)
            {
                $this->jitCompatibleCount++;
            }

            // Check the action status first, then check the replacement status
            if ($hardwareOptimizationDeviceInstance->action === HardwareOptimizationDeviceInstanceModel::ACTION_REPLACE)
            {
                $actionReplace++;
            }
            else if ($hardwareOptimizationDeviceInstance->action === HardwareOptimizationDeviceInstanceModel::ACTION_RETIRE)
            {
                $actionRetire++;
            }
            else
            {
                $actionKeep++;
            }

            // Get the age rank of the device instance
            $ageRank = Functions::getValueFromRangeStepTable($deviceInstance->getMasterDevice()->getAge(), self::$ageRankTable, false);

            // Get the replacement device of the device instance if there is one
            $replacementDevice = ($hardwareOptimizationDeviceInstance->masterDeviceId > 0) ? $hardwareOptimizationDeviceInstance->getMasterDevice() : false;

            if ($hardwareOptimizationDeviceInstance->action !== HardwareOptimizationDeviceInstanceModel::ACTION_RETIRE)
            {
                // Assigned the optimized age rank if replacement device exists
                if ($replacementDevice instanceof MasterDeviceModel)
                {
                    $optimizedAgeRank = Functions::getValueFromRangeStepTable($replacementDevice->getAge(), self::$ageRankTable, false);
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


            if ($replacementDevice instanceof MasterDeviceModel)
            {
                $excessDevices []   = $deviceInstance;
                $replacedDevices [] = $deviceInstance;

                if ($replacementDevice->isCapableOfReportingTonerLevels)
                {
                    $this->replacementJitCompatibleCount++;
                }
            }
            else if ($hardwareOptimizationDeviceInstance->action === HardwareOptimizationDeviceInstanceModel::ACTION_RETIRE)
            {
                $retiredDevices [] = $deviceInstance;
            }
            else if ($hardwareOptimizationDeviceInstance->action === HardwareOptimizationDeviceInstanceModel::ACTION_DNR)
            {
                $flaggedDevices [] = $deviceInstance;
            }
            else
            {
                $keepDevices [] = $deviceInstance;
            }
        }

//        $this->averageSupplyCount   = '';
//        $this->supplyTypeCount      = count($this->getUniqueTonerList($this->getUniquePurchasedMasterDevices($proposal->getDevices()->purchasedDeviceInstances->getDeviceInstances())));
//        $this->optimizedSupplyCount = count($this->getUniqueTonerList($this->getUniquePurchasedMasterDevices($this->getAllMasterDevicesWithReplacements())));

        $excludedDevices = $this->_optimization->getExcludedDevices();
        $leasedDevices   = $this->_optimization->getDevices()->leasedDeviceInstances->getDeviceInstances();

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
     * @param $devices MasterDeviceModel [] | DeviceInstanceModel []
     *
     * @return MasterDeviceModel [] | bool
     */
    protected function getUniquePurchasedMasterDevices ($devices)
    {
        $masterDeviceList = array();
        if (reset($devices) instanceof DeviceInstanceModel)
        {
            foreach ($devices as $deviceInstance)
            {
                $masterDeviceModel = $deviceInstance->getMasterDevice();
                // Does the master device exist in the array
                // if not add in it.
                if (!isset($masterDeviceList[$masterDeviceModel->id]))
                {
                    $masterDeviceList [$masterDeviceModel->id] = $masterDeviceModel;
                }

            }
        }
        else if (reset($devices) instanceof MasterDeviceModel)
        {
            foreach ($devices as $masterDevice)
            {
                // Does the master device exist in the array
                // if not add in it.
                if (!isset($masterDeviceList[$masterDevice->id]))
                {
                    $masterDeviceList [$masterDevice->id] = $masterDevice;
                }
            }
        }
        else
        {
            $masterDeviceList = array();
        }

        return $masterDeviceList;
    }

    /**
     * Gets a list of all master devices and replacement devices
     *
     * @return MasterDeviceModel []
     */
    protected function getAllMasterDevicesWithReplacements ()
    {
        $masterDevices = array();
        /* @var $deviceInstance DeviceInstanceModel */
        foreach ($this->_optimization->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $deviceInstance)
        {
            $hardwareOptimizationDeviceInstance = $deviceInstance->getHardwareOptimizationDeviceInstance($this->_hardwareOptimization->id);

            if ($hardwareOptimizationDeviceInstance->action === HardwareOptimizationDeviceInstanceModel::ACTION_REPLACE)
            {
                $replacementDevice = $hardwareOptimizationDeviceInstance->getMasterDevice();
                if ($replacementDevice instanceof MasterDeviceModel)
                {
                    $masterDevices [] = $replacementDevice;
                }
                else
                {
                    $masterDevices [] = $deviceInstance->getMasterDevice();
                }
            }
            else
            {
                $masterDevices [] = $deviceInstance->getMasterDevice();
            }
        }

        return $masterDevices;
    }


    /**
     * Gets a list of unique toners for a list of master devices
     * Toners users are toners that will be used in the assessment
     *
     * @param $masterDevices MasterDeviceModel []
     *
     * @return TonerModel[]
     */
    protected function getUniqueTonerList ($masterDevices)
    {
        $uniqueTonerList = array();

        if (count($masterDevices) > 0)
        {
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
        }

        return $uniqueTonerList;
    }


    /**
     * Gets the maximum supply devices for the list of master devices provided.
     *
     * @param MasterDeviceModel [] $masterDevices
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
                case TonerConfigModel::BLACK_ONLY:
                    $maximumSupplyCount += 1;
                    break;
                case TonerConfigModel::THREE_COLOR_SEPARATED:
                    $maximumSupplyCount += 4;
                    break;
                case TonerConfigModel::THREE_COLOR_COMBINED:
                    $maximumSupplyCount += 2;
                    break;
                case TonerConfigModel::FOUR_COLOR_COMBINED:
                    $maximumSupplyCount += 1;
                    break;
            }
        }

        return $maximumSupplyCount;
    }
}