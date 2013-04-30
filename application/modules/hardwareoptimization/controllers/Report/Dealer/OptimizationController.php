<?php
/**
 * Class Hardwareoptimization_Report_Dealer_OptimizationController
 */
class Hardwareoptimization_Report_Dealer_OptimizationController extends Hardwareoptimization_Library_Controller_Action
{
    public function indexAction ()
    {
        $this->_navigation->setActiveStep(Hardwareoptimization_Model_Hardware_Optimization_Steps::STEP_FINISHED);

        $this->initHtmlReport();
        $this->initReportList();


        $this->view->formats = array(
            "/hardwareoptimization/report_dealer_optimization/generate/format/docx" => $this->_wordFormat
        );

        try
        {
            $this->clearCacheForReport();
            $this->view->optimization = $this->getOptimizationViewModel();
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
                $dealerOptimization = new Hardwareoptimization_Model_Optimization_Dealer($this->_hardwareOptimization);
                $graphs             = $this->cachePNGImages($dealerOptimization->getGraphs(), true);

                require_once ('PHPWord.php');
                $this->view->phpword    = new PHPWord();
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

        $filename = "dealerHardwareOptimization.$format";

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