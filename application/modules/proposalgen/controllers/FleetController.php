<?php

/**
 * Class Proposalgen_FleetController
 */
class Proposalgen_FleetController extends Tangent_Controller_Action
{
    /**
     * @var int
     */
    protected $_selectedClientId;

    /**
     * The namespace for our MPS application
     *
     * @var Zend_Session_Namespace
     */
    protected $_mpsSession;

    /**
     * @var int
     */
    protected $_userId;

    /**
     * Our identity that is stored in the session
     *
     * @var stdClass
     */
    protected $_identity;

    /**
     * @var Proposalgen_Model_Fleet_Steps
     */
    protected $_navigation;

    public function init ()
    {
        /* Initialize action controller here */
        $this->_mpsSession = new Zend_Session_Namespace('mps-tools');
        $this->_identity   = Zend_Auth::getInstance()->getIdentity();
        $this->_userId     = $this->_identity->id;
        $this->_navigation = Proposalgen_Model_Fleet_Steps::getInstance();
        if (isset($this->_mpsSession->selectedClientId))
        {
            $client = Quotegen_Model_Mapper_Client::getInstance()->find($this->_mpsSession->selectedClientId);

            // Make sure the selected client is ours!
            if ($client && $client->dealerId == $this->_identity->dealerId)
            {
                $this->_selectedClientId      = $this->_mpsSession->selectedClientId;
                $this->view->selectedClientId = $this->_selectedClientId;
            }
        }
    }

    /**
     * Users can upload/see uploaded data on this step
     */
    public function indexAction ()
    {
        $this->view->headTitle('RMS Upload');
        $this->view->headTitle('Upload');
        $time        = -microtime(true);
        $rmsUploadId = $this->_getParam('rmsUploadId', false);
        $this->_navigation->setActiveStep(Proposalgen_Model_Fleet_Steps::STEP_FLEET_UPLOAD);
        $rmsUpload = null;
        if ($rmsUploadId > 0)
        {
            $rmsUpload = Proposalgen_Model_Mapper_Rms_Upload::getInstance()->find($rmsUploadId);
        }

        if ($rmsUpload instanceof Proposalgen_Model_Rms_Upload)
        {
            $this->_navigation->updateAccessibleSteps(Proposalgen_Model_Fleet_Steps::STEP_FLEET_SUMMARY);
        }
        else
        {
            $this->_navigation->updateAccessibleSteps(Proposalgen_Model_Fleet_Steps::STEP_FLEET_UPLOAD);
        }

        $uploadService = new Proposalgen_Service_Rms_Upload($this->_userId, $this->_selectedClientId, $rmsUpload);

        $form = $uploadService->getForm();

        if ($this->getRequest()->isPost())
        {
            $values = $this->getRequest()->getPost();
            if (isset($values ["goBack"]))
            {
                // Bring the user back to the home page
                $this->redirector("index", "index", "index");
            }
            else if (isset($values ["performUpload"]) && !($rmsUpload instanceof Proposalgen_Model_Rms_Upload))
            {
                /*
                 * Handle Upload
                 */
                if ($form->isValid($values))
                {
                    $success = $uploadService->processUpload($values, $this->_identity->dealerId);

                    /**
                     * Log how much time it took
                     */
                    $time += microtime(true);
                    $filename = $uploadService->rmsUpload->fileName;

                    Tangent_Log::debug("It took {$time} seconds to process the CSV upload ({$filename}). ");

                    if ($success)
                    {
                        $timeElapsed    = number_format($time, 4);
                        $validDevices   = number_format($uploadService->rmsUpload->validRowCount);
                        $invalidDevices = number_format($uploadService->rmsUpload->invalidRowCount);
                        $this->_flashMessenger->addMessage(array("success" => "Processed {$validDevices} valid devices and {$invalidDevices} invalid devices in {$timeElapsed} seconds."));
                        $this->redirector(null, null, null, array("rmsUploadId" => $uploadService->rmsUpload->id));
                    }
                    else
                    {
                        $this->_flashMessenger->addMessage(array("danger" => $uploadService->errorMessages));
                    }

                }
            }
            else if (isset($values ["saveAndContinue"]))
            {
                $this->redirector('mapping', null, null, array("rmsUploadId" => $uploadService->rmsUpload->id));
            }
        }

        $this->view->form = $form;

        $this->view->rmsUpload = $uploadService->rmsUpload;

        $navigationButtons          = ($uploadService->rmsUpload instanceof Proposalgen_Model_Rms_Upload) ? Proposalgen_Form_Assessment_Navigation::BUTTONS_BACK_NEXT : Proposalgen_Form_Assessment_Navigation::BUTTONS_BACK;
        $this->view->navigationForm = new Proposalgen_Form_Assessment_Navigation($navigationButtons);

    }

