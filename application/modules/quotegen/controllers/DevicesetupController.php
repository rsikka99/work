<?php
use MPSToolbox\Legacy\Models\Acl\AppAclModel;
use MPSToolbox\Legacy\Models\Acl\AdminAclModel;
use MPSToolbox\Legacy\Models\Acl\QuoteGeneratorAclModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DeviceTonerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\ManufacturerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\MasterDeviceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DealerMasterDeviceAttributeModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceTonerModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerConfigModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerColorModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Forms\DeviceSetupForm;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\OptionMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceOptionMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceConfigurationMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceOptionModel;
use Tangent\Controller\Action;
use Tangent\Service\JQGrid;

/**
 * Class Quotegen_DevicesetupController
 */
class Quotegen_DevicesetupController extends Action
{

    public function init ()
    {
        /* Initialize action controller here */
    }

    /**
     * Displays all devices
     */
    public function indexAction ()
    {
        $this->_pageTitle    = ['All Devices'];
        $this->isAdmin       = $this->view->IsAllowed(AdminAclModel::RESOURCE_ADMIN_TONER_WILDCARD, AppAclModel::PRIVILEGE_ADMIN);
        $this->view->isAdmin = $this->isAdmin;
    }

    /**
     * Gets the list of devices for the hardware library "all devices" page
     */
    public function allDevicesListAction ()
    {
        $postData          = $this->getAllParams();
        $filterCanSell     = ($this->_getParam('filterCanSell', null) == 'true') ? true : false;
        $filterUnapproved  = ($this->_getParam('filterUnapproved', null) == 'true') ? true : false;
        $filterSearchIndex = $this->_getParam('filterSearchIndex', null);
        $filterSearchValue = $this->_getParam('filterSearchValue', null);
        $columnFactory     = new \Tangent\Grid\Order\ColumnFactory([
            'deviceName', 'oemSku', 'dealerSku', 'isSystemDevice'
        ]);

        $gridRequest        = new \Tangent\Grid\Request\JqGridRequest($postData, $columnFactory);
        $gridResponse       = new \Tangent\Grid\Response\JqGridResponse($gridRequest);
        $masterDeviceMapper = MasterDeviceMapper::getInstance();
        $dataAdapter        = new \MPSToolbox\Grid\DataAdapter\MasterDeviceDataAdapter($masterDeviceMapper, $filterCanSell);

        /**
         * Setup Filters
         */
        $filterCriteriaValidator = new Zend_Validate_InArray(['haystack' => ['deviceName', 'oemSku', 'dealerSku']]);


        if ($filterSearchIndex !== null && $filterSearchValue !== null && $filterCriteriaValidator->isValid($filterSearchIndex))
        {
            $dataAdapter->addFilter(new \Tangent\Grid\Filter\Contains($filterSearchIndex, $filterSearchValue));
        }

        if ($filterUnapproved)
        {
            $dataAdapter->addFilter(new \Tangent\Grid\Filter\IsNot('isSystemDevice', true));
        }

        /**
         * Setup grid
         */
        $gridService = new \Tangent\Grid\Grid($gridRequest, $gridResponse, $dataAdapter);
        $this->sendJson($gridService->getGridResponseAsArray());

        return;
    }

