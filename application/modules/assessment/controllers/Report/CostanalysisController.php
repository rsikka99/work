<?php

class Assessment_Report_CostanalysisController extends Assessment_Library_Controller_Action
{
    public function indexAction ()
    {
        $this->_navigation->setActiveStep(Assessment_Model_Assessment_Steps::STEP_FINISHED);

        $this->initReportList();
        $this->initHtmlReport();

        $this->view->availableReports->CustomerCostAnalysis->active = true;


        $this->view->availableReports->CustomerCostAnalysis->active = true;
        $this->view->formats                                        = array(
            "/assessment/report_costanalysis/generate/format/csv"  => $this->_csvFormat,
            "/assessment/report_costanalysis/generate/format/docx" => $this->_wordFormat
        );

        try
        {
            // Clear the cache for the report before proceeding
            $this->clearCacheForReport();
            $proposal             = $this->getAssessmentViewModel();
            $this->view->proposal = $proposal;
        }
        catch (Exception $e)
        {
            throw new Exception("Could not generate gross margin report.");
        }
    }

    public function generateAction ()
    {
        $format = $this->_getParam("format", "csv");

        $this->clearCacheForReport();

        switch ($format)
        {
            case "csv" :
                $this->_helper->layout->disableLayout();
                $this->initCSVCostAnalysis();
                break;
            case "docx" :
                require_once ('PHPWord.php');
                $this->view->phpword = new PHPWord();
                $this->view->wordStyles = $this->getWordStyles();
                $this->_helper->layout->disableLayout();
                break;
            default :
                throw new Exception("Invalid Format Requested!");
                break;
        }

        $filename = "costanalysis.$format";

        $this->initReportVariables($filename);

        // Render early
        try
        {
            $this->render($format . "/00-render");
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
    public function initCSVCostAnalysis ()
    {
        try
        {
            $proposal = $this->getAssessmentViewModel();

            $this->view->monochromeCPP = $this->view->currency($proposal->calculateCustomerWeightedAverageMonthlyCostPerPage()->monochromeCostPerPage, array("precision" => 4));
            $this->view->colorCPP      = $this->view->currency($proposal->calculateCustomerWeightedAverageMonthlyCostPerPage()->colorCostPerPage, array("precision" => 4));
        }
        catch (Exception $e)
        {
            throw new Exception("Could not generate gross margin csv report.");
        }

        // Define our field titles
        $fieldTitleList = array(
            'Device Name',
            '% Of Monthly Cost',
            'Monthly Black Volume',
            'Monthly Color Volume',
            'Black CPP',
            'Color CPP',
            'Estimated Monthly Cost',
        );

        try
        {
            $fieldList_Values = "";
            /* @var $deviceInstance Proposalgen_Model_DeviceInstance() */
            foreach ($proposal->getMonthlyHighCostPurchasedDevice($proposal->getCostPerPageSettingForCustomer()) as $deviceInstance)
            {

                $percentOfMonthlyCost = ($proposal->calculateTotalMonthlyCost() > 0) ? number_format($deviceInstance->calculateMonthlyCost($proposal->getCostPerPageSettingForCustomer()) / $proposal->calculateTotalMonthlyCost() * 100, 2) : 0;
                $isColor              = ($deviceInstance->getMasterDevice()->tonerConfigId != Proposalgen_Model_TonerConfig::BLACK_ONLY) ? true : false;

                // Create an array of purchased devices (this will be the dynamic CSV body)
                $fieldList    = array();
                $fieldList [] = $deviceInstance->getDeviceName();
                $fieldList [] = "%" . number_format($percentOfMonthlyCost, 2);
                $fieldList [] = round($deviceInstance->getAverageMonthlyBlackAndWhitePageCount());
                $fieldList [] = ($isColor) ? round($deviceInstance->getAverageMonthlyColorPageCount()) : '-';
                $fieldList [] = $this->view->currency($deviceInstance->calculateCostPerPage($proposal->getCostPerPageSettingForCustomer())->monochromeCostPerPage, array("precision" => 4));
                $fieldList [] = ($isColor) ? $this->view->currency($deviceInstance->calculateCostPerPage($proposal->getCostPerPageSettingForCustomer())->colorCostPerPage, array("precision" => 4)) : '-';
                $fieldList [] = $this->view->currency($deviceInstance->calculateMonthlyCost($proposal->getCostPerPageSettingForCustomer()));

                $fieldList_Values .= implode(",", $fieldList) . "\n";
            }

        }
        catch (Exception $e)
        {
            throw new Exception("CSV File could not be opened/written for export.", 0, $e);
        }

        $this->view->fieldTitleList = implode(",", $fieldTitleList) . "\n";
        $this->view->fieldList      = $fieldList_Values;

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
                                   ), "_", $proposal->assessment->CustomerCompanyName);
    }
}