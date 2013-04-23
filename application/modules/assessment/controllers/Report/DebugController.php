<?php

class Assessment_Report_DebugController extends Assessment_Library_Controller_Action
{

    /**
     * The debug action displays information about all devices in the fleet
     */
    public function indexAction ()
    {
        $this->_navigation->setActiveStep(Assessment_Model_Assessment_Steps::STEP_FINISHED);

        $this->initReportList();
        $this->initHtmlReport();


        $this->view->reportTitle = "Debug";
        try
        {
            if (false !== ($proposal = $this->getAssessmentViewModel()))
            {

            }
            else
            {
                throw new Exception("Assessment View Model is false");
            }
            $this->view->proposal = $proposal;
        }
        catch (Exception $e)
        {
            throw new Exception("Debug report could not be generated.", 0, $e);
        }
    }
}


