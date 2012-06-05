<?php

class Proposalgen_Model_Mapper_Report extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_Report";
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
     * Maps a database row object to an Proposalgen_Model
     * 
     * @param Zend_Db_Table_Row $row            
     * @return The appropriate Proposalgen_Model
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
        
        /* @var $report Proposalgen_Model_Report */
        $currentReport = $this->find($report_id);
        $report = clone $currentReport;
        
        $db->beginTransaction();
        try
        {
            // Get new users array
            $users = explode(",", str_replace("'", "", $user_list));
            
            foreach ( $users as $newUserId )
            {
                /*
                 * Copy the report to the new user. Note that we only need to clear the report id and set the new user
                 * id since we already selected the data.
                 */
                $report->setReportId(null);
                $report->setUserId($newUserId);
                $newReportId = $this->save($report);
                $report->setReportId($newReportId);
                
                /*
                 * Note the following SQL Statements. The big change is putting them all in double quotes. Using {}
                 * around inserted variables instead of adding strings together. With the Zend Formatter configured
                 * properly it won't rejoin strings so you can keep a whole string on multiple lines.
                 */
                
                // Copy Date Answers
                $answerDatesTable = new Proposalgen_Model_DbTable_DateAnswer();
                $sql = "INSERT INTO date_answers (question_id, report_id, date_answer) 
                        SELECT question_id, {$newReportId}, date_answer FROM answers_dates 
                        WHERE report_id = {$report_id};";
                $newAnswerDate = $answerDatesTable->getAdapter()->prepare($sql);
                $newAnswerDate->execute();
                
                // Copy Numeric Answers
                $answerNumericTable = new Proposalgen_Model_DbTable_NumericAnswer();
                $sql = "INSERT INTO numeric_answers (question_id, report_id, numierc_answer)
                SELECT question_id, {$newReportId}, numierc_answer FROM numeric_answers
                WHERE report_id = {$report_id};";
                $newAnswerNumeric = $answerNumericTable->getAdapter()->prepare($sql);
                $newAnswerNumeric->execute();
                
                // Copy Textual Answers
                $answerTextualTable = new Proposalgen_Model_DbTable_TextAnswer();
                $sql = "INSERT INTO textual_answers (question_id, report_id, textual_answer)
                SELECT question_id, {$newReportId}, textual_answer FROM textual_answers
                WHERE report_id = {$report_id};";
                
                $newAnswerTextual = $answerTextualTable->getAdapter()->prepare($sql);
                $newAnswerTextual->execute();
                
                // Copy all the upload data collector rows
                $uploadDataCollectorRows = Proposalgen_Model_Mapper_UploadDataCollectorRow::getInstance()->fetchAll(array (
                        'report_id = ?', 
                        $report_id 
                ));
                /* @var $uploadDataCollectorRow Proposalgen_Model_UploadDataCollectorRow */
                foreach ( $uploadDataCollectorRows as $uploadDataCollectorRow )
                {
                    $oldUploadDataCollectorRowId = $uploadDataCollectorRow->UploadDataCollectorId;
                    
                    /*
                     * Note here that we've already fetched the row. Since we already have an object, we can just change
                     * whats needed and perform the change.
                     */
                    $uploadDataCollectorRow->setUploadDataCollectorId(null);
                    $uploadDataCollectorRow->setReportId($newReportId);
                    $newUploadDataCollectorId = Proposalgen_Model_Mapper_UploadDataCollectorRow::getInstance()->save($uploadDataCollectorRow);
                    
                    // Copy the unknown device record
                    /*
                     * Note here that we've already fetched the row. Since we already have an object, we can just change
                     * whats needed and perform the change.
                     */
                    
                    /* @var $unknownDeviceInstance Proposalgen_Model_UnknownDeviceInstance */
                    $unknownDeviceInstance = Proposalgen_Model_Mapper_UnknownDeviceInstance::getInstance()->fetchRow(array (
                            'upload_data_collector_row_id = ?' => $oldUploadDataCollectorRowId 
                    ));
                    
                    if ($unknownDeviceInstance)
                    {
                        $oldUnknownDeviceInstanceId = $unknownDeviceInstance->getUnknownDeviceInstanceId();
                        
                        $unknownDeviceInstance->setUnknownDeviceInstanceId(null);
                        
                        $unknownDeviceInstance->setUserId($newUserId);
                        $unknownDeviceInstance->setReportId($newReportId);
                        $unknownDeviceInstance->setUploadDataCollectorId($newUploadDataCollectorId);
                        $newUnknownDeviceInstanceId = Proposalgen_Model_Mapper_UnknownDeviceInstance::getInstance()->save($unknownDeviceInstance);
                    }
                    
                    // Copy Device Instance
                    /*
                     * Note here that we've already fetched the row. Since we already have an object, we can just change
                     * whats needed and perform the change.
                     */
                    /* @var $deviceInstance Proposalgen_Model_DeviceInstance */
                    $deviceInstance = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->fetchRow(array (
                            'upload_data_collector_row_id = ?' => $oldUploadDataCollectorRowId 
                    ));
                    if ($deviceInstance)
                    {
                        $oldDeviceInstanceId = $deviceInstance->getDeviceInstanceId();
                        
                        $deviceInstance->setDeviceInstanceId(null);
                        $deviceInstance->setReportId($newReportId);
                        $deviceInstance->setUploadDataCollectorId($newUploadDataCollectorId);
                        $newDeviceInstanceId = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->save($deviceInstance);
                        
                        // Copy Meters
                        /*
                         * Note. This is a good user of the sql as it selects multiple meters
                         */
                        $meterTable = new Proposalgen_Model_DbTable_Meter();
                        $sql = "INSERT INTO meters (device_instance_id, meter_type, start_meter, end_meter)
                                    SELECT {$newDeviceInstanceId}, meter_type, start_meter, end_meter FROM meters
                                WHERE device_instance_id = {$oldDeviceInstanceId};";
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
