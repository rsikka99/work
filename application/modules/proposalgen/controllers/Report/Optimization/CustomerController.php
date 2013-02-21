<?php
class Proposalgen_Report_Optimization_CustomerController  extends Proposalgen_Library_Controller_Proposal
{
    public function indexAction ()
    {
        $this->initReportList();
        $this->initHtmlReport();
        $this->setActiveReportStep(Proposalgen_Model_Report_Step::STEP_FINISHED);
        $this->view->availableReports->CustomerHardwareOptimization->active = true;

        $this->view->formats = array(
            "/proposalgen/report_optimization_customer/generate/format/docx" => $this->_wordFormat
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
            throw new Exception("Could not generate solution report.");
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
                require_once ('PHPWord.php');
                $this->view->phpword = new PHPWord();
                $proposal = $this->getProposal();
                $customerProfitability = new Proposalgen_Model_Optimization_Customer($proposal);
                $graphs   = $this->cachePNGImages($customerProfitability->getGraphs(), true);
                $customerProfitability->setGraphs($graphs);
                $this->view->graphs = $graphs;
                $this->_helper->layout->disableLayout();
                break;
            case "pdf" :
                throw new Exception("PDF Format not available through this page yet!");
                break;
            default :
                throw new Exception("Invalid Format Requested!");
                break;
        }

        $filename = "customerHardwareOptimization.$format";

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