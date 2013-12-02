<?php
/**
 * Class Assessment_Report_TonersController
 */
class Assessment_Report_TonersController extends Assessment_Library_Controller_Action
{
    public function indexAction ()
    {
        $this->_navigation->setActiveStep(Assessment_Model_Assessment_Steps::STEP_FINISHED);

        $this->initReportList();
        $this->initHtmlReport();

        $this->view->availableReports['JITSupplyAndTonerSku']['active'] = true;
        $this->view->formats                                            = array(
            "/assessment/report_toners/generate/format/csv" => $this->_csvFormat,
        );

        try
        {
            // Clear the cache for the report before proceeding
            $this->clearCacheForReport();
            $assessmentViewModel             = $this->getAssessmentViewModel();
            $this->view->assessmentViewModel = $assessmentViewModel;
        }
        catch (Exception $e)
        {
            throw new Exception("Could not generate the toner list.");
        }
    }

    /**
     * The Index action of the solution.
     */
    public function generateAction ()
    {
        $format = $this->_getParam("format", "docx");

        switch ($format)
        {
            case "csv" :
                $this->initCSVPrintingDeviceList();
                $this->_helper->layout->disableLayout();
                break;
            case "docx" :
                throw new Exception(".docx format is not available for this report.");
                break;
            default :
                throw new Exception("Invalid Format Requested! ($format)");
                break;
        }

        $filename = "tonerreport.$format";

        $this->initReportVariables($filename);

        // Render early
        try
        {
            $this->render($format . "/00_render");
        }
        catch (Exception $e)
        {
            throw new Exception("Controller caught the exception!", 0, $e);
        }
    }

