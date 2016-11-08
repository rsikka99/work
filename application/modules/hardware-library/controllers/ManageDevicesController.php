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
                $tonerCount = TonerMapper::getInstance()->fetchTonersAssignedToDeviceForCurrentDealer($masterDeviceId, true);
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
                $toners = TonerMapper::getInstance()->fetchTonersAssignedToDeviceForCurrentDealer($masterDeviceId);
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
     * Gets a list of all the available toners
     */
    public function availableTonersListAction ()
    {
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

            $count = $tonerMapper->countTonersForDealer($filterManufacturerId,$filterTonerSku,$filterTonerColorId);
            $jqGridService->setRecordCount($count);

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

            $jqGridService->setRows($tonerMapper->fetchTonersForDealer(
                $sortOrder,
                $jqGridService->getRecordsPerPage(),
                $startRecord,
                $filterManufacturerId,
                $filterTonerSku,
                $filterTonerColorId
            ));

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
        $rmsDeviceInstanceId = $this->_getParam('rmsDeviceInstanceId', false);

        $masterDevice = MasterDeviceMapper::getInstance()->find($masterDeviceId);
        $isAllowed    = ((!$masterDevice instanceof MasterDeviceModel || !$masterDevice->isSystemDevice || $this->_isAdmin) ? true : false);

        $service = new ManageMasterDevicesService($masterDeviceId, $this->_identity->dealerId, ($rmsUploadRowId > 0 ? true : $isAllowed), $this->_isAdmin);

        if ($rmsUploadRowId)
        {
            $rmsUploadRow = RmsUploadRowMapper::getInstance()->find($rmsUploadRowId);

            if ($rmsUploadRow instanceof RmsUploadRowModel)
            {
                $service->populate($rmsUploadRow->toArray());

                $this->view->modelName      = $rmsUploadRow->modelName;
                $this->view->manufacturerId = $rmsUploadRow->manufacturerId;
            }
        }
        else if ($rmsDeviceInstanceId) {
            /** @var \MPSToolbox\Entities\RmsDeviceInstanceEntity $rmsDeviceInstance */
            $rmsDeviceInstance = \MPSToolbox\Entities\RmsDeviceInstanceEntity::find($rmsDeviceInstanceId);
            if ($rmsDeviceInstance) {
                $manufacturer = ManufacturerMapper::getInstance()->fetchByName($rmsDeviceInstance->getManufacturer());
                $this->view->modelName      = $rmsDeviceInstance->getModelName();
                if ($manufacturer) {
                    $this->view->manufacturerId = $manufacturer->id;
                }
            }
        }
        else if ($masterDevice instanceof MasterDeviceModel)
        {
            $this->view->modelName      = $masterDevice->modelName;
            $this->view->manufacturerId = $masterDevice->manufacturerId;
        }

        $isSelling = false;
        if ($masterDevice) {
            $device = DeviceMapper::getInstance()->find([$masterDevice->id, $this->_identity->dealerId]);
            $isSelling = $device->isSelling;
        }

        $forms = $service->getForms(true, true, true, true, true, $isSelling, true, $this->_isAdmin, true);

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

    public function deleteServiceAction() {
        $masterDeviceId = $this->getParam('masterDeviceId');
        $masterDevice = MasterDeviceMapper::getInstance()->find($masterDeviceId);
        $id = $this->getParam('id');
        if ($masterDevice && $id) {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->query('DELETE FROM master_device_service WHERE id=? and masterDeviceId=?', [$id, $masterDeviceId])->execute();
        }
        $result = \MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement\DistributorsForm::getServices($masterDevice->id);
        $this->sendJson($result);
    }
    public function addServiceAction() {
        $masterDeviceId = $this->getParam('masterDeviceId');
        $sku = $this->getParam('sku');
        $masterDevice = MasterDeviceMapper::getInstance()->find($masterDeviceId);
        $db = Zend_Db_Table::getDefaultAdapter();
        if ($masterDevice && $sku) {
            $db->prepare('insert into master_device_service set masterDeviceId=?, vpn=?')->execute([$masterDeviceId, $sku]);
        }
        $result = \MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement\DistributorsForm::getServices($masterDevice->id);
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
            $str = $this->getParam('tonerIds', '');
            if (!empty($str)) {
                $tonerIds = explode(',', $str);
                $tonerErrorMessages = $manageMasterDeviceService->validateToners(
                    $tonerIds,
                    ($postData['suppliesAndService']['tonerConfigId']) ?: $masterDevice->tonerConfigId,
                    $manufacturerId,
                    $masterDeviceId
                );

                if (!$postData['suppliesAndService']['isLeased'] && $tonerErrorMessages !== true) {
                    $suppliesErrors['suppliesAndService']['errorMessages']['assignedTonersMistakes'] = $tonerErrorMessages;
                }
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
                                        "modelName"      => $modelName,
                                        "sku"            => $this->getParam('sku', ''),
                                        "UPC"            => $this->getParam('UPC', ''),
                                        "weight"            => $this->getParam('weight', ''),
                                        "tech"            => $this->getParam('tech', ''),
                                    ]
                                ),
                                $approve
                            );

                        if (!$saveSuppliesAndServiceResult)
                        {
                            $this->sendJsonError("Failed to save Supplies & Service and Device Attributes");
                        }

                        if (!empty($tonerIds))
                        {

                            $tonerIdsToAdd    = [];
                            $finalTonerIdList = [];
                            $tonerIdsToDelete = [];
                            $currentToners    = [];

                            #foreach ($db->query("select printer_consumable from oem_printing_device_consumable where printing_device={$masterDeviceId}") as $line) {
                            #    $currentToners[] = $line['printer_consumable'];
                            #}
                            $currentToners = TonerMapper::getInstance()->fetchOemSupplyIdsForDevice($masterDeviceId);

                            // Existing Toners
                            foreach ($currentToners as $id)
                            {
                                if (in_array($id, $tonerIds))
                                {
                                    $finalTonerIdList[] = $id;
                                }
                                else
                                {
                                    $tonerIdsToDelete[] = $id;
                                }
                            }

                            // Toners being added
                            foreach ($tonerIds as $id)
                            {
                                if (!in_array($id, $finalTonerIdList))
                                {
                                    $tonerIdsToAdd[]    = $id;
                                    $finalTonerIdList[] = $id;
                                }
                            }

                            /**
                             * Delete Device Toners
                             */
                            if (count($tonerIdsToDelete) > 0)
                            {
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
                                $addTonersResult = $manageMasterDeviceService->addToners($manageMasterDeviceService->masterDeviceId, $tonerIdsToAdd, $approve);
                                if (!$addTonersResult)
                                {
                                    $this->sendJsonError("Failed to assign selected toners.");
                                }
                            }

                            if ($approve) {
                                $deviceTonerMapper = DeviceTonerMapper::getInstance();
                                $arr = $deviceTonerMapper->fetchAll(['master_device_id=?'=>$masterDeviceId]);
                                foreach ($arr as $deviceTonerModel) {
                                    $deviceTonerModel->isSystemDevice = 1;
                                    $deviceTonerMapper->save($deviceTonerModel);
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
                        if (!$manageMasterDeviceService->saveHardwareQuote($validData['hardwareQuote'], $manufacturerId))
                        {
                            $this->sendJsonError("Failed to save Hardware Quote");
                        }
                    }

                    $db->commit();

                    /** @var \MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement\HistoryForm $historyForm */
                    $this->sendJson([
                        "masterDeviceId" => $manageMasterDeviceService->masterDeviceId,
                        "message" => "Successfully updated master device",
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

    public function addImageAction() {
        $baseProductId = $this->getParam('baseProductId');
        $url = $this->getParam('url');
        $result = [];
        if ($url) {
            $i = new \MPSToolbox\Services\ImageService();
            $cloud_url = $i->addImage($baseProductId, $url, \MPSToolbox\Services\ImageService::LOCAL_DEVICES_DIR, \MPSToolbox\Services\ImageService::TAG_DEVICE);
            if ($cloud_url) {
                $urls = $i->getImageUrls($baseProductId);
                $tr='';
                foreach ($urls as $id=>$url) {
                    $tr.='<tr>
                        <td><img src="'.$url.'" style="width:150px;max-height:150px">
                        <a href="javascript:;" onclick="deleteImage('.$id.')" style="color:red">delete</a></td>
                    </tr>';
                }
                if (!$tr) $tr='<tr><td>no images</td></tr>';
                $result['tr'] = $tr;
            } else {
                $result['error'] = 'Download from URL failed: '.$i->lastError;
            }
        } else {
            $result['error'] = 'No URL provided';
        }
        $this->sendJson($result);
    }

    public function deleteImageAction() {
        $baseProductId = $this->getParam('baseProductId');
        $id = $this->getParam('id');
        $i = new \MPSToolbox\Services\ImageService();
        $i->deleteImageById($id);
        $urls = $i->getImageUrls($baseProductId);
        $tr='';
        foreach ($urls as $id=>$url) {
            $tr.='<tr>
                        <td><img src="'.$url.'" style="width:150px;max-height:150px">
                        <a href="javascript:;" onclick="deleteImage('.$id.')" style="color:red">delete</a></td>
                    </tr>';
        }
        if (!$tr) $tr='<tr><td>no images</td></tr>';
        $result['tr'] = $tr;
        $this->sendJson($result);
    }

    public function showProductsAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        echo '<option value=""> - select product - </option>';

        $catId = $this->getParam('catId');
        if (!$catId) return;

        $dealerId = \MPSToolbox\Legacy\Entities\DealerEntity::getDealerId();
        $db = Zend_Db_Table::getDefaultAdapter();
        foreach ($db->query("select base_product.id, base_product.sku, manufacturers.displayname as mfg, base_product.name
                    from base_product
                    join base_sku using (id)
                    join manufacturers on base_product.manufacturerId=manufacturers.id
                    join dealer_sku on dealer_sku.skuId=base_product.id and dealer_sku.dealerId={$dealerId}
                    where base_product.categoryId={$catId}
                    group by base_product.id") as $line) {
            echo '<option value="'.$line['id'].'">'.$line['mfg'].' '.$line['name'].' ('.$line['sku'].')</option>';
        }
    }

    private function listAddons($db, $dealerId, $baseProductId) {
        $result = $db->query("
          select base_product.id, base_product.sku, manufacturers.displayname as mfg, base_product.name, dealer_sku.cost, pp.price as supplier_cost
                    from base_product
                    join dealer_sku_addon on base_product.id = dealer_sku_addon.addOnId
                    join manufacturers on base_product.manufacturerId=manufacturers.id
                    left join dealer_sku on dealer_sku.skuId=base_product.id and dealer_sku.dealerId={$dealerId}
                    left join supplier_product_price pp on base_product.id=pp.baseProductId and pp.dealerId={$dealerId} and pp.price=(select min(spp.price) from supplier_product_price spp where spp.baseProductId=pp.baseProductId and spp.dealerId={$dealerId})
                    where dealer_sku_addon.baseProductId={$baseProductId}
                    group by base_product.id
        ")->fetchAll();

        foreach ($result as $i=>$line) {
            $result[$i]['cost'] = '$'. ($result[$i]['cost']>0 ? number_format($result[$i]['cost'],2) : number_format($result[$i]['supplier_cost'],2));
        }
        return $result;
    }

    public function deleteAddonAction() {
        $baseProductId = intval($this->getParam('masterDeviceId'));
        $addOnId = intval($this->getParam('id'));
        $dealerId = \MPSToolbox\Entities\DealerEntity::getDealerId();
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->query("delete from dealer_sku_addon where baseProductId={$baseProductId} and addOnId={$addOnId} and dealerId={$dealerId}");
        $this->sendJson($this->listAddons($db, $dealerId, $baseProductId));
    }

    public function addAddonAction() {
        $baseProductId = intval($this->getParam('masterDeviceId'));
        $addOnId = intval($this->getParam('product'));
        $dealerId = \MPSToolbox\Entities\DealerEntity::getDealerId();
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->query("replace into dealer_sku_addon set baseProductId={$baseProductId}, addOnId={$addOnId}, dealerId={$dealerId}");
        $this->sendJson($this->listAddons($db, $dealerId, $baseProductId));
    }

    public function loadAddonsAction() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $dealerId = \MPSToolbox\Entities\DealerEntity::getDealerId();
        $baseProductId = intval($this->getParam('masterDeviceId'));
        $this->sendJson($this->listAddons($db, $dealerId, $baseProductId));
    }

    public function searchSupplyAction() {
        $result = ['results'=>[]];
        $mfg = intval($this->getParam('mfg'));
        $q = trim(strip_tags($this->getParam('q')));
        $db = Zend_Db_Table::getDefaultAdapter();
        $st = $db->prepare('
          select base_product.id, sku, `type`, pageYield, mlYield, toner_colors.name as color
          from base_product
            join base_printer_consumable using(id)
            left join base_printer_cartridge using(id)
             left join toner_colors on base_printer_cartridge.colorId=toner_colors.id
          where sku like ? and manufacturerId=?');
        $st->execute(["%{$q}%", $mfg]);
        foreach ($st->fetchAll() as $line) {
            $yield = '';
            if ($line['mlYield']) $yield = number_format($line['mlYield']).'ml';
            if ($line['pageYield']) $yield = number_format($line['pageYield']).' pages';

            $result['results'][] = [
                'id'=>$line['id'],
                'text'=>$line['sku'],
                'type'=>$line['type'],
                'color'=>ucwords(strtolower($line['color'])),
                'yield'=>$yield,
            ];
        }
        $this->sendJson($result);
    }

    public function addonSupplyAction() {
        $dealerId = \MPSToolbox\Entities\DealerEntity::getDealerId();
        $addOnId = $this->getParam('id');
        $deviceId = $this->getParam('deviceId');
        $db = Zend_Db_Table::getDefaultAdapter();
        $a = $db->query('select * from dealer_toner_attributes where tonerId=? and dealerId=?', [ $addOnId, $dealerId ])->fetch();
        if (!$a) {
            $db->query('replace into dealer_toner_attributes set tonerId=?, dealerId=?', [ $addOnId, $dealerId ]);
            $a = $db->query('select * from dealer_toner_attributes where tonerId=? and dealerId=?', [ $addOnId, $dealerId ])->fetch();
        }
        $db->query('replace into dealer_sku_addon set dealerId=?, baseProductId=?, addOnId=?', [$dealerId, $deviceId, $addOnId]);
        $sync_result = file_get_contents('http://proxy.mpstoolbox.com/shopify/sync_toner.php?id='.$addOnId.'&dealerId='.$dealerId.'&origin='.$_SERVER['HTTP_HOST']);
        $sync_result = file_get_contents('http://proxy.mpstoolbox.com/shopify/sync_device.php?id='.$deviceId.'&dealerId='.$dealerId.'&origin='.$_SERVER['HTTP_HOST']);
        $this->sendJson(['ok'=>true]);
    }

    public function suppliesAction() {
        $result = [
            'supplies'=>[],
            'main'=>[],
            'other'=>[],
            'compatible'=>[],
        ];

        $mfgId = $this->getParam('mfgId');
        $supplies = $this->getParam('supplies');
        $deviceId = intval($this->getParam('deviceId'));

        $dealerId = \MPSToolbox\Legacy\Entities\DealerEntity::getDealerId();

        $device = DeviceMapper::getInstance()->find([$deviceId, $dealerId]);

        $db = Zend_Db_Table::getDefaultAdapter();

        $addons = [];
        if ($deviceId) {
            foreach ($db->query("select * from dealer_sku_addon where dealerId={$dealerId} and baseProductId={$deviceId}") as $row) {
                $addons[$row['addOnId']] = $row['addOnId'];
            }
        }

        $select_sql = "select base_product.id, manufacturerId, `type`, toner_colors.name as color, manufacturers.displayname as mfg, sku, pageYield, mlYield, device_list.json as device_json, _view_dist_stock_price_grouped.cost from base_product
                join base_printer_consumable using(id)
                left join base_printer_cartridge using(id)
                left join manufacturers on base_product.manufacturerId=manufacturers.id
                left join toner_colors on base_printer_cartridge.colorId=toner_colors.id
                left join device_list on base_product.id=device_list.toner_id
                left join _view_dist_stock_price_grouped on _view_dist_stock_price_grouped.tonerId=base_product.id and dealerId={$dealerId}
";
        if (empty($supplies) && !empty($deviceId)) {
            $arr = $db->query("
            {$select_sql}
            where base_product.id in (select printer_consumable from oem_printing_device_consumable where printing_device={$deviceId})
            or (
                base_product.id in (select compatible from compatible_printer_consumable where oem in (select printer_consumable from oem_printing_device_consumable where printing_device={$deviceId}))
                and base_product.manufacturerId in (select manufacturerId from dealer_toner_vendors where dealerId={$dealerId})
            )
            ");
        } else if (!empty($supplies)) {
            $supplies = implode(',', $supplies);
            $sql = "
            {$select_sql}
            where base_product.id in ({$supplies})
            or (
                base_product.id in (select compatible from compatible_printer_consumable where oem in ({$supplies}))
                and base_product.manufacturerId in (select manufacturerId from dealer_toner_vendors where dealerId={$dealerId})
            )";
            $arr =  $db->query($sql);
        }

        foreach ($arr as $line) {

            $devices='';
            $is_span=false;
            foreach (json_decode("[{$line['device_json']}]", true) as $d) {
                if ($devices) $devices.=', ';
                if (!$is_span && (strlen(strip_tags($devices))>50)) {
                    $devices.='<a href="javascript:;" onclick="$(this).hide();$(\'#span-'.$line['id'].'\').show()">...</a><span style="display:none" id="span-'.$line['id'].'">';
                    $is_span=true;
                }
                $devices.='<a href="javascript:;" data-id="'.$d['id'].'" class="edit-device">'.$d['name'].'</a>';
            }
            if ($is_span) $devices.='</span>';

            $line['color'] = str_replace([
                'Black',
                'Cyan',
                'Magenta',
                'Yellow',
            ],[
                '<i class="fa fa-fw fa-2x fa-toner-color-black" title="Black"></i>Black',
                '<i class="fa fa-fw fa-2x fa-toner-color-cyan" title="Cyan"></i>Cyan',
                '<i class="fa fa-fw fa-2x fa-toner-color-magenta" title="Magenta"></i>Magenta',
                '<i class="fa fa-fw fa-2x fa-toner-color-yellow" title="Yellow"></i>Yellow',
            ], ucfirst(strtolower($line['color'])));

            $yield = '';
            if ($line['mlYield']) $yield = number_format($line['mlYield']).'ml';
            if ($line['pageYield']) $yield = number_format($line['pageYield']).' pages';

            if ($line['manufacturerId']==$mfgId) {
                $result['supplies'][] = $line['id'];

                $actions='';
                if ($this->_isAdmin) {
                    $actions.='<a href="javascript:;" class="unassign-supply" data-id="'.$line['id'].'">Unassign</a>&nbsp;&nbsp;&nbsp;';
                    if ($device->isSelling && !isset($addons[$line['id']])) {
                        $actions.='<a href="javascript:;" class="addon-supply" data-id="'.$line['id'].'">Add to Add-ons</a>';
                    }
                }

                switch ($line['type']) {
                    case 'Inkjet Cartridge':
                    case 'Laser Cartridge':
                    case 'Printhead':
                    case 'Monochrome Toner':
                    case 'Color Toner':
                        $result['main'][] = [
                            $line['color'],
                            '<a href="javascript:;" class="edit-supply" data-id="'.$line['id'].'">'.$line['sku'].'</a>',
                            $devices,
                            $yield,
                            $line['cost']?'<div class="text-right">$'.number_format($line['cost'],2).'</div>':'<div style="width:100%;text-align:center"><i class="fa fa-fw fa-warning text-danger" title="No cost assigned"></i></div>',
                            $actions
                        ];
                        break;
                    default:
                        $result['other'][] = [
                            $line['type']?$line['type']:'Unknown',
                            $line['color'],
                            '<a href="javascript:;" class="edit-supply" data-id="'.$line['id'].'">'.$line['sku'].'</a>',
                            $devices,
                            $yield,
                            $line['cost']?'<div class="text-right">$'.number_format($line['cost'],2).'</div>':'<div style="width:100%;text-align:center"><i class="fa fa-fw fa-warning text-warning" title="No cost assigned"></i></div>',
                            $actions
                        ];
                        break;
                }
            } else {
                $actions='';
                if ($this->_isAdmin) {
                    if ($device->isSelling && !isset($addons[$line['id']])) {
                        $actions.='<a href="javascript:;" class="addon-supply" data-id="'.$line['id'].'">Add to Add-ons</a>';
                    }
                }
                $result['compatible'][] = [
                    $line['mfg'],
                    $line['type']?$line['type']:'Unknown',
                    $line['color'],
                    '<a href="javascript:;" class="edit-supply" data-id="'.$line['id'].'">'.$line['sku'].'</a>',
                    $yield,
                    $line['cost']?'<div class="text-right">$'.number_format($line['cost'],2).'</div>':'<div style="width:100%;text-align:center"><i class="fa fa-fw fa-warning text-warning" title="No cost assigned"></i></div>',
                    $actions
                ];
            }
        }

        $this->sendJson($result);
    }

}
