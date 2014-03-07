<?php

/**
 * Class Proposalgen_ManagedevicesController
 */
class Proposalgen_ManagedevicesController extends Tangent_Controller_Action
{

    protected $config;
    protected $user_id;
    protected $MPSProgramName;
    protected $ApplicationName;

    /**
     * @var bool
     */
    protected $_isAdmin;

    /**
     * @var stdClass
     */
    protected $_identity;

    function init ()
    {
        $this->config = Zend_Registry::get('config');
        $this->initView();
        $this->view->app            = $this->config->app;
        $this->view->user           = Zend_Auth::getInstance()->getIdentity();
        $this->view->user_id        = Zend_Auth::getInstance()->getIdentity()->id;
        $this->user_id              = Zend_Auth::getInstance()->getIdentity()->id;
        $this->MPSProgramName       = $this->config->app->MPSProgramName;
        $this->view->MPSProgramName = $this->config->app->MPSProgramName;
        $this->ApplicationName      = $this->config->app->ApplicationName;
        $this->_identity            = Zend_Auth::getInstance()->getIdentity();
        $this->_isAdmin             = $this->view->IsAllowed(Proposalgen_Model_Acl::RESOURCE_PROPOSALGEN_ADMIN_SAVEANDAPPROVE, Application_Model_Acl::PRIVILEGE_ADMIN);
        $this->view->isAdmin        = $this->_isAdmin;
    }

    /**
     * Gets a  list of all the assigned toners for a specific Master Device
     */
    public function assignedTonerListAction ()
    {
        $masterDeviceId   = $this->_getParam('masterDeviceId', false);
        $tonerList        = $this->_getParam('tonersList', false);
        $firstLoad        = $this->_getParam('firstLoad', false);
        $jsonArray        = array();
        $jqGridService    = new Tangent_Service_JQGrid();
        $jqGridParameters = array(
            'sidx' => $this->_getParam('sidx', 'id'),
            'sord' => $this->_getParam('sord', 'desc'),
            'page' => $this->_getParam('page', 1),
            'rows' => $this->_getParam('rows', 10),
        );

        if ($jqGridService->sortingIsValid())
        {
            $jqGridService->parseJQGridPagingRequest($jqGridParameters);
            $sortOrder = array();
            if ($jqGridService->hasGrouping())
            {
                $sortOrder[] = $jqGridService->getGroupByColumn() . ' ' . $jqGridService->getGroupBySortOrder();
            }

            if ($jqGridService->hasColumns())
            {
                $sortOrder[] = $jqGridService->getSortColumn() . ' ' . $jqGridService->getSortDirection();
            }

            $toners     = [];
            $tonerCount = 0;

            /**
             * If we passed a list of toners, it means those are all the toners assigned to a device.
             * Otherwise we'll fetch the toners that are assigned to the device already.
             */
            if ($tonerList)
            {
                $tonerCount = Proposalgen_Model_Mapper_Toner::getInstance()->fetchListOfToners($tonerList, $masterDeviceId, null, true);
            }
            else if ($masterDeviceId !== false && $masterDeviceId !== 0 && $firstLoad)
            {
                $tonerCount = Proposalgen_Model_Mapper_Toner::getInstance()->fetchTonersAssignedToDeviceWithMachineCompatibility($masterDeviceId, null, true);
            }

            $jqGridService->setRecordCount($tonerCount);

            /**
             * If we passed a list of toners, it means those are all the toners assigned to a device.
             * Otherwise we'll fetch the toners that are assigned to the device already.
             */
            if ($tonerList)
            {
                $toners = Proposalgen_Model_Mapper_Toner::getInstance()->fetchListOfToners($tonerList, $masterDeviceId, $sortOrder);
            }
            else if ($masterDeviceId !== false && $masterDeviceId !== 0 && $firstLoad)
            {
                $toners = Proposalgen_Model_Mapper_Toner::getInstance()->fetchTonersAssignedToDeviceWithMachineCompatibility($masterDeviceId, $sortOrder);
            }

            $jqGridService->setRows($toners);

            // Send back jqGrid JSON data
            $this->sendJson($jqGridService->createPagerResponseArray());
        }
        else
        {
            $this->_response->setHttpResponseCode(500);
            $this->sendJson(array(
                'error' => 'Sorting parameters are invalid'
            ));
        }

        $json = json_encode($jsonArray);
        $this->sendJson($json);
    }

