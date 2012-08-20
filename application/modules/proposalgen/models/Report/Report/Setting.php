<?php

/**
 * Class Application_Model_Report_Report_Setting
 */
class Proposalgen_Model_Report_Report_Setting extends My_Model_Abstract
{
    /**
     * The report id
     *
     * @var int
     */
    protected $_reportId;
    
    /**
     * The setting id
     *
     * @var int
     */
    protected $_reportSettingId;
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::populate()
     */
    public function populate ($params)
    {
        // Convert the array into an object
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }
        
        // Set the fields if they were passed in
        if (isset($params->reportId))
            $this->setReportId($params->reportId);
        if (isset($params->reportSettingId))
            $this->setReportSettingId($params->reportSettingId);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                "reportId" => $this->getReportId(), 
                "reportSettingId" => $this->getReportSettingId() 
        );
    }

    /**
     * Gets the report id
     *
     * @return number
     */
    public function getReportId ()
    {
        return $this->_reportId;
    }

    /**
     * Sets the report id
     *
     * @param number $_reportId            
     */
    public function setReportId ($_reportId)
    {
        $this->_reportId = $_reportId;
        return $this;
    }

    /**
     * Gets the report setting id
     *
     * @return number
     */
    public function getReportSettingId ()
    {
        return $this->_reportSettingId;
    }

    /**
     * Sets the report setting id
     *
     * @param number $_reportSettingId            
     */
    public function setReportSettingId ($_reportSettingId)
    {
        $this->_reportSettingId = $_reportSettingId;
        return $this;
    }
}