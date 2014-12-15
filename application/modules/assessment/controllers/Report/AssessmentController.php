<?php
use MPSToolbox\Legacy\Modules\Assessment\Models\AssessmentStepsModel;

/**
 * Class Assessment_Report_AssessmentController
 */
class Assessment_Report_AssessmentController extends Assessment_Library_Controller_Action
{

    /**
     * The assessmentAction displays the OD assessment report.
     * Data is retrieved
     * from the database and displayed using HTML, CSS, and javascript.
     */
    public function indexAction ()
    {
        $this->_pageTitle = array('Assessment');
        $this->_navigation->setActiveStep(AssessmentStepsModel::STEP_FINISHED);


        $this->initReportList();
        $this->initHtmlReport();

        $this->view->availableReports['Assessment']['active'] = true;

        $this->view->formats = array(
            "/assessment/report_assessment/generate/format/docx" => $this->_wordFormat
        );

        $this->view->reportTitle = My_Brand::getDealerBranding()->assessmentTitle;

        $format = $this->_getParam("format", "html");
        try
        {
            // Clear the cache for the report before proceeding
            $this->clearCacheForReport();
            if (false !== ($assessmentViewModel = $this->getAssessmentViewModel()))
            {
                switch ($format)
                {
                    case "docx" :
                        // Add DOCX Logic here
                        $this->view->phpword = new \PhpOffice\PhpWord\PhpWord();
                        break;
                    case "html" :
                    default :
                        // Add HTML Logic here
                        break;
                }
            }
            else
            {
                throw new Exception(sprintf('Assessment View Model is false. ["%s"]', implode(' | ', $this->view->ErrorMessages)));
            }
            $this->view->assessmentViewModel = $assessmentViewModel;
        }
        catch (Exception $e)
        {
            throw new Exception("Assessment could not be generated.", 0, $e);
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
                $this->view->phpword = new \PhpOffice\PhpWord\PhpWord();
                $assessmentViewModel = $this->getAssessmentViewModel();
                $graphs              = $this->cachePNGImages($assessmentViewModel->getGraphs(), true);
                $assessmentViewModel->setGraphs($graphs);
                $this->view->wordStyles = $this->getWordStyles();
                $this->_helper->layout->disableLayout();
                break;
            default :
                throw new Exception("Invalid Format Requested!");
                break;
        }

        $filename = $this->generateReportFilename($this->getAssessment()->getClient(), My_Brand::getDealerBranding()->assessmentTitle) . ".$format";

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