    /**
     * Gets a list of all the available toners for a specific master device
     */
    public function availableTonersListAction ()
    {
        $masterDeviceId     = $this->_getParam('masterDeviceId', false);
        $tonerColorConfigId = $this->_getParam('tonerColorConfigId', false);
        $tonerColorId       = $this->_getParam('tonerColorId', false);
        $manufacturerId     = $this->_getParam('manufacturerId', false);
        $filter             = $this->_getParam('filter', false);
        $criteria           = $this->_getParam('criteria', false);
        $jsonArray          = array();
        $jqGridService      = new Tangent_Service_JQGrid();
        $tonerMapper        = Proposalgen_Model_Mapper_Toner::getInstance();
        $jqGridParameters   = array(
            'sidx' => $this->_getParam('sidx', 'id'),
            'sord' => $this->_getParam('sord', 'desc'),
            'page' => $this->_getParam('page', 1),
            'rows' => $this->_getParam('rows', 10),
        );

        if ($jqGridService->sortingIsValid())
        {
            $jqGridService->parseJQGridPagingRequest($jqGridParameters);
            if ($tonerColorConfigId)
            {
                $jqGridService->setRecordCount(count($tonerMapper->fetchTonersWithMachineCompatibilityUsingColorConfigId($tonerColorConfigId, null, 10000, 0, $manufacturerId, $filter, $criteria, $masterDeviceId)));
            }
            else
            {
                $jqGridService->setRecordCount(count($tonerMapper->fetchTonersWithMachineCompatibilityUsingColorId($tonerColorId, null, 10000, 0, $manufacturerId, $filter, $criteria, $masterDeviceId)));
            }

            // Validate current page number since we don't want to be out of bounds
            if ($jqGridService->getCurrentPage() < 1)
            {
                $jqGridService->setCurrentPage(1);
            }
            else if ($jqGridService->getCurrentPage() > $jqGridService->calculateTotalPages())
            {
                $jqGridService->setCurrentPage($jqGridService->calculateTotalPages());
            }

            // Return a small subset of the results based on the jqGrid parameters
            $startRecord = $jqGridService->getRecordsPerPage() * ($jqGridService->getCurrentPage() - 1);

            if ($startRecord < 0)
            {
                $startRecord = 0;
            }

            $sortOrder = array();

            if ($jqGridService->hasGrouping())
            {
                $sortOrder[] = $jqGridService->getGroupByColumn() . ' ' . $jqGridService->getGroupBySortOrder();
            }

            if ($jqGridService->hasColumns())
            {
                $sortOrder[] = $jqGridService->getSortColumn() . ' ' . $jqGridService->getSortDirection();
            }
            if ($tonerColorConfigId)
            {
                $jqGridService->setRows($tonerMapper->fetchTonersWithMachineCompatibilityUsingColorConfigId($tonerColorConfigId, $sortOrder, $jqGridService->getRecordsPerPage(), $startRecord, $manufacturerId, $filter, $criteria, $masterDeviceId));
            }
            else
            {
                $jqGridService->setRows($tonerMapper->fetchTonersWithMachineCompatibilityUsingColorId($tonerColorId, $sortOrder, $jqGridService->getRecordsPerPage(), $startRecord, $manufacturerId, $filter, $criteria, $masterDeviceId));

            }

            // Send back jqGrid JSON data
            $this->sendJson($jqGridService->createPagerResponseArray());
        }
        else
        {
            $this->_response->setHttpResponseCode(500);
            $this->sendJson(array(
                'error' => 'Sorting parameters are invalid'
            ));
        }

        $json = json_encode($jsonArray);
        $this->sendJson($json);
    }

