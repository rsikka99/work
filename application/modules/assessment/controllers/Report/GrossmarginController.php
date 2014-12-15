<?php
use MPSToolbox\Legacy\Modules\Assessment\Models\AssessmentStepsModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DealerTonerAttributeMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DealerTonerAttributeModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerColorModel;

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

            $this->redirectToRoute('assessment');
        }

        parent::init();
    }

    /**
     * The gross margin Action will be used to display the gross margin report
     */
    public function indexAction ()
    {
        $this->_pageTitle = array('Assessment', 'Gross Margin');
        $this->_navigation->setActiveStep(AssessmentStepsModel::STEP_FINISHED);

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
            throw new Exception("Could not generate gross margin report.", 0, $e);
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
                $this->view->phpword = new \PhpOffice\PhpWord\PhpWord();
                $assessmentViewModel = $this->getAssessmentViewModel();
                $graphs              = $this->cachePNGImages($assessmentViewModel->getGraphs(), true);
                $assessmentViewModel->setGraphs($graphs);
                $this->_helper->layout->disableLayout();
                break;
            default :
                throw new Exception("Invalid Format Requested!");
                break;
        }

        $filename = $this->generateReportFilename($this->getAssessment()->getClient(), 'Gross Margin') . ".$format";

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

            $this->view->PrintIQ_Black_And_White_CPP  = $this->view->formatCostPerPage($assessmentViewModel->getMPSBlackAndWhiteCPP());
            $this->view->PrintIQ_Color_CPP            = $this->view->formatCostPerPage($assessmentViewModel->getMPSColorCPP());
            $this->view->Weighted_Black_And_White_CPP = $this->view->formatCostPerPage($assessmentViewModel->getGrossMarginWeightedCPP()->BlackAndWhite);
            $this->view->Weighted_Color_CPP           = $this->view->formatCostPerPage($assessmentViewModel->getGrossMarginWeightedCPP()->Color);
            $this->view->Black_And_White_Margin       = number_format($assessmentViewModel->getGrossMarginBlackAndWhiteMargin());

            $this->view->Total_Cost     = $this->view->currency($assessmentViewModel->getGrossMarginTotalMonthlyCost()->Combined);
            $this->view->Total_Revenue  = $this->view->currency($assessmentViewModel->getGrossMarginTotalMonthlyRevenue()->Combined);
            $this->view->Monthly_Profit = $this->view->currency($assessmentViewModel->getGrossMarginMonthlyProfit());
            $this->view->Overall_Margin = number_format($assessmentViewModel->getGrossMarginOverallMargin());
            $this->view->Color_Margin   = number_format($assessmentViewModel->getGrossMarginColorMargin());
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
            '',
            My_Brand::$dealerSku . 's',
            '',
            '',
            '',
            '',
            '',
            'OEM SKUs',
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
            'Total Printing Cost',
            'Black',
            'Cyan',
            'Magenta',
            'Yellow',
            'Three Color',
            'Four Color',
            'Black',
            'Cyan',
            'Magenta',
            'Yellow',
            'Three Color',
            'Four Color',
        );

        try
        {
            $dealerId         = Zend_Auth::getInstance()->getIdentity()->dealerId;
            $fieldList_Values = array();
            /* @var $deviceInstance DeviceInstanceModel() */
            foreach ($assessmentViewModel->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                $blackToner = null;
                $colorToner = null;

                $toners    = $deviceInstance->getMasterDevice()->getCheapestTonerSetByVendor($assessmentViewModel->getCostPerPageSettingForDealer());
                $tonerSkus = array();

                foreach ($toners as $toner)
                {
                    $dealerTonerAttribute = DealerTonerAttributeMapper::getInstance()->findTonerAttributeByTonerId($toner->id, $dealerId);
                    $dealerSku            = null;

                    if ($dealerTonerAttribute instanceof DealerTonerAttributeModel)
                    {
                        $dealerSku = $dealerTonerAttribute->dealerSku;
                    }

                    switch ($toner->tonerColorId)
                    {
                        case TonerColorModel::BLACK:
                            $blackToner                      = $toner;
                            $tonerSkus['black']['sku']       = $toner->sku;
                            $tonerSkus['black']['dealerSku'] = $dealerSku;
                            break;
                        case TonerColorModel::CYAN:
                            $colorToner                     = $toner;
                            $tonerSkus['cyan']['sku']       = $toner->sku;
                            $tonerSkus['cyan']['dealerSku'] = $dealerSku;
                            break;
                        case TonerColorModel::MAGENTA:
                            $tonerSkus['magenta']['sku']       = $toner->sku;
                            $tonerSkus['magenta']['dealerSku'] = $dealerSku;
                            $colorToner                        = $toner;
                            break;
                        case TonerColorModel::YELLOW:
                            $tonerSkus['yellow']['sku']       = $toner->sku;
                            $tonerSkus['yellow']['dealerSku'] = $dealerSku;
                            $colorToner                       = $toner;
                            break;
                        case TonerColorModel::THREE_COLOR:
                            $tonerSkus['threeColor']['sku']       = $toner->sku;
                            $tonerSkus['threeColor']['dealerSku'] = $dealerSku;
                            $colorToner                           = $toner;
                            break;
                        case TonerColorModel::FOUR_COLOR:
                            $tonerSkus['fourColor']['sku']       = $toner->sku;
                            $tonerSkus['fourColor']['dealerSku'] = $dealerSku;
                            $blackToner                          = $toner;
                            $colorToner                          = $toner;
                            break;
                        default:
                            break;
                    }
                }

                // Black Toner
                $blackCost  = $this->view->currency($blackToner->cost);
                $blackYield = number_format($blackToner->yield);

                // Color Toner
                $colorCost  = "-";
                $colorYield = "-";
                $isColor    = false;
                if ($colorToner)
                {
                    $colorCost  = $this->view->currency($colorToner->cost);
                    $colorYield = number_format($colorToner->yield);
                    $isColor    = true;
                }

                // Create an array of purchased devices (this will be the dynamic CSV body)
                $fieldList    = array();
                $fieldList [] = str_ireplace("hewlett-packard", "HP", $deviceInstance->getDeviceName()) . " (" . $deviceInstance->ipAddress . " - " . $deviceInstance->serialNumber . ")";
                $fieldList [] = $this->view->formatPageVolume($deviceInstance->getPageCounts()->getBlackPageCount()->getMonthly());
                $fieldList [] = $blackCost;
                $fieldList [] = $blackYield;
                $fieldList [] = $this->view->formatCostPerPage($deviceInstance->calculateCostPerPage($assessmentViewModel->getCostPerPageSettingForDealer())->getCostPerPage()->monochromeCostPerPage);

                $fieldList [] = $this->view->currency($deviceInstance->getMonthlyBlackAndWhiteCost($assessmentViewModel->getCostPerPageSettingForDealer()));
                $fieldList [] = $isColor ? $this->view->formatPageVolume($deviceInstance->getPageCounts()->getColorPageCount()->getMonthly()) : "-";
                $fieldList [] = $colorCost;
                $fieldList [] = $colorYield;
                $fieldList [] = $isColor ? $this->view->formatCostPerPage($deviceInstance->calculateCostPerPage($assessmentViewModel->getCostPerPageSettingForDealer())->getCostPerPage()->colorCostPerPage) : "-";
                $fieldList [] = $isColor ? $this->view->currency($deviceInstance->calculateMonthlyColorCost($assessmentViewModel->getCostPerPageSettingForDealer())) : "-";
                $fieldList [] = isset($tonerSkus['black']['dealerSku']) ? $tonerSkus['black']['dealerSku'] : '-';
                $fieldList [] = isset($tonerSkus['cyan']['dealerSku']) ? $tonerSkus['cyan']['dealerSku'] : '-';
                $fieldList [] = isset($tonerSkus['magenta']['dealerSku']) ? $tonerSkus['magenta']['dealerSku'] : '-';
                $fieldList [] = isset($tonerSkus['yellow']['dealerSku']) ? $tonerSkus['yellow']['dealerSku'] : '-';
                $fieldList [] = isset($tonerSkus['threeColor']['dealerSku']) ? $tonerSkus['threeColor']['dealerSku'] : '-';
                $fieldList [] = isset($tonerSkus['fourColor']['dealerSku']) ? $tonerSkus['fourColor']['dealerSku'] : '-';
                $fieldList [] = isset($tonerSkus['black']['sku']) ? $tonerSkus['black']['sku'] : '-';
                $fieldList [] = isset($tonerSkus['cyan']['sku']) ? $tonerSkus['cyan']['sku'] : '-';
                $fieldList [] = isset($tonerSkus['magenta']['sku']) ? $tonerSkus['magenta']['sku'] : '-';
                $fieldList [] = isset($tonerSkus['yellow']['sku']) ? $tonerSkus['yellow']['sku'] : '-';
                $fieldList [] = isset($tonerSkus['threeColor']['sku']) ? $tonerSkus['threeColor']['sku'] : '-';
                $fieldList [] = isset($tonerSkus['fourColor']['sku']) ? $tonerSkus['fourColor']['sku'] : '-';

                $fieldList_Values [] = $fieldList;
            }

            $fieldTotals    = array();
            $fieldTotals [] = 'Totals for ' . number_format($assessmentViewModel->getDevices()->allIncludedDeviceInstances->getCount()) . ' devices:';
            $fieldTotals [] = $this->view->formatPageVolume($assessmentViewModel->getDevices()->purchasedDeviceInstances->getPageCounts()->getBlackPageCount()->getMonthly());
            $fieldTotals [] = $this->view->formatPageVolume($assessmentViewModel->getDevices()->purchasedDeviceInstances->getPageCounts()->getBlackPageCount()->getMonthly());
            $fieldTotals [] = '';
            $fieldTotals [] = '';
            $fieldTotals [] = '';
            $fieldTotals [] = $this->view->currency($assessmentViewModel->getGrossMarginTotalMonthlyCost()->BlackAndWhite);
            $fieldTotals [] = $this->view->formatPageVolume($assessmentViewModel->getDevices()->purchasedDeviceInstances->getPageCounts()->getColorPageCount()->getMonthly());
            $fieldTotals [] = '';
            $fieldTotals [] = '';
            $fieldTotals [] = $this->view->currency($assessmentViewModel->getGrossMarginTotalMonthlyCost()->Color);
        }
        catch (Exception $e)
        {
            throw new Exception("CSV File could not be opened/written for export.", 0, $e);
        }

        $this->view->fieldTitlesLvl1 = $fieldTitlesLvl1;
        $this->view->fieldTitlesLvl2 = $fieldTitlesLvl2;
        $this->view->fieldList       = $fieldList_Values;
        $this->view->fieldTotals     = $fieldTotals;
    }
}