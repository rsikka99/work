<?php

/**
 * Class Proposalgen_Model_Report
 *
 * @author "Lee Robert"
 */
class Proposalgen_Model_Report extends Tangent_Model_Abstract
{
    protected $ReportSteps;
    protected $ReportId;
    protected $UserId;
    protected $CustomerCompanyName;
    protected $UserPricingOverride;
    protected $ReportStage;
    protected $QuestionsetId;
    protected $DateCreated;
    protected $LastModified;
    protected $ReportDate;
    protected $DevicesModified;
    protected $Settings;
    protected $ReportSettings;

    /**
     *
     * @return the $ReportId
     */
    public function getReportId ()
    {
        if (! isset($this->ReportId))
        {
            
            $this->ReportId = null;
        }
        return $this->ReportId;
    }

    /**
     *
     * @param field_type $ReportId            
     */
    public function setReportId ($ReportId)
    {
        $this->ReportId = $ReportId;
        return $this;
    }

    /**
     *
     * @return the $UserId
     */
    public function getUserId ()
    {
        if (! isset($this->UserId))
        {
            
            $this->UserId = null;
        }
        return $this->UserId;
    }

    /**
     *
     * @param field_type $UserId            
     */
    public function setUserId ($UserId)
    {
        $this->UserId = $UserId;
        return $this;
    }

    /**
     *
     * @return the $CustomerCompanyName
     */
    public function getCustomerCompanyName ()
    {
        if (! isset($this->CustomerCompanyName))
        {
            
            $this->CustomerCompanyName = null;
        }
        return $this->CustomerCompanyName;
    }

    /**
     *
     * @param field_type $CustomerCompanyName            
     */
    public function setCustomerCompanyName ($CustomerCompanyName)
    {
        $this->CustomerCompanyName = $CustomerCompanyName;
        return $this;
    }

    /**
     *
     * @return the $UserPricingOverride
     */
    public function getUserPricingOverride ()
    {
        if (! isset($this->UserPricingOverride))
        {
            
            $this->UserPricingOverride = null;
        }
        return $this->UserPricingOverride;
    }

    /**
     *
     * @param field_type $UserPricingOverride            
     */
    public function setUserPricingOverride ($UserPricingOverride)
    {
        $this->UserPricingOverride = $UserPricingOverride;
        return $this;
    }

    /**
     *
     * @return the $ReportStage
     */
    public function getReportStage ()
    {
        if (! isset($this->ReportStage))
        {
            
            $this->ReportStage = null;
        }
        return $this->ReportStage;
    }

    /**
     *
     * @param field_type $ReportStage            
     */
    public function setReportStage ($ReportStage)
    {
        $this->ReportStage = $ReportStage;
        return $this;
    }

    /**
     *
     * @return the $QuestionsetId
     */
    public function getQuestionsetId ()
    {
        if (! isset($this->QuestionsetId))
        {
            
            $this->QuestionsetId = null;
        }
        return $this->QuestionsetId;
    }

    /**
     *
     * @param field_type $QuestionsetId            
     */
    public function setQuestionsetId ($QuestionsetId)
    {
        $this->QuestionsetId = $QuestionsetId;
        return $this;
    }

    /**
     *
     * @return the $DateCreated
     */
    public function getDateCreated ()
    {
        if (! isset($this->DateCreated))
        {
            
            $this->DateCreated = null;
        }
        return $this->DateCreated;
    }

    /**
     *
     * @param field_type $DateCreated            
     */
    public function setDateCreated ($DateCreated)
    {
        $this->DateCreated = $DateCreated;
        return $this;
    }

    /**
     *
     * @return the $LastModified
     */
    public function getLastModified ()
    {
        if (! isset($this->LastModified))
        {
            
            $this->LastModified = null;
        }
        return $this->LastModified;
    }

    /**
     *
     * @param field_type $LastModified            
     */
    public function setLastModified ($LastModified)
    {
        $this->LastModified = $LastModified;
        return $this;
    }

    /**
     *
     * @return the $ReportDate
     */
    public function getReportDate ()
    {
        if (! isset($this->ReportDate))
        {
            
            $this->ReportDate = null;
        }
        return $this->ReportDate;
    }

