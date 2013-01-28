<?php

class Application_Model_ProfitabilityDevices
{
    /**
     * Devices that have replacement devices attached to them
     *
     * @var Proposalgen_Model_DeviceInstance []
     */
    protected $_replaced;

    /**
     * Deices that have their action flagged as retire
     *
     * @var Proposalgen_Model_DeviceInstance []
     */
    protected $_retired;

    /**
     * Devices that are either replacement devices that haven't been replacement, or good devices that haven't been
     * replaced
     *
     * @var Proposalgen_Model_DeviceInstance []
     */
    protected $_kept;

    /**
     * Devices that have been swapped out
     *
     * @var Proposalgen_Model_DeviceInstance []
     */
    protected $_excess;

    /**
     * Devices that have been excluded from the original reporting
     *
     * @var Proposalgen_Model_DeviceInstance []
     */
    protected $_excluded;

    /**
     * Devices that are leased
     *
     * @var Proposalgen_Model_DeviceInstance  []
     */
    protected $_leased;

    /**
     * Devices that action is replace, with no replacement devices assigned
     *
     * @var Proposalgen_Model_DeviceInstance  []
     */
    protected $_flagged;
    /**
     * The count of devices that's action is Keep
     *
     * @var number
     */
    protected $_actionKeepCount;
    /**
     * The count of devices that's action is Replace
     *
     * @var number
     */
    protected $_actionReplaceCount;
    /**
     * The count of devices that's action is Retire
     *
     * @var number
     */
    protected $_actionRetireCount;

    public function __construct (Application_Model_Proposal_Abstract $proposal)
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
        foreach ($proposal->getSortedDevicesbyPercentOfMonthlyCost() as $deviceInstance)
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

            if ($replacementDevice instanceof Application_Model_MasterDevice)
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


        $leasedDevices   = $proposal->getDevices()->getLeased();
        $excludedDevices = $proposal->getDevices()->getExcluded();

        $this->setActionKeepCount($actionKeep);
        $this->setActionReplaceCount($actionReplace);
        $this->setActionRetireCount($actionRetire);
        $this->setExcess($excessDevices);
        $this->setFlagged($flaggedDevices);
        $this->setExcluded($excludedDevices);
        $this->setKept($keepDevices);
        $this->setLeased($leasedDevices);
        $this->setReplaced($replacedDevices);
        $this->setRetired($retiredDevices);
    }

    /**
     * Getter for $_replaced
     *
     * @return Proposalgen_Model_DeviceInstance
     */
    public function getReplaced ()
    {
        return $this->_replaced;
    }

    /**
     * Setter for $_replaced
     *
     * @param Proposalgen_Model_DeviceInstance $_replaced
     *            The new value
     */
    public function setReplaced ($_replaced)
    {
        $this->_replaced = $_replaced;
    }

    /**
     * Getter for $_retired
     *
     * @return Proposalgen_Model_DeviceInstance
     */
    public function getRetired ()
    {
        return $this->_retired;
    }

    /**
     * Setter for $_retired
     *
     * @param Proposalgen_Model_DeviceInstance $_retired
     *            The new value
     */
    public function setRetired ($_retired)
    {
        $this->_retired = $_retired;
    }

    /**
     * Getter for $_kept
     *
     * @return Proposalgen_Model_DeviceInstance
     */
    public function getKept ()
    {
        return $this->_kept;
    }

    /**
     * Setter for $_kept
     *
     * @param Proposalgen_Model_DeviceInstance $_kept
     *            The new value
     */
    public function setKept ($_kept)
    {
        $this->_kept = $_kept;
    }

    /**
     * Getter for $_excess
     *
     * @return Proposalgen_Model_DeviceInstance
     */
    public function getExcess ()
    {
        return $this->_excess;
    }

    /**
     * Setter for $_excess
     *
     * @param Proposalgen_Model_DeviceInstance $_excess
     *            The new value
     */
    public function setExcess ($_excess)
    {
        $this->_excess = $_excess;
    }

    /**
     * Getter for $_excluded
     *
     * @return Proposalgen_Model_DeviceInstance
     */
    public function getExcluded ()
    {
        return $this->_excluded;
    }

    /**
     * Setter for $_excluded
     *
     * @param Proposalgen_Model_DeviceInstance $_excluded
     *            The new value
     */
    public function setExcluded ($_excluded)
    {
        $this->_excluded = $_excluded;
    }

    /**
     * Getter for $_leased
     *
     * @return Proposalgen_Model_DeviceInstance
     */
    public function getLeased ()
    {
        return $this->_leased;
    }

    /**
     * Setter for $_leased
     *
     * @param Proposalgen_Model_DeviceInstance $_leased
     *            The new value
     */
    public function setLeased ($_leased)
    {
        $this->_leased = $_leased;
    }

    /**
     * Getter for $_flagged
     *
     * @return Proposalgen_Model_DeviceInstance []
     */
    public function getFlagged ()
    {
        return $this->_flagged;
    }

    /**
     * Setter for $_flagged
     *
     * @param Proposalgen_Model_DeviceInstance $_flagged
     *            The new value
     */
    public function setFlagged ($_flagged)
    {
        $this->_flagged = $_flagged;
    }

    /**
     * Getter for $_actionKeepCount
     *
     * @return number
     */
    public function getActionKeepCount ()
    {
        return $this->_actionKeepCount;
    }

    /**
     * Setter for $_actionKeepCount
     *
     * @param number $_actionKeepCount The new value
     */
    public function setActionKeepCount ($_actionKeepCount)
    {
        $this->_actionKeepCount = $_actionKeepCount;
    }

    /**
     * Getter for $_actionReplaceCount
     *
     * @return number
     */
    public function getActionReplaceCount ()
    {
        return $this->_actionReplaceCount;
    }

    /**
     * Setter for $_actionReplaceCount
     *
     * @param number $_actionReplaceCount The new value
     */
    public function setActionReplaceCount ($_actionReplaceCount)
    {
        $this->_actionReplaceCount = $_actionReplaceCount;
    }

    /**
     * Getter for $_actionRetireCount
     *
     * @return number
     */
    public function getActionRetireCount ()
    {
        return $this->_actionRetireCount;
    }

    /**
     * Setter for $_actionRetireCount
     *
     * @param number $_actionRetireCount The new value
     */
    public function setActionRetireCount ($_actionRetireCount)
    {
        $this->_actionRetireCount = $_actionRetireCount;
    }

}