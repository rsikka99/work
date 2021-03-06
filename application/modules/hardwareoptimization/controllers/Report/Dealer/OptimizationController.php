<?php

use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\HardwareOptimizationStepsModel;

/**
 * Class Hardwareoptimization_Report_Dealer_OptimizationController
 */
class Hardwareoptimization_Report_Dealer_OptimizationController extends Hardwareoptimization_Library_Controller_Action
{
    public function indexAction ()
    {
        $png = $this->_getParam('png');
        if ($png) {
            $this->_helper->viewRenderer->setNoRender(true);
            $this->_helper->layout->disableLayout();
            $this->getOptimizationViewModel()->dealer_png($png);
            return;
        }

        $this->_pageTitle = ['Hardware Optimization', 'Dealer'];
        $this->_navigation->setActiveStep(HardwareOptimizationStepsModel::STEP_FINISHED);

        $this->initHtmlReport();
        $this->initReportList();

        $this->view->formats = [
            "/hardwareoptimization/report_dealer_optimization/generate/format/docx" => $this->_wordFormat
        ];

        try
        {
            $this->view->optimization                                     = $this->getOptimizationViewModel();
            $this->view->availableReports['DealerOptimization']['active'] = true;
            $this->view->hardwareOptimization                             = $this->_hardwareOptimization;
        }
        catch (Exception $e)
        {
            throw new Exception("Couldn't generate hardware optimization.", 0, $e);
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
                throw new Exception("CSV Format not available through this page yet!");
                break;
            case "docx" :
                $this->clearCacheForReport();
                $this->view->optimization   = $this->getOptimizationViewModel();
                $this->view->phpword        = new \PhpOffice\PhpWord\PhpWord();
                $this->view->wordStyles     = $this->getWordStyles();
                $this->view->graphs         = $this->cachePNGImages($this->view->optimization->getDealerCharts(), true);
                $this->view->dealerLogoFile = $this->getDealerLogoFile();

                $this->_helper->layout->disableLayout();
                break;
            case "pdf" :
                throw new Exception("PDF Format not available through this page yet!");
                break;
            default :
                throw new Exception("Invalid Format Requested!");
                break;
        }

        $filename = $this->generateReportFilename($this->getHardwareOptimization()->getClient(), 'Dealer_Hardware_Optimization') . ".$format";

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
}