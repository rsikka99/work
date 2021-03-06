<?php
use MPSToolbox\Legacy\Modules\Assessment\Models\AssessmentStepsModel;

/**
 * Class Assessment_Report_DebugController
 */
class Assessment_Report_DebugController extends Assessment_Library_Controller_Action
{

    /**
     * The debug action displays information about all devices in the fleet
     */
    public function indexAction ()
    {
        $this->_pageTitle = ['Assessment', 'Debug'];
        $this->_navigation->setActiveStep(AssessmentStepsModel::STEP_FINISHED);

        $this->initReportList();
        $this->initHtmlReport();


        $this->view->reportTitle = "Debug";
        try
        {
            if (false !== ($assessmentViewModel = $this->getAssessmentViewModel()))
            {

            }
            else
            {
                throw new Exception("Assessment View Model is false");
            }
            $this->view->assessmentViewModel = $assessmentViewModel;
        }
        catch (Exception $e)
        {
            throw new Exception("Debug report could not be generated.", 0, $e);
        }
    }
}



