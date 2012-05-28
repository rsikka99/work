<?php

class Proposalgen_Model_Mapper_Report extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_Reports";
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
            $object = new Proposalgen_Model_Report();
            $object->setReportId($row->report_id)
                ->setUserId($row->user_id)
                ->setCustomerCompanyName($row->customer_company_name)
                ->setCompanyImageOverride($row->company_image_override)
                ->setFullCompanyImageOverride($row->full_company_image_override)
                ->setCompanyReportColorOverride($row->company_report_color_override)
                ->setUserPricingOverride($row->user_pricing_override)
                ->setReportStage($row->report_stage)
                ->setQuestionSetId($row->questionset_id)
                ->setDateCreated($row->date_created)
                ->setLastModified($row->last_modified)
                ->setReportServiceCostPerPage($row->report_service_cost_per_page)
                ->setReportAdminChargePerPage($row->report_admin_charge_per_page)
                ->setReportPricingMargin($row->report_pricing_margin)
                ->setReportAverageNonLeasePrinterCost($row->report_avg_nonlease_printer_cost)
                ->setReportLeasedBWPerPage($row->report_leased_bw_per_page)
                ->setReportLeasedColorPerPage($row->report_leased_color_per_page)
                ->setReportMPSBWPerPage($row->report_mps_bw_per_page)
                ->setReportMPSColorPerPage($row->report_mps_color_per_page)
                ->setReportMonthlyLeasePayment($row->report_monthly_lease_payment)
                ->setReportKilowattsPerHour($row->report_kilowatts_per_hour)
                ->setReportPricingConfigId($row->report_pricing_config_id)
                ->setReportGrossMarginPricingConfigId($row->report_gross_margin_pricing_config_id)
                ->setReportDefaultBWTonerCost($row->report_default_BW_toner_cost)
                ->setReportDefaultBWTonerYield($row->report_default_BW_toner_yield)
                ->setReportDefaultColorTonerCost($row->report_default_color_toner_cost)
                ->setReportDefaultColorTonerYield($row->report_default_color_toner_yield)
                ->setReportDefaultThreeColorTonerCost($row->report_default_three_color_toner_cost)
                ->setReportDefaultThreeColorTonerYield($row->report_default_three_color_toner_yield)
                ->setReportDefaultFourColorTonerCost($row->report_default_four_color_toner_cost)
                ->setReportDefaultFourColorTonerYield($row->report_default_four_color_toner_yield)
                ->setReportActualPageCoverageMono($row->report_actual_page_coverage_mono)
                ->setReportActualPageCoverageColor($row->report_actual_page_coverage_color)
                ->setReportEstimatedPageCoverageMono($row->report_estimated_page_coverage_mono)
                ->setReportEstimatedPageCoverageColor($row->report_estimated_page_coverage_color)
                ->setReportDate($row->report_date)
                ->setDevicesModified($row->devices_modified);
        }
        catch ( Exception $e )
        {
            throw new Exception("Failed to map a report row", 0, $e);
        }
        return $object;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     * 
     * @param unknown_type $object            
     */
    public function save (Proposalgen_Model_Report $object)
    {
        $primaryKey = 0;
        try
        {
            $data ["report_id"] = $object->getReportId();
            $data ["user_id"] = $object->getUserId();
            $data ["customer_company_name"] = $object->getCustomerCompanyName();
            $data ["company_image_override"] = $object->getCompanyImageOverride();
            $data ["user_pricing_override"] = $object->getUserPricingOverride();
            $data ["report_stage"] = $object->getReportStage();
            $data ["questionset_id"] = $object->getQuestionSetId();
            $data ["date_created"] = $object->getDateCreated();
            $data ["last_modified"] = $object->getLastModified();
            $data ["report_service_cost_per_page"] = $object->getReportServiceCostPerPage();
            $data ["report_admin_charge_per_page"] = $object->getReportAdminChargePerPage();
            $data ["report_pricing_margin"] = $object->getReportPricingMargin();
            $data ["report_avg_nonlease_printer_cost"] = $object->getReportAverageNonLeasePrinterCost();
            $data ["report_leased_bw_per_page"] = $object->getReportLeasedBWPerPage();
            $data ["report_leased_color_per_page"] = $object->getReportLeasedColorPerPage();
            $data ["report_mps_bw_per_page"] = $object->getReportMPSBWPerPage();
            $data ["report_mps_color_per_page"] = $object->getReportMPSColorPerPage();
            $data ["report_monthly_lease_payment"] = $object->getReportMonthlyLeasePayment();
            $data ["report_kilowatts_per_hour"] = $object->getReportKilowattsPerHour();
            $data ["report_pricing_config_id"] = $object->getReportPricingConfigId();
            $data ["report_gross_margin_pricing_config_id"] = $object->getReportGrossMarginPricingConfigId();
            $data ["report_default_BW_toner_cost"] = $object->getReportDefaultBWTonerCost();
            $data ["report_default_BW_toner_yield"] = $object->getReportDefaultBWTonerYield();
            $data ["report_default_color_toner_cost"] = $object->getReportDefaultColorTonerCost();
            $data ["report_default_color_toner_yield"] = $object->getReportDefaultColorTonerYield();
            $data ["report_default_three_color_toner_cost"] = $object->getReportDefaultThreeColorTonerCost();
            $data ["report_default_three_color_toner_yield"] = $object->getReportDefaultThreeColorTonerYield();
            $data ["report_default_four_color_toner_cost"] = $object->getReportDefaultFourColorTonerCost();
            $data ["report_default_four_color_toner_yield"] = $object->getReportDefaultFourColorTonerYield();
            $data ["report_default_four_color_toner_cost"] = $object->getReportDefaultFourColorTonerCost();
            $data ["report_actual_page_coverage_mono"] = $object->getReportActualPageCoverageMono();
            $data ["report_actual_page_coverage_color"] = $object->getReportActualPageCoverageColor();
            $data ["report_estimated_page_coverage_mono"] = $object->getReportEstimatedPageCoverageMono();
            $data ["report_estimated_page_coverage_color"] = $object->getReportEstimatedPageCoverageColor();
            $data ["report_date"] = $object->getReportDate();
            $data ["devices_modified"] = $object->getDevicesModified();
            
            $primaryKey = $this->saveRow($data);
        }
        catch ( Exception $e )
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }
        return $primaryKey;
    }

    /**
     * Clones a report and all related records
     *
     * @param $report_id Integer            
     * @param $user_list Array
     *            of user ids
     * @return $message Return status message
     */
    public function cloneReport ($report_id, $user_list)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql = "";
        $message = "";
        
        $report = Proposalgen_Model_Mapper_Report::getInstance()->find($report_id);
        
        $db->beginTransaction();
        try
        {
            // Get Reports
            $curReports = Proposalgen_Model_Mapper_Report::getInstance()->find($report_id);
            $reportsCustomerCompanyName = $curReports->CustomerCompanyName ? $curReports->CustomerCompanyName : 'null';
            $reportsFullCompanyImageOverride = $curReports->FullCompanyImageOverride ? $curReports->FullCompanyImageOverride : 'null';
            $reportsCompanyImageOverride = $curReports->CompanyImageOverride ? $curReports->CompanyImageOverride : 'null';
            $reportsCompanyReportColorOverride = $curReports->CompanyReportColorOverride ? $curReports->CompanyReportColorOverride : 'null';
            $reportsUserPricingOverride = $curReports->UserPricingOverride ? $curReports->UserPricingOverride : 'null';
            $reportsReportStage = $curReports->ReportStage ? $curReports->ReportStage : 'null';
            $reportsQuestionsetId = $curReports->QuestionsetId ? $curReports->QuestionsetId : 'null';
            $reportsDateCreated = $curReports->DateCreated ? $curReports->DateCreated : 'null';
            $reportsLastModified = $curReports->LastModified ? $curReports->LastModified : 'null';
            $reportsReportServiceCostPerPage = $curReports->ReportServiceCostPerPage ? $curReports->ReportServiceCostPerPage : 'null';
            $reportsReportAdminChargePerPage = $curReports->ReportAdminChargePerPage ? $curReports->ReportAdminChargePerPage : 'null';
            $reportsReportPricingMargin = $curReports->ReportPricingMargin ? $curReports->ReportPricingMargin : 'null';
            $reportsReportAverageNonLeasePrinterCost = $curReports->ReportAverageNonLeasePrinterCost ? $curReports->ReportAverageNonLeasePrinterCost : 'null';
            $reportsReportLeasedBwPerPage = $curReports->ReportLeasedBwPerPage ? $curReports->ReportLeasedBwPerPage : 'null';
            $reportsReportLeasedColorPerPage = $curReports->ReportLeasedColorPerPage ? $curReports->ReportLeasedColorPerPage : 'null';
            $reportsReportMpsBwPerPage = $curReports->ReportMpsBwPerPage ? $curReports->ReportMpsBwPerPage : 'null';
            $reportsReportMpsColorPerPage = $curReports->ReportMpsColorPerPage ? $curReports->ReportMpsColorPerPage : 'null';
            $reportsReportMonthlyLeasePayment = $curReports->ReportMonthlyLeasePayment ? $curReports->ReportMonthlyLeasePayment : 'null';
            $reportsReportKilowattsPerHour = $curReports->ReportKilowattsPerHour ? $curReports->ReportKilowattsPerHour : 'null';
            $reportsReportPricingConfigId = $curReports->ReportPricingConfigId ? $curReports->ReportPricingConfigId : 'null';
            $reportsReportGrossMarginPricingConfigId = $curReports->ReportGrossMarginPricingConfigId ? $curReports->ReportGrossMarginPricingConfigId : 'null';
            $reportsReportDefaultBwTonerCost = $curReports->ReportDefaultBwTonerCost ? $curReports->ReportDefaultBwTonerCost : 'null';
            $reportsReportDefaultBwTonerYield = $curReports->ReportDefaultBwTonerYield ? $curReports->ReportDefaultBwTonerYield : 'null';
            $reportsReportDefaultColorTonerCost = $curReports->ReportDefaultColorTonerCost ? $curReports->ReportDefaultColorTonerCost : 'null';
            $reportsReportDefaultColorTonerYield = $curReports->ReportDefaultColorTonerYield ? $curReports->ReportDefaultColorTonerYield : 'null';
            $reportsReportDefaultThreeColorTonerCost = $curReports->ReportDefaultThreeColorTonerCost ? $curReports->ReportDefaultThreeColorTonerCost : 'null';
            $reportsReportDefaultThreeColorTonerYield = $curReports->ReportDefaultThreeColorTonerYield ? $curReports->ReportDefaultThreeColorTonerYield : 'null';
            $reportsReportDefaultFourColorTonerCost = $curReports->ReportDefaultFourColorTonerCost ? $curReports->ReportDefaultFourColorTonerCost : 'null';
            $reportsReportDefaultFourColorTonerYield = $curReports->ReportDefaultFourColorTonerYield ? $curReports->ReportDefaultFourColorTonerYield : 'null';
            $reportsReportActualPageCoverageMono = $curReports->ReportActualPageCoverageMono ? $curReports->ReportActualPageCoverageMono : 'null';
            $reportsReportActualPageCoverageColor = $curReports->ReportActualPageCoverageColor ? $curReports->ReportActualPageCoverageColor : 'null';
            $reportsReportEstimatedPageCoverageMono = $curReports->ReportEstimatedPageCoverageMono ? $curReports->ReportEstimatedPageCoverageMono : 'null';
            $reportsReportEstimatedPageCoverageColor = $curReports->ReportEstimatedPageCoverageColor ? $curReports->ReportEstimatedPageCoverageColor : 'null';
            $reportsReportDate = $curReports->ReportDate ? $curReports->ReportDate : 'null';
            $reportsDevicesModified = $curReports->DevicesModified ? $curReports->DevicesModified : 'null';
            
            // Get new users array
            $users = explode(",", str_replace("'", "", $user_list));
            
            foreach ( $users as $user )
            {
                // get current user_id
                $newUserId = $user;
                
                // copy report
                $reportsTable = new Proposalgen_Model_DbTable_Reports();
                $sql = "INSERT INTO reports (";
                $sql .= "user_id, ";
                $sql .= "customer_company_name, ";
                $sql .= "company_report_color_override, ";
                $sql .= "user_pricing_override, ";
                $sql .= "report_stage, ";
                $sql .= "questionset_id, ";
                $sql .= "date_created, ";
                $sql .= "last_modified, ";
                $sql .= "report_service_cost_per_page, ";
                $sql .= "report_admin_charge_per_page, ";
                $sql .= "report_pricing_margin, ";
                $sql .= "report_avg_nonlease_printer_cost, ";
                $sql .= "report_leased_bw_per_page, ";
                $sql .= "report_leased_color_per_page, ";
                $sql .= "report_mps_bw_per_page, ";
                $sql .= "report_mps_color_per_page, ";
                $sql .= "report_monthly_lease_payment, ";
                $sql .= "report_kilowatts_per_hour, ";
                $sql .= "report_pricing_config_id, ";
                $sql .= "report_gross_margin_pricing_config_id, ";
                $sql .= "report_default_bw_toner_cost, ";
                $sql .= "report_default_bw_toner_yield, ";
                $sql .= "report_default_color_toner_cost, ";
                $sql .= "report_default_color_toner_yield, ";
                $sql .= "report_default_three_color_toner_cost, ";
                $sql .= "report_default_three_color_toner_yield, ";
                $sql .= "report_default_four_color_toner_cost, ";
                $sql .= "report_default_four_color_toner_yield, ";
                $sql .= "report_actual_page_coverage_mono, ";
                $sql .= "report_actual_page_coverage_color, ";
                $sql .= "report_estimated_page_coverage_mono, ";
                $sql .= "report_estimated_page_coverage_color, ";
                $sql .= "report_date, ";
                $sql .= "devices_modified";
                $sql .= ") VALUES (";
                $sql .= $newUserId . ", ";
                $sql .= "'" . str_replace("'", "\'", $reportsCustomerCompanyName) . "', ";
                $sql .= "'" . $reportsCompanyReportColorOverride . "', ";
                $sql .= $reportsUserPricingOverride . ", ";
                $sql .= "'" . $reportsReportStage . "', ";
                $sql .= $reportsQuestionsetId . ", ";
                $sql .= "'" . $reportsDateCreated . "', ";
                $sql .= "'" . $reportsLastModified . "', ";
                $sql .= $reportsReportServiceCostPerPage . ", ";
                $sql .= $reportsReportAdminChargePerPage . ", ";
                $sql .= $reportsReportPricingMargin . ", ";
                $sql .= $reportsReportAverageNonLeasePrinterCost . ", ";
                $sql .= $reportsReportLeasedBwPerPage . ", ";
                $sql .= $reportsReportLeasedColorPerPage . ", ";
                $sql .= $reportsReportMpsBwPerPage . ", ";
                $sql .= $reportsReportMpsColorPerPage . ", ";
                $sql .= $reportsReportMonthlyLeasePayment . ", ";
                $sql .= $reportsReportKilowattsPerHour . ", ";
                $sql .= $reportsReportPricingConfigId . ", ";
                $sql .= $reportsReportGrossMarginPricingConfigId . ", ";
                $sql .= $reportsReportDefaultBwTonerCost . ", ";
                $sql .= $reportsReportDefaultBwTonerYield . ", ";
                $sql .= $reportsReportDefaultColorTonerCost . ", ";
                $sql .= $reportsReportDefaultColorTonerYield . ", ";
                $sql .= $reportsReportDefaultThreeColorTonerCost . ", ";
                $sql .= $reportsReportDefaultThreeColorTonerYield . ", ";
                $sql .= $reportsReportDefaultFourColorTonerCost . ", ";
                $sql .= $reportsReportDefaultFourColorTonerYield . ", ";
                $sql .= $reportsReportActualPageCoverageMono . ", ";
                $sql .= $reportsReportActualPageCoverageColor . ", ";
                $sql .= $reportsReportEstimatedPageCoverageMono . ", ";
                $sql .= $reportsReportEstimatedPageCoverageColor . ", ";
                $sql .= "'" . $reportsReportDate . "', ";
                $sql .= $reportsDevicesModified . ")";
                $newReport = $reportsTable->getAdapter()->prepare($sql);
                $newReport->execute();
                $newReportId = $reportsTable->getAdapter()->lastInsertId();
                
                // update report images
                // FIXME: The above copy breaks when images reach a certain size. This method below of updating seems to work though
                //        Need to correct the above method and get rid of this update
                $reportsTable = new Proposalgen_Model_DbTable_Reports();
                $sql = "UPDATE reports r1 JOIN reports r2 SET ";
                $sql .= "r1.full_company_image_override = r2.full_company_image_override, ";
                $sql .= "r1.company_image_override = r2.company_image_override ";
                $sql .= "WHERE r1.report_id = " . $newReportId . " ";
                $sql .= "AND r2.report_id = " . $report_id . ";";
                $updateReport = $reportsTable->getAdapter()->prepare($sql);
                $updateReport->execute();
                
                // copy answers_dates
                $answerDatesTable = new Proposalgen_Model_DbTable_DateAnswers();
                $sql = "INSERT INTO answers_dates (";
                $sql .= "question_id, ";
                $sql .= "report_id, ";
                $sql .= "date_answer";
                $sql .= ") SELECT ";
                $sql .= "question_id, ";
                $sql .= $newReportId . ", ";
                $sql .= "date_answer ";
                $sql .= "FROM answers_dates ";
                $sql .= "WHERE report_id = " . $report_id . ";";
                $newAnswerDate = $answerDatesTable->getAdapter()->prepare($sql);
                $newAnswerDate->execute();
                
                // copy answers_numeric
                $answerNumericTable = new Proposalgen_Model_DbTable_NumericAnswers();
                $sql = "INSERT INTO answers_numeric (";
                $sql .= "question_id, ";
                $sql .= "report_id, ";
                $sql .= "numeric_answer";
                $sql .= ") SELECT ";
                $sql .= "question_id, ";
                $sql .= $newReportId . ", ";
                $sql .= "numeric_answer ";
                $sql .= "FROM answers_numeric ";
                $sql .= "WHERE report_id = " . $report_id . ";";
                $newAnswerNumeric = $answerNumericTable->getAdapter()->prepare($sql);
                $newAnswerNumeric->execute();
                
                // copy answers_textual
                $answerTextualTable = new Proposalgen_Model_DbTable_TextAnswers();
                $sql = "INSERT INTO answers_textual (";
                $sql .= "question_id, ";
                $sql .= "report_id, ";
                $sql .= "textual_answer";
                $sql .= ") SELECT ";
                $sql .= "question_id, ";
                $sql .= $newReportId . ", ";
                $sql .= "textual_answer ";
                $sql .= "FROM answers_textual ";
                $sql .= "WHERE report_id = " . $report_id . ";";
                $newAnswerTextual = $answerTextualTable->getAdapter()->prepare($sql);
                $newAnswerTextual->execute();
                
                // copy upload_data_collector records
                $upload_data_collector = Proposalgen_Model_Mapper_UploadDataCollector::getInstance()->fetchAll('report_id=' . $report_id);
                foreach ( $upload_data_collector as $udc )
                {
                    $udc_id = $udc->UploadDataCollectorId;
                    
                    $udcTable = new Proposalgen_Model_DbTable_UploadDataCollector();
                    $sql = "INSERT INTO upload_data_collector (";
                    $sql .= "report_id, ";
                    $sql .= "devices_pf_id, ";
                    $sql .= "startdate, ";
                    $sql .= "enddate, ";
                    $sql .= "printermodelid, ";
                    $sql .= "ipaddress, ";
                    $sql .= "serialnumber, ";
                    $sql .= "modelname, ";
                    $sql .= "manufacturer, ";
                    $sql .= "is_color, ";
                    $sql .= "is_copier, ";
                    $sql .= "is_scanner, ";
                    $sql .= "is_fax, ";
                    $sql .= "ppm_black, ";
                    $sql .= "ppm_color, ";
                    $sql .= "date_introduction, ";
                    $sql .= "date_adoption, ";
                    $sql .= "discovery_date, ";
                    $sql .= "black_prodcodeoem, ";
                    $sql .= "black_yield, ";
                    $sql .= "black_prodcostoem, ";
                    $sql .= "cyan_prodcodeoem, ";
                    $sql .= "cyan_yield, ";
                    $sql .= "cyan_prodcostoem, ";
                    $sql .= "magenta_prodcodeoem, ";
                    $sql .= "magenta_yield, ";
                    $sql .= "magenta_prodcostoem, ";
                    $sql .= "yellow_prodcodeoem, ";
                    $sql .= "yellow_yield, ";
                    $sql .= "yellow_prodcostoem, ";
                    $sql .= "duty_cycle, ";
                    $sql .= "wattspowernormal, ";
                    $sql .= "wattspoweridle, ";
                    $sql .= "startmeterlife, ";
                    $sql .= "endmeterlife, ";
                    $sql .= "startmeterblack, ";
                    $sql .= "endmeterblack, ";
                    $sql .= "startmetercolor, ";
                    $sql .= "endmetercolor, ";
                    $sql .= "startmeterprintblack, ";
                    $sql .= "endmeterprintblack, ";
                    $sql .= "startmeterprintcolor, ";
                    $sql .= "endmeterprintcolor, ";
                    $sql .= "startmetercopyblack, ";
                    $sql .= "endmetercopyblack, ";
                    $sql .= "startmetercopycolor, ";
                    $sql .= "endmetercopycolor, ";
                    $sql .= "startmeterscan, ";
                    $sql .= "endmeterscan, ";
                    $sql .= "startmeterfax, ";
                    $sql .= "endmeterfax, ";
                    $sql .= "tonerlevel_black, ";
                    $sql .= "tonerlevel_cyan, ";
                    $sql .= "tonerlevel_magenta, ";
                    $sql .= "tonerlevel_yellow, ";
                    $sql .= "invalid_data, ";
                    $sql .= "is_excluded";
                    $sql .= ") SELECT ";
                    $sql .= $newReportId . ", ";
                    $sql .= "devices_pf_id, ";
                    $sql .= "startdate, ";
                    $sql .= "enddate, ";
                    $sql .= "printermodelid, ";
                    $sql .= "ipaddress, ";
                    $sql .= "serialnumber, ";
                    $sql .= "modelname, ";
                    $sql .= "manufacturer, ";
                    $sql .= "is_color, ";
                    $sql .= "is_copier, ";
                    $sql .= "is_scanner, ";
                    $sql .= "is_fax, ";
                    $sql .= "ppm_black, ";
                    $sql .= "ppm_color, ";
                    $sql .= "date_introduction, ";
                    $sql .= "date_adoption, ";
                    $sql .= "discovery_date, ";
                    $sql .= "black_prodcodeoem, ";
                    $sql .= "black_yield, ";
                    $sql .= "black_prodcostoem, ";
                    $sql .= "cyan_prodcodeoem, ";
                    $sql .= "cyan_yield, ";
                    $sql .= "cyan_prodcostoem, ";
                    $sql .= "magenta_prodcodeoem, ";
                    $sql .= "magenta_yield, ";
                    $sql .= "magenta_prodcostoem, ";
                    $sql .= "yellow_prodcodeoem, ";
                    $sql .= "yellow_yield, ";
                    $sql .= "yellow_prodcostoem, ";
                    $sql .= "duty_cycle, ";
                    $sql .= "wattspowernormal, ";
                    $sql .= "wattspoweridle, ";
                    $sql .= "startmeterlife, ";
                    $sql .= "endmeterlife, ";
                    $sql .= "startmeterblack, ";
                    $sql .= "endmeterblack, ";
                    $sql .= "startmetercolor, ";
                    $sql .= "endmetercolor, ";
                    $sql .= "startmeterprintblack, ";
                    $sql .= "endmeterprintblack, ";
                    $sql .= "startmeterprintcolor, ";
                    $sql .= "endmeterprintcolor, ";
                    $sql .= "startmetercopyblack, ";
                    $sql .= "endmetercopyblack, ";
                    $sql .= "startmetercopycolor, ";
                    $sql .= "endmetercopycolor, ";
                    $sql .= "startmeterscan, ";
                    $sql .= "endmeterscan, ";
                    $sql .= "startmeterfax, ";
                    $sql .= "endmeterfax, ";
                    $sql .= "tonerlevel_black, ";
                    $sql .= "tonerlevel_cyan, ";
                    $sql .= "tonerlevel_magenta, ";
                    $sql .= "tonerlevel_yellow, ";
                    $sql .= "invalid_data, ";
                    $sql .= "is_excluded ";
                    $sql .= "FROM upload_data_collector ";
                    $sql .= "WHERE upload_data_collector_id = " . $udc_id . ";";
                    $newUdc = $udcTable->getAdapter()->prepare($sql);
                    $newUdc->execute();
                    $newUdcId = $udcTable->getAdapter()->lastInsertId();
                    
                    // copy unknown_device_instance records
                    $unknown_device_instance = Proposalgen_Model_Mapper_UnknownDeviceInstance::getInstance()->fetchRow('upload_data_collector_id = ' . $udc_id);
                    if ($unknown_device_instance)
                    {
                        $udi_id = $unknown_device_instance->UnknownDeviceInstanceId;
                        
                        $udiTable = new Proposalgen_Model_DbTable_UnknownDeviceInstance();
                        $sql = "INSERT INTO unknown_device_instance (";
                        $sql .= "user_id, ";
                        $sql .= "report_id, ";
                        $sql .= "upload_data_collector_id, ";
                        $sql .= "printermodelid, ";
                        $sql .= "mps_monitor_startdate, ";
                        $sql .= "mps_monitor_enddate, ";
                        $sql .= "mps_discovery_date, ";
                        $sql .= "install_date, ";
                        $sql .= "device_manufacturer, ";
                        $sql .= "printer_model, ";
                        $sql .= "printer_serial_number, ";
                        $sql .= "toner_config_id, ";
                        $sql .= "part_type_id, ";
                        $sql .= "is_copier, ";
                        $sql .= "is_fax, ";
                        $sql .= "is_duplex, ";
                        $sql .= "is_scanner, ";
                        $sql .= "watts_power_normal, ";
                        $sql .= "watts_power_idle, ";
                        $sql .= "device_price, ";
                        $sql .= "launch_date, ";
                        $sql .= "date_created, ";
                        $sql .= "black_toner_SKU, ";
                        $sql .= "black_toner_price, ";
                        $sql .= "black_toner_yield, ";
                        $sql .= "cyan_toner_SKU, ";
                        $sql .= "cyan_toner_price, ";
                        $sql .= "cyan_toner_yield, ";
                        $sql .= "magenta_toner_SKU, ";
                        $sql .= "magenta_toner_price, ";
                        $sql .= "magenta_toner_yield, ";
                        $sql .= "yellow_toner_SKU, ";
                        $sql .= "yellow_toner_price, ";
                        $sql .= "yellow_toner_yield, ";
                        $sql .= "3color_toner_SKU, ";
                        $sql .= "3color_toner_price, ";
                        $sql .= "3color_toner_yield, ";
                        $sql .= "4color_toner_SKU, ";
                        $sql .= "4color_toner_price, ";
                        $sql .= "4color_toner_yield, ";
                        $sql .= "black_comp_SKU, ";
                        $sql .= "black_comp_price, ";
                        $sql .= "black_comp_yield, ";
                        $sql .= "cyan_comp_SKU, ";
                        $sql .= "cyan_comp_price, ";
                        $sql .= "cyan_comp_yield, ";
                        $sql .= "magenta_comp_SKU, ";
                        $sql .= "magenta_comp_price, ";
                        $sql .= "magenta_comp_yield, ";
                        $sql .= "yellow_comp_SKU, ";
                        $sql .= "yellow_comp_price, ";
                        $sql .= "yellow_comp_yield, ";
                        $sql .= "3color_comp_SKU, ";
                        $sql .= "3color_comp_price, ";
                        $sql .= "3color_comp_yield, ";
                        $sql .= "4color_comp_SKU, ";
                        $sql .= "4color_comp_price, ";
                        $sql .= "4color_comp_yield, ";
                        $sql .= "start_meter_life, ";
                        $sql .= "end_meter_life, ";
                        $sql .= "start_meter_black, ";
                        $sql .= "end_meter_black, ";
                        $sql .= "start_meter_color, ";
                        $sql .= "end_meter_color, ";
                        $sql .= "start_meter_printblack, ";
                        $sql .= "end_meter_printblack, ";
                        $sql .= "start_meter_printcolor, ";
                        $sql .= "end_meter_printcolor, ";
                        $sql .= "start_meter_copyblack, ";
                        $sql .= "end_meter_copyblack, ";
                        $sql .= "start_meter_copycolor, ";
                        $sql .= "end_meter_copycolor, ";
                        $sql .= "start_meter_fax, ";
                        $sql .= "end_meter_fax, ";
                        $sql .= "start_meter_scan, ";
                        $sql .= "end_meter_scan, ";
                        $sql .= "jit_supplies_supported, ";
                        $sql .= "is_excluded, ";
                        $sql .= "is_leased, ";
                        $sql .= "ip_address, ";
                        $sql .= "duty_cycle, ";
                        $sql .= "PPM_black, ";
                        $sql .= "PPM_color, ";
                        $sql .= "service_cost_per_page";
                        $sql .= ") SELECT ";
                        $sql .= $newUserId . ", ";
                        $sql .= $newReportId . ", ";
                        $sql .= $newUdcId . ", ";
                        $sql .= "printermodelid, ";
                        $sql .= "mps_monitor_startdate, ";
                        $sql .= "mps_monitor_enddate, ";
                        $sql .= "mps_discovery_date, ";
                        $sql .= "install_date, ";
                        $sql .= "device_manufacturer, ";
                        $sql .= "printer_model, ";
                        $sql .= "printer_serial_number, ";
                        $sql .= "toner_config_id, ";
                        $sql .= "part_type_id, ";
                        $sql .= "is_copier, ";
                        $sql .= "is_fax, ";
                        $sql .= "is_duplex, ";
                        $sql .= "is_scanner, ";
                        $sql .= "watts_power_normal, ";
                        $sql .= "watts_power_idle, ";
                        $sql .= "device_price, ";
                        $sql .= "launch_date, ";
                        $sql .= "date_created, ";
                        $sql .= "black_toner_SKU, ";
                        $sql .= "black_toner_price, ";
                        $sql .= "black_toner_yield, ";
                        $sql .= "cyan_toner_SKU, ";
                        $sql .= "cyan_toner_price, ";
                        $sql .= "cyan_toner_yield, ";
                        $sql .= "magenta_toner_SKU, ";
                        $sql .= "magenta_toner_price, ";
                        $sql .= "magenta_toner_yield, ";
                        $sql .= "yellow_toner_SKU, ";
                        $sql .= "yellow_toner_price, ";
                        $sql .= "yellow_toner_yield, ";
                        $sql .= "3color_toner_SKU, ";
                        $sql .= "3color_toner_price, ";
                        $sql .= "3color_toner_yield, ";
                        $sql .= "4color_toner_SKU, ";
                        $sql .= "4color_toner_price, ";
                        $sql .= "4color_toner_yield, ";
                        $sql .= "black_comp_SKU, ";
                        $sql .= "black_comp_price, ";
                        $sql .= "black_comp_yield, ";
                        $sql .= "cyan_comp_SKU, ";
                        $sql .= "cyan_comp_price, ";
                        $sql .= "cyan_comp_yield, ";
                        $sql .= "magenta_comp_SKU, ";
                        $sql .= "magenta_comp_price, ";
                        $sql .= "magenta_comp_yield, ";
                        $sql .= "yellow_comp_SKU, ";
                        $sql .= "yellow_comp_price, ";
                        $sql .= "yellow_comp_yield, ";
                        $sql .= "3color_comp_SKU, ";
                        $sql .= "3color_comp_price, ";
                        $sql .= "3color_comp_yield, ";
                        $sql .= "4color_comp_SKU, ";
                        $sql .= "4color_comp_price, ";
                        $sql .= "4color_comp_yield, ";
                        $sql .= "start_meter_life, ";
                        $sql .= "end_meter_life, ";
                        $sql .= "start_meter_black, ";
                        $sql .= "end_meter_black, ";
                        $sql .= "start_meter_color, ";
                        $sql .= "end_meter_color, ";
                        $sql .= "start_meter_printblack, ";
                        $sql .= "end_meter_printblack, ";
                        $sql .= "start_meter_printcolor, ";
                        $sql .= "end_meter_printcolor, ";
                        $sql .= "start_meter_copyblack, ";
                        $sql .= "end_meter_copyblack, ";
                        $sql .= "start_meter_copycolor, ";
                        $sql .= "end_meter_copycolor, ";
                        $sql .= "start_meter_fax, ";
                        $sql .= "end_meter_fax, ";
                        $sql .= "start_meter_scan, ";
                        $sql .= "end_meter_scan, ";
                        $sql .= "jit_supplies_supported, ";
                        $sql .= "is_excluded, ";
                        $sql .= "is_leased, ";
                        $sql .= "ip_address, ";
                        $sql .= "duty_cycle, ";
                        $sql .= "PPM_black, ";
                        $sql .= "PPM_color, ";
                        $sql .= "service_cost_per_page ";
                        $sql .= "FROM unknown_device_instance ";
                        $sql .= "WHERE upload_data_collector_id = " . $udc_id . ";";
                        $new_udi = $udiTable->getAdapter()->prepare($sql);
                        $new_udi->execute();
                        $new_udi_id = $udiTable->getAdapter()->lastInsertId();
                    }
                    
                    // copy device_instance records
                    $device_instance = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->fetchRow('upload_data_collector_id = ' . $udc_id);
                    if ($device_instance)
                    {
                        $di_id = $device_instance->DeviceInstanceId;
                        
                        $diTable = new Proposalgen_Model_DbTable_DeviceInstance();
                        $sql = "INSERT INTO device_instance (";
                        $sql .= "report_id, ";
                        $sql .= "master_device_id, ";
                        $sql .= "upload_data_collector_id, ";
                        $sql .= "serial_number, ";
                        $sql .= "mps_monitor_startdate, ";
                        $sql .= "mps_monitor_enddate, ";
                        $sql .= "mps_discovery_date, ";
                        $sql .= "jit_supplies_supported, ";
                        $sql .= "ip_address, ";
                        $sql .= "is_excluded";
                        $sql .= ") SELECT ";
                        $sql .= $newReportId . ", ";
                        $sql .= "master_device_id, ";
                        $sql .= $newUdcId . ", ";
                        $sql .= "serial_number, ";
                        $sql .= "mps_monitor_startdate, ";
                        $sql .= "mps_monitor_enddate, ";
                        $sql .= "mps_discovery_date, ";
                        $sql .= "jit_supplies_supported, ";
                        $sql .= "ip_address, ";
                        $sql .= "is_excluded ";
                        $sql .= "FROM device_instance ";
                        $sql .= "WHERE upload_data_collector_id = " . $udc_id . ";";
                        $newDi = $diTable->getAdapter()->prepare($sql);
                        $newDi->execute();
                        $newDiId = $diTable->getAdapter()->lastInsertId();
                        
                        // copy meters
                        $meterTable = new Proposalgen_Model_DbTable_Meters();
                        $sql = "INSERT INTO meters (";
                        $sql .= "device_instance_id, ";
                        $sql .= "meter_type, ";
                        $sql .= "start_meter, ";
                        $sql .= "end_meter";
                        $sql .= ") SELECT ";
                        $sql .= $newDiId . ", ";
                        $sql .= "meter_type, ";
                        $sql .= "start_meter, ";
                        $sql .= "end_meter ";
                        $sql .= "FROM meters ";
                        $sql .= "WHERE device_instance_id = " . $di_id . ";";
                        $new_meter = $meterTable->getAdapter()->prepare($sql);
                        $new_meter->execute();
                    }
                } // end foreach upload_data_collector
            } // end loop though $user_list
            $db->commit();
        }
        catch ( Exception $e )
        {
            $db->rollback();
            throw new Exception("Failed to clone the report.", 0, $e);
        }
        return $message;
    }
}
?>