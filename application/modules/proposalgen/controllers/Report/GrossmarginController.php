<?php
class Proposalgen_Report_GrossmarginController extends Proposalgen_Library_Controller_Proposal
{

    /**
     * The gross margin Action will be used to display the gross margin report
     */
    public function indexAction ()
    {
        // Mark the step we're on as active
        $this->setActiveReportStep(Proposalgen_Model_Report_Step::STEP_FINISHED);

        $this->initReportList();
        $this->initHtmlReport();

        $this->view->availableReports->GrossMargin->active = true;
        $this->view->formats                               = array(
            "/proposalgen/report_grossmargin/generate/format/csv"  => $this->_csvFormat,
            "/proposalgen/report_grossmargin/generate/format/docx" => $this->_wordFormat
        );

        try
        {
            // Clear the cache for the report before proceeding
            $this->clearCacheForReport();
            $proposal             = $this->getProposal();
            $this->view->proposal = $proposal;
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
                require_once ('PHPWord.php');
                $this->view->phpword = new PHPWord();
                $proposal            = $this->getProposal();
                $graphs              = $this->cachePNGImages($proposal->getGraphs(), true);
                $proposal->setGraphs($graphs);
                $this->_helper->layout->disableLayout();
                break;
            case "pdf" :
                throw new Exception("PDF Format not available through this page yet!");
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
            $proposal = $this->getProposal();

            $this->view->PrintIQ_Black_And_White_CPP  = number_format($proposal->getMPSBlackAndWhiteCPP(), 4, '.', '');
            $this->view->PrintIQ_Color_CPP            = number_format($proposal->getMPSColorCPP(), 4, '.', '');
            $this->view->Weighted_Black_And_White_CPP = number_format($proposal->getGrossMarginWeightedCPP()->BlackAndWhite, 4, '.', '');
            $this->view->Weighted_Color_CPP           = number_format($proposal->getGrossMarginWeightedCPP()->Color, 4, '.', '');
            $this->view->Black_And_White_Margin       = number_format($proposal->getGrossMarginBlackAndWhiteMargin(), 0, '.', '');

            $this->view->Total_Cost     = number_format($proposal->getGrossMarginTotalMonthlyCost()->Combined, 2, '.', '');
            $this->view->Total_Revenue  = number_format($proposal->getGrossMarginTotalMonthlyRevenue()->Combined, 2, '.', '');
            $this->view->Monthly_Profit = number_format($proposal->getGrossMarginMonthlyProfit(), 2, '.', '');
            $this->view->Overall_Margin = number_format($proposal->getGrossMarginOverallMargin(), 0, '.', '');
            $this->view->Color_Margin   = number_format($proposal->getGrossMarginColorMargin(), 0, '.', '');
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
            foreach ($proposal->getPurchasedDevices() as $device)
            {
                $tonerConfig              = $device->getMasterDevice()->TonerConfigId;
                $grossMarginPricingConfig = Proposalgen_Model_MasterDevice::getGrossMarginPricingConfig();
                $completeMonoToners       = $device->getMasterDevice()->HasValidMonoGrossMarginToners;
                $completeColorToners      = $device->getMasterDevice()->HasValidColorGrossMarginToners;
                $blackToner               = null;
                $colorToner               = null;

                switch ($tonerConfig)
                {
                    case Proposalgen_Model_TonerConfig::THREE_COLOR_SEPARATED :
                        $blackToner = $device->getMasterDevice()->getCheapestToner(Proposalgen_Model_TonerColor::BLACK, $grossMarginPricingConfig);
                        $colorToner = $device->getMasterDevice()->getCheapestToner(Proposalgen_Model_TonerColor::CYAN, $grossMarginPricingConfig);
                        break;
                    case Proposalgen_Model_TonerConfig::THREE_COLOR_COMBINED :
                        $blackToner = $device->getMasterDevice()->getCheapestToner(Proposalgen_Model_TonerColor::BLACK, $grossMarginPricingConfig);
                        $colorToner = $device->getMasterDevice()->getCheapestToner(Proposalgen_Model_TonerColor::THREE_COLOR, $grossMarginPricingConfig);
                        break;
                    case Proposalgen_Model_TonerConfig::FOUR_COLOR_COMBINED :
                        $blackToner = $device->getMasterDevice()->getCheapestToner(Proposalgen_Model_TonerColor::FOUR_COLOR, $grossMarginPricingConfig);
                        $colorToner = $blackToner;
                        break;
                    default :
                        $blackToner = $device->getMasterDevice()->getCheapestToner(Proposalgen_Model_TonerColor::BLACK, $grossMarginPricingConfig);
                        break;
                }

                // Black Toner
                $blackCost  = "$" . number_format($blackToner->TonerPrice, 2, '.', '');
                $blackYield = number_format($blackToner->TonerYield, 0, '.', '');

                // Color Toner
                $colorCost  = "-";
                $colorYield = "-";
                $isColor    = false;
                if ($colorToner)
                {
                    $colorCost  = "$" . number_format($colorToner->TonerPrice, 2, '.', '');
                    $colorYield = number_format($colorToner->TonerYield, 0, '.', '');
                    $isColor    = true;
                }

                // Create an array of purchased devices (this will be the dynamic CSV body)
                $fieldList    = array();
                $fieldList [] = str_ireplace("hewlett-packard", "HP", $device->getDeviceName()) . " (" . $device->IPAddress . " - " . $device->serialNumber . ")";
                $fieldList [] = number_format($device->getAverageMonthlyBlackAndWhitePageCount(), 0, '.', '');
                $fieldList [] = $blackCost;
                $fieldList [] = $blackYield;
                $fieldList [] = number_format($device->getMasterDevice()->CostPerPage->Actual->BasePlusService->BlackAndWhite, 4, '.', '');
                $fieldList [] = "$" . number_format($device->getGrossMarginMonthlyBlackAndWhiteCost(), 2, '.', '');
                $fieldList [] = $isColor ? number_format($device->getAverageMonthlyColorPageCount(), 0, '.', '') : "-";
                $fieldList [] = $colorCost;
                $fieldList [] = $colorYield;
                $fieldList [] = $isColor ? "$" . number_format($device->getMasterDevice()->CostPerPage->Actual->BasePlusService->Color, 4, '.', '') : "-";
                $fieldList [] = $isColor ? "$" . number_format($device->getGrossMarginMonthlyColorCost(), 2, '.', '') : "-";
                $fieldList_Values .= implode(",", $fieldList) . "\n";
            }

            $fieldTotals    = array();
            $fieldTotals [] = 'Totals for ' . $proposal->getDeviceCount() . ' devices:';
            $fieldTotals [] = number_format($proposal->getPageCounts()->Purchased->BlackAndWhite->Monthly, 0, '.', '');
            $fieldTotals [] = '';
            $fieldTotals [] = '';
            $fieldTotals [] = '';
            $fieldTotals [] = '$' . number_format($proposal->getGrossMarginTotalMonthlyCost()->BlackAndWhite, 2, '.', '');
            $fieldTotals [] = number_format($proposal->getPageCounts()->Purchased->Color->Monthly, 0, '.', '');
            $fieldTotals [] = '';
            $fieldTotals [] = '';
            $fieldTotals [] = '';
            $fieldTotals [] = '$' . number_format($proposal->getGrossMarginTotalMonthlyCost()->Color, 2, '.', '');
        }
        catch (Exception $e)
        {
            throw new Exception("CSV File could not be opened/written for export.", 0, $e);
        }

        $this->view->fieldTitlesLvl1 = implode(",", $fieldTitlesLvl1) . "\n";
        $this->view->fieldTitlesLvl2 = implode(",", $fieldTitlesLvl2) . "\n";
        $this->view->fieldList       = $fieldList_Values;
        $this->view->fieldTotals     = implode(",", $fieldTotals) . "\n";

        // Removes spaces from company name, otherwise CSV filename contains + symbol
        $companyName = str_replace(array(
                                        " ",
                                        "/",
                                        "\\",
                                        ";",
                                        "?",
                                        "\"",
                                        "'",
                                        ",",
                                        "%",
                                        "&",
                                        "#",
                                        "@",
                                        "!",
                                        ">",
                                        "<",
                                        "+",
                                        "=",
                                        "{",
                                        "}",
                                        "[",
                                        "]",
                                        "|",
                                        "~",
                                        "`"
                                   ), "_", $proposal->report->CustomerCompanyName);
    }
}