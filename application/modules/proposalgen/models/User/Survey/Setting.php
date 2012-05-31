<?php

/**
 * Class Proposalgen_Model_User_Survey_Setting
 */
class Proposalgen_Model_User_Survey_Setting extends My_Model_Abstract
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
    protected $_surveySettingId;
    
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
        if (isset($params->surveySettingId))
            $this->setSurveySettingId($params->surveySettingId);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                "userId" => $this->getUserId(), 
                "surveySettingId" => $this->getSurveySettingId() 
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
     * Gets the survey setting id
     *
     * @return number
     */
    public function getSurveySettingId ()
    {
        return $this->_surveySettingId;
    }

    /**
     * Sets the survey setting id
     *
     * @param number $_surveySettingId            
     */
    public function setSurveySettingId ($_surveySettingId)
    {
        $this->_surveySettingId = $_surveySettingId;
        return $this;
    }
}