<?php
class Proposalgen_FleetController extends Proposalgen_Library_Controller_Proposal
{
    /**
     * Users can upload/see uploaded data on this step
     */
    public function indexAction ()
    {
        // Mark the step we're on as active
        $this->setActiveReportStep(Proposalgen_Model_Assessment_Step::STEP_FLEETDATA_UPLOAD);

        $report = $this->getReport();

        $uploadService = new Proposalgen_Service_Rms_Upload($this->_userId, $this->_clientId, $report->getRmsUpload());
        $form          = $uploadService->getForm();

        if ($this->getRequest()->isPost())
        {
            $values = $this->getRequest()->getPost();
            if (isset($values ["goBack"]))
            {
                $this->gotoPreviousStep();
            }
            else if (isset($values ["performUpload"]))
            {
                /*
                 * Handle Upload
                 */
                if ($form->isValid($values))
                {
                    $success = $uploadService->processUpload($values);
                    if ($success)
                    {
                        $report->rmsUploadId = $uploadService->rmsUpload->id;
                        $report->setRmsUpload($uploadService->rmsUpload);

                        $this->_flashMessenger->addMessage(array("success" => "Upload was successful."));
                        $this->saveReport();
                        $this->gotoNextStep();
                    }
                    else
                    {
                        $this->_flashMessenger->addMessage(array("danger" => $uploadService->errorMessages));
                    }

                }
            }
            else if (isset($values ["saveAndContinue"]))
            {
                $count = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->countRowsForRmsUpload($report->getRmsUpload()->id);
                if ($count < 2)
                {
                    $this->_flashMessenger->addMessage(array(
                                                            'danger' => "You must have at least 2 valid devices to continue."
                                                       ));
                }
                else
                {
                    // Call the base controller to send us to the next logical step in the proposal.
                    $this->gotoNextStep();
                }
            }
        }

        $this->view->form = $form;

        $this->view->rmsUpload = $uploadService->rmsUpload;
//        if($rmsUpload instanceof Proposalgen_Model_Rms_Upload_Row)
//        {
//            $this->view->populateGrid = true;
//        }

        $navigationButtons          = ($uploadService->rmsUpload instanceof Proposalgen_Model_Rms_Upload) ? Proposalgen_Form_Assessment_Navigation::BUTTONS_BACK_NEXT : Proposalgen_Form_Assessment_Navigation::BUTTONS_BACK;
        $this->view->navigationForm = new Proposalgen_Form_Assessment_Navigation($navigationButtons);

    }

    /**
     * This handles the mapping of devices to our master devices
     */
    public function mappingAction ()
    {
        // Mark the step we're on as active
        $this->setActiveReportStep(Proposalgen_Model_Assessment_Step::STEP_FLEETDATA_MAPDEVICES);

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
     * Generates a list of devices that were not mapped automatically
     */
    public function excludedListAction ()
    {
        $jqGrid            = new Tangent_Service_JQGrid();
        $excludedRowMapper = Proposalgen_Model_Mapper_Rms_Excluded_Row::getInstance();

        /*
         * Grab the incoming parameters
         */
        $jqGridParameters = array(
            'sidx' => $this->_getParam('sidx', 'manufacturerName'),
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
            $jqGrid->setRecordCount($excludedRowMapper->fetchAllForRmsUpload($this->getReport()->getRmsUpload()->id, $jqGrid->getSortColumn(), $jqGrid->getSortDirection(), null, null, true));

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
            $jqGrid->setRows($excludedRowMapper->fetchAllForRmsUpload($this->getReport()->getRmsUpload()->id, $jqGrid->getSortColumn(), $jqGrid->getSortDirection(), $jqGrid->getRecordsPerPage(), $startRecord));

            // Send back jqGrid json data
            $this->sendJson($jqGrid->createPagerResponseArray());
        }
        else
        {
            $this->_response->setHttpResponseCode(500);
            $this->sendJson(array('error' => 'Sorting parameters are invalid'));
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
                        }
                    }

                    $db->commit();
                }
                catch (Exception $e)
                {
                    $db->rollBack();
                    My_Log::logException($e);
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
        // Mark the step we're on as active
        $this->setActiveReportStep(Proposalgen_Model_Assessment_Step::STEP_FLEETDATA_SUMMARY);

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

        $this->view->navigationForm = new Proposalgen_Form_Assessment_Navigation(Proposalgen_Form_Assessment_Navigation::BUTTONS_ALL);
    }