    public function rmsUploadListAction ()
    {
        $this->view->headTitle('RMS Upload');
        $clientId     = $this->_mpsSession->selectedClientId;
        $jqGrid       = new Tangent_Service_JQGrid();
        $uploadMapper = Proposalgen_Model_Mapper_Rms_Upload::getInstance();
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
        $blankModel  = new Proposalgen_Model_Rms_Upload();
        $sortColumns = array_keys($blankModel->toArray());

        $jqGrid->parseJQGridPagingRequest($jqGridParameters);
        $jqGrid->setValidSortColumns($sortColumns);


        if ($jqGrid->sortingIsValid())
        {
            $jqGrid->setRecordCount($uploadMapper->fetchAllForClient($clientId, null, null, null, true));


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

            if ($startRecord < 0)
            {
                $startRecord = 0;
            }
            $rmsUploads = $uploadMapper->fetchAllForClient($clientId, $jqGrid->getSortColumn() . " " . $jqGrid->getSortDirection(), $jqGrid->getRecordsPerPage(), $startRecord);

            foreach ($rmsUploads as $rmsUpload)
            {

                $rmsUpload->providerName = $rmsUpload->getRmsProvider()->name;
            }

            $jqGrid->setRows($rmsUploads);

            // Send back jqGrid JSON data
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
     * This handles the mapping of devices to our master devices
     */
    public function mappingAction ()
    {
        $this->view->headTitle('RMS Upload');
        $this->view->headTitle('Map Devices');
        $this->_navigation->setActiveStep(Proposalgen_Model_Fleet_Steps::STEP_FLEET_MAPPING);
        $this->_navigation->updateAccessibleSteps(Proposalgen_Model_Fleet_Steps::STEP_FLEET_SUMMARY);

        $rmsUploadId = $this->_getParam('rmsUploadId', false);

        $rmsUpload = null;
        if ($rmsUploadId > 0)
        {
            $rmsUpload = Proposalgen_Model_Mapper_Rms_Upload::getInstance()->find($rmsUploadId);
        }

        if (!$rmsUpload instanceof Proposalgen_Model_Rms_Upload)
        {
            $this->redirector("index");
        }

        $this->view->rmsUpload = $rmsUpload;

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();
            if (isset($postData['saveAndContinue']))
            {
                $this->redirector('summary', null, null, array("rmsUploadId" => $rmsUploadId));
            }
            else if (isset($postData['goBack']))
            {
                // Call the base controller to send us to the next logical step in the proposal.
                $this->redirector('index', null, null, array("rmsUploadId" => $rmsUploadId));
            }
        }

        $this->view->navigationForm = new Proposalgen_Form_Assessment_Navigation(Proposalgen_Form_Assessment_Navigation::BUTTONS_BACK_NEXT);
    }

    /**
     * Generates a list of devices that were not mapped automatically
     */
    public function deviceMappingListAction ()
    {
        $rmsUploadId = $this->_getParam('rmsUploadId', false);

        if ($rmsUploadId > 0)
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
                $jqGrid->setRecordCount($mapDeviceInstanceMapper->fetchAllForRmsUpload($rmsUploadId, $jqGrid->getSortColumn(), $jqGrid->getSortDirection(), null, null, true));

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
                $jqGrid->setRows($mapDeviceInstanceMapper->fetchAllForRmsUpload($rmsUploadId, $jqGrid->getSortColumn(), $jqGrid->getSortDirection(), $jqGrid->getRecordsPerPage(), $startRecord));

                // Send back jqGrid JSON data
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
        else
        {
            $this->_response->setHttpResponseCode(500);
            $this->sendJson(array('error' => 'Invalid RMS Upload Id'));
        }
    }

    /**
     * Generates a list of devices that were not mapped automatically
     */
    public function excludedListAction ()
    {
        $rmsUploadId = $this->_getParam('rmsUploadId', false);

        if ($rmsUploadId > 0)
        {
            $jqGrid            = new Tangent_Service_JQGrid();
            $excludedRowMapper = Proposalgen_Model_Mapper_Rms_Excluded_Row::getInstance();

            /*
             * Grab the incoming parameters
             */
            $jqGridParameters = array(
                'sidx' => $this->_getParam('sidx', 'csvLineNumber'),
                'sord' => $this->_getParam('sord', 'desc'),
                'page' => $this->_getParam('page', 1),
                'rows' => $this->_getParam('rows', 10)
            );

            // Set up validation arrays
            $blankModel  = new Proposalgen_Model_Rms_Excluded_Row();
            $sortColumns = array_keys($blankModel->toArray());

            $jqGrid->parseJQGridPagingRequest($jqGridParameters);
            $jqGrid->setValidSortColumns($sortColumns);


            if ($jqGrid->sortingIsValid())
            {
                $jqGrid->setRecordCount($excludedRowMapper->fetchAllForRmsUpload($rmsUploadId, $jqGrid->getSortColumn(), $jqGrid->getSortDirection(), null, null, true));

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

                if ($startRecord < 0)
                {
                    $startRecord = 0;
                }


                $jqGrid->setRows($excludedRowMapper->fetchAllForRmsUpload($rmsUploadId, $jqGrid->getSortColumn(), $jqGrid->getSortDirection(), $jqGrid->getRecordsPerPage(), $startRecord));

                // Send back jqGrid JSON data
                $this->sendJson($jqGrid->createPagerResponseArray());
            }
            else
            {
                $this->_response->setHttpResponseCode(500);
                $this->sendJson(array('error' => 'Sorting parameters are invalid'));
            }
        }
        else
        {
            $this->_response->setHttpResponseCode(500);
            $this->sendJson(array('error' => 'Invalid RMS Upload Id'));
        }
    }

    /**
     * Handles mapping a device
     *
     * @throws Exception
     */
    public function setMappedToAction ()
    {
        $db                               = Zend_Db_Table_Abstract::getDefaultAdapter();
        $deviceInstanceIds                = $this->_getParam('deviceInstanceIds', false);
        $masterDeviceId                   = $this->_getParam('masterDeviceId', false);
        $errorMessage                     = null;
        $deviceInstanceMasterDeviceMapper = Proposalgen_Model_Mapper_Device_Instance_Master_Device::getInstance();
        $deviceInstanceMapper             = Proposalgen_Model_Mapper_DeviceInstance::getInstance();
        $successMessage                   = "Device mapped successfully";

        if ($deviceInstanceIds !== false && $masterDeviceId !== false)
        {
            $masterDevice = Proposalgen_Model_Mapper_MasterDevice::getInstance()->find($masterDeviceId);
            if ($masterDevice instanceof Proposalgen_Model_MasterDevice || $masterDeviceId == 0)
            {
                $deviceInstanceIds = explode(',', $deviceInstanceIds);

                $db->beginTransaction();
                try
                {
                    if ($masterDeviceId == 0)
                    {
                        $successMessage = "Device unmapped successfully";
                        // Delete mapping
                        foreach ($deviceInstanceIds as $deviceInstanceId)
                        {
                            $deviceInstanceMasterDeviceMapper->delete($deviceInstanceId);
                        }
                    }
                    else
                    {
                        foreach ($deviceInstanceIds as $deviceInstanceId)
                        {
                            $deviceInstanceMasterDevice = $deviceInstanceMasterDeviceMapper->find($deviceInstanceId);
                            if ($deviceInstanceMasterDevice instanceof Proposalgen_Model_Device_Instance_Master_Device)
                            {
                                $deviceInstanceMasterDevice->masterDeviceId = $masterDeviceId;
                                $deviceInstanceMasterDeviceMapper->save($deviceInstanceMasterDevice);
                            }
                            else
                            {
                                $deviceInstanceMasterDevice                   = new Proposalgen_Model_Device_Instance_Master_Device();
                                $deviceInstanceMasterDevice->deviceInstanceId = $deviceInstanceId;
                                $deviceInstanceMasterDevice->masterDeviceId   = $masterDeviceId;
                                $deviceInstanceMasterDeviceMapper->insert($deviceInstanceMasterDevice);
                            }

                            $deviceInstance = $deviceInstanceMapper->find($deviceInstanceId);
                            if ($deviceInstance instanceof Proposalgen_Model_DeviceInstance)
                            {
                                $deviceInstance->compatibleWithJitProgram = $masterDevice->isJitCompatible($this->_identity->dealerId);
                                $deviceInstanceMapper->save($deviceInstance);
                            }
                        }
                    }

                    $db->commit();
                }
                catch (Exception $e)
                {
                    $db->rollBack();
                    Tangent_Log::logException($e);
                    $errorMessage = "An error occurred while mapping";
                }
            }
            else
            {
                // Invalid Master Device
                $errorMessage = "Invalid master device selected";
            }
        }

        if ($errorMessage !== null)
        {
            $this->getResponse()->setHttpResponseCode(500);
            $this->sendJson(array("error" => true, "message" => $errorMessage));
        }
        else
        {
            $this->sendJson(array("success" => true, "message" => $successMessage));
        }
    }

    /**
     * This is the device summary page
     */
    public function summaryAction ()
    {
        $this->view->headTitle('RMS Upload');
        $this->view->headTitle('Summary');
        $rmsUploadId = $this->_getParam('rmsUploadId', false);
        $this->_navigation->setActiveStep(Proposalgen_Model_Fleet_Steps::STEP_FLEET_SUMMARY);
        $this->_navigation->updateAccessibleSteps(Proposalgen_Model_Fleet_Steps::STEP_FLEET_SUMMARY);
        $rmsUpload = null;
        if ($rmsUploadId > 0)
        {
            $rmsUpload = Proposalgen_Model_Mapper_Rms_Upload::getInstance()->find($rmsUploadId);
        }

        if (!$rmsUpload instanceof Proposalgen_Model_Rms_Upload)
        {
            $this->redirector("index");
        }

        $this->view->rmsUpload = $rmsUpload;

        if (!$rmsUpload instanceof Proposalgen_Model_Rms_Upload)
        {
            $this->redirector("index");
        }

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();
            if (isset($postData['saveAndContinue']))
            {
                // FIXME: Should this have a magic way of going to a report?
                $this->redirector('index', 'index', 'index');
            }
            else if (isset($postData['goBack']))
            {
                $this->redirector('mapping', null, null, array("rmsUploadId" => $rmsUploadId));
            }
        }

        $this->view->navigationForm = new Proposalgen_Form_Assessment_Navigation(Proposalgen_Form_Assessment_Navigation::BUTTONS_ALL);
    }

