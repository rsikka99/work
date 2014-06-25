<?php

/**
 * Class Healthcheck_Report_HealthcheckController
 */
class Healthcheck_Report_HealthcheckController extends Healthcheck_Library_Controller_Action
{

    /**
     * @throws Exception
     */
    public function indexAction ()
    {
        $this->view->headTitle('Healthcheck');
        $this->_navigation->setActiveStep(Healthcheck_Model_Healthcheck_Steps::STEP_FINISHED);

        /**
         * If we have access to the Health Check, we will switch to it
         */
        if (My_Feature::canAccess(My_Feature::HEALTHCHECK_PRINTIQ))
        {
            $this->redirector('index', 'report_printiq_healthcheck', 'healthcheck');
        }

        $this->initReportList();
        $this->initHtmlReport();

        $this->view->availableReports['Healthcheck']['active'] = true;

        $this->view->formats = array(
            "/healthcheck/report_healthcheck/generate/format/docx" => $this->_wordFormat
        );

        $this->view->reportTitle = My_Brand::getDealerBranding()->healthCheckTitle;

        $format = $this->_getParam("format", "html");
        try
        {
            // Clear the cache for the report before proceeding
            $this->clearCacheForReport();
            if (false !== ($healthcheckViewModel = $this->getHealthcheckViewModel()))
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
                throw new Exception("Healthcheck View Model is false");
            }
            $this->view->healthcheckViewModel = $healthcheckViewModel;
        }
        catch (Exception $e)
        {
            throw new Exception("Healthcheck could not be generated.", 0, $e);
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
                $healthcheck         = $this->getHealthcheckViewModel();
                $graphs              = $this->cachePNGImages($healthcheck->getGraphs(), true);
                $healthcheck->setGraphs($graphs);
                $this->view->wordStyles = $this->getWordStyles();
                $this->_helper->layout->disableLayout();
                break;
            default :
                throw new Exception("Invalid Format Requested!");
                break;
        }

        $filename = $this->generateReportFilename($this->getHealthcheck()->getClient(), My_Brand::getDealerBranding()->healthCheckTitle) . ".$format";

        $this->initReportVariables($filename);

        // Render early
        try
        {
            $this->render('/' . $format . "/00_render");
        }
        catch (Exception $e)
        {
            throw new Exception("Controller caught the exception!", 0, $e);
        }
    }
} // end index controller



