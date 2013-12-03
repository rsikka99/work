<?php
/**
 * Class Assessment_Report_TonerVendorGrossmarginController
 */
class Assessment_Report_TonervendorgrossmarginController extends Assessment_Library_Controller_Action
{
    /**
     * Makes sure the user has access to this report, otherwise it sends them back
     */
    public function init ()
    {
        if (!My_Feature::canAccess(My_Feature::ASSESSMENT_TONER_VENDOR_GROSS_MARGIN))
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
        $this->initTonerVendorGrossMargin();
        $this->view->availableReports['TonerVendorGrossMargin']['active'] = true;
        $this->view->formats                                              = array(
            "/assessment/report_tonervendorgrossmargin/generate/format/excel" => $this->_excelFormat,
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
            case "excel" :
                $this->_helper->layout->disableLayout();
                $this->view->phpexcel = new PHPExcel();
                $this->initTonerVendorGrossMargin();
                break;
            default :
                throw new Exception("Invalid Format Requested!");
                break;
        }

        $filename = "tonervendorgrossmargin.$format";

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

    function initTonerVendorGrossMargin ()
    {
        $assessmentViewModel = $this->getAssessmentViewModel();

        // Define our field titles
        $fieldTitlesLvl1 = array(
            '',
            '',
            '',
            '',
            'Monochrome',
            '',
            '',
            '',
            '',
            'Color',
            ''
        );

        $fieldTitlesLvl2 = array(
            'Manufacturer',
            'Device Name',
            'IP Address',
            'Serial Number',
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

        $fieldTitles         = array($fieldTitlesLvl1, $fieldTitlesLvl2);
        $vendorSeperatedData = array();

        // Your Preferences
        $vendorSeperatedData[] = $this->getReportData($assessmentViewModel, $fieldTitles, "Your Preferences", 0);

        // OEM
        $vendorSeperatedData[] = $this->getReportData($assessmentViewModel, $fieldTitles, "OEM", -1);

        // Individual Vendors
        foreach (Proposalgen_Model_Mapper_TonerVendorManufacturer::getInstance()->fetchAll() as $tonerVendor)
        {
            $vendorSeperatedData[] = $this->getReportData($assessmentViewModel, $fieldTitles, $tonerVendor->getManufacturerName(), $tonerVendor->manufacturerId);
        }

        $highestMarginNames = [];
        $highestMargin      = -5000;

        foreach ($vendorSeperatedData as $arrayData)
        {
            $currentPercentage = $arrayData['statisticsGroup']['right']['Overall Margin'];
            if ($currentPercentage > $highestMargin)
            {
                $highestMarginNames = array($arrayData['pageTitle']);
                $highestMargin      = $currentPercentage;
            }
            else if ($currentPercentage == $highestMargin)
            {
                $highestMarginNames[] = $arrayData['pageTitle'];
            }
        }

        $this->view->vendorSeperatedData = $vendorSeperatedData;
        $this->view->highestNames        = $highestMarginNames;
        $this->view->highestMargin       = $highestMargin . "%";
    }

    /**
     * @param $assessmentViewModel Assessment_ViewModel_Assessment
     *
     * @param $costPerPageSetting
     *
     * @return array
     */
    function getStatistics ($assessmentViewModel, $costPerPageSetting)
    {
        $statisticsGroup                                          = array();
        $statisticsGroup['left']['MPSToolbox.com Monochrome CPP'] = "$" . number_format($assessmentViewModel->getMPSBlackAndWhiteCPP(), 4, '.', '');
        $statisticsGroup['left']['MPSToolbox.com Color CPP']      = "$" . number_format($assessmentViewModel->getMPSColorCPP(), 4, '.', '');
        $statisticsGroup['left']['Weighted Monochrome CPP']       = "$" . number_format($assessmentViewModel->getGrossMarginWeightedCPP($costPerPageSetting)->BlackAndWhite, 4, '.', '');
        $statisticsGroup['left']['Weighted Color CPP']            = "$" . number_format($assessmentViewModel->getGrossMarginWeightedCPP($costPerPageSetting)->Color, 4, '.', '');
        $statisticsGroup['left']['Monochrome Margin']             = number_format($assessmentViewModel->getGrossMarginBlackAndWhiteMargin($costPerPageSetting), 0, '.', '') . "%";

        $statisticsGroup['right']['Total Cost']     = "$" . number_format($assessmentViewModel->getGrossMarginTotalMonthlyCost($costPerPageSetting)->Combined, 2, '.', '');
        $statisticsGroup['right']['Total Revenue']  = "$" . number_format($assessmentViewModel->getGrossMarginTotalMonthlyRevenue()->Combined, 2, '.', '');
        $statisticsGroup['right']['Monthly Profit'] = "$" . number_format($assessmentViewModel->getGrossMarginMonthlyProfit($costPerPageSetting), 2, '.', '');
        $statisticsGroup['right']['Overall Margin'] = number_format($assessmentViewModel->getGrossMarginOverallMargin($costPerPageSetting), 0, '.', '');
        $statisticsGroup['right']['Color Margin']   = number_format($assessmentViewModel->getGrossMarginColorMargin($costPerPageSetting), 0, '.', '') . "%";

        return $statisticsGroup;
    }

    /**
     * @param  Assessment_ViewModel_Assessment $assessmentViewModel
     * @param  array                           $fieldTitles
     * @param  string                          $pageTitle
     * @param null|int                         $tonerVendorId
     *
     * @throws Exception
     *
     * @return array
     */
    function getReportData ($assessmentViewModel, $fieldTitles, $pageTitle, $tonerVendorId = 0)
    {
        // This is used to clear the assessment view models caching
        $assessmentViewModel = new Assessment_ViewModel_Assessment($assessmentViewModel->assessment);
        $costPerPageSetting  = null;

        // If we are using a specific toner vendor
        if ($tonerVendorId > 0)
        {
            $tonerRankSet            = new Proposalgen_Model_Toner_Vendor_Ranking_Set();
            $ranking                 = new Proposalgen_Model_Toner_Vendor_Ranking();
            $ranking->manufacturerId = $tonerVendorId;
            $tonerRankSet->setRankings($ranking);
            $costPerPageSetting                         = $assessmentViewModel->getCostPerPageSettingForDealer();
            $costPerPageSetting->monochromeTonerRankSet = $tonerRankSet;
            $costPerPageSetting->colorTonerRankSet      = $tonerRankSet;
        }
        // If we are using their default preferences
        else if ($tonerVendorId == 0)
        {
            $costPerPageSetting = $assessmentViewModel->getCostPerPageSettingForDealer();
        }
        // OEM
        else
        {
            $tonerRankSet                               = new Proposalgen_Model_Toner_Vendor_Ranking_Set();
            $costPerPageSetting                         = $assessmentViewModel->getCostPerPageSettingForDealer();
            $costPerPageSetting->monochromeTonerRankSet = $tonerRankSet;
            $costPerPageSetting->colorTonerRankSet      = $tonerRankSet;
        }

        // Get the statistics
        $statisticsGroup = $this->getStatistics($assessmentViewModel, $costPerPageSetting);

        $fieldLists = array();
        try
        {
            foreach ($assessmentViewModel->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                $blackToner          = null;
                $colorToner          = null;
                $completeMonoToners  = $deviceInstance->getMasterDevice()->getHasValidMonoGrossMarginToners($costPerPageSetting);
                $completeColorToners = $deviceInstance->getMasterDevice()->getHasValidColorGrossMarginToners($costPerPageSetting);
                if ($tonerVendorId > 0)
                {
                    $toners = $deviceInstance->getMasterDevice()->getCheapestTonerSetByVendorId($tonerVendorId);
                }
                else
                {
                    $toners = $deviceInstance->getMasterDevice()->getCheapestTonerSetByVendor($costPerPageSetting);
                }

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
                $blackCost  = number_format($blackToner->cost, 2, '.', '');
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
                $rowData                     = array();
                $rowData [0]['deviceName']   = str_ireplace("hewlett-packard", "HP", $deviceInstance->getDeviceName());
                $rowData [0]['manufacturer'] = $deviceInstance->getMasterDevice()->getManufacturer()->fullname;
                $rowData [0]['name']         = $deviceInstance->getMasterDevice()->modelName;
                $rowData [0]['ipAddress']    = $deviceInstance->ipAddress;
                $rowData [0]['serialNumber'] = $deviceInstance->serialNumber;
                $rowData [1]                 = $deviceInstance->getPageCounts()->getBlackPageCount()->getMonthly();
                $rowData [2]                 = $blackCost;
                $rowData [3]                 = $blackYield;
                $blackCPP                    = $deviceInstance->calculateCostPerPage($costPerPageSetting)->monochromeCostPerPage;
                $rowData [4]                 = $blackCPP;
                $rowData [5]                 = $deviceInstance->getMonthlyBlackAndWhiteCost($costPerPageSetting);
                $rowData [6]                 = $isColor ? number_format($deviceInstance->getPageCounts()->getColorPageCount()->getMonthly(), 0, '.', '') : "-";
                $rowData [7]                 = $colorCost;
                $rowData [8]                 = $colorYield;
                $rowData [9]                 = $isColor ? "$" . number_format($deviceInstance->calculateCostPerPage($costPerPageSetting)->colorCostPerPage, 4, '.', '') : "-";
                $rowData [10]                = $isColor ? "$" . number_format($deviceInstance->calculateMonthlyColorCost($costPerPageSetting), 2, '.', '') : "-";
                $rowData ['completeMono']    = $completeMonoToners;
                $rowData ['completeColor']   = $completeColorToners;
                $fieldLists[]                = $rowData;
            }

            $fieldTotals      = array();
            $fieldTotals [0]  = 'Totals for ' . $assessmentViewModel->getDevices()->purchasedDeviceInstances->getCount() . ' devices:';
            $fieldTotals [4]  = $assessmentViewModel->getDevices()->purchasedDeviceInstances->getPageCounts()->getBlackPageCount()->getMonthly();
            $fieldTotals [5]  = '';
            $fieldTotals [6]  = '';
            $fieldTotals [7]  = '';
            $fieldTotals [8]  = $assessmentViewModel->getGrossMarginTotalMonthlyCost($costPerPageSetting)->BlackAndWhite;
            $fieldTotals [9]  = $assessmentViewModel->getDevices()->purchasedDeviceInstances->getPageCounts()->getColorPageCount()->getMonthly();
            $fieldTotals [10] = '';
            $fieldTotals [11] = '';
            $fieldTotals [12] = '';
            $fieldTotals [13] = $assessmentViewModel->getGrossMarginTotalMonthlyCost()->Color;
        }
        catch (Exception $e)
        {
            throw new Exception("CSV File could not be opened/written for export.", 0, $e);
        }

        return array('pageTitle' => $pageTitle, 'fieldTitles' => $fieldTitles, 'statisticsGroup' => $statisticsGroup, 'fieldTotals' => $fieldTotals, 'fieldLists' => $fieldLists);
    }
}