    /**
     * The list of devices that are mapped in the report. It's used on the summary page
     */
    public function deviceSummaryListAction ()
    {
        $rmsUploadId = $this->_getParam('rmsUploadId', false);

        if ($rmsUploadId > 0)
        {
            $jqGrid               = new Tangent_Service_JQGrid();
            $deviceInstanceMapper = Proposalgen_Model_Mapper_DeviceInstance::getInstance();

            /*
             * Grab the incoming parameters
             */
            $jqGridParameters = array(
                'sidx' => $this->_getParam('sidx', 'id'),
                'sord' => $this->_getParam('sord', 'ASC'),
                'page' => $this->_getParam('page', 1),
                'rows' => $this->_getParam('rows', 10)
            );

            // Set up validation arrays
            $validSortColumns = array('id');
            $sortColumns      = array_keys($validSortColumns);

            $jqGrid->parseJQGridPagingRequest($jqGridParameters);
            $jqGrid->setValidSortColumns($sortColumns);


            if ($jqGrid->sortingIsValid())
            {
                $jqGrid->setRecordCount($deviceInstanceMapper->getMappedDeviceInstances($rmsUploadId, $jqGrid->getSortColumn(), $jqGrid->getSortDirection(), null, null, true));

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
                $startRecord     = $jqGrid->getRecordsPerPage() * ($jqGrid->getCurrentPage() - 1);
                $deviceInstances = $deviceInstanceMapper->getMappedDeviceInstances($rmsUploadId, $jqGrid->getSortColumn(), $jqGrid->getSortDirection(), $jqGrid->getRecordsPerPage(), $startRecord);

                $rows = array();
                foreach ($deviceInstances as $deviceInstance)
                {
                    $row = array(
                        "id"                       => $deviceInstance->id,
                        "isExcluded"               => $deviceInstance->isExcluded,
                        "isManaged"                => $deviceInstance->isManaged,
                        "compatibleWithJitProgram" => $deviceInstance->compatibleWithJitProgram,
                        "ampv"                     => number_format($deviceInstance->getPageCounts()->getCombinedPageCount()->getMonthly()),
                        "reportsTonerLevels"       => $deviceInstance->reportsTonerLevels ? "Yes" : "No",
                        "isLeased"                 => $deviceInstance->isLeased,
                        "validToners"              => $deviceInstance->hasValidToners()
                    );

                    $row["deviceName"] = $deviceInstance->getRmsUploadRow()->manufacturer . " " . $deviceInstance->getRmsUploadRow()->modelName . "<br>" . $deviceInstance->ipAddress;

                    if ($deviceInstance->getMasterDevice() instanceof Proposalgen_Model_MasterDevice)
                    {
                        $row["mappedToDeviceName"]              = $deviceInstance->getMasterDevice()->getManufacturer()->fullname . " " . $deviceInstance->getMasterDevice()->modelName;
                        $row["isCapableOfReportingTonerLevels"] = $deviceInstance->getMasterDevice()->isCapableOfReportingTonerLevels ? "Yes" : "No";
                    }
                    else
                    {
                        $row["mappedToDeviceName"]              = $deviceInstance->getRmsUploadRow()->getManufacturer()->fullname . " " . $deviceInstance->getRmsUploadRow()->modelName;
                        $row["isCapableOfReportingTonerLevels"] = "No";
                    }

                    $rows[] = $row;

                }

                $jqGrid->setRows($rows);

                // Send back jqGrid JSON data
                $this->sendJson($jqGrid->createPagerResponseArray());
            }
            else
            {
                $this->_response->setHttpResponseCode(500);
                $this->sendJson(array('error' => 'Sorting parameters are invalid'));
            }
        }
        else
        {
            $this->sendJson(array('error' => 'Invalid RMS Upload Id'));
        }
    }

