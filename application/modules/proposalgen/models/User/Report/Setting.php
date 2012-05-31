<?php

/**
 * Class Application_Model_User_Report_Setting
 */
class Proposalgen_Model_User_Report_Setting extends My_Model_Abstract
{
    /**
     * The user id
     *
     * @var int
     */
    protected $_userId;
    
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
        if (isset($params->userId))
            $this->setUserId($params->userId);
        if (isset($params->reportSettingId))
            $this->setReportSettingId($params->reportSettingId);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                "userId" => $this->getUserId(), 
                "reportSettingId" => $this->getReportSettingId() 
        );
    }

    /**
     * Gets the user id
     *
     * @return number
     */
    public function getUserId ()
    {
        return $this->_userId;
    }

    /**
     * Sets the user id
     *
     * @param number $_userId            
     */
    public function setUserId ($_userId)
    {
        $this->_userId = $_userId;
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