    /**
     * Gets all the options available to a quote device
     */
    public function optionListAction ()
    {
        $jsonArray        = array();
        $jqGridService    = new Tangent_Service_JQGrid();
        $optionMapper     = Quotegen_Model_Mapper_Option::getInstance();
        $masterDeviceId   = $this->_getParam('masterDeviceId', false);
        $jqGridParameters = array(
            'sidx' => $this->_getParam('sidx', 'oemSku'),
            'sord' => $this->_getParam('sord', 'desc'),
            'page' => $this->_getParam('page', 1),
            'rows' => $this->_getParam('rows', 10),
        );
        $sortColumns      = array(
            'oemSku',
            'dealerSku',
            'name',
            'option',
            'cost',
            'description',
        );
        $jqGridService->setValidSortColumns($sortColumns);
        if ($jqGridService->sortingIsValid())
        {
            $jqGridService->parseJQGridPagingRequest($jqGridParameters);
            $searchCriteria = $this->_getParam('criteriaFilter', null);
            $searchValue    = $this->_getParam('criteria', null);

            $filterCriteriaValidator = new Zend_Validate_InArray(array(
                'haystack' => $sortColumns
            ));

            // If search criteria or value is null then we don't need either one of them. Same goes if our criteria is invalid.
            if ($searchCriteria === null || $searchValue === null || !$filterCriteriaValidator->isValid($searchCriteria))
            {
                $searchCriteria = null;
                $searchValue    = null;
            }
            $options = $optionMapper->fetchAllOptionsWithDeviceOptions($masterDeviceId, $this->_identity->dealerId, null, $jqGridService->getSortDirection(), $searchCriteria, $searchValue, 1000, 0);
            $jqGridService->setRecordCount(count($options));

            // Validate current page number since we don't want to be out of bounds
            if ($jqGridService->getCurrentPage() < 1)
            {
                $jqGridService->setCurrentPage(1);
            }
            else if ($jqGridService->getCurrentPage() > $jqGridService->calculateTotalPages())
            {
                $jqGridService->setCurrentPage($jqGridService->calculateTotalPages());
            }

            // Return a small subset of the results based on the jqGrid parameters
            $startRecord = $jqGridService->getRecordsPerPage() * ($jqGridService->getCurrentPage() - 1);
            if ($startRecord < 0)
            {
                $startRecord = 0;
            }

            $sortOrder = array();
            if ($jqGridService->hasGrouping())
            {
                $sortOrder[] = $jqGridService->getGroupByColumn() . ' ' . $jqGridService->getGroupBySortOrder();
            }

            if ($jqGridService->hasColumns())
            {
                $sortOrder[] = $jqGridService->getSortColumn() . ' ' . $jqGridService->getSortDirection();
            }
            $options = $optionMapper->fetchAllOptionsWithDeviceOptions($masterDeviceId, $this->_identity->dealerId, $sortOrder, null, $searchCriteria, $searchValue, $jqGridService->getRecordsPerPage(), $startRecord);
            $jqGridService->setRows($options);
            // Send back jqGrid JSON data
            $this->sendJson($jqGridService->createPagerResponseArray());
        }
        else
        {
            $this->_response->setHttpResponseCode(500);
            $this->sendJson(array(
                'error' => 'Sorting parameters are invalid'
            ));
        }

        $json = json_encode($jsonArray);
        $this->sendJson($json);
    }

    /**
     * Assigns or unassigns an option when on the available options page
     */
    public function assignAvailableOptionAction ()
    {
        $json = "Failed to assign or unassign option";
        if ($this->_request->isPost())
        {
            $optionId       = $this->_request->getParam('optionId', false);
            $masterDeviceId = $this->_request->getParam('masterDeviceId', false);
            if ($optionId && $masterDeviceId)
            {
                $device = Quotegen_Model_Mapper_Device::getInstance()->find(array($masterDeviceId, $this->_identity->dealerId));
                if ($device)
                {
                    $deviceOptionMapper = Quotegen_Model_Mapper_DeviceOption::getInstance();
                    $deviceOption       = $deviceOptionMapper->find(array($masterDeviceId, $optionId));
                    if ($deviceOption)
                    {
                        $deviceOptionMapper->delete($deviceOption);
                        $json = "Successfully unassigned option";
                    }
                    else
                    {
                        $deviceOption                   = new Quotegen_Model_DeviceOption();
                        $deviceOption->masterDeviceId   = $masterDeviceId;
                        $deviceOption->dealerId         = $this->_identity->dealerId;
                        $deviceOption->optionId         = $optionId;
                        $deviceOption->includedQuantity = 0;
                        $deviceOptionMapper->insert($deviceOption);
                        $json = "Successfully assigned option";
                    }
                }
            }
        }
        $this->sendJson($json);

    }

