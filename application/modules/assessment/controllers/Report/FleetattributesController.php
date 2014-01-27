<?php

/**
 * Class Assessment_Report_FleetattributesController
 */
class Assessment_Report_FleetattributesController extends Assessment_Library_Controller_Action
{
    /**
     * Makes sure the user has access to this report, otherwise it sends them back
     */
    public function init ()
    {
        if (!My_Feature::canAccess(My_Feature::ASSESSMENT_FLEET_ATTRIBUTES))
        {
            $this->_flashMessenger->addMessage(array(
                "error" => "You do not have permission to access this."
            ));

            $this->redirector('index', 'index', 'index');
        }

        parent::init();
    }

    public function indexAction ()
    {
        $this->view->headTitle('Assessment');
        $this->view->headTitle('Fleet Attributes');
        $this->_navigation->setActiveStep(Assessment_Model_Assessment_Steps::STEP_FINISHED);

        $this->initReportList();
        $this->initHtmlReport();

        $this->view->availableReports['FleetAttributes']['active'] = true;
        $this->view->formats                                       = array(
            "/assessment/report_fleetattributes/generate/format/excel" => $this->_excelFormat,
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
            throw new Exception("Could not generate the Fleet Attributes report.");
        }
    }

    /**
     * The Generate Action
     */
    public function generateAction ()
    {
        $this->view->headTitle('Generate Fleet Attributes');
        $format = $this->_getParam("format", "excel");

        switch ($format)
        {
            case "excel" :
                $this->_helper->layout->disableLayout();
                $this->view->phpexcel = new PHPExcel();
                $this->initExcelFleetAttributes();
                break;
            default :
                throw new Exception("Invalid Format Requested! ($format)");
                break;
        }

        $filename = $this->generateReportFilename($this->getAssessment()->getClient(), 'Fleet_Attributes_Report') . ".$format";

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
    public function initExcelFleetAttributes ()
    {
        try
        {
            $assessmentViewModel = $this->getAssessmentViewModel();
        }
        catch (Exception $e)
        {
            throw new Exception("Could not generate Fleet Attributes excel report.");
        }

        $fleetAttributesData = array();
        $deviceCounter       = 0;
        $dealerId            = Zend_Auth::getInstance()->getIdentity()->dealerId;

        /**
         * @var $deviceInstance Proposalgen_Model_DeviceInstance
         */
        foreach ($assessmentViewModel->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $deviceInstance)
        {
            $fleetAttributesData[$deviceCounter]['Device Name']                       = str_ireplace("hewlett-packard", "HP", $deviceInstance->getDeviceName());
            $fleetAttributesData[$deviceCounter]['IP Address']                        = $deviceInstance->ipAddress;
            $fleetAttributesData[$deviceCounter]['Serial Number']                     = $deviceInstance->serialNumber;
            $fleetAttributesData[$deviceCounter]['Device Age (Years)']                = $deviceInstance->getAge();
            $fleetAttributesData[$deviceCounter]['Monthly Page Volume']               = $deviceInstance->getPageCounts()->getCombinedPageCount()->getMonthly();
            $fleetAttributesData[$deviceCounter]['Suggested Maximum Page Volume']     = $deviceInstance->getMasterDevice()->getMaximumMonthlyPageVolume($assessmentViewModel->getCostPerPageSettingForCustomer());
            $fleetAttributesData[$deviceCounter]['Percent of Total Page Volume']      = $deviceInstance->getPageCounts()->getCombinedPageCount()->getMonthly() / $assessmentViewModel->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly();
            $fleetAttributesData[$deviceCounter]['Reports Toner Levels']              = $deviceInstance->getMasterDevice()->isCapableOfReportingTonerLevels ? 'Yes' : 'No';
            $fleetAttributesData[$deviceCounter]['Compatible with ' . My_Brand::$jit] = $deviceInstance->getMasterDevice()->isJitCompatible($dealerId) ? 'Yes' : 'No';
            $fleetAttributesData[$deviceCounter]['Is Managed']                        = $deviceInstance->isLeased ? 'No' : ($deviceInstance->isManaged ? 'Yes' : 'No');
            $fleetAttributesData[$deviceCounter]['Can Copy/Scan']                     = $deviceInstance->getMasterDevice()->isCopier ? 'Yes' : 'No';
            $fleetAttributesData[$deviceCounter]['Can Duplex']                        = $deviceInstance->getMasterDevice()->isDuplex ? 'Yes' : 'No';
            $fleetAttributesData[$deviceCounter]['Can Fax']                           = $deviceInstance->getMasterDevice()->isFax ? 'Yes' : 'No';
            $fleetAttributesData[$deviceCounter]['Can Print A3']                      = $deviceInstance->getMasterDevice()->isA3 ? 'Yes' : 'No';
            $fleetAttributesData[$deviceCounter]['Print Speed Mono']                  = $deviceInstance->getMasterDevice()->ppmBlack;
            $fleetAttributesData[$deviceCounter]['Print Speed Color']                 = $deviceInstance->getMasterDevice()->ppmColor;
            $fleetAttributesData[$deviceCounter]['Operating Wattage']                 = $deviceInstance->getMasterDevice()->wattsPowerNormal;
            $fleetAttributesData[$deviceCounter]['Idle/Sleep Wattage']                = $deviceInstance->getMasterDevice()->wattsPowerIdle;
            $fleetAttributesData[$deviceCounter]['Launch Date']                       = $deviceInstance->getMasterDevice()->launchDate;
            $deviceCounter++;
        }

        $this->view->fleetAttributesData = $fleetAttributesData;
    }
}