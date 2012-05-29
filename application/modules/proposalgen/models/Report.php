<?php

/**
 * Class Proposalgen_Model_Report
 *
 * @author "Lee Robert"
 */
class Proposalgen_Model_Report extends Tangent_Model_Abstract
{
    protected $ReportId;
    protected $UserId;
    protected $CustomerCompanyName;
    protected $CompanyImageOverride;
    protected $FullCompanyImageOverride;
    protected $CompanyReportColorOverride;
    protected $UserPricingOverride;
    protected $ReportStage;
    protected $QuestionsetId;
    protected $DateCreated;
    protected $LastModified;
    protected $ReportServiceCostPerPage; /* new field */
    protected $ReportAdminChargePerPage;
    protected $ReportPricingMargin;
    protected $ReportAverageNonLeasePrinterCost;
    protected $ReportLeasedBWPerPage;
    protected $ReportLeasedColorPerPage;
    protected $ReportMPSBWPerPage;
    protected $ReportMPSColorPerPage;
    protected $ReportMonthlyLeasePayment;
    protected $ReportKilowattsPerHour;
    protected $ReportPricingConfigId;
    protected $ReportGrossMarginPricingConfigId;
    protected $ReportDefaultBWTonerCost;
    protected $ReportDefaultBWTonerYield;
    protected $ReportDefaultColorTonerCost;
    protected $ReportDefaultColorTonerYield;
    protected $ReportDefaultThreeColorTonerCost;
    protected $ReportDefaultThreeColorTonerYield;
    protected $ReportDefaultFourColorTonerCost;
    protected $ReportDefaultFourColorTonerYield;
    protected $PricingConfig;
    protected $GrossMarginPricingConfig;
    protected $ReportActualPageCoverageMono;
    protected $ReportActualPageCoverageColor;
    protected $ReportEstimatedPageCoverageMono;
    protected $ReportEstimatedPageCoverageColor;
    protected $ReportDate;
    protected $DevicesModified;
    protected $Settings;
    protected $ReportSettings;

    /**
     *
     * @return the $PricingConfig
     */
    public function getPricingConfig ()
    {
        if (! isset($this->PricingConfig))
        {
            $this->PricingConfig = Proposalgen_Model_Mapper_PricingConfig::getInstance()->find($this->getReportPricingConfigId());
        }
        return $this->PricingConfig;
    }

    /**
     *
     * @param field_type $PricingConfig            
     */
    public function setPricingConfig ($PricingConfig)
    {
        $this->PricingConfig = $PricingConfig;
        return $this;
    }

    /**
     *
     * @return the $GrossMarginPricingConfig
     */
    public function getGrossMarginPricingConfig ()
    {
        if (! isset($this->GrossMarginPricingConfig))
        {
            $this->GrossMarginPricingConfig = Proposalgen_Model_Mapper_PricingConfig::getInstance()->find($this->getReportGrossMarginPricingConfigId());
        }
        return $this->GrossMarginPricingConfig;
    }

    /**
     *
     * @param field_type $GrossMarginPricingConfig            
     */
    public function setGrossMarginPricingConfig ($GrossMarginPricingConfig)
    {
        $this->GrossMarginPricingConfig = $GrossMarginPricingConfig;
        return $this;
    }

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
     * @return the $CompanyImageOverride
     */
    public function getCompanyImageOverride ()
    {
        if (! isset($this->CompanyImageOverride))
        {
            
            $this->CompanyImageOverride = null;
        }
        return $this->CompanyImageOverride;
    }

    /**
     *
     * @param field_type $CompanyImageOverride            
     */
    public function setCompanyImageOverride ($CompanyImageOverride)
    {
        $this->CompanyImageOverride = $CompanyImageOverride;
        return $this;
    }

    /**
     *
     * @return the $CompanyReportColorOverride
     */
    public function getCompanyReportColorOverride ()
    {
        if (! isset($this->CompanyReportColorOverride))
        {
            
            $this->CompanyReportColorOverride = null;
        }
        return $this->CompanyReportColorOverride;
    }

