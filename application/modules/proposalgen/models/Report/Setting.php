<?php

/**
 * Class Application_Model_Report_Setting
 *
 * These rows store settings for a proposal. We're able to use the heirarchy this way.
 */
class Proposalgen_Model_Report_Setting extends My_Model_Abstract
{
    /**
     * The database id
     *
     * @var int
     */
    protected $_id;
    
    /**
     * The actual monochrome page coverage as a whole number
     *
     * @var int
     */
    protected $_actualPageCoverageMono;
    
    /**
     * The actual color page coverage as a whole number
     *
     * @var int
     */
    protected $_actualPageCoverageColor;
    
    /**
     * The service cost per page
     *
     * @var int
     */
    protected $_serviceCostPerPage;
    
    /**
     * The admin cost per page
     *
     * @var int
     */
    protected $_adminCostPerPage;
    
    /**
     * The margin applied to the assessment
     *
     * @var int
     */
    protected $_assessmentReportMargin;
    
    /**
     * The margin applied to the gross margin
     *
     * @var int
     */
    protected $_grossMarginReportMargin;
    
    /**
     * The monthly lease payment for calculation with leased printers
     *
     * @var int
     */
    protected $_monthlyLeasePayment;
    
    /**
     * The default printer cost to use when a printer does not have a cost
     *
     * @var int
     */
    protected $_defaultPrinterCost;
    
    /**
     * The monochrome cost per page for a leased printer
     *
     * @var int
     */
    protected $_leasedBwCostPerPage;
    
    /**
     * The color cost per page for a leased printer
     *
     * @var int
     */
    protected $_leasedColorCostPerPage;
    
    /**
     * The MPS monochrome cost per page
     *
     * @var int
     */
    protected $_mpsBwCostPerPage;
    
    /**
     * The MPS color cost per page
     *
     * @var int
     */
    protected $_mpsColorCostPerPage;
    
    /**
     * The cost of electricty
     *
     * @var int
     */
    protected $_kilowattsPerHour;
    
    /**
     * The id of the assessment pricing configuration
     *
     * @var int
     */
    protected $_assessmentPricingConfigId;
    
    /**
     * The id of the gross margin pricing configuration
     *
     * @var int
     */
    protected $_grossMarginPricingConfigId;
    
    /**
     * The assessment pricing configuration
     *
     * @var Proposalgen_Model_PricingConfig
     */
    protected $AssessmentPricingConfig;
    /**
     * The gross margin pricing configuration
     *
     * @var Proposalgen_Model_PricingConfig
     */
    protected $GrossMarginPricingConfig;

    /**
     * Overrides all the settings.
     * Null values will be excluded.
     *
     * @param Proposalgen_Model_Report_Setting $settings
     *            These can be either a Proposalgen_Model_Report_Setting or an array of settings
     */
    public function ApplyOverride ($settings)
    {
        if ($settings instanceof Proposalgen_Model_Report_Setting)
        {
            $settings = $settings->toArray();
        }
        
        $this->populate($settings);
    }

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
        
