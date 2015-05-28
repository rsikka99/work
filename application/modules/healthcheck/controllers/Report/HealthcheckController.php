<?php
use MPSToolbox\Legacy\Modules\HealthCheck\Models\HealthCheckStepsModel;

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
        $png = $this->_getParam('png');
        if ($png) {
            $this->_helper->viewRenderer->setNoRender(true);
            $this->_helper->layout->disableLayout();
            $this->getHealthcheckViewModel()->png($png);
            return;
        }

        $this->_pageTitle = ['Healthcheck'];
        $this->_navigation->setActiveStep(HealthCheckStepsModel::STEP_FINISHED);

        /**
         * If we have access to the Health Check, we will switch to it
         */
        if (My_Feature::canAccess(My_Feature::HEALTHCHECK_PRINTIQ))
        {
            $this->redirectToRoute('healthcheck.report-printiq');
        }

        $this->initReportList();
        $this->initHtmlReport();

        $this->view->availableReports['Healthcheck']['active'] = true;

        $this->view->formats = [
            "/healthcheck/report_healthcheck/generate/format/excel" => $this->_excelFormat,
            "/healthcheck/report_healthcheck/generate/format/docx"  => $this->_wordFormat
        ];

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
                        $this->view->excel = new PHPExcel();
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
            case 'excel' :
                $this->_helper->layout->disableLayout();
                $this->view->phpexcel                 = new PHPExcel();
                $healthcheck                          = $this->getHealthcheckViewModel();
                $this->healthcheckDeviceListViewModel = new Healthcheck_ViewModel_HealthcheckDeviceListViewModel($healthcheck);
                $reportTitle .= ' Device List';
                $filename = $this->generateReportFilename($this->getHealthcheck()->getClient(), $reportTitle) . ".xlsx";

                break;
            case 'docx' :
                $this->view->phpword = new \PhpOffice\PhpWord\PhpWord();
                $healthcheck         = $this->getHealthcheckViewModel();
                // Clear the cache for the report before proceeding
                $this->clearCacheForReport();
                // New graphs being passed to view
                $this->view->graphs = $this->cachePNGImages($healthcheck->getCharts());
                $this->view->wordStyles = $this->getWordStyles();
                $this->_helper->layout->disableLayout();
                $filename = $this->generateReportFilename($this->getHealthcheck()->getClient(), $reportTitle) . ".$format";
                break;
            default :
                throw new Exception('Invalid Format Requested!');
                break;
        }


        $this->initReportVariables($filename);

        // Render early
        try
        {
            $this->render('/' . $format . '/00_render');
        }
        catch (Exception $e)
        {
            throw new Exception('Controller caught the exception!', 0, $e);
        }
    }

    public function quadrantAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        $this->view->healthcheck = $this->getHealthcheckViewModel();
        $this->render('/png/quadrant');
    }

    public function ageAction() {
        $this->_pageTitle = ['Healthcheck'];
        $this->_navigation->setActiveStep(HealthCheckStepsModel::STEP_FINISHED);

        $this->initReportList();
        $this->initHtmlReport();

        $this->view->availableReports['Device Age']['active'] = true;

        $this->view->formats = [
        ];

        $this->view->reportTitle = My_Brand::getDealerBranding()->healthCheckTitle;
    }

} // end index controller