    /**
     * creates the service, tells it which forms we want to use and displays them
     */
    public function managemasterdevicesAction ()
    {
        $this->_helper->layout()->disableLayout();
        $masterDeviceId = $this->_getParam('masterDeviceId', false);
        $rmsUploadRowId = $this->_getParam('rmsUploadRowId', false);

        $masterDevice = Proposalgen_Model_Mapper_MasterDevice::getInstance()->find($masterDeviceId);
        $isAllowed    = ((!$masterDevice instanceof Proposalgen_Model_MasterDevice || !$masterDevice->isSystemDevice || $this->_isAdmin) ? true : false);

        $service = new Proposalgen_Service_ManageMasterDevices($masterDeviceId, $this->_identity->dealerId, ($rmsUploadRowId > 0 ? true : $isAllowed), $this->_isAdmin);

        if ($rmsUploadRowId > 0)
        {
            $rmsUploadRow = Proposalgen_Model_Mapper_Rms_Upload_Row::getInstance()->find($rmsUploadRowId);

            if ($rmsUploadRow instanceof Proposalgen_Model_Rms_Upload_Row)
            {
                $service->populate($rmsUploadRow->toArray());

                $this->view->modelName      = $rmsUploadRow->modelName;
                $this->view->manufacturerId = $rmsUploadRow->manufacturerId;
            }
        }
        else if ($masterDevice instanceof Proposalgen_Model_MasterDevice)
        {
            $this->view->modelName      = $masterDevice->modelName;
            $this->view->manufacturerId = $masterDevice->manufacturerId;
        }

        $forms = $service->getForms(true, true, true, true, true, true);

        // If we wanted to use custom data we would need to set the views modelName and manufacturerId to the custom data values
        foreach ($forms as $formName => $form)
        {
            $this->view->$formName = $form;
        }

        $this->view->isAllowed                   = $isAllowed;
        $this->view->manufacturers               = Proposalgen_Model_Mapper_Manufacturer::getInstance()->fetchAllAvailableManufacturers();
        $this->view->tonerColors                 = Proposalgen_Model_Mapper_TonerColor::getInstance()->fetchAll();
        $this->view->masterDevice                = $masterDevice;
        $this->view->isMasterDeviceAdministrator = $this->_isAdmin;
    }

