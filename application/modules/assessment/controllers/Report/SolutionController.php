<?php
class Assessment_Report_SolutionController extends Assessment_Library_Controller_Action
{

    /**
     * The solution Action will be used to display the solution report
     * Data is grabbed from the database, and displayed using HTML, CSS, and
     * javascript.
     */
    public function indexAction ()
    {
        $this->_navigation->setActiveStep(Assessment_Model_Assessment_Steps::STEP_FINISHED);

        $this->initReportList();
        $this->initHtmlReport();

        $this->view->availableReports->Solution->active = true;

        $this->view->formats = array(
            "/assessment/report_solution/generate/format/docx" => $this->_wordFormat
        );

        try
        {
            // Clear the cache for the report before proceeding
            $this->clearCacheForReport();

            $proposal             = $this->getAssessmentViewModel();
            $this->view->proposal = $proposal;
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
                require_once ('PHPWord.php');
                $this->view->phpword = new PHPWord();
                $proposal = $this->getAssessmentViewModel();
                $graphs   = $this->cachePNGImages($proposal->getGraphs(), true);
                $this->view->wordStyles = $this->getWordStyles();
                $proposal->setGraphs($graphs);
                $this->_helper->layout->disableLayout();
                break;
            default :
                throw new Exception("Invalid Format Requested!");
                break;
        }

        $filename = "solution.$format";

        $this->initReportVariables($filename);

        // Render early
        try
        {
            $this->render($format  . "/00_render");
        }
        catch (Exception $e)
        {
            throw new Exception("Controller caught the exception!", 0, $e);
        }
    }
} // end index controller