        // Set the fields if they were passed in
        if (isset($params->id))
            $this->setId($params->id);
        if (isset($params->actualPageCoverageMono))
            $this->setActualPageCoverageMono($params->actualPageCoverageMono);
        if (isset($params->actualPageCoverageColor))
            $this->setActualPageCoverageColor($params->actualPageCoverageColor);
        if (isset($params->serviceCostPerPage))
            $this->setServiceCostPerPage($params->serviceCostPerPage);
        if (isset($params->adminChargePerPage))
            $this->setAdminChargePerPage($params->adminChargePerPage);
        if (isset($params->assessmentReportMargin))
            $this->setAssessmentReportMargin($params->assessmentReportMargin);
        if (isset($params->grossMarginReportMargin))
            $this->setGrossMarginReportMargin($params->grossMarginReportMargin);
        if (isset($params->monthlyLeasePayment))
            $this->setMonthlyLeasePayment($params->monthlyLeasePayment);
        if (isset($params->defaultPrinterCost))
            $this->setDefaultPrinterCost($params->defaultPrinterCost);
        if (isset($params->leasedBwPerPage))
            $this->setLeasedBwPerPage($params->leasedBwPerPage);
        if (isset($params->leasedColorPerPage))
            $this->setLeasedColorPerPage($params->leasedColorPerPage);
        if (isset($params->mpsBwPerPage))
            $this->setMpsBwPerPage($params->mpsBwPerPage);
        if (isset($params->mpsColorPerPage))
            $this->setMpsColorPerPage($params->mpsColorPerPage);
        if (isset($params->kilowattsPerHour))
            $this->setKilowattsPerHour($params->kilowattsPerHour);
        if (isset($params->assessmentPricingConfigId))
            $this->setAssessmentPricingConfigId($params->assessmentPricingConfigId);
        if (isset($params->grossMarginPricingConfigId))
            $this->setGrossMarginPricingConfigId($params->grossMarginPricingConfigId);
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
                "actualPageCoverageMono" => $this->getActualPageCoverageMono(), 
                "actualPageCoverageColor" => $this->getActualPageCoverageColor(), 
                "serviceCostPerPage" => $this->getServiceCostPerPage(), 
                "adminChargePerPage" => $this->getAdminChargePerPage(), 
                "assessmentReportMargin" => $this->getAssessmentReportMargin(), 
                "grossMarginReportMargin" => $this->getGrossMarginReportMargin(), 
                "monthlyLeasePayment" => $this->getMonthlyLeasePayment(), 
                "defaultPrinterCost" => $this->getDefaultPrinterCost(), 
                "leasedBwPerPage" => $this->getLeasedBwPerPage(), 
                "leasedColorPerPage" => $this->getLeasedColorPerPage(), 
                "mpsBwPerPage" => $this->getMpsBwPerPage(), 
                "mpsColorPerPage" => $this->getMpsColorPerPage(), 
                "kilowattsPerHour" => $this->getKilowattsPerHour(), 
                "assessmentPricingConfigId" => $this->getAssessmentPricingConfigId(), 
                "grossMarginPricingConfigId" => $this->getGrossMarginPricingConfigId() 
        );
    }

    /**
     * Gets the id
     *
     * @return the $_id
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
     * Gets the actual monochrome page coverage
     *
     * @return the $_actualPageCoverageMono
     */
    public function getActualPageCoverageMono ()
    {
        return $this->_actualPageCoverageMono;
    }

    /**
     * Sets the actual monochrome page coverage
     *
     * @param number $_actualPageCoverageMono            
     */
    public function setActualPageCoverageMono ($_actualPageCoverageMono)
    {
        $this->_actualPageCoverageMono = $_actualPageCoverageMono;
        return $this;
    }

    /**
     * Gets the actual color page coverage
     *
     * @return the $_actualPageCoverageColor
     */
    public function getActualPageCoverageColor ()
    {
        return $this->_actualPageCoverageColor;
    }

    /**
     * Sets the actual color page coverage
     *
     * @param number $_actualPageCoverageColor            
     */
    public function setActualPageCoverageColor ($_actualPageCoverageColor)
    {
        $this->_actualPageCoverageColor = $_actualPageCoverageColor;
        return $this;
    }

    /**
     * Gets the service cost per page
     *
     * @return the $_serviceCostPerPage
     */
    public function getServiceCostPerPage ()
    {
        return $this->_serviceCostPerPage;
    }

    /**
     * Sets the service cost per page
     *
     * @param number $_serviceCostPerPage            
     */
    public function setServiceCostPerPage ($_serviceCostPerPage)
    {
        $this->_serviceCostPerPage = $_serviceCostPerPage;
        return $this;
    }

    /**
     * Gets the admin cost per page
     *
     * @return the $_adminCostPerPage
     */
    public function getAdminCostPerPage ()
    {
        return $this->_adminCostPerPage;
    }

    /**
     * Sets the admin cost per page
     *
     * @param number $_adminCostPerPage            
     */
    public function setAdminCostPerPage ($_adminCostPerPage)
    {
        $this->_adminCostPerPage = $_adminCostPerPage;
        return $this;
    }

    /**
     * Gets the assessment report margin
     *
     * @return the $_assessmentReportMargin
     */
    public function getAssessmentReportMargin ()
    {
        return $this->_assessmentReportMargin;
    }

    /**
     * Sets the assessment report margin
     *
     * @param number $_assessmentReportMargin            
     */
    public function setAssessmentReportMargin ($_assessmentReportMargin)
    {
        $this->_assessmentReportMargin = $_assessmentReportMargin;
        return $this;
    }

    /**
     * Gets the gross margin report margin
     *
     * @return the $_grossMarginReportMargin
     */
    public function getGrossMarginReportMargin ()
    {
        return $this->_grossMarginReportMargin;
    }

    /**
     * Sets the gross margin report margin
     *
     * @param number $_grossMarginReportMargin            
     */
    public function setGrossMarginReportMargin ($_grossMarginReportMargin)
    {
        $this->_grossMarginReportMargin = $_grossMarginReportMargin;
        return $this;
    }

    /**
     * Gets the monthly lease payment
     *
     * @return the $_monthlyLeasePayment
     */
    public function getMonthlyLeasePayment ()
    {
        return $this->_monthlyLeasePayment;
    }

    /**
     * Sets the monthly lease payment
     *
     * @param number $_monthlyLeasePayment            
     */
    public function setMonthlyLeasePayment ($_monthlyLeasePayment)
    {
        $this->_monthlyLeasePayment = $_monthlyLeasePayment;
        return $this;
    }

    /**
     * Gets the default printer cost
     *
     * @return the $_defaultPrinterCost
     */
    public function getDefaultPrinterCost ()
    {
        return $this->_defaultPrinterCost;
    }

    /**
     * Sets the default printer cost
     *
     * @param number $_defaultPrinterCost            
     */
    public function setDefaultPrinterCost ($_defaultPrinterCost)
    {
        $this->_defaultPrinterCost = $_defaultPrinterCost;
        return $this;
    }

    /**
     * Gets the leased monochrome cost per page
     *
     * @return the $_leasedBwCostPerPage
     */
    public function getLeasedBwCostPerPage ()
    {
        return $this->_leasedBwCostPerPage;
    }

    /**
     * Sets the leased monochrome cost per page
     *
     * @param number $_leasedBwCostPerPage            
     */
    public function setLeasedBwCostPerPage ($_leasedBwCostPerPage)
    {
        $this->_leasedBwCostPerPage = $_leasedBwCostPerPage;
        return $this;
    }

    /**
     * Gets the leased color cost per page
     *
     * @return the $_leasedColorCostPerPage
     */
    public function getLeasedColorCostPerPage ()
    {
        return $this->_leasedColorCostPerPage;
    }

    /**
     * Sets the leased color cost per page
     *
     * @param number $_leasedColorCostPerPage            
     */
    public function setLeasedColorCostPerPage ($_leasedColorCostPerPage)
    {
        $this->_leasedColorCostPerPage = $_leasedColorCostPerPage;
        return $this;
    }

    /**
     * Gets the MPS monochrome cost per page
     *
     * @return the $_mpsBwCostPerPage
     */
    public function getMpsBwCostPerPage ()
    {
        return $this->_mpsBwCostPerPage;
    }

    /**
     * Sets the MPS monochrome cost per page
     *
     * @param number $_mpsBwCostPerPage            
     */
    public function setMpsBwCostPerPage ($_mpsBwCostPerPage)
    {
        $this->_mpsBwCostPerPage = $_mpsBwCostPerPage;
        return $this;
    }

    /**
     * Gets the MPS color cost per page
     *
     * @return the $_mpsColorCostPerPage
     */
    public function getMpsColorCostPerPage ()
    {
        return $this->_mpsColorCostPerPage;
    }

    /**
     * Sets the MPS color cost per page
     *
     * @param number $_mpsColorCostPerPage            
     */
    public function setMpsColorCostPerPage ($_mpsColorCostPerPage)
    {
        $this->_mpsColorCostPerPage = $_mpsColorCostPerPage;
        return $this;
    }

    /**
     * Gets the cost of enegery
     *
     * @return the $_kilowattsPerHour
     */
    public function getKilowattsPerHour ()
    {
        return $this->_kilowattsPerHour;
    }

    /**
     * Sets the cost of energy
     *
     * @param number $_kilowattsPerHour            
     */
    public function setKilowattsPerHour ($_kilowattsPerHour)
    {
        $this->_kilowattsPerHour = $_kilowattsPerHour;
        return $this;
    }

    /**
     * Gets the pricing config id for the assessment
     *
     * @return the $_assessmentPricingConfigId
     */
    public function getAssessmentPricingConfigId ()
    {
        return $this->_assessmentPricingConfigId;
    }

    /**
     * Sets the pricing config id for the assessment
     *
     * @param number $_assessmentPricingConfigId            
     */
    public function setAssessmentPricingConfigId ($_assessmentPricingConfigId)
    {
        $this->_assessmentPricingConfigId = $_assessmentPricingConfigId;
        return $this;
    }

    /**
     * Gets the pricing config id for the gross margin report
     *
     * @return the $_grossMarginPricingConfigId
     */
    public function getGrossMarginPricingConfigId ()
    {
        return $this->_grossMarginPricingConfigId;
    }

    /**
     * Sets the pricing config id for the gross margin report
     *
     * @param number $_grossMarginPricingConfigId            
     */
    public function setGrossMarginPricingConfigId ($_grossMarginPricingConfigId)
    {
        $this->_grossMarginPricingConfigId = $_grossMarginPricingConfigId;
        return $this;
    }

    /**
     * Gets the asessment pricing configuration object
     *
     * @return Proposalgen_Model_PricingConfig
     */
    public function getAssessmentPricingConfig ()
    {
        if (! isset($this->AssessmentPricingConfig))
        {
            $this->AssessmentPricingConfig = Proposalgen_Model_Mapper_PricingConfig::getInstance()->find($this->getAssessmentPricingConfigId());
        }
        return $this->AssessmentPricingConfig;
    }

    /**
     *
     * @param $AssessmentPricingConfig Proposalgen_Model_PricingConfig
     *            The pricing configuration to set
     */
    public function setAssessmentPricingConfig ($AssessmentPricingConfig)
    {
        $this->AssessmentPricingConfig = $AssessmentPricingConfig;
        return $this;
    }

    /**
     * Gets the gross margin pricing configuration object
     *
     * @return Proposalgen_Model_PricingConfig
     */
    public function getGrossMarginPricingConfig ()
    {
        if (! isset($this->GrossMarginPricingConfig))
        {
            $this->GrossMarginPricingConfig = Proposalgen_Model_Mapper_PricingConfig::getInstance()->find($this->getGrossMarginPricingConfigId());
        }
        return $this->GrossMarginPricingConfig;
    }

    /**
     * Sets the gross margin pricing configutarion object
     *
     * @param $GrossMarginPricingConfig Proposalgen_Model_PricingConfig
     *            The pricing configuration to set
     */
    public function setGrossMarginPricingConfig ($GrossMarginPricingConfig)
    {
        $this->GrossMarginPricingConfig = $GrossMarginPricingConfig;
        return $this;
    }
}