    /**
     * This is where a user can modify the properties of an RMS upload row in a way that will make it valid
     */
    public function editUnknownDeviceAction ()
    {
        $rmsUploadId               = $this->_getParam('rmsUploadId', false);
        $deviceInstanceIdsAsString = $this->_getParam("deviceInstanceIds", false);
        $db                        = Zend_Db_Table_Abstract::getDefaultAdapter();


        if ($deviceInstanceIdsAsString !== false && $rmsUploadId > 0)
        {
            $deviceInstanceIds             = explode(",", $deviceInstanceIdsAsString);
            $this->view->deviceInstanceIds = $deviceInstanceIds;

            $form = new Proposalgen_Form_Fleet_AddDevice();
            $form->deviceInstanceIds->setValue($deviceInstanceIdsAsString);

            $rmsUploadRowMapper   = Proposalgen_Model_Mapper_Rms_Upload_Row::getInstance();
            $deviceInstanceMapper = Proposalgen_Model_Mapper_DeviceInstance::getInstance();
            $deviceInstance       = $deviceInstanceMapper->find($deviceInstanceIds[0]);
            if (!$deviceInstance instanceof Proposalgen_Model_DeviceInstance)
            {
                $this->_flashMessenger->addMessage(array('danger' => 'There was an error selecting the device you wanted to add.'));

                // Send back to the mapping page
                $this->redirector('mapping', null, null, array('rmsUploadId' => $rmsUploadId));
            }

            $rmsUploadRow             = $deviceInstance->getRmsUploadRow();
            $this->view->rmsUploadRow = $rmsUploadRow;
            $form->populate(array('reportsTonerLevels' => $deviceInstance->reportsTonerLevels));
            $form->populate($rmsUploadRow->toArray());


            /*
             * Received a POST
             */
            if ($this->getRequest()->isPost())
            {
                $postData = $this->getRequest()->getPost();
                if (isset($postData['submit']))
                {
                    /*
                     * POST was a submit from our form
                     */
                    $form->setValidationOnToners($postData['tonerConfigId']);
                    if ($form->isValid($postData))
                    {

                        /*
                         * Form is VALID
                         */
                        $formValues = $form->getValues();
                        $db->beginTransaction();

                        try
                        {
                            /**
                             * Here we need to set any blank fields to NULL
                             */
                            foreach ($formValues as &$formValue)
                            {
                                if (is_string($formValue) && strlen($formValue) < 1)
                                {
                                    $formValue = new Zend_Db_Expr("NULL");
                                }
                            }

                            /**
                             * Save each of our devices
                             */
                            foreach ($deviceInstanceIds as $deviceInstanceId)
                            {
                                $deviceInstance = $deviceInstanceMapper->find($deviceInstanceId);

                                // Update the RMS upload row
                                $rmsUploadRow = $deviceInstance->getRmsUploadRow();
                                $rmsUploadRow->populate($formValues);
                                $rmsUploadRow->hasCompleteInformation = true;
                                $rmsUploadRowMapper->save($rmsUploadRow);


                                $deviceInstance->useUserData        = true;
                                $deviceInstance->reportsTonerLevels = $formValues['reportsTonerLevels'];
                                $deviceInstanceMapper->save($deviceInstance);
                            }
                            $db->commit();

                            $this->_flashMessenger->addMessage(array("success" => "Device successfully mapped!"));
                            $this->redirector("mapping", null, null, array('rmsUploadId' => $rmsUploadId));
                        }
                        catch (Exception $e)
                        {
                            /**
                             * Error Saving
                             */
                            $db->rollBack();
                            Tangent_Log::logException($e);
                            $this->_flashMessenger->addMessage(array("danger" => "There was a system error while saving your device. Please try again. Reference #" . Tangent_Log::getUniqueId()));
                        }
                    }
                    else
                    {
                        /**
                         * Form is INVALID
                         */
                        $this->_flashMessenger->addMessage(array("danger" => "Please check the errors below and resubmit your request."));
                    }
                }
                else
                {
                    if (isset($postData['cancel']))
                    {
                        $this->redirector("mapping", null, null, array('rmsUploadId' => $rmsUploadId));
                    }
                }
            }
            $this->view->form = $form;
        }
        else
        {
            $this->_flashMessenger->addMessage(array("warning" => "Invalid Device Specified."));
            $this->redirector("mapping", null, null, array('rmsUploadId' => $rmsUploadId));
        }
    }