    /**
     * Create new device
     */
    public function createAction ()
    {
        $where          = null;
        $filter         = null;
        $view_filter    = null;
        $assignedToners = null;
        $txtCriteria    = null;
        $cboCriteria    = null;

        // Populate manufacturers drop down
        $manufacturers = ManufacturerMapper::getInstance()->fetchAll();

        // Create a new form with the mode and roles set
        $form = new DeviceSetupForm();

        // Make sure we are posting data
        $request = $this->getRequest();
        if ($request->isPost())
        {
            // Get the post data
            $values         = $request->getPost();
            $assignedToners = $values ['toner_array'];

            // If we cancelled we don't need to validate anything
            if (!isset($values ['cancel']))
            {
                try
                {
                    // Filter
                    if (isset($values ['btnSearch']))
                    {
                        // Toners Search Filter
                        $filter = $values ['criteria_filter'];

                        $txtCriteria             = $values ['txtCriteria'];
                        $this->view->txtCriteria = $txtCriteria;

                        $cboCriteria             = $values ['cboCriteria'];
                        $this->view->cboCriteria = $cboCriteria;

                        if ($filter == 'sku')
                        {
                            $where = array_merge((array)$where, [
                                'sku LIKE ( ? )' => '%' . $txtCriteria . '%'
                            ]);
                        }
                        else
                        {
                            $where = array_merge((array)$where, [
                                'manufacturerid = ?' => $cboCriteria
                            ]);
                        }

                        $form->populate($values);
                    }

                    // Clear Filter
                    else if (isset($values ['btnClearSearch']))
                    {
                        $view_filter = null;
                        $form->populate($values);
                    }

                    else if ($form->isValid($values))
                    {
                        $toner_config_id = $values ['tonerConfigId'];
                        $toners          = explode(',', $values ['toner_array']);
                        foreach ($toners as $key => $toner_id)
                        {
                            $toners[$key] = str_replace("'", "", $toner_id);
                        }

                        // An array of required toners
                        $requiredToners = TonerConfigModel::getRequiredTonersForTonerConfig($toner_config_id);

                        // An array for counting each color
                        $tonerCounts = [
                            TonerColorModel::BLACK       => 0,
                            TonerColorModel::CYAN        => 0,
                            TonerColorModel::MAGENTA     => 0,
                            TonerColorModel::YELLOW      => 0,
                            TonerColorModel::THREE_COLOR => 0,
                            TonerColorModel::FOUR_COLOR  => 0
                        ];

                        $tonerErrorMessage = "";
                        $hasValidToners    = true;

                        // Count the toners
                        foreach ($toners as $toner_id)
                        {
                            // Validate that the toner exists in our database
                            if (($curToner = TonerMapper::getInstance()->find((int)$toner_id)) != false)
                            {
                                $tonerCounts [$curToner->tonerColorId]++;
                            }
                        }

                        // Validate toner counts
                        foreach ($tonerCounts as $tonerColorId => $tonerCount)
                        {
                            // Check to see if it's a required toner color
                            if (in_array($tonerColorId, $requiredToners))
                            {
                                // Must have 1 or more toners of a required color
                                if ($tonerCount < 1)
                                {
                                    $requiredTonerList = [];
                                    foreach ($requiredToners as $tonerColorId)
                                    {
                                        $requiredTonerList [] = TonerColorModel::$ColorNames [$tonerColorId];
                                    }

                                    $hasValidToners    = false;
                                    $tonerErrorMessage = "You must have at least one of the following toner colors: " . implode(', ', $requiredTonerList);;
                                    break;
                                }
                            }
                            else
                            {
                                // Invalid toner for this configuration has been selected.
                                if ($tonerCount > 0)
                                {
                                    $hasValidToners    = false;
                                    $tonerErrorMessage = "You cannot add a " . TonerColorModel::$ColorNames [$tonerColorId] . " toner to this device because your toner configuration is set to " . TonerConfigModel::$TonerConfigNames [$toner_config_id] . ".";
                                    break;
                                }
                            }
                        }

                        // Do we have valid toners? If so, save the device.
                        if ($hasValidToners)
                        {
                            // Save Master Device
                            $masterDeviceMapper        = new MasterDeviceMapper();
                            $masterDevice              = new MasterDeviceModel();
                            $masterDevice->dateCreated = date('Y-m-d H:i:s');

                            foreach ($values as $value)
                            {
                                if (strlen($value) < 1)
                                {
                                    $value = null;
                                }
                            }

                            $masterDevice->populate($values);

                            // Make sure device doesn't exist
                            $checkwhere = "manufacturerId = {$values ['manufacturerId']} AND modelName LIKE '%{$values['modelName']}%'";
                            $exists     = $masterDeviceMapper->fetch($checkwhere);

                            if ($exists)
                            {
                                $this->_flashMessenger->addMessage([
                                    'danger' => "Your new device was not created because a device named {$masterDevice->getFullDeviceName()} already exists."
                                ]);
                            }
                            else
                            {
                                // Device not found... add it
                                $masterDeviceId = $masterDeviceMapper->insert($masterDevice);

                                // Save Toners
                                foreach ($toners as $value)
                                {
                                    $deviceToner                   = new DeviceTonerModel();
                                    $deviceToner->toner_id         = $value;
                                    $deviceToner->master_device_id = $masterDeviceId;
                                    DeviceTonerMapper::getInstance()->save($deviceToner);
                                }

                                // Save Quotegen Device
                                $oemSku    = $values ['oemSku'];
                                $dealerSku = $values ['dealerSku'];

                                if ($masterDeviceId > 0 && strlen($oemSku) > 0)
                                {
                                    // Save Device SKU
                                    $devicemapper = new DeviceMapper();
                                    $device       = new DeviceModel();
                                    $devicevalues = [
                                        'masterDeviceId' => $masterDeviceId,
                                        'dealerId'       => Zend_Auth::getInstance()->getIdentity()->dealerId,
                                        'oemSku'         => $oemSku,
                                        'dealerSku'      => $dealerSku,
                                        'description'    => $values ['description'],
                                        'cost'           => $values['cost']
                                    ];
                                    $device->populate($devicevalues);
                                    $devicemapper->insert($device);
                                }
                                $this->_flashMessenger->addMessage([
                                    'success' => "The {$masterDevice->getFullDeviceName()} device has been updated successfully."
                                ]);

                                // Redirect them here so that the form reloads
                                $this->redirectToRoute('hardware-library.all-devices.edit', [
                                    'id' => $masterDeviceId
                                ]);
                            }
                        }
                        else
                        {
                            $this->_flashMessenger->addMessage([
                                'danger' => $tonerErrorMessage
                            ]);
                        }
                    }

                    // Error
                    else
                    {
                        throw new InvalidArgumentException("Please correct the errors below.");
                    }
                }
                catch (InvalidArgumentException $e)
                {
                    $this->_flashMessenger->addMessage([
                        'danger' => $e->getMessage()
                    ]);
                }
            }
            else
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $this->redirectToRoute('hardware-library.all-devices');
            }
        }

