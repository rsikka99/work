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

    public function __construct (Proposalgen_Model_Proposal_OfficeDepot $proposal)
    {
        // Set up the arrays of devices to be produced
        $retiredDevices      = array();
        $replacedDevices     = array();
        $keepDevices         = array();
        $excessDevices       = array();
        $flaggedDevices      = array();
        $actionKeep          = 0;
        $actionReplace       = 0;
        $actionRetire        = 0;
        $deviceInstanceCount = 0;

        // Go through each purchase devices that rank it's classifictions
        /* @var $deviceInstance Proposalgen_Model_DeviceInstance */
        foreach ($proposal->getPurchasedDevices() as $deviceInstance)
        {
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

        $this->actionKeepCount = $actionKeep;
        $this->actionReplaceCount = $actionReplace;
        $this->actionRetireCount = $actionRetire;
        $this->excess = $excessDevices;
        $this->flagged = $flaggedDevices;
        $this->excluded = $excludedDevices ;
        $this->kept = $keepDevices;
        $this->leased = $leasedDevices;
        $this->replaced = $replacedDevices;
        $this->retired = $retiredDevices;
    }

}