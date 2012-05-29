<?php

class Proposal_SolutionController extends My_Controller_Report
{

    public function indexAction ()
    {
        $this->_redirect("/proposal/solution/generate");
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
                $this->initDocx();
                $this->_helper->layout->disableLayout();
                break;
            case "pdf" :
                throw new Exception("PDF Format not available through this page yet!");
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
            $this->render($format . "/00_render");
        }
        catch ( Exception $e )
        {
            throw new Exception("Controller caught the exception!", 0, $e);
        }
    }
} // end index controller