    /**
     *
     * @param field_type $CompanyReportColorOverride            
     */
    public function setCompanyReportColorOverride ($CompanyReportColorOverride)
    {
        $this->CompanyReportColorOverride = $CompanyReportColorOverride;
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
     * @return the $IsDeleted
     */
    public function getIsDeleted ()
    {
        if (! isset($this->IsDeleted))
        {
            
            $this->IsDeleted = null;
        }
        return $this->IsDeleted;
    }

    /**
     *
     * @param field_type $IsDeleted            
     */
    public function setIsDeleted ($IsDeleted)
    {
        $this->IsDeleted = $IsDeleted;
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
     * @return the $ReportAdminChargePerPage
     */
    public function getReportAdminChargePerPage ()
    {
        if (! isset($this->ReportAdminChargePerPage))
        {
            
            $this->ReportAdminChargePerPage = null;
        }
        return $this->ReportAdminChargePerPage;
    }

    /**
     *
     * @param field_type $ReportAdminChargePerPage            
     */
    public function setReportAdminChargePerPage ($ReportAdminChargePerPage)
    {
        $this->ReportAdminChargePerPage = $ReportAdminChargePerPage;
        return $this;
    }

    /**
     *
     * @return the $ReportPricingMargin
     */
    public function getReportPricingMargin ()
    {
        if (! isset($this->ReportPricingMargin))
        {
            
            $this->ReportPricingMargin = null;
        }
        return $this->ReportPricingMargin;
    }

    /**
     *
     * @param field_type $ReportPricingMargin            
     */
    public function setReportPricingMargin ($ReportPricingMargin)
    {
        $this->ReportPricingMargin = $ReportPricingMargin;
        return $this;
    }

    /**
     *
     * @return the $ReportAverageNonLeasePrinterCost
     */
    public function getReportAverageNonLeasePrinterCost ()
    {
        if (! isset($this->ReportAverageNonLeasePrinterCost))
        {
            
            $this->ReportAverageNonLeasePrinterCost = null;
        }
        return $this->ReportAverageNonLeasePrinterCost;
    }

    /**
     *
     * @param field_type $ReportAverageNonLeasePrinterCost            
     */
    public function setReportAverageNonLeasePrinterCost ($ReportAverageNonLeasePrinterCost)
    {
        $this->ReportAverageNonLeasePrinterCost = $ReportAverageNonLeasePrinterCost;
        return $this;
    }

    /**
     *
     * @return the $ReportLeasedBWPerPage
     */
    public function getReportLeasedBWPerPage ()
    {
        if (! isset($this->ReportLeasedBWPerPage))
        {
            
            $this->ReportLeasedBWPerPage = null;
        }
        return $this->ReportLeasedBWPerPage;
    }

    /**
     *
     * @param field_type $ReportLeasedBWPerPage            
     */
    public function setReportLeasedBWPerPage ($ReportLeasedBWPerPage)
    {
        $this->ReportLeasedBWPerPage = $ReportLeasedBWPerPage;
        return $this;
    }

    /**
     *
     * @return the $ReportLeasedColorPerPage
     */
    public function getReportLeasedColorPerPage ()
    {
        if (! isset($this->ReportLeasedColorPerPage))
        {
            
            $this->ReportLeasedColorPerPage = null;
        }
        return $this->ReportLeasedColorPerPage;
    }

    /**
     *
     * @param field_type $ReportLeasedColorPerPage            
     */
    public function setReportLeasedColorPerPage ($ReportLeasedColorPerPage)
    {
        $this->ReportLeasedColorPerPage = $ReportLeasedColorPerPage;
        return $this;
    }

    /**
     *
     * @return the $ReportMPSBWPerPage
     */
    public function getReportMPSBWPerPage ()
    {
        if (! isset($this->ReportMPSBWPerPage))
        {
            
            $this->ReportMPSBWPerPage = null;
        }
        return $this->ReportMPSBWPerPage;
    }

    /**
     *
     * @param field_type $ReportMPSBWPerPage            
     */
    public function setReportMPSBWPerPage ($ReportMPSBWPerPage)
    {
        $this->ReportMPSBWPerPage = $ReportMPSBWPerPage;
        return $this;
    }

    /**
     *
     * @return the $ReportMPSColorPerPage
     */
    public function getReportMPSColorPerPage ()
    {
        if (! isset($this->ReportMPSColorPerPage))
        {
            
            $this->ReportMPSColorPerPage = null;
        }
        return $this->ReportMPSColorPerPage;
    }

    /**
     *
     * @param field_type $ReportMPSColorPerPage            
     */
    public function setReportMPSColorPerPage ($ReportMPSColorPerPage)
    {
        $this->ReportMPSColorPerPage = $ReportMPSColorPerPage;
        return $this;
    }

    /**
     *
     * @return the $ReportMonthlyLeasePayment
     */
    public function getReportMonthlyLeasePayment ()
    {
        if (! isset($this->ReportMonthlyLeasePayment))
        {
            
            $this->ReportMonthlyLeasePayment = null;
        }
        return $this->ReportMonthlyLeasePayment;
    }

    /**
     *
     * @param field_type $ReportMonthlyLeasePayment            
     */
    public function setReportMonthlyLeasePayment ($ReportMonthlyLeasePayment)
    {
        $this->ReportMonthlyLeasePayment = $ReportMonthlyLeasePayment;
        return $this;
    }

    /**
     *
     * @return the $ReportKilowattsPerHour
     */
    public function getReportKilowattsPerHour ()
    {
        if (! isset($this->ReportKilowattsPerHour))
        {
            
            $this->ReportKilowattsPerHour = null;
        }
        return $this->ReportKilowattsPerHour;
    }

    /**
     *
     * @param field_type $ReportKilowattsPerHour            
     */
    public function setReportKilowattsPerHour ($ReportKilowattsPerHour)
    {
        $this->ReportKilowattsPerHour = $ReportKilowattsPerHour;
        return $this;
    }

    /**
     *
     * @return the $ReportPricingConfigId
     */
    public function getReportPricingConfigId ()
    {
        if (! isset($this->ReportPricingConfigId))
        {
            
            $this->ReportPricingConfigId = null;
        }
        return $this->ReportPricingConfigId;
    }

    /**
     *
     * @param field_type $ReportPricingConfigId            
     */
    public function setReportPricingConfigId ($ReportPricingConfigId)
    {
        $this->ReportPricingConfigId = $ReportPricingConfigId;
        return $this;
    }

    /**
     *
     * @return the $ReportDefaultBWTonerCost
     */
    public function getReportDefaultBWTonerCost ()
    {
        if (! isset($this->ReportDefaultBWTonerCost))
        {
            
            $this->ReportDefaultBWTonerCost = null;
        }
        return $this->ReportDefaultBWTonerCost;
    }

    /**
     *
     * @param field_type $ReportDefaultBWTonerCost            
     */
    public function setReportDefaultBWTonerCost ($ReportDefaultBWTonerCost)
    {
        $this->ReportDefaultBWTonerCost = $ReportDefaultBWTonerCost;
        return $this;
    }

    /**
     *
     * @return the $ReportDefaultBWTonerYield
     */
    public function getReportDefaultBWTonerYield ()
    {
        if (! isset($this->ReportDefaultBWTonerYield))
        {
            
            $this->ReportDefaultBWTonerYield = null;
        }
        return $this->ReportDefaultBWTonerYield;
    }

    /**
     *
     * @param field_type $ReportDefaultBWTonerYield            
     */
    public function setReportDefaultBWTonerYield ($ReportDefaultBWTonerYield)
    {
        $this->ReportDefaultBWTonerYield = $ReportDefaultBWTonerYield;
        return $this;
    }

    /**
     *
     * @return the $ReportDefaultColorTonerCost
     */
    public function getReportDefaultColorTonerCost ()
    {
        if (! isset($this->ReportDefaultColorTonerCost))
        {
            
            $this->ReportDefaultColorTonerCost = null;
        }
        return $this->ReportDefaultColorTonerCost;
    }

    /**
     *
     * @param field_type $ReportDefaultColorTonerCost            
     */
    public function setReportDefaultColorTonerCost ($ReportDefaultColorTonerCost)
    {
        $this->ReportDefaultColorTonerCost = $ReportDefaultColorTonerCost;
        return $this;
    }

    /**
     *
     * @return the $ReportDefaultColorTonerYield
     */
    public function getReportDefaultColorTonerYield ()
    {
        if (! isset($this->ReportDefaultColorTonerYield))
        {
            
            $this->ReportDefaultColorTonerYield = null;
        }
        return $this->ReportDefaultColorTonerYield;
    }

    /**
     *
     * @param field_type $ReportDefaultColorTonerYield            
     */
    public function setReportDefaultColorTonerYield ($ReportDefaultColorTonerYield)
    {
        $this->ReportDefaultColorTonerYield = $ReportDefaultColorTonerYield;
        return $this;
    }

    /**
     *
     * @return the $ReportDefaultThreeColorTonerCost
     */
    public function getReportDefaultThreeColorTonerCost ()
    {
        if (! isset($this->ReportDefaultThreeColorTonerCost))
        {
            
            $this->ReportDefaultThreeColorTonerCost = null;
        }
        return $this->ReportDefaultThreeColorTonerCost;
    }

    /**
     *
     * @param field_type $ReportDefaultThreeColorTonerCost            
     */
    public function setReportDefaultThreeColorTonerCost ($ReportDefaultThreeColorTonerCost)
    {
        $this->ReportDefaultThreeColorTonerCost = $ReportDefaultThreeColorTonerCost;
        return $this;
    }

    /**
     *
     * @return the $ReportDefaultThreeColorTonerYield
     */
    public function getReportDefaultThreeColorTonerYield ()
    {
        if (! isset($this->ReportDefaultThreeColorTonerYield))
        {
            
            $this->ReportDefaultThreeColorTonerYield = null;
        }
        return $this->ReportDefaultThreeColorTonerYield;
    }

    /**
     *
     * @param field_type $ReportDefaultThreeColorTonerYield            
     */
    public function setReportDefaultThreeColorTonerYield ($ReportDefaultThreeColorTonerYield)
    {
        $this->ReportDefaultThreeColorTonerYield = $ReportDefaultThreeColorTonerYield;
        return $this;
    }

    /**
     *
     * @return the $ReportDefaultFourColorTonerCost
     */
    public function getReportDefaultFourColorTonerCost ()
    {
        if (! isset($this->ReportDefaultFourColorTonerCost))
        {
            
            $this->ReportDefaultFourColorTonerCost = null;
        }
        return $this->ReportDefaultFourColorTonerCost;
    }

    /**
     *
     * @param field_type $ReportDefaultFourColorTonerCost            
     */
    public function setReportDefaultFourColorTonerCost ($ReportDefaultFourColorTonerCost)
    {
        $this->ReportDefaultFourColorTonerCost = $ReportDefaultFourColorTonerCost;
        return $this;
    }

    /**
     *
     * @return the $ReportDefaultFourColorTonerYield
     */
    public function getReportDefaultFourColorTonerYield ()
    {
        if (! isset($this->ReportDefaultFourColorTonerYield))
        {
            
            $this->ReportDefaultFourColorTonerYield = null;
        }
        return $this->ReportDefaultFourColorTonerYield;
    }

    /**
     *
     * @param field_type $ReportDefaultFourColorTonerYield            
     */
    public function setReportDefaultFourColorTonerYield ($ReportDefaultFourColorTonerYield)
    {
        $this->ReportDefaultFourColorTonerYield = $ReportDefaultFourColorTonerYield;
        return $this;
    }

    /**
     *
     * @return the $FullCompanyImageOverride
     */
    public function getFullCompanyImageOverride ()
    {
        if (! isset($this->FullCompanyImageOverride))
        {
            
            $this->FullCompanyImageOverride = null;
        }
        return $this->FullCompanyImageOverride;
    }

    /**
     *
     * @param field_type $FullCompanyImageOverride            
     */
    public function setFullCompanyImageOverride ($FullCompanyImageOverride)
    {
        $this->FullCompanyImageOverride = $FullCompanyImageOverride;
        return $this;
    }

    /**
     *
     * @return the $ReportServiceCostPerPage
     */
    public function getReportServiceCostPerPage ()
    {
        if (! isset($this->ReportServiceCostPerPage))
        {
            
            $this->ReportServiceCostPerPage = null;
        }
        return $this->ReportServiceCostPerPage;
    }

    /**
     *
     * @param field_type $ReportServiceCostPerPage            
     */
    public function setReportServiceCostPerPage ($ReportServiceCostPerPage)
    {
        $this->ReportServiceCostPerPage = $ReportServiceCostPerPage;
        return $this;
    }

    /**
     *
     * @return the $ReportActualPageCoverageMono
     */
    public function getReportActualPageCoverageMono ()
    {
        if (! isset($this->ReportActualPageCoverageMono))
        {
            
            $this->ReportActualPageCoverageMono = null;
        }
        return $this->ReportActualPageCoverageMono;
    }

    /**
     *
     * @return the $ReportActualPageCoverageColor
     */
    public function getReportActualPageCoverageColor ()
    {
        if (! isset($this->ReportActualPageCoverageColor))
        {
            
            $this->ReportActualPageCoverageColor = null;
        }
        return $this->ReportActualPageCoverageColor;
    }

    /**
     *
     * @param field_type $ReportActualPageCoverageMono            
     */
    public function setReportActualPageCoverageMono ($ReportActualPageCoverageMono)
    {
        $this->ReportActualPageCoverageMono = $ReportActualPageCoverageMono;
        return $this;
    }

    /**
     *
     * @param field_type $ReportActualPageCoverageColor            
     */
    public function setReportActualPageCoverageColor ($ReportActualPageCoverageColor)
    {
        $this->ReportActualPageCoverageColor = $ReportActualPageCoverageColor;
        return $this;
    }

    /**
     *
     * @return the $ReportEstimatedPageCoverageMono
     */
    public function getReportEstimatedPageCoverageMono ()
    {
        if (! isset($this->ReportEstimatedPageCoverageMono))
        {
            
            $this->ReportEstimatedPageCoverageMono = null;
        }
        return $this->ReportEstimatedPageCoverageMono;
    }

    /**
     *
     * @param field_type $ReportEstimatedPageCoverageMono            
     */
    public function setReportEstimatedPageCoverageMono ($ReportEstimatedPageCoverageMono)
    {
        $this->ReportEstimatedPageCoverageMono = $ReportEstimatedPageCoverageMono;
        return $this;
    }

    /**
     *
     * @return the $ReportEstimatedPageCoverageColor
     */
    public function getReportEstimatedPageCoverageColor ()
    {
        if (! isset($this->ReportEstimatedPageCoverageColor))
        {
            
            $this->ReportEstimatedPageCoverageColor = null;
        }
        return $this->ReportEstimatedPageCoverageColor;
    }

    /**
     *
     * @param field_type $ReportEstimatedPageCoverageColor            
     */
    public function setReportEstimatedPageCoverageColor ($ReportEstimatedPageCoverageColor)
    {
        $this->ReportEstimatedPageCoverageColor = $ReportEstimatedPageCoverageColor;
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

    /**
     *
     * @return the $ReportGrossMarginPricingConfigId
     */
    public function getReportGrossMarginPricingConfigId ()
    {
        if (! isset($this->ReportGrossMarginPricingConfigId))
        {
            
            $this->ReportGrossMarginPricingConfigId = null;
        }
        return $this->ReportGrossMarginPricingConfigId;
    }

    /**
     *
     * @param field_type $ReportGrossMarginPricingConfigId            
     */
    public function setReportGrossMarginPricingConfigId ($ReportGrossMarginPricingConfigId)
    {
        $this->ReportGrossMarginPricingConfigId = $ReportGrossMarginPricingConfigId;
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
}