    /*
     * Handles removing an unknown device.
     * This is a JSON function
     */
    public function removeUnknownDeviceAction ()
    {
        $rmsUploadId = $this->_getParam('rmsUploadId', false);
        if ($rmsUploadId > 0)
        {
            $db                        = Zend_Db_Table_Abstract::getDefaultAdapter();
            $deviceInstanceIdsAsString = $this->_getParam("deviceInstanceIds", false);

            $deviceInstanceIds    = explode(",", $deviceInstanceIdsAsString);
            $deviceInstanceMapper = Proposalgen_Model_Mapper_DeviceInstance::getInstance();

            $jsonResponse = array("success" => true, "message" => "Unknown Device successfully removed.");

            $db->beginTransaction();
            try
            {
                /*
                 * Loop through all the device instance ids and set the useUserDate to false
                 */
                foreach ($deviceInstanceIds as $deviceInstanceId)
                {
                    $deviceInstance = $deviceInstanceMapper->find($deviceInstanceId);
                    if ($deviceInstance && $rmsUploadId == $deviceInstance->rmsUploadId)
                    {

                        $deviceInstance->useUserData = false;
                        $deviceInstanceMapper->save($deviceInstance);
                    }

                }
                $db->commit();
            }
            catch (Exception $e)
            {
                $db->rollBack();
                Tangent_Log::logException($e);
                $this->getResponse()->setHttpResponseCode(500);
                $jsonResponse = array("error" => true, "message" => "There was an error removing the unknown device. Reference #" . Tangent_Log::getUniqueId());
            }
        }
        else
        {
            $jsonResponse = array("error" => true, "message" => "No RMS Upload ID provided. Cannot remove the device.");
        }
        $this->sendJson($jsonResponse);
    }

