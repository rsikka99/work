<?php
class Proposalgen_HealthcheckController extends Proposalgen_Library_Controller_Healthcheck
{
    /**
     * Users can upload/see uploaded data on this step
     */
    public function indexAction ()
    {
        // Mark the step we're on as active
        $this->setActiveReportStep(Proposalgen_Model_Healthcheck_Step::STEP_FLEETDATA_UPLOAD);

        $report               = $this->getReport();
        $rmsUpload = null;
        if(isset($report->id))
        {
            $rmsUpload = Proposalgen_Model_Mapper_Healthcheck::getInstance()->findRmsUploadRowByHardwareOptimizationId($report->id);
        }

        $uploadService = new Proposalgen_Service_Rms_Upload(Zend_Auth::getInstance()->getIdentity()->id,$this->getReport()->clientId,$rmsUpload);


        if ($this->getRequest()->isPost())
        {
            $values = $this->getRequest()->getPost();

            if (isset($values ["goBack"]))
            {
                $this->gotoPreviousStep();
            }
            else if (isset($values ["performUpload"]))
            {
                $success = $uploadService->processUpload($values);
                if ($success)
                {
                    $rmsUpload = $uploadService->rmsUpload;

                    // Save the health check object with the new id.
                    $healthcheck              = Proposalgen_Model_Mapper_Healthcheck::getInstance()->find($report->id);
                    $healthcheck->rmsUploadId = $rmsUpload->id;
                    Proposalgen_Model_Mapper_Healthcheck::getInstance()->save($healthcheck);

                    $this->_flashMessenger->addMessage(array("success" => "Upload was successful."));
                }
                else
                {
                    $this->_flashMessenger->addMessage(array("danger" => $uploadService->errorMessages));
                }
            }
            else if (isset($values ["saveAndContinue"]))
            {
                $count = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->countRowsForRmsUpload($uploadService->rmsUpload->id);
                if ($count < 2)
                {
                    $this->_flashMessenger->addMessage(array(
                                                            'danger' => "You must have at least 2 valid devices to continue."
                                                       ));
                }
                else
                {
                    $this->gotoNextStep();
                }
            }
        }
        $this->saveReport(true);
        if($rmsUpload instanceof Proposalgen_Model_Rms_Upload_Row)
        {
            $this->view->populateGrid = true;
        }
        $this->view->form = $uploadService->getForm();
        $navigationButtons          = ($rmsUpload instanceof Proposalgen_Model_Rms_Upload) ? Proposalgen_Form_Assessment_Navigation::BUTTONS_BACK_NEXT : Proposalgen_Form_Assessment_Navigation::BUTTONS_BACK;
        $this->view->navigationForm = new Proposalgen_Form_Assessment_Navigation($navigationButtons);

    }


    /**
     * Allows the user to set the report settings for a report
     */
    public function settingsAction ()
    {
        // Mark the step we're on as active
        $this->setActiveReportStep(Proposalgen_Model_Healthcheck_Step::STEP_REPORTSETTINGS);
//        $dealer                   = Admin_Model_Mapper_Dealer::getInstance()->find(Zend_Auth::getInstance()->getIdentity()->dealerId);
        $healthcheckSettingsService = new Proposalgen_Service_HealthcheckSettings($this->getReport()->id,Zend_Auth::getInstance()->getIdentity()->id,Zend_Auth::getInstance()->getIdentity()->dealerId);

        //$reportSettingsService = new Proposalgen_Service_ReportSettings($this->getReport()->id, $this->_userId, $this->_dealerId);
        if ($this->getRequest()->isPost())
        {
            $values = $this->getRequest()->getPost();

            if (isset($values ['cancel']))
            {
                $this->gotoPreviousStep();
            }
            else
            {
                if ($healthcheckSettingsService->update($values))
                {
                    $this->saveReport();
                    $this->_flashMessenger->addMessage(array(
                                                            'success' => 'Settings saved.'
                                                       ));


                    if (isset($values ['saveAndContinue']))
                    {
                        $this->gotoNextStep();
                    }
                }
                else
                {
                    $this->_flashMessenger->addMessage(array(
                                                            'danger' => 'Please correct the errors below.'
                                                       ));
                }
            }
        }

       $this->view->form = $healthcheckSettingsService->getForm();
    }

    /**
     * This handles the mapping of devices to our master devices
     */
    public function mappingAction ()
    {
        // Mark the step we're on as active
        $this->setActiveReportStep(Proposalgen_Model_Healthcheck_Step::STEP_MAPPING);

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();
            if (isset($postData['saveAndContinue']))
            {
                // Every time we save anything related to a report, we should save it (updates the modification date)
                $this->saveReport();

                // Call the base controller to send us to the next logical step in the proposal.
                $this->gotoNextStep();
            }
            else if (isset($postData['goBack']))
            {
                // Call the base controller to send us to the next logical step in the proposal.
                $this->gotoPreviousStep();
            }
        }

