<?php

class Proposalgen_Model_Mapper_DealerCompany extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_DealerCompany";
    static $_instance;

    /**
     *
     * @return Tangent_Model_Mapper_Abstract
     */
    public static function getInstance ()
    {
        if (! isset(self::$_instance))
        {
            $className = get_class();
            self::$_instance = new $className();
        }
        return self::$_instance;
    }

    /**
     * Maps a database row object to an Application_Model
     *
     * @param Zend_Db_Table_Row $row            
     * @return The appropriate Application_Model
     */
    public function mapRowToObject (Zend_Db_Table_Row $row)
    {
        $object = null;
        try
        {
            $object = new Proposalgen_Model_DealerCompany();
            $object->setDealerCompanyId($row->dealer_company_id)
                ->setCompanyName($row->company_name)
                ->setCompanyLogo($row->company_logo)
                ->setFullCompanyLogo($row->full_company_logo)
                ->setCompanyReportColor($row->company_report_color)
                ->setIsDeleted($row->is_deleted)
                ->setDcEstimatedPageCoverageMono($row->dc_estimated_page_coverage_mono)
                ->setDcEstimatedPageCoverageColor($row->dc_estimated_page_coverage_color)
                ->setDcAdminChargePerPage($row->dc_admin_charge_per_page)
                ->setDcPricingMargin($row->dc_pricing_margin)
                ->setDcReportMargin($row->dc_report_margin)
                ->setDcMonthlyLeasePayment($row->dc_monthly_lease_payment)
                ->setDcDefaultPrinterCost($row->dc_default_printer_cost)
                ->setDcLeasedBwPerPage($row->dc_leased_bw_per_page)
                ->setDcServiceCostPerPage($row->dc_service_cost_per_page)
                ->setDcLeasedColorPerPage($row->dc_leased_color_per_page)
                ->setDcMpsBwPerPage($row->dc_mps_bw_per_page)
                ->setDcMpsColorPerPage($row->dc_mps_color_per_page)
                ->setDcKilowattsPerHour($row->dc_kilowatts_per_hour)
                ->setPricingConfigId($row->pricing_config_id)
                ->setDcDefaultBWTonerCost($row->dc_default_BW_toner_cost)
                ->setDcDefaultBWTonerYield($row->dc_default_BW_toner_yield)
                ->setDcDefaultColorTonerCost($row->dc_default_color_toner_cost)
                ->setDcDefaultColorTonerYield($row->dc_default_color_toner_yield)
                ->setDcDefaultThreeColorTonerCost($row->dc_default_three_color_toner_cost)
                ->setDcDefaultThreeColorTonerYield($row->dc_default_three_color_toner_yield)
                ->setDcDefaultFourColorTonerCost($row->dc_default_four_color_toner_cost)
                ->setDcDefaultFourColorTonerYield($row->dc_default_four_color_toner_yield)
                ->setDcActualPageCoverageMono($row->dc_actual_page_coverage_mono)
                ->setDcActualPageCoverageColor($row->dc_actual_page_coverage_color);
        }
        catch ( Exception $e )
        {
            throw new Exception("Failed to map a dealer company row", 0, $e);
        }
        return $object;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     *
     * @param unknown_type $object            
     */
    public function save (Proposalgen_Model_DealerCompany $object)
    {
        $primaryKey = 0;
        try
        {
            $data ["dealer_company_id"] = $object->getDealerCompanyId();
            $data ["company_name"] = $object->getCompanyName();
            $data ["company_logo"] = $object->getCompanyLogo();
            $data ["full_company_logo"] = $object->getFullCompanyLogo();
            $data ["company_report_color"] = $object->getCompanyReportColor();
            $data ["is_deleted"] = $object->getIsDeleted();
            $data ["dc_estimated_page_coverage_mono"] = $object->getDcEstimatedPageCoverageMono();
            $data ["dc_estimated_page_coverage_color"] = $object->getDcEstimatedPageCoverageColor();
            $data ["dc_admin_charge_per_page"] = $object->getDcAdminChargePerPage();
            $data ["dc_pricing_margin"] = $object->getDcPricingMargin();
            $data ["dc_report_margin"] = $object->getDcReportMargin();
            $data ["dc_monthly_lease_payment"] = $object->getDcMonthlyLeasePayment();
            $data ["dc_default_printer_cost"] = $object->getDcDefaultPrinterCost();
            $data ["dc_leased_bw_per_page"] = $object->getDcLeasedBwPerPage();
            $data ["dc_service_cost_per_page"] = $object->getDcServiceCostPerPage();
            $data ["dc_leased_color_per_page"] = $object->getDcLeasedColorPerPage();
            $data ["dc_mps_bw_per_page"] = $object->getDcMpsBwPerPage();
            $data ["dc_mps_color_per_page"] = $object->getDcMpsColorPerPage();
            $data ["dc_kilowatts_per_hour"] = $object->getDcKilowattsPerHour();
            $data ["pricing_config_id"] = $object->getPricingConfigId();
            $data ["dc_default_BW_toner_cost"] = $object->getDcDefaultBWTonerCost();
            $data ["dc_default_BW_toner_yield"] = $object->getDcDefaultBWTonerYield();
            $data ["dc_default_color_toner_cost"] = $object->getDcDefaultColorTonerCost();
            $data ["dc_default_color_toner_yield"] = $object->getDcDefaultColorTonerYield();
            $data ["dc_default_three_color_toner_cost"] = $object->getDcDefaultThreeColorTonerCost();
            $data ["dc_default_three_color_toner_yield"] = $object->getDcDefaultThreeColorTonerYield();
            $data ["dc_default_four_color_toner_cost"] = $object->getDcDefaultFourColorTonerCost();
            $data ["dc_default_four_color_toner_yield"] = $object->getDcDefaultFourColorTonerYield();
            $data ["dc_actual_page_coverage_mono"] = $object->getDcActualPageCoverageMono();
            $data ["dc_actual_page_coverage_color"] = $object->getDcActualPageCoverageColor();
            
            $primaryKey = $this->saveRow($data);
        }
        catch ( Exception $e )
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }
        return $primaryKey;
    }
}
?>