    /**
     * Handles excluding/including devices
     */
    public function toggleExcludedFlagAction ()
    {
        $rmsUploadId = $this->_getParam('rmsUploadId', false);
        if ($rmsUploadId > 0)
        {
            $deviceInstanceId = $this->_getParam("deviceInstanceId", false);
            $isExcluded       = $this->_getParam("isExcluded", false) == 'true';
            $errorMessage     = false;

            if ($deviceInstanceId !== false)
            {
                $deviceInstanceMapper = Proposalgen_Model_Mapper_DeviceInstance::getInstance();
                $deviceInstance       = $deviceInstanceMapper->find($deviceInstanceId);
                if ($deviceInstance instanceof Proposalgen_Model_DeviceInstance)
                {
                    $rmsUpload = Proposalgen_Model_Mapper_Rms_Upload::getInstance()->find($deviceInstance->rmsUploadId);
                    if ($rmsUpload)
                    {
                        $includedDeviceInstanceCount = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->getMappedDeviceInstances($rmsUpload->id, null, null, null, null, true, true);
                        if ($includedDeviceInstanceCount > 2 || $isExcluded === false)
                        {
                            if ($rmsUpload->id == $rmsUploadId)
                            {
                                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                                $db->beginTransaction();
                                try
                                {
                                    $deviceInstance->isExcluded = $isExcluded;
                                    $deviceInstanceMapper->save($deviceInstance);
                                    $db->commit();
                                }
                                catch (Exception $e)
                                {
                                    $db->rollBack();
                                    Tangent_Log::logException($e);
                                    $errorMessage = "The system encountered an error while trying to exclude the device. Reference #" . Tangent_Log::getUniqueId();
                                }
                            }
                            else
                            {
                                $errorMessage = "You can only exclude device instances that belong to the same assessment." . $rmsUpload->id . " - " . $rmsUploadId;
                            }
                        }
                        else
                        {
                            $errorMessage = "You must include at least 2 devices in your report.";

                        }
                    }
                    else
                    {
                        $errorMessage = "Invalid RMS Upload.";
                    }
                }
                else
                {
                    $errorMessage = "Invalid device instance.";
                }
            }
            else
            {
                $errorMessage = "Invalid device instance id.";
            }

            if ($errorMessage !== false)
            {
                $this->getResponse()->setHttpResponseCode(500);
                $this->sendJson(array("error" => true, "message" => $errorMessage));
            }

            if ($isExcluded)
            {
                $this->sendJson(array("success" => true, "message" => "Device is now excluded."));
            }
            else
            {
                $this->sendJson(array("success" => true, "message" => "Device is now included. "));
            }
        }
        else
        {
            $this->getResponse()->setHttpResponseCode(500);
            $this->sendJson(array("error" => true, "message" => "Invalid RMS Upload Id"));
        }
    }

    /**
     * Handles Toggling of the isLeased flag
     */
    public function toggleLeasedFlagAction ()
    {
        $rmsUploadId = $this->_getParam('rmsUploadId', false);
        if ($rmsUploadId > 0)
        {
            $deviceInstanceId = $this->_getParam("deviceInstanceId", false);
            $isLeased         = $this->_getParam("isLeased", false) == 'true';
            $errorMessage     = false;

            if ($deviceInstanceId !== false)
            {
                $deviceInstanceMapper = Proposalgen_Model_Mapper_DeviceInstance::getInstance();
                $deviceInstance       = $deviceInstanceMapper->find($deviceInstanceId);
                if ($deviceInstance instanceof Proposalgen_Model_DeviceInstance)
                {
                    $rmsUpload = Proposalgen_Model_Mapper_Rms_Upload::getInstance()->find($deviceInstance->rmsUploadId);
                    if ($rmsUpload)
                    {
                        $includedDeviceInstanceCount = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->getMappedDeviceInstances($rmsUpload->id, null, null, null, null, true, true);
                        if ($includedDeviceInstanceCount > 2 || $isLeased === false)
                        {
                            if ($rmsUpload->id == $rmsUploadId)
                            {

                                if ($isLeased || $deviceInstance->hasValidToners())
                                {
                                    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                                    $db->beginTransaction();
                                    try
                                    {
                                        $deviceInstance->isLeased = $isLeased;
                                        $deviceInstanceMapper->save($deviceInstance);
                                        $db->commit();
                                    }
                                    catch (Exception $e)
                                    {
                                        $db->rollBack();
                                        Tangent_Log::logException($e);
                                        $errorMessage = "The system encountered an error while trying to toggle the leased state of the device. Reference #" . Tangent_Log::getUniqueId();
                                    }
                                }
                                else
                                {
                                    $errorMessage = "Device does not have valid toners";
                                }
                            }
                            else
                            {
                                $errorMessage = "You can only change the leased state of device instances that belong to the same assessment." . $rmsUpload->id . " - " . $rmsUploadId;
                            }
                        }
                        else
                        {
                            $errorMessage = "You must include at least 2 devices in your report.";

                        }
                    }
                    else
                    {
                        $errorMessage = "Invalid RMS Upload.";
                    }
                }
                else
                {
                    $errorMessage = "Invalid device instance.";
                }
            }
            else
            {
                $errorMessage = "Invalid device instance id.";
            }

            if ($errorMessage !== false)
            {
                $this->getResponse()->setHttpResponseCode(500);
                $this->sendJson(array("error" => true, "message" => $errorMessage));
            }

            if ($isLeased)
            {
                $this->sendJson(array("success" => true, "message" => "Device is now leased."));
            }
            else
            {
                $this->sendJson(array("success" => true, "message" => "Device is no longer leased. "));
            }
        }
        else
        {
            $this->getResponse()->setHttpResponseCode(500);
            $this->sendJson(array("error" => true, "message" => "Invalid RMS Upload Id"));
        }
    }