        $this->view->navigationForm = new Proposalgen_Form_Assessment_Navigation(Proposalgen_Form_Assessment_Navigation::BUTTONS_BACK_NEXT);
    }

    /**
     * Generates a list of devices that were not mapped automatically
     */
    public function deviceMappingListAction ()
    {
        $jqGrid                  = new Tangent_Service_JQGrid();
        $mapDeviceInstanceMapper = Proposalgen_Model_Mapper_Map_Device_Instance::getInstance();

        /*
         * Grab the incoming parameters
         */
        $jqGridParameters = array(
            'sidx' => $this->_getParam('sidx', 'deviceCount'),
            'sord' => $this->_getParam('sord', 'desc'),
            'page' => $this->_getParam('page', 1),
            'rows' => $this->_getParam('rows', 10)
        );

        // Set up validation arrays
        $blankModel  = new Proposalgen_Model_Map_Device_Instance();
        $sortColumns = array_keys($blankModel->toArray());

        $jqGrid->parseJQGridPagingRequest($jqGridParameters);
        $jqGrid->setValidSortColumns($sortColumns);


        if ($jqGrid->sortingIsValid())
        {
            $jqGrid->setRecordCount($mapDeviceInstanceMapper->fetchAllForRmsUpload($this->getReport()->getRmsUpload()->id, $jqGrid->getSortColumn(), $jqGrid->getSortDirection(), null, null, true));

            // Validate current page number since we don't want to be out of bounds
            if ($jqGrid->getCurrentPage() < 1)
            {
                $jqGrid->setCurrentPage(1);
            }
            else if ($jqGrid->getCurrentPage() > $jqGrid->calculateTotalPages())
            {
                $jqGrid->setCurrentPage($jqGrid->calculateTotalPages());
            }

            // Return a small subset of the results based on the jqGrid parameters
            $startRecord = $jqGrid->getRecordsPerPage() * ($jqGrid->getCurrentPage() - 1);
            $jqGrid->setRows($mapDeviceInstanceMapper->fetchAllForRmsUpload($this->getReport()->getRmsUpload()->id, $jqGrid->getSortColumn(), $jqGrid->getSortDirection(), $jqGrid->getRecordsPerPage(), $startRecord));

            // Send back jqGrid json data
            $this->sendJson($jqGrid->createPagerResponseArray());
        }
        else
        {
            $this->_response->setHttpResponseCode(500);
            $this->sendJson(array(
                                 'error' => 'Sorting parameters are invalid'
                            ));
        }
    }


    /**
     * The healthcheckAction displays the healthcheck report.
     * Data is retrieved
     * from the database and displayed using HTML, CSS, and javascript.
     */
    public function reportAction ()
    {
        // Mark the step we're on as active
        $this->setActiveReportStep(Proposalgen_Model_Healthcheck_Step::STEP_FINISHED);

        $this->initReportList();
        $this->initHtmlReport();

        $this->view->availableReports->Healthcheck->active = true;

        $this->view->formats = array(
            "/proposalgen/healthcheck/generate/format/docx" => $this->_wordFormat
        );
        $this->view->reportTitle = "Health Check";

        $format = $this->_getParam("format", "html");
        try
        {
            // Clear the cache for the report before proceeding
            $healthcheck = new Proposalgen_Model_Healthcheck_Healthcheck($this->getProposal());
            die();
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
                throw new Exception("The proposal cannot be found.");
            }
            $this->view->healthcheck = $healthcheck;
        }
        catch (Exception $e)
        {
            throw new Exception("Health Check could not be generated.", 0, $e);
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
                $this->view->phpword     = new PHPWord();
                $healthcheck             = new Proposalgen_Model_Healthcheck_Healthcheck($this->getProposal());
                $this->view->healthcheck = $healthcheck;
                $graphs                  = $this->cachePNGImages($healthcheck->getGraphs(), true);
                $this->view->graphs      = $graphs;
                $this->_helper->layout->disableLayout();
                break;
            case "pdf" :
                throw new Exception("PDF Format not available through this page yet!");
                break;
            default :
                throw new Exception("Invalid Format Requested!");
                break;
        }

        $filename = "healthcheck.$format";

        $this->initReportVariables($filename);
        // Render early
        try
        {
            $this->render($this->view->App()->theme . '/' . $format  . "/00_render");
        }
        catch (Exception $e)
        {
            throw new Exception("Controller caught the exception!", 0, $e);
        }
    }
}