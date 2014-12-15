<?php
use MPSToolbox\Legacy\Modules\HealthCheck\Models\HealthCheckStepsModel;

/**
 * Class Healthcheck_Report_Printiq_HealthcheckController
 */
class Healthcheck_Report_Printiq_HealthcheckController extends Healthcheck_Library_Controller_Action
{

    /**
     * @throws Exception
     */
    public function indexAction ()
    {
        $this->_navigation->setActiveStep(HealthCheckStepsModel::STEP_FINISHED);

        /**
         * If we don't have access to PrintIQ Health Check, switch to normal Health Check
         */
        if (!My_Feature::canAccess(My_Feature::HEALTHCHECK_PRINTIQ))
        {
            $this->redirectToRoute('healthcheck.report');
        }

        $this->initReportList();
        $this->initHtmlReport();

        $this->view->availableReports['Printiq_Healthcheck']['active'] = true;

        $this->view->formats = array(
            "/healthcheck/report_printiq_healthcheck/generate/format/excel" => $this->_excelFormat,
            "/healthcheck/report_printiq_healthcheck/generate/format/docx"  => $this->_wordFormat,
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
                    case "excel" :
                        $this->view->phpExcel = new PHPExcel();
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

        $reportTitle = My_Brand::getDealerBranding()->healthCheckTitle;

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
                $filename = $this->generateReportFilename($this->getHealthcheck()->getClient(), $reportTitle) . ".$format";
                break;
            case 'excel' :
                $this->_helper->layout->disableLayout();
                $this->view->phpexcel                 = new PHPExcel();
                $healthcheck                          = $this->getHealthcheckViewModel();
                $this->healthcheckDeviceListViewModel = new Healthcheck_ViewModel_HealthcheckDeviceListViewModel($healthcheck);
                $reportTitle .= ' Device List';
                $filename = $this->generateReportFilename($this->getHealthcheck()->getClient(), $reportTitle) . ".xlsx";

                break;
            default :
                throw new Exception("Invalid Format Requested!");
                break;
        }

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