    /**
     * Handles Toggling of the isManaged flag
     */
    public function toggleManagedFlagAction ()
    {
        $rmsUploadId = $this->_getParam('rmsUploadId', false);
        if ($rmsUploadId > 0)
        {
            $deviceInstanceId = $this->_getParam("deviceInstanceId", false);
            $isManaged        = $this->_getParam("isManaged", false) == 'true';
            $errorMessage     = false;

            if ($deviceInstanceId !== false)
            {
                $deviceInstanceMapper = Proposalgen_Model_Mapper_DeviceInstance::getInstance();
                $deviceInstance       = $deviceInstanceMapper->find($deviceInstanceId);

                if ($deviceInstance instanceof Proposalgen_Model_DeviceInstance)
                {
                    $rmsUpload = Proposalgen_Model_Mapper_Rms_Upload::getInstance()->find($deviceInstance->rmsUploadId);

                    if ($rmsUpload)
                    {
                        $includedDeviceInstanceCount = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->getMappedDeviceInstances($rmsUpload->id, null, null, null, null, true, true);

                        if ($includedDeviceInstanceCount > 2 || $isManaged === false)
                        {
                            if ($rmsUpload->id == $rmsUploadId)
                            {
                                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                                $db->beginTransaction();
                                try
                                {
                                    $deviceInstance->isManaged = $isManaged;
                                    $deviceInstanceMapper->save($deviceInstance);
                                    $db->commit();
                                }
                                catch (Exception $e)
                                {
                                    $db->rollBack();
                                    Tangent_Log::logException($e);
                                    $errorMessage = "The system encountered an error while trying to toggle the managed state of the device. Reference #" . Tangent_Log::getUniqueId();
                                }
                            }
                            else
                            {
                                $errorMessage = "You can only change the leased state of device instances that belong to the same report." . $rmsUpload->id . " - " . $rmsUploadId;
                            }
                        }
                        else
                        {
                            $errorMessage = "You must include at least 2 devices in your report.";

                        }
                    }
                    else
                    {
                        $errorMessage = "Invalid RMS Upload.";
                    }
                }
                else
                {
                    $errorMessage = "Invalid device instance.";
                }
            }
            else
            {
                $errorMessage = "Invalid device instance id.";
            }

            if ($errorMessage !== false)
            {
                $this->getResponse()->setHttpResponseCode(500);
                $this->sendJson(array("error" => true, "message" => $errorMessage));
            }

            if ($isManaged)
            {
                $this->sendJson(array("success" => true, "message" => "Device is now managed."));
            }
            else
            {
                $this->sendJson(array("success" => true, "message" => "Device is no longer managed. "));
            }
        }
        else
        {
            $this->getResponse()->setHttpResponseCode(500);
            $this->sendJson(array("error" => true, "message" => "Invalid RMS Upload Id"));
        }
    }

    /**
     * Handles Toggling the JIT Compatibility of devices
     */
    public function toggleJitFlagAction ()
    {
        $rmsUploadId = $this->_getParam('rmsUploadId', false);
        if ($rmsUploadId > 0)
        {
            $deviceInstanceId = $this->_getParam("deviceInstanceId", false);
            $isJitCompatible  = $this->_getParam("compatibleWithJitProgram", false) == 'true';
            $errorMessage     = false;

            if ($deviceInstanceId !== false)
            {
                $deviceInstanceMapper = Proposalgen_Model_Mapper_DeviceInstance::getInstance();
                $deviceInstance       = $deviceInstanceMapper->find($deviceInstanceId);
                if ($deviceInstance instanceof Proposalgen_Model_DeviceInstance)
                {
                    $rmsUpload = Proposalgen_Model_Mapper_Rms_Upload::getInstance()->find($deviceInstance->rmsUploadId);
                    if ($rmsUpload)
                    {
                        $includedDeviceInstanceCount = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->getMappedDeviceInstances($rmsUpload->id, null, null, null, null, true, true);
                        if ($includedDeviceInstanceCount > 2 || $isJitCompatible === false)
                        {
                            if ($rmsUpload->id == $rmsUploadId)
                            {
                                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                                $db->beginTransaction();
                                try
                                {
                                    $deviceInstance->compatibleWithJitProgram = $isJitCompatible;
                                    $deviceInstanceMapper->save($deviceInstance);
                                    $db->commit();
                                }
                                catch (Exception $e)
                                {
                                    $db->rollBack();
                                    Tangent_Log::logException($e);
                                    $errorMessage = "The system encountered an error while trying to toggle the " . My_Brand::$jit . " compatibility of the device. Reference #" . Tangent_Log::getUniqueId();
                                }
                            }
                            else
                            {
                                $errorMessage = "You can only change the " . My_Brand::$jit . " compatibility of device instances that belong to the same assessment." . $rmsUpload->id . " - " . $rmsUploadId;
                            }
                        }
                        else
                        {
                            $errorMessage = "You must include at least 2 devices in your report.";

                        }
                    }
                    else
                    {
                        $errorMessage = "Invalid RMS Upload.";
                    }
                }
                else
                {
                    $errorMessage = "Invalid device instance.";
                }
            }
            else
            {
                $errorMessage = "Invalid device instance id.";
            }

            if ($errorMessage !== false)
            {
                $this->getResponse()->setHttpResponseCode(500);
                $this->sendJson(array("error" => true, "message" => $errorMessage));
            }

            if ($isJitCompatible)
            {
                $this->sendJson(array("success" => true, "message" => "Device is now " . My_Brand::$jit . " Compatible."));
            }
            else
            {
                $this->sendJson(array("success" => true, "message" => "Device is now no longer " . My_Brand::$jit . " Compatible. "));
            }
        }
        else
        {
            $this->getResponse()->setHttpResponseCode(500);
            $this->sendJson(array("error" => true, "message" => "Invalid RMS Upload Id"));
        }
    }

