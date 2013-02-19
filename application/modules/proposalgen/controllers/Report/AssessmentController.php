<?php

class Proposalgen_Report_AssessmentController extends Proposalgen_Library_Controller_Proposal
{

    /**
     * The assessmentAction displays the OD assessment report.
     * Data is retrieved
     * from the database and displayed using HTML, CSS, and javascript.
     */
    public function indexAction ()
    {
        $this->initReportList();
        $this->initHtmlReport();

        $this->view->availableReports->Assessment->active = true;

        $this->view->formats = array(
            "/proposalgen/report_assessment/generate/format/docx" => $this->_wordFormat
        );

        $this->view->reportTitle = "Assessment";

        $format = $this->_getParam("format", "html");
        try
        {
            // Clear the cache for the report before proceeding
            $this->clearCacheForReport();
            if (false !== ($proposal = $this->getProposal()))
            {
                switch ($format)
                {
                    case "docx" :
                        // Add DOCX Logic here

                        $this->view->phpword = new PHPWord();
                        break;
                    case "pdf" :
                        // Add PDF Logic here
                        break;
                    case "html" :
                    default :
                        // Add HTML Logic here
                        break;
                }
            }
            else
            {
                throw new Exception("Proposal is false");
            }
            $this->view->proposal = $proposal;
        }
        catch (Exception $e)
        {
            throw new Exception("Assessment could not be generated.", 0, $e);
        }

//        $this->_helper->layout->setLayout('htmlreport');
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
                $graphs   = $this->cachePNGImages($proposal->getGraphs(), true);
                $proposal->setGraphs($graphs);
                $this->_helper->layout->disableLayout();
                break;
            case "pdf" :
                throw new Exception("PDF Format not available through this page yet!");
                break;
            default :
                throw new Exception("Invalid Format Requested!");
                break;
        }

        $filename = "assessment.$format";

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