    /**
     * Validates all the main forms and saves them
     * Returns JSON, A list of errors if the forms did not validate, Or a success message if they did
     */
    public function updateMasterDeviceAction ()
    {
        $masterDeviceId = $this->_getParam('masterDeviceId', false);
        $modelName      = $this->_getParam('modelName', false);
        $manufacturerId = $this->_getParam('manufacturerId', false);
        $approve        = ($this->_getParam('approve', false) === 'true' ? true : false);

        $masterDevice = Proposalgen_Model_Mapper_MasterDevice::getInstance()->find($masterDeviceId);
        //Are they allowed to modify data? If they are creating yes, if its not a system device then yes, otherwise use their admin privilege
        $isAllowed                 = ((!$masterDevice instanceof Proposalgen_Model_MasterDevice || !$masterDevice->isSystemDevice || $this->_isAdmin) ? true : false);
        $manageMasterDeviceService = new Proposalgen_Service_ManageMasterDevices($masterDeviceId, $this->_identity->dealerId, $isAllowed, $this->_isAdmin);

        $forms                      = array();
        $suppliesErrors             = array();
        $modelAndManufacturerErrors = array();
        $formData                   = null;
        $formErrors                 = null;
        $tonersList                 = null;

        if ($this->_request->isPost())
        {
            // Validate model name and manufacturer
            if ($manufacturerId <= 0)
            {
                $modelAndManufacturerErrors['modelAndManufacturer']['errorMessages']['manufacturerId'] = "Please select a valid manufacturer";
            }

            if ($modelName == false)
            {
                $modelAndManufacturerErrors['modelAndManufacturer']['errorMessages']['modelName'] = "Please enter a model name";
            }

            $formData = $this->_request->getPost();

            foreach ($formData as $key => $form)
            {
                parse_str($formData[$key], $formData[$key]);
            }


            if (count($formData['suppliesAndService']) > 0)
            {
                if (count($formData['hardwareQuote'] > 0))
                {
                    $manageMasterDeviceService->isQuoteDevice = ($formData['hardwareQuote']['isSelling'] == '1' ? true : false);
                }

                $forms['suppliesAndService'] = $manageMasterDeviceService->getSuppliesAndServicesForm();
                $tonersList                  = $formData['suppliesAndService']['tonersList'];

                if ($formData['deviceAttributes']['launchDate'] != '')
                {
                    $launch_date                                = new Zend_Date($formData['deviceAttributes']['launchDate']);
                    $formData['deviceAttributes']['launchDate'] = $launch_date->toString('yyyy-MM-dd');
                }

                $errorMessages = $manageMasterDeviceService->validateToners($tonersList, $formData['suppliesAndService']['tonerConfigId'], $manufacturerId, $formData['suppliesAndService']['isLeased']);

                if ($errorMessages != null)
                {
                    $suppliesErrors['suppliesAndService']['errorMessages']['assignedTonersMistakes'] = $errorMessages;
                }
            }

            if (count($formData['deviceAttributes']) > 0)
            {
                $forms['deviceAttributes'] = new Proposalgen_Form_MasterDeviceManagement_DeviceAttributes(null, $isAllowed);
            }

            if (count($formData['hardwareOptimization']) > 0)
            {
                $forms['hardwareOptimization'] = new Proposalgen_Form_MasterDeviceManagement_HardwareOptimization();
            }

            if (count($formData['hardwareQuote']) > 0)
            {
                $forms['hardwareQuote'] = new Proposalgen_Form_MasterDeviceManagement_HardwareQuote();
            }

            $formErrors = array();

            foreach ($forms as $formName => $form)
            {
                $json = $manageMasterDeviceService->validateData($form, $formData[$formName], $formName);

                if ($json != null)
                {
                    $formErrors[$formName] = $json;
                }
            }
        }

        if ($formErrors || count($suppliesErrors) > 0 || count($modelAndManufacturerErrors) > 0)
        {
            $this->sendJsonError(array_merge($formErrors, $suppliesErrors, $modelAndManufacturerErrors));
        }
        else
        {
            if (count($formData['suppliesAndService']) > 0)
            {
                if (!$manageMasterDeviceService->saveSuppliesAndDeviceAttributes(array_merge($formData['suppliesAndService'], $formData['deviceAttributes'], array("manufacturerId" => $manufacturerId, "modelName" => $modelName)), $tonersList, $approve))
                {
                    $this->sendJsonError("Failed to save Supplies & Service and Device Attributes");
                }
            }

            if (count($formData['hardwareOptimization']) > 0)
            {
                if (!$manageMasterDeviceService->saveHardwareOptimization(array_merge($formData['hardwareOptimization'], array('isSelling' => $formData['hardwareQuote']['isSelling']))))
                {
                    $this->sendJsonError("Failed to save Hardware Optimization");
                }
            }

            if (count($formData['hardwareQuote']) > 0)
            {
                if (!$manageMasterDeviceService->saveHardwareQuote($formData['hardwareQuote']))
                {
                    $this->sendJsonError("Failed to save Hardware Quote");
                }
            }

            $this->sendJson(array("masterDeviceId" => $manageMasterDeviceService->masterDeviceId, "message" => "Successfully updated master device"));
        }
    }

