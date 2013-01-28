<?php
class Proposalgen_Report_OptimizationController extends Proposalgen_Library_Controller_Proposal
{
    public function indexAction ()
    {
        $this->initReportList();

        $this->view->availableReports->Solution->active = true;

        $this->view->formats = array(
            "/proposalgen/solution/generate/format/docx" => $this->wordFormat
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

        $this->_helper->layout->setLayout('htmlreport');
    }
}