    /**
     * The list of devices that are mapped in the report. It's used on the summary page
     */
    public function deviceSummaryListAction ()
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
            $jqGrid->setRecordCount($deviceInstanceMapper->getMappedDeviceInstances($this->getReport()->getRmsUpload()->id, $jqGrid->getSortColumn(), $jqGrid->getSortDirection(), null, null, true));

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
            $deviceInstances = $deviceInstanceMapper->getMappedDeviceInstances($this->getReport()->getRmsUpload()->id, $jqGrid->getSortColumn(), $jqGrid->getSortDirection(), $jqGrid->getRecordsPerPage(), $startRecord);

            $rows = array();
            foreach ($deviceInstances as $deviceInstance)
            {

                $row = array(
                    "id"         => $deviceInstance->id,
                    "isExcluded" => $deviceInstance->isExcluded,
                    "ampv"       => number_format($deviceInstance->getAverageMonthlyPageCount()),
                    "isLeased"   => ($deviceInstance->getIsLeased()) ? "Leased" : "Purchased"
                );

                $row["deviceName"] = $deviceInstance->getRmsUploadRow()->manufacturer . " " . $deviceInstance->getRmsUploadRow()->modelName . "<br>" . $deviceInstance->ipAddress;

                if ($deviceInstance->getMasterDevice() instanceof Proposalgen_Model_MasterDevice)
                {
                    $row["mappedToDeviceName"] = $deviceInstance->getMasterDevice()->getManufacturer()->fullname . " " . $deviceInstance->getMasterDevice()->modelName;
                }
                else
                {
                    $row["mappedToDeviceName"] = $deviceInstance->getRmsUploadRow()->getManufacturer()->fullname . " " . $deviceInstance->getRmsUploadRow()->modelName;
                }
                $rows[] = $row;

            }

            $jqGrid->setRows($rows);

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
     * Allows the user to set the report settings for a report
     */
    public function reportsettingsAction ()
    {
        // Mark the step we're on as active
        $this->setActiveReportStep(Proposalgen_Model_Assessment_Step::STEP_REPORTSETTINGS);

        $reportSettingsService = new Proposalgen_Service_ReportSettings($this->getReport()->id, $this->_userId, $this->_dealerId);

        if ($this->getRequest()->isPost())
        {
            $values = $this->getRequest()->getPost();

            if (isset($values ['cancel']))
            {
                $this->gotoPreviousStep();
            }
            else
            {
                if ($reportSettingsService->update($values))
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

        $this->view->form = $reportSettingsService->getForm();
    }

    /**
     * This is where a user can modify the properties of an rms upload row in a way that will make it valid
     */
    public function editUnknownDeviceAction ()
    {
        $db                        = Zend_Db_Table_Abstract::getDefaultAdapter();
        $deviceInstanceIdsAsString = $this->_getParam("deviceInstanceIds", false);

        if ($deviceInstanceIdsAsString !== false)
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
                $this->_helper->redirector('mapping');
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

                                // Update the rms upload row
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
                            $this->redirector("mapping");
                        }
                        catch (Exception $e)
                        {
                            /**
                             * Error Saving
                             */
                            $db->rollBack();
                            My_Log::logException($e);
                            $this->_flashMessenger->addMessage(array("danger" => "There was a system error while saving your device. Please try again. Reference #" . My_Log::getUniqueId()));
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
            }
            $this->view->form = $form;
        }
        else
        {
            $this->_flashMessenger->addMessage(array("warning" => "Invalid Device Specified."));
            $this->redirector("mapping");
        }
    }

