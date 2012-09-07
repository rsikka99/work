<?php

/**
 * Class Proposalgen_Model_Report
 *
 * @author "Lee Robert"
 */
class Proposalgen_Model_Report extends My_Model_Abstract
{
    /**
     * The id assigned by the database
     *
     * @var int
     */
    protected $_id;
    
    /**
     * The user id
     *
     * @var int
     */
    protected $_userId;
    
    /**
     * The company name the proposal is being made for
     *
     * @var string
     */
    protected $_customerCompanyName;
    
    /**
     * Whether or not the user has overriden pricing?
     *
     * @var boolean
     */
    protected $_userPricingOverride;
    
    /**
     * Which state the report is in.
     *
     * @var string
     */
    protected $_reportStage;
    
    /**
     * The question set used to create this proposal
     *
     * @var int
     */
    protected $_questionSetId;
    
    /**
     * The mysql date this report was created
     *
     * @var String
     */
    protected $_dateCreated;
    
    /**
     * The mysql date this report was last modified
     *
     * @var String
     */
    protected $_lastModified;
    
    /**
     * The mysql date this report was made for
     *
     * @var String
     */
    protected $_reportDate;
    
    /**
     * Whether or not devices have been modified
     *
     * @var boolean
     */
    protected $_devicesModified;
    
    // Non database fields
    /**
     * The report settings for this proposal
     *
     * @var Proposalgen_Model_Report_Setting
     */
    protected $_reportSettings;
    
    /**
     * The report steps for this proposal
     *
     * @var Proposalgen_Model_Report_Step
     */
    protected $_reportSteps;

    /**
     * Populates the model with data from an array
     *
     * @see My_Model_Abstract::populate()
     */
    public function populate ($params)
    {
        // Convert the array into an object
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }
        
        if (isset($params->id) && ! is_null($params->id))
            $this->setId($params->id);
        
        if (isset($params->userId) && ! is_null($params->userId))
            $this->setUserId($params->userId);
        
        if (isset($params->customerCompanyName) && ! is_null($params->customerCompanyName))
            $this->setCustomerCompanyName($params->customerCompanyName);
        
        if (isset($params->userPricingOverride) && ! is_null($params->userPricingOverride))
            $this->setUserPricingOverride($params->userPricingOverride);
        
        if (isset($params->reportStage) && ! is_null($params->reportStage))
            $this->setReportStage($params->reportStage);
        
        if (isset($params->questionSetId) && ! is_null($params->questionSetId))
            $this->setQuestionSetId($params->questionSetId);
        
        if (isset($params->dateCreated) && ! is_null($params->dateCreated))
            $this->setDateCreated($params->dateCreated);
        
        if (isset($params->lastModified) && ! is_null($params->lastModified))
            $this->setLastModified($params->lastModified);
        
        if (isset($params->reportDate) && ! is_null($params->reportDate))
            $this->setReportDate($params->reportDate);
        
