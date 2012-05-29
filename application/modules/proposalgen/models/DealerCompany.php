<?php

/**
 * Class Proposalgen_Model_DealerCompany
 *
 * @author "Kevin Jervis"
 */
class Proposalgen_Model_DealerCompany extends Tangent_Model_Abstract
{
    static $CurrentUserCompany;
    static $MasterCompany;
    protected $DealerCompanyId;
    protected $CompanyName;
    protected $CompanyLogo;
    protected $FullCompanyLogo;
    protected $CompanyReportColor;
    protected $IsDeleted;
    protected $DcEstimatedPageCoverageMono;
    protected $DcEstimatedPageCoverageColor;
    protected $DcAdminChargePerPage;
    protected $DcPricingMargin;
    protected $DcReportMargin;
    protected $DcMonthlyLeasePayment;
    protected $DcDefaultPrinterCost;
    protected $DcLeasedBwPerPage;
    protected $DcLeasedColorPerPage;
    protected $DcMpsBwPerPage;
    protected $DcMpsColorPerPage;
    protected $DcKilowattsPerHour;
    protected $PricingConfigId;
    protected $DcDefaultBWTonerCost;
    protected $DcDefaultBWTonerYield;
    protected $DcDefaultColorTonerCost;
    protected $DcDefaultColorTonerYield;
    protected $DcDefaultThreeColorTonerCost;
    protected $DcDefaultThreeColorTonerYield;
    protected $DcDefaultFourColorTonerCost;
    protected $DcDefaultFourColorTonerYield;
    protected $DcServiceCostPerPage;
    protected $DcActualPageCoverageMono;
    protected $DcActualPageCoverageColor;
    protected $IsMasterCompany;
    protected $ReportSettings;

    /**
     *
     * @return Proposalgen_Model_DealerCompany $CurrentUserCompany
     */
    public static function getCurrentUserCompany ()
    {
        if (! isset(Proposalgen_Model_DealerCompany::$CurrentUserCompany))
        {
            Proposalgen_Model_DealerCompany::$CurrentUserCompany = Proposalgen_Model_Mapper_DealerCompany::getInstance()->find(Proposalgen_Model_User::getCurrentUser()->getDealerCompanyId());
        }
        return Proposalgen_Model_DealerCompany::$CurrentUserCompany;
    }

    /**
     *
     * @param Proposalgen_Model_DealerCompany $CurrentUserCompany            
     */
    public static function setCurrentUserCompany ($CurrentUserCompany)
    {
        Proposalgen_Model_DealerCompany::$CurrentUserCompany = $CurrentUserCompany;
    }

    /**
     *
     * @return Proposalgen_Model_DealerCompany $MasterCompany
     */
    public static function getMasterCompany ()
    {
        if (! isset(Proposalgen_Model_DealerCompany::$MasterCompany))
        {
            Proposalgen_Model_DealerCompany::$MasterCompany = Proposalgen_Model_Mapper_DealerCompany::getInstance()->find(Proposalgen_Model_User::getMasterUser()->getDealerCompanyId());
            Proposalgen_Model_DealerCompany::$MasterCompany->setIsMasterCompany(true);
        }
        return Proposalgen_Model_DealerCompany::$MasterCompany;
    }

    /**
     *
     * @param Proposalgen_Model_DealerCompany $MasterCompany            
     */
    public static function setMasterCompany ($MasterCompany)
    {
        Proposalgen_Model_DealerCompany::$MasterCompany = $MasterCompany;
        Proposalgen_Model_DealerCompany::$MasterCompany->setIsMasterCompany(true);
    }

    /**
     *
     * @return the $DealerCompanyId
     */
    public function getDealerCompanyId ()
    {
        if (! isset($this->DealerCompanyId))
        {
            
            $this->DealerCompanyId = null;
        }
        return $this->DealerCompanyId;
    }

    /**
     *
     * @param field_type $DealerCompanyId            
     */
    public function setDealerCompanyId ($DealerCompanyId)
    {
        $this->DealerCompanyId = $DealerCompanyId;
        return $this;
    }

    /**
     *
     * @return the $CompanyName
     */
    public function getCompanyName ()
    {
        if (! isset($this->CompanyName))
        {
            
            $this->CompanyName = null;
        }
        return $this->CompanyName;
    }

    /**
     *
     * @param field_type $CompanyName            
     */
    public function setCompanyName ($CompanyName)
    {
        $this->CompanyName = $CompanyName;
        return $this;
    }