    /*
     * Handles removing an unknown device.
     * This is a JSON function
     */
    public function removeUnknownDeviceAction ()
    {
        $db                        = Zend_Db_Table_Abstract::getDefaultAdapter();
        $deviceInstanceIdsAsString = $this->_getParam("deviceInstanceIds", false);

        $deviceInstanceIds    = explode(",", $deviceInstanceIdsAsString);
        $reportId             = $this->getReport()->id;
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
                $assessment     = Proposalgen_Model_Mapper_Assessment::getInstance()->find($reportId);
                if ($deviceInstance && $assessment && $assessment->rmsUploadId == $deviceInstance->rmsUploadId)
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
            My_Log::logException($e);
            $this->getResponse()->setHttpResponseCode(500);
            $jsonResponse = array("error" => true, "message" => "There was an error removing the unknown device. Reference #" . My_Log::getUniqueId());
        }

        $this->sendJson($jsonResponse);
    }

    /**
     * Handles excluding/including devices
     */
    public function toggleExcludedFlagAction ()
    {
        $deviceInstanceId = $this->_getParam("deviceInstanceId", false);
        $isExcluded       = $this->_getParam("isExcluded", false);
        if ($isExcluded == 'true')
        {
            $isExcluded = true;
        }
        else
        {
            $isExcluded = false;
        }
        $errorMessage = false;

        if ($deviceInstanceId !== false)
        {
            $deviceInstanceMapper = Proposalgen_Model_Mapper_DeviceInstance::getInstance();
            $deviceInstance       = $deviceInstanceMapper->find($deviceInstanceId);
            if ($deviceInstance instanceof Proposalgen_Model_DeviceInstance)
            {
                $rmsUpload = Proposalgen_Model_Mapper_Rms_Upload::getInstance()->find($deviceInstance->rmsUploadId);
                if ($rmsUpload)
                {
                    if ($rmsUpload->id == $this->getReport()->rmsUploadId)
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
                            My_Log::logException($e);
                            $errorMessage = "The system encountered an error while trying to exclude the device. Reference #" . My_Log::getUniqueId();
                        }
                    }
                    else
                    {
                        $errorMessage = "You can only exclude device instances that belong to the same assessment." . $rmsUpload->id . " - " . $this->getReport()->rmsUploadId;
                    }
                }
                else
                {
                    $errorMessage = "Invalid Rms Upload.";
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

    /**
     * Gets all the details for a device instance
     */
    public function deviceInstanceDetailsAction ()
    {
        $jsonResponse = array();
        $errorMessage = false;

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
                if ($deviceInstance->rmsUploadId == $this->getReport()->getRmsUpload()->id)
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
                    $jsonResponse['masterDevice']['dutyCycle']          = number_format($jsonResponse['masterDevice']['dutyCycle']);
                    $jsonResponse['masterDevice']['leasedTonerYield']   = number_format($jsonResponse['masterDevice']['leasedTonerYield']);
                    $jsonResponse["masterDevice"]["manufacturer"]       = $deviceInstance->getMasterDevice()->getManufacturer()->toArray();
                    $jsonResponse["masterDevice"]["reportsTonerLevels"] = $deviceInstance->isCapableOfReportingTonerLevels();
                    $jsonResponse["masterDevice"]["tonerConfigName"]    = $deviceInstance->getMasterDevice()->getTonerConfig()->tonerConfigName;

                    foreach ($deviceInstance->getMasterDevice()->getToners() as $tonersByPartType)
                    {
                        foreach ($tonersByPartType as $tonersByColor)
                        {
                            /* @var $toner Proposalgen_Model_Toner */
                            foreach ($tonersByColor as $toner)
                            {
                                $tonerArray                               = $toner->toArray();
                                $tonerArray['cost']                       = $this->view->currency((float)$tonerArray['cost']);
                                $tonerArray['yield']                      = number_format($tonerArray['yield']);
                                $tonerArray['manufacturer']               = ($toner->getManufacturer()) ? $toner->getManufacturer()->toArray() : "Unknown";
                                $tonerArray['partTypeName']               = Proposalgen_Model_PartType::$PartTypeNames[$toner->partTypeId];
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

        if ($errorMessage !== false)
        {
            $jsonResponse = array("error" => true, "message" => $errorMessage);
            $this->getResponse()->setHttpResponseCode(500);
        }

        $this->sendJson($jsonResponse);
    }
}