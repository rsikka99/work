<?php
abstract class Proposalgen_Model_Optimization_Abstract
{
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
     * Stores the proposal object for future use
     *
     * @var Proposalgen_Model_Proposal_OfficeDepot
     */
    public $proposal;
    /**
     * Stores count of devices based on age ranking
     *
     * @var array
     */
    public $deviceAges = array();

    /**
     * Stores the count of devices based on categories required
     *
     * @var array
     */
    public $deviceCategories = array();

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
     * Array of purchased devices as device instances
     *
     * @var Proposalgen_Model_DeviceInstance []
     */
    protected $_purchasedDevices;

    public function __construct (Proposalgen_Model_Proposal_OfficeDepot $proposal)
    {
        $this->proposal = $proposal;

        // Set up the arrays of devices to be produced
        $retiredDevices           = array();
        $replacedDevices          = array();
        $keepDevices              = array();
        $excessDevices            = array();
        $flaggedDevices           = array();
        $actionKeep               = 0;
        $actionReplace            = 0;
        $actionRetire             = 0;
        $this->jitCompatibleCount = 0;


        // Initialize the values for each age rank
        foreach (self::$ageRanks as $ageRank => $ageRankName)
        {
            $this->deviceAges[$ageRank] = 0;
        }

        // Initialize the categories variables count to 0
        $this->deviceCategories["copy"]   = 0;
        $this->deviceCategories["scan"]   = 0;
        $this->deviceCategories["duplex"] = 0;

        // Go through each purchase devices that rank it's classifications
        /* @var $deviceInstance Proposalgen_Model_DeviceInstance */
        foreach ($proposal->getPurchasedDevices() as $deviceInstance)
        {
            // For each age rank, check if device is greater than range.
            foreach (self::$ageRanks as $ageRank => $ageRankName)
            {
                if ($deviceInstance->getMasterDevice()->getAge() >= $ageRank)
                {
                    $this->deviceAges[$ageRank]++;
                    break;
                }
            }

            if ($deviceInstance->getMasterDevice()->isCopier)
            {
                $this->deviceCategories["copy"]++;
            }
            if ($deviceInstance->getMasterDevice()->isScanner)
            {
                $this->deviceCategories["scan"]++;
            }
            if ($deviceInstance->getMasterDevice()->isDuplex)
            {
                $this->deviceCategories["duplex"]++;
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

            $replacementDevice = $deviceInstance->getReplacementMasterDevice();

            if ($replacementDevice instanceof Proposalgen_Model_MasterDevice)
            {
                $excessDevices []   = $deviceInstance;
                $replacedDevices [] = $deviceInstance;
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

        $leasedDevices   = $proposal->getLeasedDevices();
        $excludedDevices = $proposal->getExcludedDevices();

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
}