    /**
     * Gets all the details for a device instance
     */
    public function deviceInstanceDetailsAction ()
    {
        $rmsUploadId  = $this->_getParam('rmsUploadId', false);
        $jsonResponse = array();
        $errorMessage = false;

        if ($rmsUploadId > 0)
        {
            $deviceInstanceId = $this->_getParam("deviceInstanceId", false);

            /*
             * We must get a deviceInstanceId
             */
            if ($deviceInstanceId !== false)
            {
                /*
                 * Devices must exist
                 */
                $deviceInstance = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->find($deviceInstanceId);
                if ($deviceInstance instanceof Proposalgen_Model_DeviceInstance)
                {
                    /*
                     * Devices must be part of our report
                     */
                    if ($deviceInstance->rmsUploadId == $rmsUploadId)
                    {
                        /*
                         * Once we get here we can start populating our response
                         */
                        $jsonResponse                                       = $deviceInstance->toArray();
                        $jsonResponse['masterDevice']                       = $deviceInstance->getMasterDevice()->toArray();
                        $launchDate                                         = new Zend_Date($jsonResponse['masterDevice']['launchDate']);
                        $jsonResponse['masterDevice']['launchDate']         = $launchDate->toString(Zend_Date::DATE_MEDIUM);
                        $jsonResponse['masterDevice']['cost']               = ($jsonResponse['cost'] > 0) ? $this->view->currency((float)$jsonResponse['cost']) : '';
                        $jsonResponse['masterDevice']['ppmBlack']           = ($jsonResponse['masterDevice']['ppmBlack'] > 0) ? number_format($jsonResponse['masterDevice']['ppmBlack']) : '';
                        $jsonResponse['masterDevice']['ppmColor']           = ($jsonResponse['masterDevice']['ppmColor'] > 0) ? number_format($jsonResponse['masterDevice']['ppmColor']) : '';
                        $jsonResponse['masterDevice']['wattsPowerNormal']   = number_format($jsonResponse['masterDevice']['wattsPowerNormal']);
                        $jsonResponse['masterDevice']['wattsPowerIdle']     = number_format($jsonResponse['masterDevice']['wattsPowerIdle']);
                        $jsonResponse['masterDevice']['leasedTonerYield']   = number_format($jsonResponse['masterDevice']['leasedTonerYield']);
                        $jsonResponse["masterDevice"]["manufacturer"]       = $deviceInstance->getMasterDevice()->getManufacturer()->toArray();
                        $jsonResponse["masterDevice"]["reportsTonerLevels"] = $deviceInstance->getMasterDevice()->isCapableOfReportingTonerLevels;
                        $jsonResponse["masterDevice"]["tonerConfigName"]    = $deviceInstance->getMasterDevice()->getTonerConfig()->tonerConfigName;
                        $jsonResponse["masterDevice"]["compatibleWithJit"]  = $deviceInstance->getMasterDevice()->isJitCompatible($this->_identity->dealerId);
                        $jsonResponse["masterDevice"]["toners"]             = [];

                        foreach ($deviceInstance->getMasterDevice()->getToners() as $tonersByManufacturer)
                        {
                            foreach ($tonersByManufacturer as $tonersByColor)
                            {
                                /* @var $toner Proposalgen_Model_Toner */
                                foreach ($tonersByColor as $toner)
                                {
                                    $tonerArray                               = $toner->toArray();
                                    $tonerArray['cost']                       = $this->view->currency((float)$tonerArray['cost']);
                                    $tonerArray['yield']                      = number_format($tonerArray['yield']);
                                    $tonerArray['manufacturer']               = ($toner->getManufacturer()) ? $toner->getManufacturer()->toArray() : "Unknown";
                                    $tonerArray['tonerColorName']             = Proposalgen_Model_TonerColor::$ColorNames[$toner->tonerColorId];
                                    $jsonResponse["masterDevice"]["toners"][] = $tonerArray;

                                }
                            }
                        }
                    }
                    else
                    {
                        $errorMessage = "Device does not belong to your report.";
                    }
                }
                else
                {
                    $errorMessage = "Invalid device specified.";
                }
            }
            else
            {
                $errorMessage = "Missing/Invalid Parameter 'deviceInstanceId'.";
            }
        }
        else
        {
            $errorMessage = "Missing/Invalid Parameter 'rmsUploadId'.";
        }

        if ($errorMessage !== false)
        {
            $jsonResponse = array("error" => true, "message" => $errorMessage);
            $this->getResponse()->setHttpResponseCode(500);
        }

        $this->sendJson($jsonResponse);
    }

    /**
     * (non-PHPdoc)
     *
     * @see Zend_Controller_Action::postDispatch()
     */
    public function postDispatch ()
    {
        $this->view->placeholder('ProgressionNav')->set($this->view->NavigationMenu($this->_navigation));
    }
}