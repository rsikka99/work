<?php
class Proposalgen_Model_Proposal_Abstract
{
    /**
     * @var Proposalgen_Model_Proposal_Devices
     */
    protected $_devices;

    /**
     * @var Proposalgen_Model_Report
     */
    public $report;


    /**
     * Constructor
     *
     * @param int|Proposalgen_Model_Report $report The report model, or the id of our report
     */
    public function __construct ($report)
    {
        if ($report instanceof Proposalgen_Model_Report)
        {
            $this->report = $report;
        }
        else
        {
            $this->report = Proposalgen_Model_Mapper_Report::getInstance()->find($report);
        }
    }

    /**
     * Gets the devices object for the report.
     *
     * @return Proposalgen_Model_Proposal_Devices
     */
    public function getDevices ()
    {
        if (!isset($this->_devices))
        {
            $this->_devices = new Proposalgen_Model_Proposal_Devices($this->report);
        }

        return $this->_devices;
    }
}