    /**
     *
     * @return the $CompanyLogo
     */
    public function getCompanyLogo ()
    {
        if (! isset($this->CompanyLogo))
        {
            
            $this->CompanyLogo = null;
        }
        return $this->CompanyLogo;
    }

    /**
     *
     * @param field_type $CompanyLogo            
     */
    public function setCompanyLogo ($CompanyLogo)
    {
        $this->CompanyLogo = $CompanyLogo;
        return $this;
    }

    /**
     *
     * @return the $CompanyReportColor
     */
    public function getCompanyReportColor ()
    {
        if (! isset($this->CompanyReportColor))
        {
            
            $this->CompanyReportColor = null;
        }
        return $this->CompanyReportColor;
    }

    /**
     *
     * @param field_type $CompanyReportColor            
     */
    public function setCompanyReportColor ($CompanyReportColor)
    {
        $this->CompanyReportColor = $CompanyReportColor;
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
     * @return the $DcEstimatedPageCoverageMono
     */
    public function getDcEstimatedPageCoverageMono ()
    {
        if (! isset($this->DcEstimatedPageCoverageMono))
        {
            
            $this->DcEstimatedPageCoverageMono = null;
        }
        return $this->DcEstimatedPageCoverageMono;
    }

    /**
     *
     * @param field_type $DcEstimatedPageCoverageMono            
     */
    public function setDcEstimatedPageCoverageMono ($DcEstimatedPageCoverageMono)
    {
        $this->DcEstimatedPageCoverageMono = $DcEstimatedPageCoverageMono;
        return $this;
    }

    /**
     *
     * @return the $DcEstimatedPageCoverageColor
     */
    public function getDcEstimatedPageCoverageColor ()
    {
        if (! isset($this->DcEstimatedPageCoverageColor))
        {
            
            $this->DcEstimatedPageCoverageColor = null;
        }
        return $this->DcEstimatedPageCoverageColor;
    }

    /**
     *
     * @param field_type $DcEstimatedPageCoverageColor            
     */
    public function setDcEstimatedPageCoverageColor ($DcEstimatedPageCoverageColor)
    {
        $this->DcEstimatedPageCoverageColor = $DcEstimatedPageCoverageColor;
        return $this;
    }

    /**
     *
     * @return the $DcAdminChargePerPage
     */
    public function getDcAdminChargePerPage ()
    {
        if (! isset($this->DcAdminChargePerPage))
        {
            
            $this->DcAdminChargePerPage = null;
        }
        return $this->DcAdminChargePerPage;
    }

    /**
     *
     * @param field_type $DcAdminChargePerPage            
     */
    public function setDcAdminChargePerPage ($DcAdminChargePerPage)
    {
        $this->DcAdminChargePerPage = $DcAdminChargePerPage;
        return $this;
    }

    /**
     *
     * @return the $DcPricingMargin
     */
    public function getDcPricingMargin ()
    {
        if (! isset($this->DcPricingMargin))
        {
            
            $this->DcPricingMargin = null;
        }
        return $this->DcPricingMargin;
    }

    /**
     *
     * @param field_type $DcPricingMargin            
     */
    public function setDcPricingMargin ($DcPricingMargin)
    {
        $this->DcPricingMargin = $DcPricingMargin;
        return $this;
    }

    /**
     *
     * @return the $DcMonthlyLeasePayment
     */
    public function getDcMonthlyLeasePayment ()
    {
        if (! isset($this->DcMonthlyLeasePayment))
        {
            
            $this->DcMonthlyLeasePayment = null;
        }
        return $this->DcMonthlyLeasePayment;
    }

    /**
     *
     * @param field_type $DcMonthlyLeasePayment            
     */
    public function setDcMonthlyLeasePayment ($DcMonthlyLeasePayment)
    {
        $this->DcMonthlyLeasePayment = $DcMonthlyLeasePayment;
        return $this;
    }

    /**
     *
     * @return the $DcDefaultPrinterCost
     */
    public function getDcDefaultPrinterCost ()
    {
        if (! isset($this->DcDefaultPrinterCost))
        {
            
            $this->DcDefaultPrinterCost = null;
        }
        return $this->DcDefaultPrinterCost;
    }

    /**
     *
     * @param field_type $DcDefaultPrinterCost            
     */
    public function setDcDefaultPrinterCost ($DcDefaultPrinterCost)
    {
        $this->DcDefaultPrinterCost = $DcDefaultPrinterCost;
        return $this;
    }

    /**
     *
     * @return the $DcLeasedBwPerPage
     */
    public function getDcLeasedBwPerPage ()
    {
        if (! isset($this->DcLeasedBwPerPage))
        {
            
            $this->DcLeasedBwPerPage = null;
        }
        return $this->DcLeasedBwPerPage;
    }

    /**
     *
     * @param field_type $DcLeasedBwPerPage            
     */
    public function setDcLeasedBwPerPage ($DcLeasedBwPerPage)
    {
        $this->DcLeasedBwPerPage = $DcLeasedBwPerPage;
        return $this;
    }

    /**
     *
     * @return the $DcLeasedColorPerPage
     */
    public function getDcLeasedColorPerPage ()
    {
        if (! isset($this->DcLeasedColorPerPage))
        {
            
            $this->DcLeasedColorPerPage = null;
        }
        return $this->DcLeasedColorPerPage;
    }

    /**
     *
     * @param field_type $DcLeasedColorPerPage            
     */
    public function setDcLeasedColorPerPage ($DcLeasedColorPerPage)
    {
        $this->DcLeasedColorPerPage = $DcLeasedColorPerPage;
        return $this;
    }

    /**
     *
     * @return the $DcMpsBwPerPage
     */
    public function getDcMpsBwPerPage ()
    {
        if (! isset($this->DcMpsBwPerPage))
        {
            
            $this->DcMpsBwPerPage = null;
        }
        return $this->DcMpsBwPerPage;
    }

    /**
     *
     * @param field_type $DcMpsBwPerPage            
     */
    public function setDcMpsBwPerPage ($DcMpsBwPerPage)
    {
        $this->DcMpsBwPerPage = $DcMpsBwPerPage;
        return $this;
    }

    /**
     *
     * @return the $DcMpsColorPerPage
     */
    public function getDcMpsColorPerPage ()
    {
        if (! isset($this->DcMpsColorPerPage))
        {
            
            $this->DcMpsColorPerPage = null;
        }
        return $this->DcMpsColorPerPage;
    }

    /**
     *
     * @param field_type $DcMpsColorPerPage            
     */
    public function setDcMpsColorPerPage ($DcMpsColorPerPage)
    {
        $this->DcMpsColorPerPage = $DcMpsColorPerPage;
        return $this;
    }

    /**
     *
     * @return the $DcKilowattsPerHour
     */
    public function getDcKilowattsPerHour ()
    {
        if (! isset($this->DcKilowattsPerHour))
        {
            
            $this->DcKilowattsPerHour = null;
        }
        return $this->DcKilowattsPerHour;
    }

    /**
     *
     * @param field_type $DcKilowattsPerHour            
     */
    public function setDcKilowattsPerHour ($DcKilowattsPerHour)
    {
        $this->DcKilowattsPerHour = $DcKilowattsPerHour;
        return $this;
    }

    /**
     *
     * @return the $PricingConfigId
     */
    public function getPricingConfigId ()
    {
        if (! isset($this->PricingConfigId))
        {
            
            $this->PricingConfigId = null;
        }
        return $this->PricingConfigId;
    }

    /**
     *
     * @param field_type $PricingConfigId            
     */
    public function setPricingConfigId ($PricingConfigId)
    {
        $this->PricingConfigId = $PricingConfigId;
        return $this;
    }

    /**
     *
     * @return the $DcDefaultBWTonerCost
     */
    public function getDcDefaultBWTonerCost ()
    {
        if (! isset($this->DcDefaultBWTonerCost))
        {
            
            $this->DcDefaultBWTonerCost = null;
        }
        return $this->DcDefaultBWTonerCost;
    }

    /**
     *
     * @param field_type $DcDefaultBWTonerCost            
     */
    public function setDcDefaultBWTonerCost ($DcDefaultBWTonerCost)
    {
        $this->DcDefaultBWTonerCost = $DcDefaultBWTonerCost;
        return $this;
    }

    /**
     *
     * @return the $DcDefaultBWTonerYield
     */
    public function getDcDefaultBWTonerYield ()
    {
        if (! isset($this->DcDefaultBWTonerYield))
        {
            
            $this->DcDefaultBWTonerYield = null;
        }
        return $this->DcDefaultBWTonerYield;
    }

    /**
     *
     * @param field_type $DcDefaultBWTonerYield            
     */
    public function setDcDefaultBWTonerYield ($DcDefaultBWTonerYield)
    {
        $this->DcDefaultBWTonerYield = $DcDefaultBWTonerYield;
        return $this;
    }

    /**
     *
     * @return the $DcDefaultColorTonerCost
     */
    public function getDcDefaultColorTonerCost ()
    {
        if (! isset($this->DcDefaultColorTonerCost))
        {
            
            $this->DcDefaultColorTonerCost = null;
        }
        return $this->DcDefaultColorTonerCost;
    }

    /**
     *
     * @param field_type $DcDefaultColorTonerCost            
     */
    public function setDcDefaultColorTonerCost ($DcDefaultColorTonerCost)
    {
        $this->DcDefaultColorTonerCost = $DcDefaultColorTonerCost;
        return $this;
    }

    /**
     *
     * @return the $DcDefaultColorTonerYield
     */
    public function getDcDefaultColorTonerYield ()
    {
        if (! isset($this->DcDefaultColorTonerYield))
        {
            
            $this->DcDefaultColorTonerYield = null;
        }
        return $this->DcDefaultColorTonerYield;
    }

    /**
     *
     * @param field_type $DcDefaultColorTonerYield            
     */
    public function setDcDefaultColorTonerYield ($DcDefaultColorTonerYield)
    {
        $this->DcDefaultColorTonerYield = $DcDefaultColorTonerYield;
        return $this;
    }

    /**
     *
     * @return the $DcDefaultThreeColorTonerCost
     */
    public function getDcDefaultThreeColorTonerCost ()
    {
        if (! isset($this->DcDefaultThreeColorTonerCost))
        {
            
            $this->DcDefaultThreeColorTonerCost = null;
        }
        return $this->DcDefaultThreeColorTonerCost;
    }

    /**
     *
     * @param field_type $DcDefaultThreeColorTonerCost            
     */
    public function setDcDefaultThreeColorTonerCost ($DcDefaultThreeColorTonerCost)
    {
        $this->DcDefaultThreeColorTonerCost = $DcDefaultThreeColorTonerCost;
        return $this;
    }

    /**
     *
     * @return the $DcDefaultThreeColorTonerYield
     */
    public function getDcDefaultThreeColorTonerYield ()
    {
        if (! isset($this->DcDefaultThreeColorTonerYield))
        {
            
            $this->DcDefaultThreeColorTonerYield = null;
        }
        return $this->DcDefaultThreeColorTonerYield;
    }

    /**
     *
     * @param field_type $DcDefaultThreeColorTonerYield            
     */
    public function setDcDefaultThreeColorTonerYield ($DcDefaultThreeColorTonerYield)
    {
        $this->DcDefaultThreeColorTonerYield = $DcDefaultThreeColorTonerYield;
        return $this;
    }

    /**
     *
     * @return the $DcDefaultFourColorTonerCost
     */
    public function getDcDefaultFourColorTonerCost ()
    {
        if (! isset($this->DcDefaultFourColorTonerCost))
        {
            
            $this->DcDefaultFourColorTonerCost = null;
        }
        return $this->DcDefaultFourColorTonerCost;
    }

    /**
     *
     * @param field_type $DcDefaultFourColorTonerCost            
     */
    public function setDcDefaultFourColorTonerCost ($DcDefaultFourColorTonerCost)
    {
        $this->DcDefaultFourColorTonerCost = $DcDefaultFourColorTonerCost;
        return $this;
    }

    /**
     *
     * @return the $DcDefaultFourColorTonerYield
     */
    public function getDcDefaultFourColorTonerYield ()
    {
        if (! isset($this->DcDefaultFourColorTonerYield))
        {
            
            $this->DcDefaultFourColorTonerYield = null;
        }
        return $this->DcDefaultFourColorTonerYield;
    }

    /**
     *
     * @param field_type $DcDefaultFourColorTonerYield            
     */
    public function setDcDefaultFourColorTonerYield ($DcDefaultFourColorTonerYield)
    {
        $this->DcDefaultFourColorTonerYield = $DcDefaultFourColorTonerYield;
        return $this;
    }

    /**
     *
     * @return the $FullCompanyLogo
     */
    public function getFullCompanyLogo ()
    {
        if (! isset($this->FullCompanyLogo))
        {
            
            $this->FullCompanyLogo = null;
        }
        return $this->FullCompanyLogo;
    }

    /**
     *
     * @param field_type $FullCompanyLogo            
     */
    public function setFullCompanyLogo ($FullCompanyLogo)
    {
        $this->FullCompanyLogo = $FullCompanyLogo;
        return $this;
    }

    /**
     *
     * @return the $DcServiceCostPerPage
     */
    public function getDcServiceCostPerPage ()
    {
        if (! isset($this->DcServiceCostPerPage))
        {
            
            $this->DcServiceCostPerPage = null;
        }
        return $this->DcServiceCostPerPage;
    }

    /**
     *
     * @param field_type $DcServiceCostPerPage            
     */
    public function setDcServiceCostPerPage ($DcServiceCostPerPage)
    {
        $this->DcServiceCostPerPage = $DcServiceCostPerPage;
        return $this;
    }

    /**
     *
     * @return the $DcActualPageCoverageMono
     */
    public function getDcActualPageCoverageMono ()
    {
        if (! isset($this->DcActualPageCoverageMono))
        {
            
            $this->DcActualPageCoverageMono = null;
        }
        return $this->DcActualPageCoverageMono;
    }

    /**
     *
     * @return the $DcActualPageCoverageColor
     */
    public function getDcActualPageCoverageColor ()
    {
        if (! isset($this->DcActualPageCoverageColor))
        {
            
            $this->DcActualPageCoverageColor = null;
        }
        return $this->DcActualPageCoverageColor;
    }

    /**
     *
     * @param field_type $DcActualPageCoverageMono            
     */
    public function setDcActualPageCoverageMono ($DcActualPageCoverageMono)
    {
        $this->DcActualPageCoverageMono = $DcActualPageCoverageMono;
        return $this;
    }

    /**
     *
     * @param field_type $DcActualPageCoverageColor            
     */
    public function setDcActualPageCoverageColor ($DcActualPageCoverageColor)
    {
        $this->DcActualPageCoverageColor = $DcActualPageCoverageColor;
        return $this;
    }

    /**
     *
     * @return the $IsMasterCompany
     */
    public function getIsMasterCompany ()
    {
        if (! isset($this->IsMasterCompany))
        {
            $this->IsMasterCompany = false;
        }
        return $this->IsMasterCompany;
    }

    /**
     *
     * @param field_type $IsMasterCompany            
     */
    public function setIsMasterCompany ($IsMasterCompany)
    {
        $this->IsMasterCompany = $IsMasterCompany;
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
            $settings = null;
            
            $dealersettings = array (
                    "estimated_page_coverage_mono" => $this->getDcEstimatedPageCoverageMono(), 
                    "estimated_page_coverage_color" => $this->getDcEstimatedPageCoverageColor(), 
                    "actual_page_coverage_mono" => $this->getDcActualPageCoverageMono(), 
                    "actual_page_coverage_color" => $this->getDcActualPageCoverageColor(), 
                    "service_cost_per_page" => $this->getDcServiceCostPerPage(), 
                    "admin_charge_per_page" => $this->getDcAdminChargePerPage(), 
                    "pricing_margin" => $this->getDcReportMargin(), 
                    "monthly_lease_payment" => $this->getDcMonthlyLeasePayment(), 
                    "default_printer_cost" => $this->getDcDefaultPrinterCost(), 
                    "leased_bw_per_page" => $this->getDcLeasedBwPerPage(), 
                    "leased_color_per_page" => $this->getDcLeasedColorPerPage(), 
                    "mps_bw_per_page" => $this->getDcMpsBwPerPage(), 
                    "mps_color_per_page" => $this->getDcMpsColorPerPage(), 
                    "kilowatts_per_hour" => $this->getDcKilowattsPerHour(), 
                    "pricing_config_id" => $this->getPricingConfigId() 
            );
            
            if (! $this->getIsMasterCompany())
            {
                $settings = Proposalgen_Model_DealerCompany::getMasterCompany()->getReportSettings();
                $dealersettings ['pricing_config_id'] = $settings ['pricing_config_id'];
            }
            
            if (! is_null($settings) && $getOverrides)
            {
                if ($dealersettings ["pricing_config_id"] === 1)
                {
                    $dealersettings ["pricing_config_id"] = null;
                }
                foreach ( $dealersettings as $setting => $value )
                {
                    if (! empty($value))
                    {
                        $settings [$setting] = $value;
                    }
                }
            }
            else
            {
                $settings = $dealersettings;
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
     * @return the $DcReportMargin
     */
    public function getDcReportMargin ()
    {
        if (! isset($this->DcReportMargin))
        {
            
            $this->DcReportMargin = null;
        }
        return $this->DcReportMargin;
    }

    /**
     *
     * @param field_type $DcReportMargin            
     */
    public function setDcReportMargin ($DcReportMargin)
    {
        $this->DcReportMargin = $DcReportMargin;
        return $this;
    }
}