    /**
     * One Function to rule them all,
     * One Function to validate them,
     * One Function to update them all,
     * and in the modals bind them
     *
     * Sauron handles the create and edit jqGrid buttons
     * It validates the forms
     * Returns errors if they exist
     * And calls the update functions
     */
    public function sauronAction ()
    {
        $this->_helper->layout()->disableLayout();

        if ($this->_request->isPost())
        {
            $formData       = $this->_request->getPost();
            $masterDeviceId = $this->_getParam('masterDeviceId', false);
            $formName       = $this->_getParam('formName', false);

            $masterDevice = Proposalgen_Model_Mapper_MasterDevice::getInstance()->find($masterDeviceId);
            $isAllowed    = ((!$masterDevice instanceof Proposalgen_Model_MasterDevice || !$masterDevice->isSystemDevice || $this->_isAdmin) ? true : false);


            $manageMasterDeviceService = new Proposalgen_Service_ManageMasterDevices($masterDeviceId, $this->_identity->dealerId, $isAllowed, $this->_isAdmin);

            // Each array needs to be parsed
            foreach ($formData as $key => $form)
            {
                parse_str($formData[$key], $formData[$key]);
            }

            $form = null;

            if ($formName == 'availableOptionsForm')
            {
                $form = $manageMasterDeviceService->getAvailableOptionsForm();
            }
            else if ($formName == 'hardwareConfigurationsForm')
            {
                $form = new Proposalgen_Form_MasterDeviceManagement_HardwareConfigurations(0, $masterDeviceId);
            }
            else if ($formName == 'availableTonersForm')
            {
                $form = $manageMasterDeviceService->getAvailableTonersForm(isset($formData['form']['availableTonersid']) ? $formData['form']['availableTonersid'] : null);
            }

            $formErrors = array();
            $errorArray = $manageMasterDeviceService->validateData($form, $formData['form'], $formName);

            if ($errorArray != null)
            {
                $formErrors[$formName] = $errorArray;
            }

            if ($formErrors)
            {
                $this->sendJsonError($formErrors);
            }
            else
            {
                if ($formName == 'availableOptionsForm')
                {
                    if ($manageMasterDeviceService->updateAvailableOptionsForm($formData['form']))
                    {
                        $this->sendJson("Successfully updated available options");
                    }
                    else
                    {
                        $this->sendJsonError("Failed to update available options form");
                    }
                }
                else if ($formName == 'hardwareConfigurationsForm')
                {
                    if ($manageMasterDeviceService->updateHardwareConfigurationsForm($formData['form']))
                    {
                        $this->sendJson("Successfully updated hardware configurations");
                    }
                    else
                    {
                        $this->sendJsonError("Failed to update hardware configurations form");
                    }
                }
                else if ($formName == 'availableTonersForm')
                {
                    $id = $manageMasterDeviceService->updateAvailableTonersForm($formData['form'], 0);
                    if ($id > 0)
                    {
                        $this->sendJson(array("Message" => "Successfully updated available toners form", "id" => $id));
                    }
                    else
                    {
                        $this->sendJsonError("Failed to update available toners form");
                    }
                }
            }
        }
        $this->sendJsonError("Failed to save, no form found for the form data");
    }

    /**
     * Handles the delete button in jqGrid
     */
    public function deleteAction ()
    {
        $formName = $this->_getParam('formName', false);
        $tonerId  = $this->_getParam('id', false);

        $manageMasterDeviceService = new Proposalgen_Service_ManageMasterDevices(0, $this->_identity->dealerId, $this->_isAdmin);

        if ($formName == 'availableOptions')
        {
            if ($manageMasterDeviceService->updateAvailableOptionsForm(false, $tonerId))
            {
                $this->sendJson("Successfully deleted option");
            }
            else
            {
                $this->sendJsonError("Failed to delete option");
            }
        }
        else if ($formName == 'hardwareConfigurations')
        {
            if ($manageMasterDeviceService->updateHardwareConfigurationsForm(false, $tonerId))
            {
                $this->sendJson("Successfully deleted hardware configuration");
            }
            else
            {
                $this->sendJsonError("Failed to delete hardware configuration");
            }
        }
        else if ($formName == 'availableToners')
        {
            if ($manageMasterDeviceService->updateAvailableTonersForm(false, $tonerId))
            {
                $this->sendJson("Successfully deleted toner");
            }
            else
            {
                $this->sendJsonError("Failed to delete toner");
            }
        }

        $this->sendJsonError("Failed to delete, no forms matched the data sent");
    }