    /**
     *
     * @param field_type $ReportDate            
     */
    public function setReportDate ($ReportDate)
    {
        $this->ReportDate = $ReportDate;
        return $this;
    }

    /**
     *
     * @return the $ReportSettings
     */
    public function getReportSettings ($getOverrides = true)
    {
        if (! isset($this->ReportSettings))
        {
            
            $settings = Proposalgen_Model_User::getCurrentUser()->getReportSettings();
            $report_date = new Zend_Date($this->getReportDate(), "yyyy-MM-dd HH:ss");
            
            $reportsettings = array (
                    "estimated_page_coverage_mono" => $this->getReportEstimatedPageCoverageMono(), 
                    "estimated_page_coverage_color" => $this->getReportEstimatedPageCoverageColor(), 
                    "actual_page_coverage_mono" => $this->getReportActualPageCoverageMono(), 
                    "actual_page_coverage_color" => $this->getReportActualPageCoverageColor(), 
                    "service_cost_per_page" => $this->getReportServiceCostPerPage(), 
                    "admin_charge_per_page" => $this->getReportAdminChargePerPage(), 
                    "pricing_margin" => $this->getReportPricingMargin(), 
                    "monthly_lease_payment" => $this->getReportMonthlyLeasePayment(), 
                    "default_printer_cost" => $this->getReportAverageNonLeasePrinterCost(), 
                    "leased_bw_per_page" => $this->getReportLeasedBwPerPage(), 
                    "leased_color_per_page" => $this->getReportLeasedColorPerPage(), 
                    "mps_bw_per_page" => $this->getReportMpsBwPerPage(), 
                    "mps_color_per_page" => $this->getReportMpsColorPerPage(), 
                    "kilowatts_per_hour" => $this->getReportKilowattsPerHour(), 
                    "pricing_config_id" => $this->getReportPricingConfigId(), 
                    "gross_margin_pricing_config_id" => $this->getReportGrossMarginPricingConfigId(), 
                    "pricing_margin" => $this->getReportPricingMargin(), 
                    "report_date" => ($report_date->toString('M/d/yyyy') > '01/31/1999' ? $report_date->toString('M/d/yyyy') : date("m/d/Y")) 
            );
            
            if ($getOverrides)
            {
                foreach ( $reportsettings as $setting => $value )
                {
                    if (! empty($value))
                    {
                        $settings [$setting] = $value;
                    }
                }
            }
            else
            {
                $settings = $reportsettings;
            }
            $this->ReportSettings = $settings;
        }
        return $this->ReportSettings;
    }

    /**
     *
     * @param field_type $ReportSettings            
     */
    public function setReportSettings ($ReportSettings)
    {
        $this->ReportSettings = $ReportSettings;
        return $this;
    }

    public function getDevicesModified ()
    {
        if (! isset($this->DevicesModified))
        {
            
            $this->DevicesModified = null;
        }
        return $this->DevicesModified;
    }

    public function setDevicesModified ($DevicesModified)
    {
        $this->DevicesModified = $DevicesModified;
        return $this;
    }

    /**
     * Gets the report steps for this report
     *
     * @return Proposalgen_Model_Report_Step
     */
    public function getReportSteps ()
    {
        if (! isset($this->ReportSteps))
        {
            $stage = ($this->getReportStage()) ?  : Proposalgen_Model_Report_Step::STEP_SURVEY_COMPANY;
            
            $this->ReportSteps = Proposalgen_Model_Report_Step::getSteps();
            
            /* @var $step Proposalgen_Model_Report_Step */
            foreach ( $this->ReportSteps as $step )
            {
                $step->setCanAccess(true);
                
                if (strcasecmp($step->getName(), $stage) === 0)
                {
                    break;
                }
            }
        }
        return $this->ReportSteps;
    }

    /**
     * Sets the report steps for this report
     *
     * @param field_type $ReportSteps            
     */
    public function setReportSteps ($ReportSteps)
    {
        $this->ReportSteps = $ReportSteps;
        return $this;
    }
}