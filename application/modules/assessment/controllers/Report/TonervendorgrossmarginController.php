<?php
use MPSToolbox\Legacy\Modules\Admin\Mappers\DealerTonerVendorMapper;
use MPSToolbox\Legacy\Modules\Assessment\Models\AssessmentStepsModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerColorModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerVendorRankingSetModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerVendorRankingModel;

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
            $this->_flashMessenger->addMessage([
                "error" => "You do not have permission to access this."
            ]);

            $this->redirectToRoute('assessment');
        }

        parent::init();
    }

    /**
     * The gross margin Action will be used to display the gross margin report
     */
    public function indexAction ()
    {
        $this->_pageTitle = ['Assessment', 'Toner Vendor Gross Margin'];
        $this->_navigation->setActiveStep(AssessmentStepsModel::STEP_FINISHED);

        $this->initReportList();
        $this->initHtmlReport();
        $this->initTonerVendorGrossMargin();
        $this->view->availableReports['TonerVendorGrossMargin']['active'] = true;
        $this->view->formats                                              = [
            "/assessment/report_tonervendorgrossmargin/generate/format/excel" => $this->_excelFormat,
        ];

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

        $filename = $this->generateReportFilename($this->getAssessment()->getClient(), 'Toner Vendor Gross Margin') . '.xlsx';

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
        $fieldTitlesLvl1 = [
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
        ];

        $fieldTitlesLvl2 = [
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
        ];

        $fieldTitles         = [$fieldTitlesLvl1, $fieldTitlesLvl2];
        $vendorSeparatedData = [];

        $dealerTonerVendors = DealerTonerVendorMapper::getInstance()->fetchAllForDealer($this->_identity->dealerId);

        if (count($dealerTonerVendors) > 0)
        {
            // Your Preferences
            $vendorSeparatedData[] = $this->getReportData($assessmentViewModel, $fieldTitles, "Your Preferences", 0);
        }

        // OEM
        $vendorSeparatedData[] = $this->getReportData($assessmentViewModel, $fieldTitles, "OEM", -1);

        // Individual Vendors
        foreach ($dealerTonerVendors as $dealerTonerVendor)
        {

            $vendorSeparatedData[] = $this->getReportData($assessmentViewModel, $fieldTitles, $dealerTonerVendor->getManufacturer()->fullname, $dealerTonerVendor->manufacturerId);
        }

        $highestMarginNames = [];
        $highestMargin      = -5000;

        foreach ($vendorSeparatedData as $arrayData)
        {
            $currentPercentage = $arrayData['statisticsGroup']['right']['Overall Margin'];
            if ($currentPercentage > $highestMargin)
            {
                $highestMarginNames = [$arrayData['pageTitle']];
                $highestMargin      = $currentPercentage;
            }
            else if ($currentPercentage == $highestMargin)
            {
                $highestMarginNames[] = $arrayData['pageTitle'];
            }
        }

        $this->view->vendorSeparatedData = $vendorSeparatedData;
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
        $statisticsGroup                                                                            = [];
        $statisticsGroup['left'][My_Brand::getDealerBranding()->mpsProgramName . ' Monochrome CPP'] = $this->view->formatCostPerPage($assessmentViewModel->getMPSBlackAndWhiteCPP());
        $statisticsGroup['left'][My_Brand::getDealerBranding()->mpsProgramName . ' Color CPP']      = $this->view->formatCostPerPage($assessmentViewModel->getMPSColorCPP());
        $statisticsGroup['left']['Weighted Monochrome CPP']                                         = $this->view->formatCostPerPage($assessmentViewModel->getGrossMarginWeightedCPP($costPerPageSetting)->BlackAndWhite);
        $statisticsGroup['left']['Weighted Color CPP']                                              = $this->view->formatCostPerPage($assessmentViewModel->getGrossMarginWeightedCPP($costPerPageSetting)->Color);
        $statisticsGroup['left']['Monochrome Margin']                                               = number_format($assessmentViewModel->getGrossMarginBlackAndWhiteMargin($costPerPageSetting)) . "%";

        $statisticsGroup['right']['Total Cost']     = $this->view->currency($assessmentViewModel->getGrossMarginTotalMonthlyCost($costPerPageSetting)->Combined);
        $statisticsGroup['right']['Total Revenue']  = $this->view->currency($assessmentViewModel->getGrossMarginTotalMonthlyRevenue()->Combined);
        $statisticsGroup['right']['Monthly Profit'] = $this->view->currency($assessmentViewModel->getGrossMarginMonthlyProfit($costPerPageSetting));
        $statisticsGroup['right']['Overall Margin'] = number_format($assessmentViewModel->getGrossMarginOverallMargin($costPerPageSetting));
        $statisticsGroup['right']['Color Margin']   = number_format($assessmentViewModel->getGrossMarginColorMargin($costPerPageSetting)) . "%";

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
            $tonerRankSet            = new TonerVendorRankingSetModel();
            $ranking                 = new TonerVendorRankingModel();
            $ranking->manufacturerId = $tonerVendorId;
            $tonerRankSet->setRankings([$ranking]);
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
            $tonerRankSet                               = new TonerVendorRankingSetModel();
            $costPerPageSetting                         = $assessmentViewModel->getCostPerPageSettingForDealer();
            $costPerPageSetting->monochromeTonerRankSet = $tonerRankSet;
            $costPerPageSetting->colorTonerRankSet      = $tonerRankSet;
        }

        // Get the statistics
        $statisticsGroup = $this->getStatistics($assessmentViewModel, $costPerPageSetting);

        $fieldLists = [];
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

                $black_isUsingDealerPricing = true;
                $color_isUsingDealerPricing = true;

                foreach ($toners as $toner)
                {
                    switch ($toner->tonerColorId)
                    {
                        case TonerColorModel::BLACK:
                            $blackToner = $toner;
                            $black_isUsingDealerPricing      = $toner->isUsingDealerPricing;
                            break;
                        case TonerColorModel::CYAN:
                        case TonerColorModel::MAGENTA:
                        case TonerColorModel::YELLOW:
                            $colorToner = $toner;
                        $color_isUsingDealerPricing     &= $toner->isUsingDealerPricing;
                            break;
                        case TonerColorModel::THREE_COLOR:
                            $colorToner = $toner;
                            $color_isUsingDealerPricing     &= $toner->isUsingDealerPricing;
                            break;
                        case TonerColorModel::FOUR_COLOR:
                            $blackToner = $toner;
                            $colorToner = $toner;
                            $color_isUsingDealerPricing     &= $toner->isUsingDealerPricing;
                            break;
                        default:
                            break;
                    }
                }

                // Black Toner
                $blackCost  = '-';
                $blackYield = '-';
                if ($blackToner instanceof \MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerModel)
                {
                    $blackCost  = $blackToner->calculatedCost;
                    $blackYield = $blackToner->yield;
                }


                // Color Toner
                $colorCost  = '-';
                $colorYield = '-';
                $isColor    = false;

                if ($colorToner)
                {
                    $colorCost  = $colorToner->calculatedCost;
                    $colorYield = $colorToner->yield;
                    $isColor    = true;
                }

                // Create an array of purchased devices (this will be the dynamic CSV body)
                $rowData                     = [];
                $rowData [0]['deviceName']   = str_ireplace("hewlett-packard", "HP", $deviceInstance->getDeviceName());
                $rowData [0]['manufacturer'] = $deviceInstance->getMasterDevice()->getManufacturer()->fullname;
                $rowData [0]['name']         = $deviceInstance->getMasterDevice()->modelName;
                $rowData [0]['ipAddress']    = $deviceInstance->ipAddress;
                $rowData [0]['serialNumber'] = $deviceInstance->serialNumber;
                $rowData [1]                 = $deviceInstance->getPageCounts()->getBlackPageCount()->getMonthly();
                $rowData [2]                 = $blackCost;
                $rowData [3]                 = $blackYield;
                $blackCPP                    = $deviceInstance->calculateCostPerPage($costPerPageSetting)->getCostPerPage()->monochromeCostPerPage;
                $rowData [4]                 = $blackCPP;
                $rowData [5]                 = $deviceInstance->getMonthlyBlackAndWhiteCost($costPerPageSetting);
                $rowData [6]                 = ($isColor) ? $deviceInstance->getPageCounts()->getColorPageCount()->getMonthly() : "-";
                $rowData [7]                 = $colorCost;
                $rowData [8]                 = $colorYield;
                $rowData [9]                 = ($isColor) ? $deviceInstance->calculateCostPerPage($costPerPageSetting)->getCostPerPage()->colorCostPerPage : "-";
                $rowData [10]                = ($isColor) ? $deviceInstance->calculateMonthlyColorCost($costPerPageSetting) : "-";
                $rowData ['completeMono']    = $completeMonoToners;
                $rowData ['completeColor']   = $completeColorToners;
                $rowData ['black_isUsingDealerPricing']    = $black_isUsingDealerPricing;
                $rowData ['color_isUsingDealerPricing']    = $color_isUsingDealerPricing;
                $fieldLists[]                = $rowData;
            }

            $fieldTotals      = [];
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

        return ['pageTitle' => $pageTitle, 'fieldTitles' => $fieldTitles, 'statisticsGroup' => $statisticsGroup, 'fieldTotals' => $fieldTotals, 'fieldLists' => $fieldLists];
    }
}