    /**
     * Gets all the options available to a device
     */
    public function hardwareConfigurationListAction ()
    {
        $masterDeviceId = $this->_getParam('masterDeviceId', false);
        $searchCriteria = $this->_getParam('criteriaFilter', null);
        $searchValue    = $this->_getParam('criteria', null);

        $configurationMapper = Quotegen_Model_Mapper_DeviceConfiguration::getInstance();

        $jqGridService = new Tangent_Service_JQGrid();

        $jqGridParameters = array(
            'sidx' => $this->_getParam('sidx', 'modelName'),
            'sord' => $this->_getParam('sord', 'desc'),
            'page' => $this->_getParam('page', 1),
            'rows' => $this->_getParam('rows', 10),
        );

        $sortColumns = array(
            'id',
            'name',
            'description'
        );

        $jqGridService->setValidSortColumns($sortColumns);

        if ($jqGridService->sortingIsValid())
        {
            $jqGridService->parseJQGridPagingRequest($jqGridParameters);

            $filterCriteriaValidator = new Zend_Validate_InArray(array(
                'haystack' => $sortColumns

            ));

            // If search criteria or value is null then we don't need either one of them. Same goes if our criteria is invalid.
            if ($searchCriteria === null || $searchValue === null || !$filterCriteriaValidator->isValid($searchCriteria))
            {
                $searchCriteria = null;
                $searchValue    = null;
            }

            $jqGridService->setRecordCount(count($configurationMapper->fetchAllDeviceConfigurationByDeviceId($masterDeviceId)));

            // Validate current page number since we don't want to be out of bounds
            if ($jqGridService->getCurrentPage() < 1)
            {
                $jqGridService->setCurrentPage(1);
            }
            else if ($jqGridService->getCurrentPage() > $jqGridService->calculateTotalPages())
            {
                $jqGridService->setCurrentPage($jqGridService->calculateTotalPages());
            }

            $sortOrder = array();

            if ($jqGridService->hasGrouping())
            {
                $sortOrder[] = $jqGridService->getGroupByColumn() . ' ' . $jqGridService->getGroupBySortOrder();
            }

            if ($jqGridService->hasColumns())
            {
                $sortOrder[] = $jqGridService->getSortColumn() . ' ' . $jqGridService->getSortDirection();
            }

            $jqGridService->setRows($configurationMapper->fetchAllDeviceConfigurationByDeviceId($masterDeviceId));

            // Send back jqGrid JSON data
            $this->sendJson($jqGridService->createPagerResponseArray());
        }
        else
        {
            $this->sendJsonError('Sorting parameters are invalid.');
        }
    }

    /**
     * Reloads the Hardware Configurations Form
     */
    public function reloadHardwareConfigurationsFormAction ()
    {
        $this->_helper->layout()->disableLayout();

        $masterDeviceId = $this->_getParam('masterDeviceId', false);
        $id             = $this->_getParam('id', false);

        $deviceConfiguration = Quotegen_Model_Mapper_DeviceConfiguration::getInstance()->find($id);
        $form                = new Proposalgen_Form_MasterDeviceManagement_HardwareConfigurations($id, $masterDeviceId);

        if ($deviceConfiguration instanceof Quotegen_Model_DeviceConfiguration)
        {
            $data                                      = $deviceConfiguration->toArray();
            $data['hardwareConfigurationsname']        = $data['name'];
            $data['hardwareConfigurationsdescription'] = $data['description'];
            $form->populate($data);
        }

        $this->view->hardwareConfigurationsForm = $form;
    }

    /**
     * Assigns a toner to a master device
     */
    public function assignTonerAction ()
    {
        $this->_helper->layout()->disableLayout();

        $masterDeviceId = $this->_getParam('masterDeviceId', false);
        $tonerId        = $this->_getParam('tonerId', false);

        if ($tonerId && $masterDeviceId)
        {
            $deviceToner                   = new Proposalgen_Model_DeviceToner();
            $deviceToner->master_device_id = $masterDeviceId;
            $deviceToner->toner_id         = $tonerId;

            try
            {
                Proposalgen_Model_Mapper_DeviceToner::getInstance()->save($deviceToner);
            }
            catch (Exception $e)
            {
                Tangent_Log::logException($e);

                $this->sendJsonError("Failed to save device toner");
            }

            $this->sendJson(array("Successfully assigned toner"));
        }
        else
        {
            $this->sendJsonError("Cannot assign toner, missing required id.");
        }
    }