        if (isset($params->devicesModified) && ! is_null($params->devicesModified))
            $this->setDevicesModified($params->devicesModified);
    }

    /**
     * Converts the model into an array
     *
     * @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                "id" => $this->getId(), 
                "userId" => $this->getUserId(), 
                "customerCompanyName" => $this->getCustomerCompanyName(), 
                "userPricingOverride" => $this->getUserPricingOverride(), 
                "reportStage" => $this->getReportStage(), 
                "questionSetId" => $this->getQuestionSetId(), 
                "dateCreated" => $this->getDateCreated(), 
                "lastModified" => $this->getLastModified(), 
                "reportDate" => $this->getReportDate(), 
                "devicesModified" => $this->getDevicesModified() 
        );
    }

    /**
     * Gets the id
     *
     * @return number
     */
    public function getId ()
    {
        return $this->_id;
    }

    /**
     * Sets the id
     *
     * @param number $_id            
     */
    public function setId ($_id)
    {
        $this->_id = $_id;
        return $this;
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
     * Gets the company name
     *
     * @return string
     */
    public function getCustomerCompanyName ()
    {
        return $this->_customerCompanyName;
    }

    /**
     * Sets the company name
     *
     * @param string $_customerCompanyName            
     */
    public function setCustomerCompanyName ($_customerCompanyName)
    {
        $this->_customerCompanyName = $_customerCompanyName;
        return $this;
    }

    /**
     * Gets the user pricing override flag
     *
     * @return boolean
     */
    public function getUserPricingOverride ()
    {
        return $this->_userPricingOverride;
    }

    /**
     * Sets the user pricing override flag
     *
     * @param boolean $_userPricingOverride            
     */
    public function setUserPricingOverride ($_userPricingOverride)
    {
        $this->_userPricingOverride = $_userPricingOverride;
        return $this;
    }

    /**
     * Gets the report stage
     *
     * @return string
     */
    public function getReportStage ()
    {
        return $this->_reportStage;
    }

    /**
     * Sets the report stage
     *
     * @param string $_reportStage            
     */
    public function setReportStage ($_reportStage)
    {
        $this->_reportStage = $_reportStage;
        return $this;
    }

    /**
     * Gets the question set id
     *
     * @return number
     */
    public function getQuestionSetId ()
    {
        return $this->_questionSetId;
    }

    /**
     * Sets the question set id
     *
     * @param number $_questionSetId            
     */
    public function setQuestionSetId ($_questionSetId)
    {
        $this->_questionSetId = $_questionSetId;
        return $this;
    }

    /**
     * Gets the date this report was created
     *
     * @return string
     */
    public function getDateCreated ()
    {
        return $this->_dateCreated;
    }

    /**
     * Sets the date this report was created
     *
     * @param string $_dateCreated            
     */
    public function setDateCreated ($_dateCreated)
    {
        $this->_dateCreated = $_dateCreated;
        return $this;
    }

    /**
     * Gets the date this report was last modified
     *
     * @return string
     */
    public function getLastModified ()
    {
        return $this->_lastModified;
    }

    /**
     * Sets the date this report was last modified
     *
     * @param string $_lastModified            
     */
    public function setLastModified ($_lastModified)
    {
        $this->_lastModified = $_lastModified;
        return $this;
    }

    /**
     * Gets the report date
     *
     * @return string
     */
    public function getReportDate ()
    {
        return $this->_reportDate;
    }

    /**
     * Sets the report date
     *
     * @param string $_reportDate            
     */
    public function setReportDate ($_reportDate)
    {
        $this->_reportDate = $_reportDate;
        return $this;
    }

    /**
     * Gets the devices modified flag
     *
     * @return boolean
     */
    public function getDevicesModified ()
    {
        return $this->_devicesModified;
    }

    /**
     * Sets the devices modified flag
     *
     * @param boolean $_devicesModified            
     */
    public function setDevicesModified ($_devicesModified)
    {
        $this->_devicesModified = $_devicesModified;
        return $this;
    }

    /**
     * Gets the report settings for the report
     *
     * @return Proposalgen_Model_Report_Setting
     */
    public function getReportSettings ()
    {
        if (! isset($this->_reportSettings))
        {
            $this->_reportSettings = Proposalgen_Model_Mapper_Report_Setting::getInstance()->fetchReportReportSetting($this->getId());
        }
        return $this->_reportSettings;
    }

    /**
     * Sets the report settings for the report
     *
     * @param Proposalgen_Model_Report_Setting $_reportSettings            
     */
    public function setReportSettings ($_reportSettings)
    {
        $this->_reportSettings = $_reportSettings;
        return $this;
    }

    /**
     * Gets the report steps for this report
     *
     * @return Proposalgen_Model_Report_Step
     */
    public function getReportSteps ()
    {
        if (! isset($this->_reportSteps))
        {
            $stage = ($this->getReportStage()) ?  : Proposalgen_Model_Report_Step::STEP_SURVEY_COMPANY;
            
            $this->_reportSteps = Proposalgen_Model_Report_Step::getSteps();
            Proposalgen_Model_Report_Step::updateAccessibleSteps($this->_reportSteps, $stage);
        }
        return $this->_reportSteps;
    }

    /**
     * Sets the report steps for this report
     *
     * @param field_type $ReportSteps            
     */
    public function setReportSteps ($ReportSteps)
    {
        $this->_reportSteps = $ReportSteps;
        return $this;
    }
}