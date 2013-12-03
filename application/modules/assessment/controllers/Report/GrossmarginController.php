<?php
/**
 * Class Assessment_Report_GrossmarginController
 */
class Assessment_Report_GrossmarginController extends Assessment_Library_Controller_Action
{
    /**
     * Makes sure the user has access to this report, otherwise it sends them back
     */
    public function init ()
    {
        if (!My_Feature::canAccess(My_Feature::ASSESSMENT_GROSS_MARGIN))
        {
            $this->_flashMessenger->addMessage(array(
                                                    "error" => "You do not have permission to access this."
                                               ));

            $this->redirector('index', 'index', 'index');
        }

        parent::init();
    }

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
     * Function to hold the old CSV code for the gross margin report
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
            throw new Exception("Could not generate gross margin CSV report.");
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
            /* @var $deviceInstance Proposalgen_Model_DeviceInstance() */
            foreach ($assessmentViewModel->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                $blackToner = null;
                $colorToner = null;

                $toners = $deviceInstance->getMasterDevice()->getCheapestTonerSetByVendor($assessmentViewModel->getCostPerPageSettingForDealer());

                foreach ($toners as $toner)
                {
                    switch ($toner->tonerColorId)
                    {
                        case Proposalgen_Model_TonerColor::BLACK:
                            $blackToner = $toner;
                            break;
                        case Proposalgen_Model_TonerColor::CYAN:
                        case Proposalgen_Model_TonerColor::MAGENTA:
                        case Proposalgen_Model_TonerColor::YELLOW:
                            $colorToner = $toner;
                            break;
                        case Proposalgen_Model_TonerColor::THREE_COLOR:
                            $colorToner = $toner;
                            break;
                        case Proposalgen_Model_TonerColor::FOUR_COLOR:
                            $blackToner = $toner;
                            $colorToner = $toner;
                            break;
                        default:
                            break;
                    }
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
                $fieldList [] = str_ireplace("hewlett-packard", "HP", $deviceInstance->getDeviceName()) . " (" . $deviceInstance->ipAddress . " - " . $deviceInstance->serialNumber . ")";
                $fieldList [] = number_format($deviceInstance->getPageCounts()->getBlackPageCount()->getMonthly(), 0, '.', '');
                $fieldList [] = $blackCost;
                $fieldList [] = $blackYield;
                $fieldList [] = number_format($deviceInstance->calculateCostPerPage($assessmentViewModel->getCostPerPageSettingForDealer())->monochromeCostPerPage, 4, '.', '');

                $fieldList [] = "$" . number_format($deviceInstance->getMonthlyBlackAndWhiteCost($assessmentViewModel->getCostPerPageSettingForDealer()), 2, '.', '');
                $fieldList [] = $isColor ? number_format($deviceInstance->getPageCounts()->getColorPageCount()->getMonthly(), 0, '.', '') : "-";
                $fieldList [] = $colorCost;
                $fieldList [] = $colorYield;
                $fieldList [] = $isColor ? "$" . number_format($deviceInstance->calculateCostPerPage($assessmentViewModel->getCostPerPageSettingForDealer())->colorCostPerPage, 4, '.', '') : "-";
                $fieldList [] = $isColor ? "$" . number_format($deviceInstance->calculateMonthlyColorCost($assessmentViewModel->getCostPerPageSettingForDealer()), 2, '.', '') : "-";
                $fieldList_Values .= implode(",", $fieldList) . "\n";
            }

            $fieldTotals    = array();
            $fieldTotals [] = 'Totals for ' . $assessmentViewModel->getDevices()->allIncludedDeviceInstances->getCount() . ' devices:';
            $fieldTotals [] = number_format($assessmentViewModel->getDevices()->purchasedDeviceInstances->getPageCounts()->getBlackPageCount()->getMonthly(), 0, '.', '');
            $fieldTotals [] = number_format($assessmentViewModel->getDevices()->purchasedDeviceInstances->getPageCounts()->getBlackPageCount()->getMonthly(), 0, '.', '');
            $fieldTotals [] = '';
            $fieldTotals [] = '';
            $fieldTotals [] = '';
            $fieldTotals [] = '$' . number_format($assessmentViewModel->getGrossMarginTotalMonthlyCost()->BlackAndWhite, 2, '.', '');
            $fieldTotals [] = number_format($assessmentViewModel->getDevices()->purchasedDeviceInstances->getPageCounts()->getColorPageCount()->getMonthly(), 0, '.', '');
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