    /**
     * Function to hold the old CSV code for the printing device list
     *
     * @throws Exception
     */
    public function initCSVPrintingDeviceList ()
    {
        try
        {
            /* @var $assessmentViewModel Assessment_ViewModel_Assessment */
            $assessmentViewModel = $this->getAssessmentViewModel();
        }
        catch (Exception $e)
        {
            throw new Exception("Could not generate toner CSV report.");
        }

        // Define our field titles
        $fieldTitlesLvl1 = array('', '', '', 'Customer Preferred', '', '', '', '', '', '', '', '', '', '', '', 'Dealer Preferred',
                                 '', '', '', '', '', '', '', '', '', '', '', '', '');

        $fieldTitlesLvl2 = array('Device Name', 'IP Address', 'Serial Number', 'Black SKU', 'Black Cost', 'Cyan SKU',
                                 'Cyan Cost', 'Magenta SKU', 'Magenta Cost', 'Yellow SKU', 'Yellow Cost', '3Color SKU',
                                 '3Color Cost', '4Color SKU', '4Color Cost', 'Black SKU', 'Black Cost', 'Cyan SKU',
                                 'Cyan Cost', 'Magenta SKU', 'Magenta Cost', 'Yellow SKU', 'Yellow Cost', '3Color SKU',
                                 '3Color Cost', '4Color SKU', '4Color Cost');

        try
        {
            $fieldList_Values = "";
            /* @var $deviceInstance Proposalgen_Model_DeviceInstance() */
            $customerCostPerPageSetting = $assessmentViewModel->getCostPerPageSettingForCustomer();
            $dealerCostPerPageSetting   = $assessmentViewModel->getCostPerPageSettingForDealer();
            foreach ($assessmentViewModel->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                $oemToners        = $deviceInstance->getMasterDevice()->getCheapestTonerSetByVendor($customerCostPerPageSetting);
                $compatibleToners = $deviceInstance->getMasterDevice()->getCheapestTonerSetByVendor($dealerCostPerPageSetting);

                // Create an array of purchased devices (this will be the dynamic CSV body)
                $fieldList    = array();
                $fieldList [] = $deviceInstance->getDeviceName();
                $fieldList [] = $deviceInstance->ipAddress;
                $fieldList [] = $deviceInstance->serialNumber;
                $fieldList [] = (isset($oemToners[Proposalgen_Model_TonerColor::BLACK])) ? $oemToners[Proposalgen_Model_TonerColor::BLACK]->sku : "";
                $fieldList [] = (isset($oemToners[Proposalgen_Model_TonerColor::BLACK])) ? $oemToners[Proposalgen_Model_TonerColor::BLACK]->cost : "";
                $fieldList [] = (isset($oemToners[Proposalgen_Model_TonerColor::CYAN])) ? $oemToners[Proposalgen_Model_TonerColor::CYAN]->sku : "";
                $fieldList [] = (isset($oemToners[Proposalgen_Model_TonerColor::CYAN])) ? $oemToners[Proposalgen_Model_TonerColor::CYAN]->cost : "";
                $fieldList [] = (isset($oemToners[Proposalgen_Model_TonerColor::MAGENTA])) ? $oemToners[Proposalgen_Model_TonerColor::MAGENTA]->sku : "";
                $fieldList [] = (isset($oemToners[Proposalgen_Model_TonerColor::MAGENTA])) ? $oemToners[Proposalgen_Model_TonerColor::MAGENTA]->cost : "";
                $fieldList [] = (isset($oemToners[Proposalgen_Model_TonerColor::YELLOW])) ? $oemToners[Proposalgen_Model_TonerColor::YELLOW]->sku : "";
                $fieldList [] = (isset($oemToners[Proposalgen_Model_TonerColor::YELLOW])) ? $oemToners[Proposalgen_Model_TonerColor::YELLOW]->cost : "";
                $fieldList [] = (isset($oemToners[Proposalgen_Model_TonerColor::THREE_COLOR])) ? $oemToners[Proposalgen_Model_TonerColor::THREE_COLOR]->sku : "";
                $fieldList [] = (isset($oemToners[Proposalgen_Model_TonerColor::THREE_COLOR])) ? $oemToners[Proposalgen_Model_TonerColor::THREE_COLOR]->cost : "";
                $fieldList [] = (isset($oemToners[Proposalgen_Model_TonerColor::FOUR_COLOR])) ? $oemToners[Proposalgen_Model_TonerColor::FOUR_COLOR]->sku : "";
                $fieldList [] = (isset($oemToners[Proposalgen_Model_TonerColor::FOUR_COLOR])) ? $oemToners[Proposalgen_Model_TonerColor::FOUR_COLOR]->cost : "";
                $fieldList [] = (isset($compatibleToners[Proposalgen_Model_TonerColor::BLACK])) ? $compatibleToners[Proposalgen_Model_TonerColor::BLACK]->sku : "";
                $fieldList [] = (isset($compatibleToners[Proposalgen_Model_TonerColor::BLACK])) ? $compatibleToners[Proposalgen_Model_TonerColor::BLACK]->cost : "";
                $fieldList [] = (isset($compatibleToners[Proposalgen_Model_TonerColor::CYAN])) ? $compatibleToners[Proposalgen_Model_TonerColor::CYAN]->sku : "";
                $fieldList [] = (isset($compatibleToners[Proposalgen_Model_TonerColor::CYAN])) ? $compatibleToners[Proposalgen_Model_TonerColor::CYAN]->cost : "";
                $fieldList [] = (isset($compatibleToners[Proposalgen_Model_TonerColor::MAGENTA])) ? $compatibleToners[Proposalgen_Model_TonerColor::MAGENTA]->sku : "";
                $fieldList [] = (isset($compatibleToners[Proposalgen_Model_TonerColor::MAGENTA])) ? $compatibleToners[Proposalgen_Model_TonerColor::MAGENTA]->cost : "";
                $fieldList [] = (isset($compatibleToners[Proposalgen_Model_TonerColor::YELLOW])) ? $compatibleToners[Proposalgen_Model_TonerColor::YELLOW]->sku : "";
                $fieldList [] = (isset($compatibleToners[Proposalgen_Model_TonerColor::YELLOW])) ? $compatibleToners[Proposalgen_Model_TonerColor::YELLOW]->cost : "";
                $fieldList [] = (isset($compatibleToners[Proposalgen_Model_TonerColor::THREE_COLOR])) ? $compatibleToners[Proposalgen_Model_TonerColor::THREE_COLOR]->sku : "";
                $fieldList [] = (isset($compatibleToners[Proposalgen_Model_TonerColor::THREE_COLOR])) ? $compatibleToners[Proposalgen_Model_TonerColor::THREE_COLOR]->cost : "";
                $fieldList [] = (isset($compatibleToners[Proposalgen_Model_TonerColor::FOUR_COLOR])) ? $compatibleToners[Proposalgen_Model_TonerColor::FOUR_COLOR]->sku : "";
                $fieldList [] = (isset($compatibleToners[Proposalgen_Model_TonerColor::FOUR_COLOR])) ? $compatibleToners[Proposalgen_Model_TonerColor::FOUR_COLOR]->cost : "";
                $fieldList_Values .= implode(",", $fieldList) . "\n";
            }

            // Define our field titles for the excluded devices section
            $excluded_titles = array("Device Name,IP Address,Serial,Exclusion Reason");

            $excluded_values = "";
            try
            {
                foreach ($assessmentViewModel->getExcludedDevices() as $deviceInstance)
                {
                    $row    = array();
                    $row [] = $deviceInstance->getDeviceName();
                    $row [] = ($deviceInstance->ipAddress) ? $deviceInstance->ipAddress : "Unknown IP";
                    $row [] = (strlen($deviceInstance->serialNumber) > 0) ? $deviceInstance->serialNumber : "Unknown";

                    $row [] = $deviceInstance->_exclusionReason;
                    $excluded_values .= implode(",", $row) . "\n";
                } // end Purchased Devices foreach
            }
            catch (Exception $e)
            {
                throw new Exception("Error while generating CSV Report.", 0, $e);
            }

        }
        catch (Exception $e)
        {
            throw new Exception("CSV File could not be opened/written for export.", 0, $e);
        }

        $this->view->fieldTitlesLvl1    = implode(",", $fieldTitlesLvl1) . "\n";
        $this->view->fieldTitlesLvl2    = implode(",", $fieldTitlesLvl2) . "\n";
        $this->view->fieldList          = $fieldList_Values;
        $this->view->excludedTableTitle = "Excluded Devices";
        $this->view->excludedTitles     = implode(",", $excluded_titles) . "\n";
        $this->view->excludedValues     = $excluded_values;
    }
}