<?php
use MPSToolbox\Legacy\Modules\Assessment\Models\AssessmentStepsModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DealerMasterDeviceAttributeModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel;

/**
 * Class Assessment_Report_LeasebuybackController
 */
class Assessment_Report_LeasebuybackController extends Assessment_Library_Controller_Action
{
    /**
     * Makes sure the user has access to this report, otherwise it sends them back
     */
    public function init ()
    {
        if (!My_Feature::canAccess(My_Feature::ASSESSMENT_LEASE_BUYBACK))
        {
            $this->_flashMessenger->addMessage(array(
                "error" => "You do not have permission to access this."
            ));

            $this->redirectToRoute('assessment');
        }

        parent::init();
    }

    public function indexAction ()
    {
        $this->_pageTitle = array('Assessment', 'Lease Buyback');
        $this->_navigation->setActiveStep(AssessmentStepsModel::STEP_FINISHED);

        $this->initReportList();
        $this->initHtmlReport();

        $this->view->availableReports['LeaseBuyback']['active'] = true;
        $this->view->formats                                    = array(
            "/assessment/report_leasebuyback/generate/format/excel" => $this->_excelFormat,
        );

        try
        {
            // Clear the cache for the report before proceeding
            $this->clearCacheForReport();
            $assessmentViewModel             = $this->getAssessmentViewModel();
            $this->view->assessmentViewModel = $assessmentViewModel;
        }
        catch (Exception $e)
        {
            throw new Exception("Could not generate the toner list.");
        }
    }

    /**
     * The Generate Action
     */
    public function generateAction ()
    {
        $this->_pageTitle = array('Generate Lease Buyback');
        $format           = $this->_getParam("format", "excel");

        switch ($format)
        {
            case "excel" :
                $this->_helper->layout->disableLayout();
                $this->view->phpexcel = new PHPExcel();
                $this->initExcelLeaseBuyback();
                break;
            default :
                throw new Exception("Invalid Format Requested! ($format)");
                break;
        }

        $filename = $this->generateReportFilename($this->getAssessment()->getClient(), 'Toner Report') . '.xlsx';

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

    /**
     * Sets up the excel data array
     *
     * @throws Exception
     */
    public function initExcelLeaseBuyback ()
    {
        try
        {
            $assessmentViewModel = $this->getAssessmentViewModel();
        }
        catch (Exception $e)
        {
            throw new Exception("Could not generate Lease Buyback excel report.");
        }

        $leaseDeviceData = array();
        $deviceCounter   = 0;

        /**
         * @var $deviceInstance DeviceInstanceModel
         */
        foreach ($assessmentViewModel->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $deviceInstance)
        {
            $leaseBuybackPrice            = "-";
            $dealerMasterDeviceAttributes = $deviceInstance->getMasterDevice()->getDealerAttributes();
            if ($dealerMasterDeviceAttributes instanceof DealerMasterDeviceAttributeModel && $dealerMasterDeviceAttributes->leaseBuybackPrice != null && $dealerMasterDeviceAttributes->leaseBuybackPrice >= 0)
            {
                $leaseBuybackPrice = $this->view->currency($dealerMasterDeviceAttributes->leaseBuybackPrice);
            }

            $leaseDeviceData[$deviceCounter]['deviceName']        = str_ireplace("hewlett-packard", "HP", $deviceInstance->getDeviceName());
            $leaseDeviceData[$deviceCounter]['ipAddress']         = $deviceInstance->ipAddress;
            $leaseDeviceData[$deviceCounter]['serialNumber']      = $deviceInstance->serialNumber;
            $leaseDeviceData[$deviceCounter]['leaseBuybackPrice'] = $leaseBuybackPrice;
            $deviceCounter++;
        }

        $this->view->leaseDeviceData   = $leaseDeviceData;
        $this->view->totalBuybackPrice = $assessmentViewModel->getTotalLeaseBuybackPrice();
    }
}