<?php

use MPSToolbox\Legacy\Models\Acl\AppAclModel;
use MPSToolbox\Legacy\Models\Acl\ProposalgenAclModel;
use MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement\DeviceAttributesForm;
use MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement\DeviceImageForm;
use MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement\HardwareConfigurationsForm;
use MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement\HardwareOptimizationForm;
use MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement\HardwareQuoteForm;
use MPSToolbox\Legacy\Modules\HardwareLibrary\Services\ManageMasterDevicesService;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DeviceTonerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\ManufacturerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\MasterDeviceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerColorMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\RmsUploadRowMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceTonerModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\ManufacturerModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\RmsUploadRowModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\OptionMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceOptionMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceConfigurationMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceConfigurationModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceOptionModel;
use Tangent\Controller\Action;
use Tangent\Logger\Logger;
use Tangent\Service\JQGrid;

/**
 * Class HardwareLibrary_ManageDevicesController
 */
class HardwareLibrary_ManageDevicesController extends Action
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
        $this->_isAdmin             = $this->view->IsAllowed(ProposalgenAclModel::RESOURCE_PROPOSALGEN_ADMIN_SAVEANDAPPROVE, AppAclModel::PRIVILEGE_ADMIN);
        $this->view->isAdmin        = $this->_isAdmin;
    }

    /**
     * Gets a  list of all the assigned toners for a specific Master Device
     */
    public function assignedTonerListAction ()
    {
        $masterDeviceId = $this->_getParam('masterDeviceId', false);
        $tonerList      = $this->_getParam('tonersList', false);
        $firstLoad      = $this->_getParam('firstLoad', false);

        $jqGridService    = new JQGrid();
        $jqGridParameters = [
            'sidx' => $this->_getParam('sidx', 'id'),
            'sord' => $this->_getParam('sord', 'desc'),
            'page' => $this->_getParam('page', 1),
            'rows' => $this->_getParam('rows', 10),
        ];

        $jqGridService->setValidSortColumns([
            'id',
            'tonerColorId',
            'sku',
            'dealerSku',
            'manufacturer',
            'yield',
            'dealerCost',
            'device_list',
        ]);

        $jqGridService->parseJQGridPagingRequest($jqGridParameters);


        if ($jqGridService->sortingIsValid())
        {
            $sortOrder = [];
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
                $tonerCount = TonerMapper::getInstance()->fetchListOfToners($tonerList, $masterDeviceId, null, true);
            }
            else if ($masterDeviceId !== false && $masterDeviceId !== 0 && $firstLoad)
            {
                $tonerCount = TonerMapper::getInstance()->fetchTonersAssignedToDeviceWithMachineCompatibility($masterDeviceId, null, true);
            }

            $jqGridService->setRecordCount($tonerCount);

            /**
             * If we passed a list of toners, it means those are all the toners assigned to a device.
             * Otherwise we'll fetch the toners that are assigned to the device already.
             */
            if ($tonerList)
            {
                $toners = TonerMapper::getInstance()->fetchListOfToners($tonerList, $masterDeviceId, $sortOrder);
            }
            else if ($masterDeviceId !== false && $masterDeviceId !== 0 && $firstLoad)
            {
                $toners = TonerMapper::getInstance()->fetchTonersAssignedToDeviceWithMachineCompatibility($masterDeviceId, $sortOrder);
            }

            $jqGridService->setRows($toners);

            // Send back jqGrid JSON data
            $this->sendJson($jqGridService->createPagerResponseArray());
        }
        else
        {
            $this->_response->setHttpResponseCode(500);
            $this->sendJson([
                'error' => sprintf('Sort index "%s" is not a valid sorting index.', $jqGridService->getSortColumn())
            ]);
        }
    }

    /**
     * Gets a list of all the available toners for a specific master device
     */
    public function availableTonersListAction ()
    {
        $masterDeviceId     = $this->_getParam('masterDeviceId', false);
        $tonerColorConfigId = $this->_getParam('tonerColorConfigId', false);

        $filterManufacturerId = $this->_getParam('filterManufacturerId', false);
        $filterTonerSku       = $this->_getParam('filterTonerSku', false);
        $filterTonerColorId   = $this->_getParam('filterTonerColorId', false);

        $jqGridParameters = [
            'sidx' => $this->_getParam('sidx', 'id'),
            'sord' => $this->_getParam('sord', 'desc'),
            'page' => $this->_getParam('page', 1),
            'rows' => $this->_getParam('rows', 10),
        ];

        $jqGridService = new JQGrid();

        $jqGridService->setValidSortColumns([
            'id',
            'tonerColorId',
            'sku',
            'dealerSku',
            'manufacturer',
            'yield',
            'dealerCost',
            'device_list',
        ]);
        $jqGridService->parseJQGridPagingRequest($jqGridParameters);

        $tonerMapper = TonerMapper::getInstance();

        if ($jqGridService->sortingIsValid())
        {

            if ($tonerColorConfigId)
            {
                $jqGridService->setRecordCount(count($tonerMapper->fetchTonersWithMachineCompatibilityUsingColorConfigId(
                    $tonerColorConfigId,
                    null,
                    10000,
                    0,
                    $filterManufacturerId,
                    $filterTonerSku,
                    $filterTonerColorId,
                    $masterDeviceId
                )));
            }
            else
            {
                $jqGridService->setRecordCount(count($tonerMapper->fetchTonersWithMachineCompatibilityUsingColorId(
                    null,
                    10000,
                    0,
                    $filterManufacturerId,
                    $filterTonerSku,
                    $filterTonerColorId,
                    $masterDeviceId
                )));
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

            $sortOrder = [];

            if ($jqGridService->hasGrouping())
            {
                $sortOrder[] = $jqGridService->getGroupByColumn() . ' ' . $jqGridService->getGroupBySortOrder();
            }

            if ($jqGridService->hasColumns())
            {
                if (strcasecmp($jqGridService->getSortColumn(), 'dealerCost') === 0)
                {
                    $sortOrder[] = $jqGridService->getSortColumn() . ' ' . $jqGridService->getSortDirection();
                    $sortOrder[] = 'toners.cost' . ' ' . $jqGridService->getSortDirection();
                }
                $sortOrder[] = $jqGridService->getSortColumn() . ' ' . $jqGridService->getSortDirection();
            }

            if ($tonerColorConfigId)
            {
                $jqGridService->setRows($tonerMapper->fetchTonersWithMachineCompatibilityUsingColorConfigId(
                    $tonerColorConfigId,
                    $sortOrder,
                    $jqGridService->getRecordsPerPage(),
                    $startRecord,
                    $filterManufacturerId,
                    $filterTonerSku,
                    $filterTonerColorId,
                    $masterDeviceId
                ));
            }
            else
            {
                $jqGridService->setRows($tonerMapper->fetchTonersWithMachineCompatibilityUsingColorId(
                    $sortOrder,
                    $jqGridService->getRecordsPerPage(),
                    $startRecord,
                    $filterManufacturerId,
                    $filterTonerSku,
                    $filterTonerColorId,
                    $masterDeviceId
                ));

            }

            // Send back jqGrid JSON data
            $this->sendJson($jqGridService->createPagerResponseArray());
        }
        else
        {
            $this->_response->setHttpResponseCode(500);
            $this->sendJson([
                'error' => sprintf('Sort index "%s" is not a valid sorting index.', $jqGridService->getSortColumn())
            ]);
        }
    }

    /**
     * Assigns or unassigns an option when on the available options page
     */
    public function assignAvailableOptionAction ()
    {
        $identity = \Zend_Auth::getInstance()->getIdentity();
        $db = \Zend_Db_Table::getDefaultAdapter();

        $json = "Failed to assign or unassign option";
        if ($this->_request->isPost())
        {
            $optionId       = $this->_request->getParam('optionId', false);
            $masterDeviceId = $this->_request->getParam('masterDeviceId', false);
            if ($optionId && $masterDeviceId)
            {
                $device = DeviceMapper::getInstance()->find([$masterDeviceId, $this->_identity->dealerId]);
                $option = OptionMapper::getInstance()->find($optionId);
                if ($device && $option)
                {
                    $deviceOptionMapper = DeviceOptionMapper::getInstance();
                    $deviceOption       = $deviceOptionMapper->find([$masterDeviceId, $optionId]);
                    if ($deviceOption)
                    {
                        $deviceOptionMapper->delete($deviceOption);
                        $json = "Successfully unassigned option";

                        $sql = "insert into `history` set `userId`={$identity->id}, `masterDeviceId`={$masterDeviceId}, `action`='Unassigned Option: {$option->name}'";
                        $db->query($sql);
                    }
                    else
                    {
                        $deviceOption                   = new DeviceOptionModel();
                        $deviceOption->masterDeviceId   = $masterDeviceId;
                        $deviceOption->dealerId         = $this->_identity->dealerId;
                        $deviceOption->optionId         = $optionId;
                        $deviceOption->includedQuantity = 0;
                        $deviceOptionMapper->insert($deviceOption);
                        $json = "Successfully assigned option";

                        $sql = "insert into `history` set `userId`={$identity->id}, `masterDeviceId`={$masterDeviceId}, `action`='Assigned Option: {$option->name}'";
                        $db->query($sql);
                    }
                }
            }
        }
        $this->sendJson($json);

    }

    /**
     * creates the service, tells it which forms we want to use and displays them
     */
    public function manageMasterDevicesAction ()
    {
        $this->_helper->layout()->disableLayout();
        $masterDeviceId = $this->_getParam('masterDeviceId', false);
        $rmsUploadRowId = $this->_getParam('rmsUploadRowId', false);

        $masterDevice = MasterDeviceMapper::getInstance()->find($masterDeviceId);
        $isAllowed    = ((!$masterDevice instanceof MasterDeviceModel || !$masterDevice->isSystemDevice || $this->_isAdmin) ? true : false);

        $service = new ManageMasterDevicesService($masterDeviceId, $this->_identity->dealerId, ($rmsUploadRowId > 0 ? true : $isAllowed), $this->_isAdmin);

        if ($rmsUploadRowId > 0)
        {
            $rmsUploadRow = RmsUploadRowMapper::getInstance()->find($rmsUploadRowId);

            if ($rmsUploadRow instanceof RmsUploadRowModel)
            {
                $service->populate($rmsUploadRow->toArray());

                $this->view->modelName      = $rmsUploadRow->modelName;
                $this->view->manufacturerId = $rmsUploadRow->manufacturerId;
            }
        }
        else if ($masterDevice instanceof MasterDeviceModel)
        {
            $this->view->modelName      = $masterDevice->modelName;
            $this->view->manufacturerId = $masterDevice->manufacturerId;
        }

        $forms = $service->getForms(true, true, true, true, true, true, true);

        // If we wanted to use custom data we would need to set the views modelName and manufacturerId to the custom data values
        foreach ($forms as $formName => $form)
        {
            $this->view->$formName = $form;
        }

        $this->view->isAllowed                   = $isAllowed;
        $this->view->manufacturers               = ManufacturerMapper::getInstance()->fetchAllAvailableManufacturers();
        $this->view->tonerColors                 = TonerColorMapper::getInstance()->fetchAll();
        $this->view->masterDevice                = $masterDevice;
        $this->view->isMasterDeviceAdministrator = $this->_isAdmin;
    }

    public function imageAction() {
        $masterDeviceId = $this->_getParam('id', false);
        $masterDevice = MasterDeviceMapper::getInstance()->find($masterDeviceId);
        if (!$masterDevice) {
            $this->sendJsonError('not found');
            return;
        }

        $isAllowed = ((!$masterDevice instanceof MasterDeviceModel || !$masterDevice->isSystemDevice || $this->_isAdmin) ? true : false);
        if (!$isAllowed) {
            $this->sendJsonError('not allowed');
            return;
        }
        $manageMasterDeviceService = new ManageMasterDevicesService($masterDeviceId, $this->_identity->dealerId, $isAllowed, $this->_isAdmin);
        foreach ($_FILES as $upload) {
            $manageMasterDeviceService->uploadImage($masterDevice, $upload);
            MasterDeviceMapper::getInstance()->save($masterDevice);
        }

        $result = array(
           'filename'=>$masterDevice->imageFile
        );
        $this->sendJson($result);
    }

    /**
     * Validates all the main forms and saves them
     * Returns JSON, A list of errors if the forms did not validate, Or a success message if they did
     */
    public function updateMasterDeviceAction ()
    {
        if ($this->_request->isPost())
        {
            $postData = $this->_request->getPost();

            $masterDeviceId = $this->getParam('masterDeviceId', false);
            $modelName      = $this->getParam('modelName', false);
            $manufacturerId = $this->getParam('manufacturerId', false);
            $approve        = ($this->getParam('approve', false) === 'true' ? true : false);

            $masterDevice = MasterDeviceMapper::getInstance()->find($masterDeviceId);

            // Are they allowed to modify data? If they are creating yes, if its not a system device then yes, otherwise use their admin privilege
            $isAllowed                 = ((!$masterDevice instanceof MasterDeviceModel || !$masterDevice->isSystemDevice || $this->_isAdmin) ? true : false);
            $manageMasterDeviceService = new ManageMasterDevicesService($masterDeviceId, $this->_identity->dealerId, $isAllowed, $this->_isAdmin);

            $forms                      = [];
            $suppliesErrors             = [];
            $modelAndManufacturerErrors = [];
            $formErrors                 = null;
            $tonersList                 = null;

            // Validate model name and manufacturer
            if ($manufacturerId <= 0)
            {
                $modelAndManufacturerErrors['modelAndManufacturer']['errorMessages']['manufacturerId'] = "Please select a valid manufacturer";
            }

            if ($modelName == false)
            {
                $modelAndManufacturerErrors['modelAndManufacturer']['errorMessages']['modelName'] = "Please enter a model name";
            }


            foreach ($postData as $key => $form)
            {
                parse_str($postData[$key], $postData[$key]);
            }

            /**
             * Validate Toners
             */
            $tonerIds = explode(',', $this->getParam('tonerIds', ''));

            $tonerErrorMessages = $manageMasterDeviceService->validateToners(
                $tonerIds,
                ($postData['suppliesAndService']['tonerConfigId']) ?: $masterDevice->tonerConfigId,
                $manufacturerId,
                $masterDeviceId
            );

            if (!$postData['suppliesAndService']['isLeased'] && $tonerErrorMessages !== true)
            {
                $suppliesErrors['suppliesAndService']['errorMessages']['assignedTonersMistakes'] = $tonerErrorMessages;
            }


            /**
             * Validate Supplies And Service Form
             */
            if (count($postData['suppliesAndService']) > 0)
            {
                if (count($postData['hardwareQuote']) > 0)
                {
                    $manageMasterDeviceService->isQuoteDevice = ($postData['hardwareQuote']['isSelling'] == '1' ? true : false);
                }

                $forms['suppliesAndService'] = $manageMasterDeviceService->getSuppliesAndServicesForm();


            }

            /**
             * Validate Device Attributes Form
             */
            if (count($postData['deviceAttributes']) > 0)
            {
                $forms['deviceAttributes'] = new DeviceAttributesForm(null, $isAllowed);
            }

            /**
             * Validate Device Image Form
             */
            if (count($postData['deviceImage']) > 0)
            {
                $forms['deviceImage'] = new DeviceImageForm(null, $isAllowed);
            }

            /**
             * Validate Hardware Optimization Form
             */
            if (count($postData['hardwareOptimization']) > 0)
            {
                $forms['hardwareOptimization'] = new HardwareOptimizationForm();
            }

            /**
             * Validate Hardware Quote Form
             */
            if (count($postData['hardwareQuote']) > 0)
            {
                $forms['hardwareQuote'] = new HardwareQuoteForm();
            }

            $formErrors = [];
            $validData  = [];

            foreach ($forms as $formName => $form)
            {
                $response = $manageMasterDeviceService->validateData($form, $postData[$formName], $formName);

                if (isset($response['errorMessages']))
                {
                    $formErrors[$formName] = $response;
                }
                else
                {
                    $validData[$formName] = $response;
                }
            }

            /**
             * Check to see if we had errors. If not lets save!
             */
            if ($formErrors || count($suppliesErrors) > 0 || count($modelAndManufacturerErrors) > 0)
            {
                $this->sendJsonError(array_merge($formErrors, $suppliesErrors, $modelAndManufacturerErrors));
            }
            else
            {
                $db = Zend_Db_Table::getDefaultAdapter();
                try
                {
                    $db->beginTransaction();
                    if (count($validData['suppliesAndService']) > 0)
                    {
                        if (!isset($validData['deviceAttributes'])) $validData['deviceAttributes'] = [];
                        if (!isset($validData['deviceImage'])) $validData['deviceImage'] = [];
                        $saveSuppliesAndServiceResult = $manageMasterDeviceService
                            ->saveSuppliesAndDeviceAttributes(
                                array_merge(
                                    $validData['suppliesAndService'],
                                    $validData['deviceAttributes'],
                                    $validData['deviceImage'],
                                    [
                                        "manufacturerId" => $manufacturerId,
                                        "modelName"      => $modelName
                                    ]
                                ),
                                $approve
                            );

                        if (!$saveSuppliesAndServiceResult)
                        {
                            $this->sendJsonError("Failed to save Supplies & Service and Device Attributes");
                        }

                        if ($tonerIds)
                        {
                            $tonerIdsToAdd    = [];
                            $finalTonerIdList = [];
                            $tonerIdsToDelete = [];
                            $currentToners    = [];
                            $newTonerIds      = [];

                            foreach (TonerMapper::getInstance()->find($tonerIds) as $toner)
                            {
                                $newTonerIds[(int)$toner->id] = $toner;
                            }

                            //foreach (TonerMapper::getInstance()->fetchTonersAssignedToDevice($masterDeviceId) as $toner)
                            foreach (TonerMapper::getInstance()->fetchTonersAssignedToDeviceWithMachineCompatibility($masterDeviceId) as $toner)
                            {

                                $currentToners[(int)$toner->id] = $toner;
                            }

                            // Existing Toners
                            foreach ($currentToners as $toner)
                            {
                                if (isset($newTonerIds[(int)$toner->id]))
                                {
                                    $finalTonerIdList[(int)$toner->id] = true;
                                }
                                else
                                {
                                    $tonerIdsToDelete[(int)$toner->id] = true;
                                }
                            }

                            // Toners being added
                            foreach ($newTonerIds as $toner)
                            {
                                if (!isset($finalTonerIdList[(int)$toner->id]))
                                {
                                    $tonerIdsToAdd[(int)$toner->id]    = true;
                                    $finalTonerIdList[(int)$toner->id] = true;
                                }
                            }

                            /**
                             * Delete Device Toners
                             */
                            if (count($tonerIdsToDelete) > 0)
                            {
                                $tonerIdsToDelete   = array_keys($tonerIdsToDelete);
                                $removeTonersResult = $manageMasterDeviceService->removeToners($manageMasterDeviceService->masterDeviceId, $tonerIdsToDelete);
                                if (!$removeTonersResult)
                                {
                                    $this->sendJsonError("Failed to unassign selected toners.");
                                }
                            }

                            /**
                             * Add Device Toners
                             */
                            if (count($tonerIdsToAdd) > 0)
                            {
                                $tonerIdsToAdd   = array_keys($tonerIdsToAdd);
                                $addTonersResult = $manageMasterDeviceService->addToners($manageMasterDeviceService->masterDeviceId, $tonerIdsToAdd, $approve);
                                if (!$addTonersResult)
                                {
                                    $this->sendJsonError("Failed to assign selected toners.");
                                }
                            }
                        }

                        $manageMasterDeviceService->recalculateMaximumRecommendedMonthlyPageVolume();
                    }

                    if (count($validData['hardwareOptimization']) > 0)
                    {
                        if (!$manageMasterDeviceService->saveHardwareOptimization(
                            array_merge(
                                $validData['hardwareOptimization'],
                                [
                                    'isSelling' => $validData['hardwareQuote']['isSelling']
                                ]
                            ))
                        )
                        {
                            $this->sendJsonError("Failed to save Hardware Optimization");
                        }
                    }

                    if (count($validData['hardwareQuote']) > 0)
                    {
                        if (!$manageMasterDeviceService->saveHardwareQuote($validData['hardwareQuote']))
                        {
                            $this->sendJsonError("Failed to save Hardware Quote");
                        }
                    }

                    $db->commit();

                    /** @var \MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement\HistoryForm $historyForm */
                    $this->sendJson([
                        "masterDeviceId" => $manageMasterDeviceService->masterDeviceId,
                        "message" => "Successfully updated master device",
                        'imageFile' => $masterDevice->imageFile,
                        'history' => \MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement\HistoryForm::getHistory($masterDevice)
                    ]);
                }
                catch (Exception $e)
                {
                    $db->rollBack();
                    Logger::logException($e);
                }
            }
        }

        $this->sendJsonError('This method only accepts POST');
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

            $masterDevice = MasterDeviceMapper::getInstance()->find($masterDeviceId);
            $isAllowed    = ((!$masterDevice instanceof MasterDeviceModel || !$masterDevice->isSystemDevice || $this->_isAdmin) ? true : false);


            $manageMasterDeviceService = new ManageMasterDevicesService($masterDeviceId, $this->_identity->dealerId, $isAllowed, $this->_isAdmin);

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
                $form = new HardwareConfigurationsForm(0, $masterDeviceId);
            }
            else if ($formName == 'availableTonersForm')
            {
                $form = $manageMasterDeviceService->getAvailableTonersForm(isset($formData['form']['availableTonersId']) ? $formData['form']['availableTonersId'] : null);
            }

            $formErrors = [];
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
                    $id = $manageMasterDeviceService->updateAvailableTonersForm($formData['form'], 0, $masterDevice);
                    if ($id > 0)
                    {
                        $this->sendJson(["Message" => "Successfully updated available toners form", "id" => $id]);
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

        $manageMasterDeviceService = new ManageMasterDevicesService(0, $this->_identity->dealerId, $this->_isAdmin);

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

        $configurationMapper = DeviceConfigurationMapper::getInstance();

        $jqGridService = new JQGrid();

        $jqGridParameters = [
            'sidx' => $this->_getParam('sidx', 'modelName'),
            'sord' => $this->_getParam('sord', 'desc'),
            'page' => $this->_getParam('page', 1),
            'rows' => $this->_getParam('rows', 10),
        ];

        $sortColumns = [
            'id',
            'name',
            'description',
            'modelName',
        ];

        $jqGridService->setValidSortColumns($sortColumns);
        $jqGridService->parseJQGridPagingRequest($jqGridParameters);

        if ($jqGridService->sortingIsValid())
        {

            $filterCriteriaValidator = new Zend_Validate_InArray([
                'haystack' => $sortColumns

            ]);

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

            $sortOrder = [];

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
            $this->sendJsonError(sprintf('Sort index "%s" is not a valid sorting index.', $jqGridService->getSortColumn()));
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

        $deviceConfiguration = DeviceConfigurationMapper::getInstance()->find($id);
        $form                = new HardwareConfigurationsForm($id, $masterDeviceId);

        if ($deviceConfiguration instanceof DeviceConfigurationModel)
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
            $deviceToner                   = new DeviceTonerModel();
            $deviceToner->master_device_id = $masterDeviceId;
            $deviceToner->toner_id         = $tonerId;

            try
            {
                DeviceTonerMapper::getInstance()->save($deviceToner);
            }
            catch (Exception $e)
            {
                Logger::logException($e);

                $this->sendJsonError("Failed to save device toner");
            }

            $this->sendJson(["Successfully assigned toner"]);
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
            $deviceToner                   = new DeviceTonerModel();
            $deviceToner->master_device_id = $masterDeviceId;
            $deviceToner->toner_id         = $tonerId;

            try
            {
                DeviceTonerMapper::getInstance()->delete($deviceToner);
            }
            catch (Exception $e)
            {
                Logger::logException($e);

                $this->sendJsonError("Failed to delete device toner");
            }

            $this->sendJson(["Successfully removed toner"]);
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

        $tonerMapper = TonerMapper::getInstance();

        $jqGridService = new JQGrid();

        $jqGridParameters = [
            'sidx' => $this->_getParam('sidx', 'yield'),
            'sord' => $this->_getParam('sord', 'desc'),
            'page' => $this->_getParam('page', 1),
            'rows' => $this->_getParam('rows', 10),
        ];

        $sortColumns = [
            'id',
            'name',
            'description'
        ];

        $jqGridService->setValidSortColumns($sortColumns);

        if ($jqGridService->sortingIsValid())
        {
            $jqGridService->parseJQGridPagingRequest($jqGridParameters);

            $filterCriteriaValidator = new Zend_Validate_InArray([
                'haystack' => $sortColumns
            ]);

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

            $sortOrder = [];

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
            $this->sendJsonError(sprintf('Sort index "%s" is not a valid sorting index.', $jqGridService->getSortColumn()));
        }
    }

    /**
     * JSON ACTION: Handles searching for a manufacturer by name
     */
    public function searchForManufacturerAction ()
    {
        $results        = [];
        $manufacturerId = $this->getParam('manufacturerId', false);
        if ($manufacturerId)
        {
            $manufacturer = ManufacturerMapper::getInstance()->find($manufacturerId);
            if ($manufacturer instanceof ManufacturerModel)
            {
                $results = [
                    "id"   => $manufacturer->id,
                    "text" => $manufacturer->fullname
                ];
            }
        }
        else
        {
            $searchTerm = $this->getParam('manufacturerName', false);


            if ($searchTerm !== false)
            {
                foreach (ManufacturerMapper::getInstance()->searchByName($searchTerm) as $manufacturer)
                {
                    $results[] = [
                        "id"   => $manufacturer->id,
                        "text" => $manufacturer->fullname
                    ];
                }
            }
        }
        $this->sendJson($results);
    }

    public function deleteMasterDeviceAction ()
    {
        $masterDeviceId = $this->getPAram('masterDeviceId', false);
    }
}