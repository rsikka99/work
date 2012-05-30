<?php

/**
 * Class Application_Model_Report_Setting
 */
class Proposalgen_Model_Report_Setting extends Proposalgen_Model_DbModel_Report_Setting
{
    protected $AssessmentPricingConfig;
    protected $GrossMarginPricingConfig;

    public function getSettingsAsArray ()
    {
        $settings = array (
                "actual_page_coverage_color" => $this->getActualPageCoverageColor(), 
                "actual_page_coverage_mono" => $this->getActualPageCoverageMono(), 
                "admin_cost_per_page" => $this->getAdminCostPerPage(), 
                "assessment_pricing_config_id" => $this->getAssessmentPricingConfigId(), 
                "average_purchased_printer_cost" => $this->getAveragePurchasedPrinterCost(), 
                "gross_margin_pricing_config_id" => $this->getGrossMarginPricingConfigId(), 
                "kilowatts_per_hour" => $this->getKilowattsPerHour(), 
                "labor_cost_per_month" => $this->getLaborCostPerMonth(), 
                "labor_cost_per_page" => $this->getLaborCostPerPage(), 
                "leased_cost_per_page_color" => $this->getLeasedCostPerPageColor(), 
                "leased_cost_per_page_mono" => $this->getLeasedCostPerPageMono(), 
                "monthly_lease_payment" => $this->getMonthlyLeasePayment(), 
                "parts_cost_per_page" => $this->getPartsCostPerPage(), 
                "parts_cost_per_month" => $this->getPartsCostPerMonth(), 
                "report_color_override" => $this->getReportColorOverride(), 
                "assessment_pricing_margin" => $this->getAssessmentPricingMargin(), 
                "gross_margin_pricing_margin" => $this->getGrossMarginPricingMargin(), 
                "service_billing_preference" => $this->getServiceBillingPreference() 
        );
        return $settings;
    }

    public function ApplyOverride ($settings)
    {
        $OverrideSettings = array ();
        if ($settings instanceof Proposalgen_Model_Report_Setting)
        {
            $OverrideSettings = $settings->getSettingsAsArray();
        }
        else
        {
            if (is_array($settings))
            {
                $OverrideSettings = $settings;
            }
            else
            {
                throw new Exception("You must pass an array or instance of " . get_class($this));
            }
        }
        
        $newSettings = array ();
        foreach ( $OverrideSettings as $key => $setting )
        {
            if (! is_null($setting))
            {
                $newSettings [$key] = $setting;
            }
        }
        
        // A bit of a hack, taking advantage that we use the db column names to
        // identify
        // settings within a form. This way we can override settings with it
        $this->setOptionsFromDb($newSettings);
    }

    /**
     *
     * @return the $AssessmentPricingConfig
     */
    public function getAssessmentPricingConfig ()
    {
        if (! isset($this->AssessmentPricingConfig))
        {
            $this->AssessmentPricingConfig = Application_Model_Mapper_PricingConfig::getInstance()->find($this->getAssessmentPricingConfigId());
        }
        return $this->AssessmentPricingConfig;
    }

    /**
     *
     * @param $AssessmentPricingConfig field_type            
     */
    public function setAssessmentPricingConfig ($AssessmentPricingConfig)
    {
        $this->AssessmentPricingConfig = $AssessmentPricingConfig;
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
            $this->GrossMarginPricingConfig = Application_Model_Mapper_PricingConfig::getInstance()->find($this->getGrossMarginPricingConfigId());
        }
        return $this->GrossMarginPricingConfig;
    }

    /**
     *
     * @param $GrossMarginPricingConfig field_type            
     */
    public function setGrossMarginPricingConfig ($GrossMarginPricingConfig)
    {
        $this->GrossMarginPricingConfig = $GrossMarginPricingConfig;
        return $this;
    }
}