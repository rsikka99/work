<?php

class Proposalgen_Report_DebugController extends Proposalgen_Library_Controller_Proposal
{

    /**
     * The debug action displays information about all devices in the fleet
     */
    public function indexAction ()
    {
        // Mark the step we're on as active
        $this->setActiveReportStep(Proposalgen_Model_Report_Step::STEP_FINISHED);

        $this->initReportList();
        $this->initHtmlReport();


        $this->view->reportTitle = "Debug";
        try
        {
            if (false !== ($proposal = $this->getProposal()))
            {

            }
            else
            {
                throw new Exception("Proposal is false");
            }
            $this->view->proposal = $proposal;
        }
        catch (Exception $e)
        {
            throw new Exception("Debug report could not be generated.", 0, $e);
        }
    }
}



