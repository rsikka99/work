<?php
use MPSToolbox\Legacy\Modules\Assessment\Models\AssessmentStepsModel;

/**
 * Class Assessment_Report_SolutionController
 */
class Assessment_Report_SolutionController extends Assessment_Library_Controller_Action
{
    /**
     * Makes sure the user has access to this report, otherwise it sends them back
     */
    public function init ()
    {
        if (!My_Feature::canAccess(My_Feature::ASSESSMENT_SOLUTION))
        {
            $this->_flashMessenger->addMessage([
                "error" => "You do not have permission to access this."
            ]);

            $this->redirectToRoute('assessment');
        }

        parent::init();
    }

    /**
     * The solution Action will be used to display the solution report
     * Data is grabbed from the database, and displayed using HTML, CSS, and
     * javascript.
     */
    public function indexAction ()
    {
        $this->_pageTitle = ['Assessment', 'Solution'];
        $this->_navigation->setActiveStep(AssessmentStepsModel::STEP_FINISHED);

        $this->initReportList();
        $this->initHtmlReport();

        $this->view->availableReports['Solution']['active'] = true;
        $this->view->formats                                = [
            "/assessment/report_solution/generate/format/docx" => $this->_wordFormat
        ];

        try
        {
            // Clear the cache for the report before proceeding
            $this->clearCacheForReport();

            $assessmentViewModel             = $this->getAssessmentViewModel();
            $this->view->assessmentViewModel = $assessmentViewModel;
        }
        catch (Exception $e)
        {
            throw new Exception("Could not generate solution.");
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
                $this->view->phpword    = new \PhpOffice\PhpWord\PhpWord();
                $this->view->wordStyles = $this->getWordStyles();
                $this->_helper->layout->disableLayout();
                break;
            default :
                throw new Exception("Invalid Format Requested!");
                break;
        }

        $filename = $this->generateReportFilename($this->getAssessment()->getClient(), My_Brand::getDealerBranding()->solutionTitle) . ".$format";

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
} // end index controller