    /**
     * Removes the specified toner from a master device
     */
    public function removeTonerAction ()
    {
        $this->_helper->layout()->disableLayout();

        $masterDeviceId = $this->_getParam('masterDeviceId', false);
        $tonerId        = $this->_getParam('tonerId', false);

        if ($tonerId && $masterDeviceId)
        {
            $deviceToner                   = new Proposalgen_Model_DeviceToner();
            $deviceToner->master_device_id = $masterDeviceId;
            $deviceToner->toner_id         = $tonerId;

            try
            {
                Proposalgen_Model_Mapper_DeviceToner::getInstance()->delete($deviceToner);
            }
            catch (Exception $e)
            {
                Tangent_Log::logException($e);

                $this->sendJsonError("Failed to delete device toner");
            }

            $this->sendJson(array("Successfully removed toner"));
        }
        else
        {
            $this->sendJsonError("Cannot remove toner, missing required id.");
        }
    }

    /**
     *  Gets a list of all the master devices and their toners that will be affected by the toner deletion
     */
    public function affectedReplacementTonersListAction ()
    {
        $tonerId        = $this->_getParam('tonerId', false);
        $searchCriteria = $this->_getParam('criteriaFilter', null);
        $searchValue    = $this->_getParam('criteria', null);

        $tonerMapper = Proposalgen_Model_Mapper_Toner::getInstance();

        $jqGridService = new Tangent_Service_JQGrid();

        $jqGridParameters = array(
            'sidx' => $this->_getParam('sidx', 'yield'),
            'sord' => $this->_getParam('sord', 'desc'),
            'page' => $this->_getParam('page', 1),
            'rows' => $this->_getParam('rows', 10),
        );

        $sortColumns = array(
            'id',
            'name',
            'description'
        );

        $jqGridService->setValidSortColumns($sortColumns);

        if ($jqGridService->sortingIsValid())
        {
            $jqGridService->parseJQGridPagingRequest($jqGridParameters);

            $filterCriteriaValidator = new Zend_Validate_InArray(array(
                'haystack' => $sortColumns
            ));

            // If search criteria or value is null then we don't need either one of them. Same goes if our criteria is invalid.
            if ($searchCriteria === null || $searchValue === null || !$filterCriteriaValidator->isValid($searchCriteria))
            {
                $searchCriteria = null;
                $searchValue    = null;
            }
            $jqGridService->setRecordCount(count($tonerMapper->fetchListOfAffectedToners($tonerId, null, 1000)));

            // Validate current page number since we don't want to be out of bounds
            if ($jqGridService->getCurrentPage() < 1)
            {
                $jqGridService->setCurrentPage(1);
            }
            else if ($jqGridService->getCurrentPage() > $jqGridService->calculateTotalPages())
            {
                $jqGridService->setCurrentPage($jqGridService->calculateTotalPages());
            }

            $sortOrder = array();

            if ($jqGridService->hasGrouping())
            {
                $sortOrder[] = $jqGridService->getGroupByColumn() . ' ' . $jqGridService->getGroupBySortOrder();
            }

            if ($jqGridService->hasColumns())
            {
                $sortOrder[] = $jqGridService->getSortColumn() . ' ' . $jqGridService->getSortDirection();
            }

            $jqGridService->setRows($tonerMapper->fetchListOfAffectedToners($tonerId, null, 1000, 0)); // We want to show EVERYTHING, every time

            $this->sendJson($jqGridService->createPagerResponseArray());
        }
        else
        {
            $this->sendJsonError('Sorting parameters are invalid');
        }
    }

    /**
     * JSON ACTION: Handles searching for a manufacturer by name
     */
    public function searchForManufacturerAction ()
    {
        $searchTerm = $this->getParam('manufacturerName', false);
        $results    = array();

        if ($searchTerm !== false)
        {
            foreach (Proposalgen_Model_Mapper_Manufacturer::getInstance()->searchByName($searchTerm) as $manufacturer)
            {
                $results[] = array(
                    "id"   => $manufacturer->id,
                    "text" => $manufacturer->fullname
                );
            }
        }

        $this->sendJson($results);
    }
}