<?php

/**
 * Class Application_Model_Report_Setting
 */
class Proposalgen_Model_DbModel_Report_Setting extends My_Model_Abstract
{
    /**
     * The database id
     *
     * @var int
     */
    protected $id;
    
    /**
     * The actual monochrome page coverage as a whole number
     *
     * @var int
     */
    protected $actualPageCoverageMono;
    
    /**
     * The actual color page coverage as a whole number
     *
     * @var int
     */
    protected $actualPageCoverageColor;
    
    /**
     * The service cost per page
     *
     * @var int
     */
    protected $serviceCostPerPage;
    
    /**
     * The admin cost per page
     *
     * @var int
     */
    protected $adminCostPerPage;
    
    /**
     * The margin applied to the assessment
     *
     * @var int
     */
    protected $assessmentReportMargin;
    
    /**
     * The margin applied to the gross margin
     *
     * @var int
     */
    protected $grossMarginReportMargin;
    
    /**
     * The monthly lease payment for calculation with leased printers
     *
     * @var int
     */
    protected $monthlyLeasePayment;
    
    /**
     * The default printer cost to use when a printer does not have a cost
     *
     * @var int
     */
    protected $defaultPrinterCost;
    
    /**
     * The monochrome cost per page for a leased printer
     *
     * @var int
     */
    protected $leasedBwCostPerPage;
    
    /**
     * The color cost per page for a leased printer
     *
     * @var int
     */
    protected $leasedColorCostPerPage;
    
    /**
     * The MPS monochrome cost per page
     *
     * @var int
     */
    protected $mpsBwCostPerPage;
    
    /**
     * The MPS color cost per page
     *
     * @var int
     */
    protected $mpsColorCostPerPage;
    
    /**
     * The cost of electricty
     *
     * @var int
     */
    protected $kilowattsPerHour;
    
    /**
     * The id of the assessment pricing configuration
     *
     * @var int
     */
    protected $assessmentPricingConfigId;
    
    /**
     * The id of the gross margin pricing configuration
     *
     * @var int
     */
    protected $grossMarginPricingConfigId;
}
