<?php
/**
 * Class Assessment_Report_GrossmarginController
 */
class Assessment_Report_GrossmarginController extends Assessment_Library_Controller_Action
{

    /**
     * The gross margin Action will be used to display the gross margin report
     */
    public function indexAction ()
    {
        $this->_navigation->setActiveStep(Assessment_Model_Assessment_Steps::STEP_FINISHED);

        $this->initReportList();
        $this->initHtmlReport();

        $this->view->availableReports['GrossMargin']['active'] = true;
        $this->view->formats                                   = array(
            "/assessment/report_grossmargin/generate/format/csv"  => $this->_csvFormat,
            "/assessment/report_grossmargin/generate/format/docx" => $this->_wordFormat
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
            throw new Exception("Could not generate gross margin report.");
        }
    }

    /**
     * The Index action of the solution.
     */
    public function generateAction ()
    {
        $format = $this->_getParam("format", "csv");

        switch ($format)
        {
            case "csv" :
                $this->_helper->layout->disableLayout();
                $this->initCSVGrossMargin();
                break;
            case "docx" :
                $this->view->phpword = new PHPWord();
                $assessmentViewModel = $this->getAssessmentViewModel();
                $graphs              = $this->cachePNGImages($assessmentViewModel->getGraphs(), true);
                $assessmentViewModel->setGraphs($graphs);
                $this->_helper->layout->disableLayout();
                break;
            default :
                throw new Exception("Invalid Format Requested!");
                break;
        }

        $filename = "grossmargin.$format";

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
     * Function to hold the old csv code for the gross margin report
     *
     * @throws Exception
     */
    public function initCSVGrossMargin ()
    {
        try
        {
            $assessmentViewModel = $this->getAssessmentViewModel();

            $this->view->PrintIQ_Black_And_White_CPP  = number_format($assessmentViewModel->getMPSBlackAndWhiteCPP(), 4, '.', '');
            $this->view->PrintIQ_Color_CPP            = number_format($assessmentViewModel->getMPSColorCPP(), 4, '.', '');
            $this->view->Weighted_Black_And_White_CPP = number_format($assessmentViewModel->getGrossMarginWeightedCPP()->BlackAndWhite, 4, '.', '');
            $this->view->Weighted_Color_CPP           = number_format($assessmentViewModel->getGrossMarginWeightedCPP()->Color, 4, '.', '');
            $this->view->Black_And_White_Margin       = number_format($assessmentViewModel->getGrossMarginBlackAndWhiteMargin(), 0, '.', '');

            $this->view->Total_Cost     = number_format($assessmentViewModel->getGrossMarginTotalMonthlyCost()->combined, 2, '.', '');
            $this->view->Total_Revenue  = number_format($assessmentViewModel->getGrossMarginTotalMonthlyRevenue()->Combined, 2, '.', '');
            $this->view->Monthly_Profit = number_format($assessmentViewModel->getGrossMarginMonthlyProfit(), 2, '.', '');
            $this->view->Overall_Margin = number_format($assessmentViewModel->getGrossMarginOverallMargin(), 0, '.', '');
            $this->view->Color_Margin   = number_format($assessmentViewModel->getGrossMarginColorMargin(), 0, '.', '');
        }
        catch (Exception $e)
        {
            throw new Exception("Could not generate gross margin csv report.");
        }

        // Define our field titles
        $fieldTitlesLvl1 = array(
            'Device Name',
            'Black And White',
            '',
            '',
            '',
            '',
            'Color',
            '',
            '',
            '',
            ''
        );

        $fieldTitlesLvl2 = array(
            '(IP Address - Serial Number)',
            'AMPV',
            'Toner Cost',
            'Toner Yield',
            'CPP',
            'Total Printing Cost',
            'AMPV',
            'Toner Cost',
            'Toner Yield',
            'CPP',
            'Total Printing Cost'
        );

        try
        {
            $fieldList_Values = "";
            /* @var $device Proposalgen_Model_DeviceInstance() */
            foreach ($assessmentViewModel->getPurchasedDevices() as $device)
            {
                $tonerConfig               = $device->getMasterDevice()->tonerConfigId;
                $dealerCostPerPageSettings = $assessmentViewModel->getCostPerPageSettingForDealer();
                $blackToner                = null;
                $colorToner                = null;

                switch ($tonerConfig)
                {
                    case Proposalgen_Model_TonerConfig::THREE_COLOR_SEPARATED :
                        $blackToner = $device->getMasterDevice()->getCheapestToner(Proposalgen_Model_TonerColor::BLACK, $dealerCostPerPageSettings);
                        $colorToner = $device->getMasterDevice()->getCheapestToner(Proposalgen_Model_TonerColor::CYAN, $dealerCostPerPageSettings);
                        break;
                    case Proposalgen_Model_TonerConfig::THREE_COLOR_COMBINED :
                        $blackToner = $device->getMasterDevice()->getCheapestToner(Proposalgen_Model_TonerColor::BLACK, $dealerCostPerPageSettings);
                        $colorToner = $device->getMasterDevice()->getCheapestToner(Proposalgen_Model_TonerColor::THREE_COLOR, $dealerCostPerPageSettings);
                        break;
                    case Proposalgen_Model_TonerConfig::FOUR_COLOR_COMBINED :
                        $blackToner = $device->getMasterDevice()->getCheapestToner(Proposalgen_Model_TonerColor::FOUR_COLOR, $dealerCostPerPageSettings);
                        $colorToner = $blackToner;
                        break;
                    default :
                        $blackToner = $device->getMasterDevice()->getCheapestToner(Proposalgen_Model_TonerColor::BLACK, $dealerCostPerPageSettings);
                        break;
                }

                // Black Toner
                $blackCost  = "$" . number_format($blackToner->cost, 2, '.', '');
                $blackYield = number_format($blackToner->yield, 0, '.', '');

                // Color Toner
                $colorCost  = "-";
                $colorYield = "-";
                $isColor    = false;
                if ($colorToner)
                {
                    $colorCost  = "$" . number_format($colorToner->cost, 2, '.', '');
                    $colorYield = number_format($colorToner->yield, 0, '.', '');
                    $isColor    = true;
                }

                // Create an array of purchased devices (this will be the dynamic CSV body)
                $fieldList    = array();
                $fieldList [] = str_ireplace("hewlett-packard", "HP", $device->getDeviceName()) . " (" . $device->ipAddress . " - " . $device->serialNumber . ")";
                $fieldList [] = number_format($device->getPageCounts()->monochrome->getMonthly(), 0, '.', '');
                $fieldList [] = $blackCost;
                $fieldList [] = $blackYield;
                $fieldList [] = number_format($device->calculateCostPerPage($assessmentViewModel->getCostPerPageSettingForDealer())->monochromeCostPerPage, 4, '.', '');

                $fieldList [] = "$" . number_format($device->getMonthlyBlackAndWhiteCost($assessmentViewModel->getCostPerPageSettingForDealer()), 2, '.', '');
                $fieldList [] = $isColor ? number_format($device->getPageCounts()->color->getMonthly(), 0, '.', '') : "-";
                $fieldList [] = $colorCost;
                $fieldList [] = $colorYield;
                $fieldList [] = $isColor ? "$" . number_format($device->calculateCostPerPage($assessmentViewModel->getCostPerPageSettingForDealer())->colorCostPerPage, 4, '.', '') : "-";
                $fieldList [] = $isColor ? "$" . number_format($device->calculateMonthlyColorCost($assessmentViewModel->getCostPerPageSettingForDealer()), 2, '.', '') : "-";
                $fieldList_Values .= implode(",", $fieldList) . "\n";
            }

            $fieldTotals    = array();
            $fieldTotals [] = 'Totals for ' . $assessmentViewModel->getDeviceCount() . ' devices:';
            $fieldTotals [] = number_format($assessmentViewModel->getPageCounts()->Purchased->BlackAndWhite->Monthly, 0, '.', '');
            $fieldTotals [] = '';
            $fieldTotals [] = '';
            $fieldTotals [] = '';
            $fieldTotals [] = '$' . number_format($assessmentViewModel->getGrossMarginTotalMonthlyCost()->BlackAndWhite, 2, '.', '');
            $fieldTotals [] = number_format($assessmentViewModel->getPageCounts()->Purchased->Color->Monthly, 0, '.', '');
            $fieldTotals [] = '';
            $fieldTotals [] = '';
            $fieldTotals [] = '';
            $fieldTotals [] = '$' . number_format($assessmentViewModel->getGrossMarginTotalMonthlyCost()->Color, 2, '.', '');
        }
        catch (Exception $e)
        {
            throw new Exception("CSV File could not be opened/written for export.", 0, $e);
        }

        $this->view->fieldTitlesLvl1 = implode(",", $fieldTitlesLvl1) . "\n";
        $this->view->fieldTitlesLvl2 = implode(",", $fieldTitlesLvl2) . "\n";
        $this->view->fieldList       = $fieldList_Values;
        $this->view->fieldTotals     = implode(",", $fieldTotals) . "\n";
    }
}