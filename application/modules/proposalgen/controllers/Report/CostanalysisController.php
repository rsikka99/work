<?php

class Proposalgen_Report_CostanalysisController extends Proposalgen_Library_Controller_Proposal
{
    public function indexAction ()
    { // Mark the step we're on as active
        $this->setActiveReportStep(Proposalgen_Model_Assessment_Step::STEP_FINISHED);

        $this->initReportList();
        $this->initHtmlReport();

        $this->view->availableReports->CustomerCostAnalysis->active = true;


        $this->view->availableReports->CustomerCostAnalysis->active = true;
        $this->view->formats                                        = array(
            "/proposalgen/report_costanalysis/generate/format/csv"  => $this->_csvFormat,
            "/proposalgen/report_costanalysis/generate/format/docx" => $this->_wordFormat
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
            throw new Exception("Could not generate gross margin report.");
        }
    }

    public function generateAction ()
    {

    }
}