        // Display filtered list of toners
        $paginator = new Zend_Paginator(new My_Paginator_MapperAdapter(TonerMapper::getInstance(), $where));

        // Set the current page we're on
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Set how many items to show
        $paginator->setItemCountPerPage(125);

        // Add form to page
        $form->setDecorators([
            [
                'ViewScript',
                [
                    'viewScript'     => 'forms/quotegen/create-new-device-form.phtml',
                    'manufacturers'  => $manufacturers,
                    'assignedToners' => $assignedToners,
                    'paginator'      => $paginator,
                    'viewfilter'     => $view_filter,
                    'search_filter'  => $filter,
                    'cboCriteria'    => $cboCriteria,
                    'txtCriteria'    => $txtCriteria
                ]
            ]
        ]);
        $this->view->form = $form;
    }

    /**
     * Edit device details
     */
    public function editAction ()
    {
        $masterDeviceId = $this->_getParam('id', false);
        $this->view->id = $masterDeviceId;

        // If they haven't provided an id, send them back to the view all masterDevice page
        if (!$masterDeviceId)
        {
            $this->_flashMessenger->addMessage(['warning' => 'Please select a master device to edit first.']);
            $this->redirectToRoute('hardware-library.all-devices');
        }

        // Get the masterDevice
        $mapper                 = new MasterDeviceMapper();
        $masterDevice           = $mapper->find($masterDeviceId);
        $this->view->devicename = $masterDevice->getFullDeviceName();

        // If the masterDevice doesn't exist, send them back to the view all masterDevices page
        if (!$masterDevice)
        {
            $this->_flashMessenger->addMessage(['danger' => 'There was an error selecting the master device to edit.']);
            $this->redirectToRoute('hardware-library.all-devices');
        }

        // Create a new form with the mode and roles set
        $form = new DeviceSetupForm();

        // Prepare the data for the form
        $form->populate($masterDevice->toArray());
        $isAdmin = $this->view->IsAllowed(QuoteGeneratorAclModel::RESOURCE_QUOTEGEN_DEVICESETUP_WILDCARD, AppAclModel::PRIVILEGE_ADMIN);
        if (!$isAdmin)
        {
            $tempAttribute = $masterDevice->getDealerAttributes();
            if ($tempAttribute)
            {
                $form->getElement('partsCostPerPage')->setValue($tempAttribute->partsCostPerPage);
                $form->getElement('laborCostPerPage')->setValue($tempAttribute->laborCostPerPage);
            }

            /**
             * @var $element Zend_Form_Element
             */
            foreach ($form->getElements() as $element)
            {
                $element->setAttrib('readonly', 'true');
            }
            $form->getElement('can_sell')->setAttrib('readonly', null);
            $form->getElement('oemSku')->setAttrib('readonly', null);
            $form->getElement('description')->setAttrib('readonly', null);
            $form->getElement('dealerSku')->setAttrib('readonly', null);
            $form->getElement('cost')->setAttrib('readonly', null);
            $form->getElement('submit')->setAttrib('readonly', null);
            $form->getElement('cancel')->setAttrib('readonly', null);
            $form->getElement('partsCostPerPage')->setAttrib('readonly', null);
            $form->getElement('laborCostPerPage')->setAttrib('readonly', null);
        }


        // Populate SKU
        $oemSku                     = null;
        $deviceMapper               = new DeviceMapper();
        $quoteDevice                = $deviceMapper->find([$masterDeviceId, Zend_Auth::getInstance()->getIdentity()->dealerId]);
        $this->view->quotegendevice = $quoteDevice;
        if ($quoteDevice)
        {
            $oemSku = $quoteDevice->oemSku;
            $form->getElement('can_sell')->setValue(true);
            $form->getElement('oemSku')->setValue($oemSku);
            $form->getElement('dealerSku')->setValue($quoteDevice->dealerSku);
            $form->getElement('cost')->setValue($quoteDevice->cost);
            $form->getElement('description')->setValue($quoteDevice->description);
        }
        // Make sure we are posting data
        $request = $this->getRequest();
        if ($request->isPost())
        {
            // Get the post data
            $values = $request->getPost();
            // If we cancelled we don't need to validate anything
            if (!isset($values ['cancel']))
            {
                try
                {
                    if ($form->isValid($values))
                    {
                        $formValues = $form->getValues();

                        /**
                         * With can sell flag on, that means we are saving it to the dealer table.  Regardless of if the system admin is signed in.
                         */
                        if ($isAdmin)
                        {
                            // Save Master Device
                            $mapper       = new MasterDeviceMapper();
                            $masterDevice = new MasterDeviceModel();
                            foreach ($values as &$value)
                            {
                                if (strlen($value) < 1)
                                {
                                    $value = null;
                                }
                            }
                            $masterDevice->populate($values);
                            $masterDevice->id = $masterDeviceId;

                            // Save to the database with cascade insert turned on
                            $mapper->save($masterDevice, $masterDeviceId);
                        }

                        // Process the can sell part of the form
                        if ($formValues ['can_sell'])
                        {
                            // Get the dealer id from the session
                            $dealerId = Zend_Auth::getInstance()->getIdentity()->dealerId;

                            $quoteDevice                 = new DeviceModel($formValues);
                            $quoteDevice->dealerId       = $dealerId;
                            $quoteDevice->masterDeviceId = $masterDeviceId;

                            $dealerMasterDeviceAttributes                 = new DealerMasterDeviceAttributeModel($formValues);
                            $dealerMasterDeviceAttributes->masterDeviceId = $masterDeviceId;
                            $dealerMasterDeviceAttributes->dealerId       = $dealerId;

                            $quoteDevice->saveObject();
                            $dealerMasterDeviceAttributes->saveObject();

                            $this->view->quotegendevice = $quoteDevice;
                        }
                        else
                        {
                            if ($formValues ['oemSku'] || $formValues ['cost'] || $formValues ['description'] || $formValues ['dealerSku'] || $formValues ['partsCostPerPage'] || $formValues ['laborCostPerPage'])
                            {
                                $this->_flashMessenger->addMessage(['warning' => "Can Sell must be selected to save OEM SKU, " . My_Brand::$dealerSku . ", Standard Features or Device Cost."]);
                            }

                            $deviceMapper->delete($quoteDevice);
                            $this->view->quotegendevice = null;
                        }

                        $this->_flashMessenger->addMessage(['success' => "The device has been updated successfully."]);
                    }
                    else
                    {
                        throw new InvalidArgumentException("Please correct the errors below");
                    }
                }
                catch (InvalidArgumentException $e)
                {
                    $this->_flashMessenger->addMessage(['danger' => $e->getMessage()]);
                }
            }
            else
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $this->redirectToRoute('hardware-library.all-devices');
            }
        }
        // Add form to page
        $form->setDecorators([
            [
                'ViewScript',
                [
                    'viewScript' => 'forms/quotegen/device-setup-form.phtml',
                ]
            ]
        ]);
        $this->view->form = $form;
    }

    /**
     * Deletes a device
     */
    public function deleteAction ()
    {
        $masterDeviceId = $this->_getParam('masterDeviceId', false);
        if (!$masterDeviceId)
        {
            $this->sendJsonError('Missing Master Device ID');
        }

        $masterDevice = MasterDeviceMapper::getInstance()->find($masterDeviceId);
        if (!$masterDevice)
        {
            $this->sendJsonError('Invalid Master Device');
        }

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        try
        {
            $db->beginTransaction();
            MasterDeviceMapper::getInstance()->delete($masterDevice);
            $db->commit();
            $this->sendJson(['success' => true, 'message' => 'Device successfully deleted']);
        }
        catch (Exception $e)
        {
            $db->rollBack();
            \Tangent\Logger\Logger::logException($e);
            $this->sendJsonError('A server error occurred when trying to delete the master device. Error ID #' . \Tangent\Logger\Logger::getUniqueId());
        }
    }

    /**
     * Edit device toners
     */
    public function tonersAction ()
    {
        $where          = [];
        $tonerId        = null;
        $txtCriteria    = null;
        $cboCriteria    = null;
        $masterDeviceId = $this->_getParam('id', false);

        // Pass values back to view
        $this->view->id = $masterDeviceId;

        // If they haven't provided an id, send them back to the view all master device page
        if (!$masterDeviceId)
        {
            $this->_flashMessenger->addMessage([
                'warning' => 'Please select a master device to edit first.'
            ]);
            $this->redirectToRoute('hardware-library.all-devices');
        }

        // Get the master device
        $mapper                 = new MasterDeviceMapper();
        $masterDevice           = $mapper->find($masterDeviceId);
        $this->view->devicename = $masterDevice->getFullDeviceName();

        // If the master device doesn't exist, send them back to the view all master devices page
        if (!$masterDevice)
        {
            $this->_flashMessenger->addMessage([
                'danger' => 'There was an error selecting the master device to edit.'
            ]);
            $this->redirectToRoute('hardware-library.all-devices');
        }
        $tonerConfig = $masterDevice->getTonerConfig()->name;

        // Get the toner colors that we require
        $requiredTonerColors = TonerConfigModel::getRequiredTonersForTonerConfig($masterDevice->tonerConfigId);

        // Get quotegen device
        $device                     = DeviceMapper::getInstance()->find([$masterDeviceId, Zend_Auth::getInstance()->getIdentity()->dealerId]);
        $this->view->quotegendevice = $device;

        // Populate manufacturers drop down
        $manufacturers             = ManufacturerMapper::getInstance()->fetchAll();
        $this->view->manufacturers = $manufacturers;

        // Make sure we are posting data
        $request = $this->getRequest();
        if ($request->isPost())
        {
            // Get the post data
            $values = $request->getPost();

            // If we cancelled we don't need to validate anything
            if (!isset($values ['cancel']))
            {
                try
                {
                    // Get Toner ID
                    $tonerId = $values ['tonerid'];

                    $view   = null;
                    $filter = null;

                    if (!isset($values ['btnClearSearch']))
                    {
                        // Filter view
                        $view                    = $values ['cboView'];
                        $this->view->view_filter = $view;

                        // Toners Search Filter
                        $filter                    = $values ['criteria_filter'];
                        $this->view->search_filter = $filter;

                        $txtCriteria             = $values ['txtCriteria'];
                        $this->view->txtCriteria = $txtCriteria;

                        $cboCriteria             = $values ['cboCriteria'];
                        $this->view->cboCriteria = $cboCriteria;
                    }

                    // Assign Toner
                    if (isset($values ['btnAssign']))
                    {
                        // Save if toner id and device id
                        if ($tonerId && $masterDeviceId)
                        {
                            // Get toner
                            $toner = TonerMapper::getInstance()->find($tonerId);

                            $validToner = in_array($toner->tonerColorId, $requiredTonerColors);

                            if ($validToner)
                            {
                                // Save device toner
                                $deviceToner                   = new DeviceTonerModel();
                                $deviceToner->toner_id         = $tonerId;
                                $deviceToner->master_device_id = $masterDeviceId;
                                DeviceTonerMapper::getInstance()->save($deviceToner);

                                $this->_flashMessenger->addMessage([
                                    'success' => "The toner was assigned successfully."
                                ]);
                            }
                            else
                            {
                                $this->_flashMessenger->addMessage([
                                    'danger' => "The toner is an invalid toner for this device."
                                ]);
                            }
                        }
                    }

                    // Unassign Toner
                    else if (isset($values ['btnUnassign']))
                    {
                        // An array for counting each color
                        $tonerCounts = [
                            TonerColorModel::BLACK       => 0,
                            TonerColorModel::CYAN        => 0,
                            TonerColorModel::MAGENTA     => 0,
                            TonerColorModel::YELLOW      => 0,
                            TonerColorModel::THREE_COLOR => 0,
                            TonerColorModel::FOUR_COLOR  => 0
                        ];

                        $safeToDelete         = true;
                        $tonersByManufacturer = TonerMapper::getInstance()->getTonersForDevice($masterDeviceId);

                        // Count the toners
                        foreach ($tonersByManufacturer as $tonersByColor)
                        {
                            foreach ($tonersByColor as $tonerColorId => $toners)
                            {
                                $tonerCounts [$tonerColorId] += count($toners);
                            }
                        }

                        $toner = TonerMapper::getInstance()->find($tonerId);

                        // Make sure we're not dropping below one valid toner for the color
                        if ($tonerCounts [$toner->tonerColorId] < 2)
                        {
                            $safeToDelete = false;
                        }

                        // If it's safe to delete, do so.
                        if ($safeToDelete)
                        {
                            DeviceTonerMapper::getInstance()->delete([
                                'toner_id = ?'         => $tonerId,
                                'master_device_id = ?' => $masterDeviceId
                            ]);

                            $this->_flashMessenger->addMessage(['success' => "The toner was unassigned successfully."]);
                        }
                        else
                        {
                            $this->_flashMessenger->addMessage(['danger' => 'You must have at least 1 complete set of toners for this device. If you must unassign this toner you will need to assign a new one before being able to unassign this one.']);
                        }
                    }

                    // Filter
                    else if (isset($values ['btnSearch']))
                    {
                        // Get Device Toners List
                        $deviceToners   = DeviceTonerMapper::getInstance()->getDeviceToners($masterDeviceId);
                        $assignedToners = [
                            ''
                        ];
                        foreach ($deviceToners as $deviceToner)
                        {
                            $assignedToners [] = $deviceToner->toner_id;
                        }

                        if ($view == "assigned")
                        {
                            $where = [
                                'id IN ( ? )' => $assignedToners
                            ];
                        }
                        else if ($view == "unassigned")
                        {
                            $where = [
                                'id NOT IN ( ? )' => $assignedToners
                            ];
                        }

                        else if ($filter == 'sku')
                        {
                            $where = array_merge((array)$where, [
                                'sku LIKE ( ? )' => '%' . $txtCriteria . '%'
                            ]);
                        }
                        else
                        {
                            $where = array_merge((array)$where, [
                                'manufacturerId = ?' => $cboCriteria
                            ]);
                        }
                    }

                    // Clear Filter
                    else
                    {
                        $this->view->view_filter = "all";
                    }
                }
                catch (InvalidArgumentException $e)
                {
                    $this->_flashMessenger->addMessage([
                        'danger' => $e->getMessage()
                    ]);
                }
            }
            else
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $this->redirectToRoute('hardware-library.all-devices');
            }
        }

        $deviceToners   = DeviceTonerMapper::getInstance()->getDeviceToners($masterDeviceId);
        $assignedToners = [];
        foreach ($deviceToners as $deviceToner)
        {
            $assignedToners [] = $deviceToner->toner_id;
        }
        $this->view->assignedToners = $assignedToners;

        // Display filtered list of toners
        switch ($tonerConfig)
        {
            case "3 COLOR - COMBINED" :
                $validTonerColors = [
                    1,
                    5
                ];
                break;
            case "3 COLOR - SEPARATED" :
                $validTonerColors = [
                    1,
                    2,
                    3,
                    4
                ];
                break;
            case "4 COLOR - COMBINED" :
                $validTonerColors = [
                    6
                ];
                break;
            case "BLACK ONLY" :
                $validTonerColors = [
                    1
                ];
                break;
        }

        $where = array_merge((array)$where, [
            'tonerColorId IN ( ? )' => $validTonerColors
        ]);

        $paginator = new Zend_Paginator(new My_Paginator_MapperAdapter(TonerMapper::getInstance(), $where));

        // Set the current page we're on
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Set how many items to show
        $paginator->setItemCountPerPage(15);

        // Pass the view the paginator
        $this->view->paginator = $paginator;
    }

    const OPTION_FILTER_ALL        = 0;
    const OPTION_FILTER_ASSIGNED   = 1;
    const OPTION_FILTER_UNASSIGNED = 2;

    /**
     * Edit device options
     */
    public function optionsAction ()
    {
        // Default filter
        $filterBy       = self::OPTION_FILTER_ASSIGNED;
        $view           = "all";
        $where          = null;
        $filterWhere    = null;
        $optionId       = null;
        $txtCriteria    = null;
        $cboCriteria    = null;
        $masterDeviceId = $this->_getParam('id', false);
        $this->view->id = $masterDeviceId;

        // If they haven't provided an id, send them back to the view all masterDevice page
        if (!$masterDeviceId)
        {
            $this->_flashMessenger->addMessage([
                'warning' => 'Please select a master device to edit first.'
            ]);
            $this->redirectToRoute('hardware-library.all-devices');
        }

        // Get the device and assigned options
        $quoteDevice            = DeviceMapper::getInstance()->find([$masterDeviceId, Zend_Auth::getInstance()->getIdentity()->dealerId]);
        $this->view->devicename = $quoteDevice->getMasterDevice()->getFullDeviceName();

        // Get device options list
        $assignedOptions = [];
        foreach ($quoteDevice->getDeviceOptions() as $option)
        {
            $assignedOptions [] = $option->optionId;
        }

        // Make sure we are posting data
        $request = $this->getRequest();
        if ($request->isPost())
        {
            // Get the post data
            $values = $request->getPost();

            // Filter view
            $view = $values ['cboView'];

            // If we cancelled we don't need to validate anything
            if (!isset($values ['cancel']))
            {
                try
                {
                    // Get Option ID
                    $optionId           = $values ['optionid'];
                    $deviceOptionMapper = new DeviceOptionMapper();
                    $deviceOption       = new DeviceOptionModel();

                    // Save if option and device id
                    if ($optionId && $masterDeviceId)
                    {
                        $deviceOption->masterDeviceId   = $masterDeviceId;
                        $deviceOption->dealerId         = Zend_Auth::getInstance()->getIdentity()->dealerId;
                        $deviceOption->optionId         = $optionId;
                        $deviceOption->includedQuantity = 0;

                        if (isset($values ['btnAssign']))
                        {
                            // Save device option
                            $deviceOptionMapper->insert($deviceOption);

                            $assignedOptions [] = $deviceOption->optionId;

                            $this->_flashMessenger->addMessage([
                                'success' => "The option was assigned successfully."
                            ]);
                        }

                        // Unassign Option
                        else if (isset($values ['btnUnassign']))
                        {
                            // Delete device option
                            $deviceOptionMapper->delete($deviceOption);

                            // Delete all occurrences of this option from the array
                            $arrayKeys = array_keys($assignedOptions, $deviceOption->optionId);
                            foreach ($arrayKeys as $key)
                            {
                                unset($assignedOptions [$key]);
                            }

                            $this->_flashMessenger->addMessage([
                                'success' => "The option was unassigned successfully."
                            ]);
                        }
                    }

                    // Filter
                    else if (isset($values ['btnSearch']))
                    {
                        // Nothing to do here?
                        // Options Search Filter
                        if (isset($values ['txtCriteria']))
                        {
                            $filterWhere = [
                                "{$values ['criteria_filter']} LIKE ( ? )" => "%{$values ['txtCriteria']}%"
                            ];
                        }
                    }

                    // Clear Filter
                    else if (isset($values ['btnClearSearch']))
                    {
                        $this->view->view_filter = "all";
                        $view                    = "all";
                    }
                }
                catch (Exception $e)
                {
                    $this->_flashMessenger->addMessage(['error' => "An error has occurred."]);
                    \Tangent\Logger\Logger::logException($e);
                }
            }
            else
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $this->redirectToRoute('hardware-library.all-devices');
            }
        }

        $this->view->assignedOptions = $assignedOptions;

        $where = null;

        if ($view == "assigned")
        {
            // Do nothing as this is the default
        }
        else if ($view == "unassigned")
        {
            $filterBy = self::OPTION_FILTER_UNASSIGNED;
        }
        else
        {
            $filterBy = self::OPTION_FILTER_ALL;
        }

        switch ($filterBy)
        {
            case self::OPTION_FILTER_ASSIGNED :
                if (count($assignedOptions) > 0)
                {
                    $where = [
                        'id IN ( ? )' => $assignedOptions
                    ];
                }
                else
                {
                    $where = [
                        'id IN ( ? )' => "NULL"
                    ];
                }
                break;
            case self::OPTION_FILTER_UNASSIGNED :
                if (count($assignedOptions) > 0)
                {
                    $where = [
                        'id NOT IN ( ? )' => $assignedOptions
                    ];
                }
                break;
            case self::OPTION_FILTER_ALL :
                break;
        }

        if (is_array($filterWhere))
        {
            $where = array_merge((array)$where, $filterWhere);
        }
        $whereDealer             = [
            'dealerId = ?' => Zend_Auth::getInstance()->getIdentity()->dealerId
        ];
        $where                   = array_merge((array)$where, $whereDealer);
        $this->view->view_filter = $view;

        // Display filtered list of options
        $paginator = new Zend_Paginator(new My_Paginator_MapperAdapter(OptionMapper::getInstance(), $where));
        // Set the current page we're on
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Set how many items to show
        $paginator->setItemCountPerPage(15);

        // Pass the view the paginator
        $this->view->paginator = $paginator;
    }

    /**
     * Edit device configurations
     */
    public function configurationsAction ()
    {
        // Get master device
        $masterDeviceId = $this->_getParam('id', false);
        $this->view->id = $masterDeviceId;

        // Default where to this device
        $where = [
            'masterDeviceId = ?' => $masterDeviceId
        ];

        // If they haven't provided an id, send them back to the view all masterDevice page
        if (!$masterDeviceId)
        {
            $this->_flashMessenger->addMessage([
                'warning' => 'Please select a master device to edit first.'
            ]);
            $this->redirectToRoute('hardware-library.all-devices');
        }

        // Get the device
        $device                 = DeviceMapper::getInstance()->find([$masterDeviceId, Zend_Auth::getInstance()->getIdentity()->dealerId]);
        $this->view->devicename = $device->getMasterDevice()->getFullDeviceName();

        // Get device configurations list
        $deviceConfiguration    = DeviceConfigurationMapper::getInstance()->fetchAll([
            'masterDeviceId = ?' => $masterDeviceId,
            'dealerId = ?'       => Zend_Auth::getInstance()->getIdentity()->dealerId
        ]);
        $assignedConfigurations = [];
        foreach ($deviceConfiguration as $configuration)
        {
            $assignedConfigurations [] = $configuration->id;
        }
        $this->view->assignedConfigurations = $assignedConfigurations;

        // Make sure we are posting data
        $request = $this->getRequest();
        if ($request->isPost())
        {
            // Get the post data
            $values = $request->getPost();

            // If we cancelled we don't need to validate anything
            if (!isset($values ['cancel']))
            {
                try
                {
                }
                catch (Exception $e)
                {
                    $this->_flashMessenger->addMessage(['error' => "An error has occurred."]);
                    \Tangent\Logger\Logger::logException($e);
                }
            }
            else
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $this->redirectToRoute('hardware-library.all-devices');
            }
        }

        // Display all of the devices
        $paginator = new Zend_Paginator(new My_Paginator_MapperAdapter(DeviceConfigurationMapper::getInstance(), $where));

        // Set the current page we're on
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Set how many items to show
        $paginator->setItemCountPerPage(15);

        // Pass the view the paginator
        $this->view->paginator = $paginator;
    }
}
