<?php

/**
 * Class Memjetoptimization_Report_Customer_OptimizationController
 */
class Memjetoptimization_Report_Customer_OptimizationController extends Memjetoptimization_Library_Controller_Action
{
    public function indexAction ()
    {

        $this->_navigation->setActiveStep(Memjetoptimization_Model_Memjet_Optimization_Steps::STEP_FINISHED);
        $this->initHtmlReport();
        $this->initReportList();

        $this->view->formats = array(
            "/memjetoptimization/report_customer_optimization/generate/format/docx" => $this->_wordFormat
        );

        try
        {
            $this->clearCacheForReport();
            $this->view->optimization                                       = $this->getOptimizationViewModel();
            $this->view->availableReports['CustomerOptimization']['active'] = true;
            $this->view->memjetOptimization                                 = $this->_memjetOptimization;

        }
        catch (Exception $e)
        {
            throw new Exception("Couldn't generate Memjet optimization.", 0, $e);
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
                $customerOptimization   = new Memjetoptimization_Model_Optimization_Customer($this->_memjetOptimization);
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

        $filename = $this->generateReportFilename($this->getMemjetOptimization()->getClient(), 'Customer_Memjet_Optimization') . ".$format";

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