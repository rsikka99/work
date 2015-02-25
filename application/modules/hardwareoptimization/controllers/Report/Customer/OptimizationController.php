<?php
use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\OptimizationCustomerModel;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\HardwareOptimizationStepsModel;

/**
 * Class Hardwareoptimization_Report_Customer_OptimizationController
 */
class Hardwareoptimization_Report_Customer_OptimizationController extends Hardwareoptimization_Library_Controller_Action
{
    public function indexAction ()
    {
        $this->_pageTitle = ['Hardware Optimization', 'Customer'];
        $this->_navigation->setActiveStep(HardwareOptimizationStepsModel::STEP_FINISHED);
        $this->initHtmlReport();
        $this->initReportList();

        $this->view->formats = [
            "/hardwareoptimization/report_customer_optimization/generate/format/docx" => $this->_wordFormat
        ];

        try
        {
            $this->clearCacheForReport();
            $this->view->optimization                                       = $this->getOptimizationViewModel();
            $this->view->availableReports['CustomerOptimization']['active'] = true;
            $this->view->hardwareOptimization                               = $this->_hardwareOptimization;

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
                $customerOptimization   = new OptimizationCustomerModel($this->_hardwareOptimization);
                $graphs                 = $this->cachePNGImages($customerOptimization->getGraphs(), true);
                $this->view->phpword    = new \PhpOffice\PhpWord\PhpWord();
                $this->view->wordStyles = $this->getWordStyles();
                $this->view->graphs     = $graphs;

                $this->_helper->layout->disableLayout();
                break;
            case "pdf" :
                throw new Exception("PDF Format not available through this page yet!");
                break;
            default :
                throw new Exception("Invalid Format Requested!");
                break;
        }

        $filename = $this->generateReportFilename($this->getHardwareOptimization()->getClient(), My_Brand::getDealerBranding()->customerOptimizationTitle) . ".$format";

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