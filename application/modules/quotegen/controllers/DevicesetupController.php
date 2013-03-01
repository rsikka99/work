<?php

class Quotegen_DevicesetupController extends Tangent_Controller_Action
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

    }

    /**
     * Gets the list of devices for the hardware library "all devices" page
     */
    public function allDevicesListAction ()
    {
        $jqGrid             = new Tangent_Service_JQGrid();
        $masterDeviceMapper = Proposalgen_Model_Mapper_MasterDevice::getInstance();

        $jqGridParameters = array(
            'sidx' => $this->_getParam('sidx', 'modelName'),
            'sord' => $this->_getParam('sord', 'desc'),
            'page' => $this->_getParam('page', 1),
            'rows' => $this->_getParam('rows', 10),
        );
        $canSell          = ($this->_getParam('canSell', false) === "true");

        if ($canSell)
        {
            $sortColumns = array(
                'modelName',
                'oemSku',
                'dealerSku',
            );
        }
        else
        {
            $sortColumns = array(
                'modelName',
            );
        }
        $jqGrid->setValidSortColumns($sortColumns);
        $jqGrid->parseJQGridPagingRequest($jqGridParameters);
        if ($jqGrid->sortingIsValid())
        {
            $searchCriteria = $this->_getParam('criteriaFilter', null);
            $searchValue    = $this->_getParam('criteria', null);

            $filterCriteriaValidator = new Zend_Validate_InArray(array(
                                                                      'haystack' => array(
                                                                          'deviceName',
                                                                          'oemSku',
                                                                          'dealerSku'
                                                                      )
                                                                 ));

            // If search criteria or value is null then we don't need either one of them. Same goes if our criteria is invalid.
            if ($searchCriteria === null || $searchValue === null || !$filterCriteriaValidator->isValid($searchCriteria))
            {
                $searchCriteria = null;
                $searchValue    = null;
            }

            $startRecord = $jqGrid->getRecordsPerPage() * ($jqGrid->getCurrentPage() - 1);

            $jqGrid->setRecordCount($masterDeviceMapper->getCanSellMasterDevices($jqGrid->getSortColumn(), $jqGrid->getSortDirection(), $searchCriteria, $searchValue, null, null, $canSell, true));

            if ($jqGrid->getCurrentPage() < 1)
            {
                $jqGrid->setCurrentPage(1);
            }
            else if ($jqGrid->getCurrentPage() > $jqGrid->calculateTotalPages())
            {
                $jqGrid->setCurrentPage($jqGrid->calculateTotalPages());
                $startRecord = ($jqGrid->getCurrentPage() * $jqGrid->getRecordsPerPage()) - $jqGrid->getRecordsPerPage();
            }

            $jqGrid->setRows($masterDeviceMapper->getCanSellMasterDevices($jqGrid->getSortColumn(), $jqGrid->getSortDirection(), $searchCriteria, $searchValue, $jqGrid->getRecordsPerPage(), $startRecord, $canSell));


        }
        $this->sendJson($jqGrid->createPagerResponseArray());
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
        $manufacturers = Proposalgen_Model_Mapper_Manufacturer::getInstance()->fetchAll();

        // Create a new form with the mode and roles set
        $form = new Quotegen_Form_DeviceSetup();

        // Make sure we are posting data
        $request = $this->getRequest();
        if ($request->isPost())
        {
            // Get the post data
            $values         = $request->getPost();
            $assignedToners = $values ['hdnToners'];

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

                        if ($filter == 'oemSku')
                        {
                            $where = array_merge((array)$where, array(
                                                                     'oemSku LIKE ( ? )' => '%' . $txtCriteria . '%'
                                                                ));
                        }
                        else
                        {
                            $where = array_merge((array)$where, array(
                                                                     'manufacturerid = ?' => $cboCriteria
                                                                ));
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
                        $toners          = explode(',', $values ['hdnToners']);

                        // An array of required toners
                        $requiredToners = Proposalgen_Model_TonerConfig::getRequiredTonersForTonerConfig($toner_config_id);

                        // An array for counting each color
                        $tonerCounts = array(
                            Proposalgen_Model_TonerColor::BLACK       => 0,
                            Proposalgen_Model_TonerColor::CYAN        => 0,
                            Proposalgen_Model_TonerColor::MAGENTA     => 0,
                            Proposalgen_Model_TonerColor::YELLOW      => 0,
                            Proposalgen_Model_TonerColor::THREE_COLOR => 0,
                            Proposalgen_Model_TonerColor::FOUR_COLOR  => 0
                        );

                        $tonerErrorMessage = "";
                        $hasValidToners    = true;

                        // Count the toners
                        foreach ($toners as $key => $toner_id)
                        {
                            // Validate that the toner exists in our database
                            if (($curToner = Proposalgen_Model_Mapper_Toner::getInstance()->find((int)$toner_id)) != false)
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
                                    $requiredTonerList = array();
                                    foreach ($requiredToners as $tonerColorId)
                                    {
                                        $requiredTonerList [] = Proposalgen_Model_TonerColor::$ColorNames [$tonerColorId];
                                    }

                                    $hasValidToners    = false;
                                    $tonerErrorMessage = "You must have at least one of the following toner colors: " . implode(', ', $requiredTonerList);
                                    $repopulateForm    = 1;
                                    break;
                                }
                            }
                            else
                            {
                                // Invalid toner for this configuration has been selected.
                                if ($tonerCount > 0)
                                {
                                    $hasValidToners    = false;
                                    $tonerErrorMessage = "You cannot add a " . Proposalgen_Model_TonerColor::$ColorNames [$tonerColorId] . " toner to this device because your toner configuration is set to " . Proposalgen_Model_TonerConfig::$TonerConfigNames [$toner_config_id] . ".";
                                    $repopulateForm    = 1;
                                    break;
                                }
                            }
                        }

                        // Do we have valid toners? If so, save the device.
                        if ($hasValidToners)
                        {
                            // Save Master Device
                            $masterDeviceMapper        = new Proposalgen_Model_Mapper_MasterDevice();
                            $masterDevice              = new Proposalgen_Model_MasterDevice();
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
                                $this->_helper->flashMessenger(array(
                                                                    'danger' => "Your new device was not created because a device named {$masterDevice->getFullDeviceName()} already exists."
                                                               ));
                            }
                            else
                            {
                                // Device not found... add it
                                $masterDeviceId = $masterDeviceMapper->insert($masterDevice);

                                // Save Toners
                                foreach ($toners as $key => $value)
                                {
                                    $deviceTonerMapper           = new Proposalgen_Model_Mapper_DeviceToner();
                                    $deviceToner                 = new Proposalgen_Model_DeviceToner();
                                    $deviceToner->tonerId        = $value;
                                    $deviceToner->masterDeviceId = $masterDeviceId;
                                    $deviceTonerMapper->save($deviceToner);
                                }

                                // Save Quotegen Device
                                $oemSku    = $values ['oemSku'];
                                $dealerSku = $values ['dealerSku'];

                                if ($masterDeviceId > 0 && strlen($oemSku) > 0)
                                {
                                    // Save Device SKU
                                    $devicemapper = new Quotegen_Model_Mapper_Device();
                                    $device       = new Quotegen_Model_Device();
                                    $devicevalues = array(
                                        'masterDeviceId' => $masterDeviceId,
                                        'oemSku'         => $oemSku,
                                        'dealerSku'      => $dealerSku,
                                        'description'    => $values ['description']
                                    );
                                    $device->populate($devicevalues);
                                    $devicemapper->insert($device);
                                }

                                $this->_helper->flashMessenger(array(
                                                                    'success' => "The {$masterDevice->getFullDeviceName()} device has been updated sucessfully."
                                                               ));

                                // Redirect them here so that the form reloads
                                $this->_helper->redirector('edit', null, null, array(
                                                                                    'id' => $masterDeviceId
                                                                               ));
                            }
                        }
                        else
                        {
                            $this->_helper->flashMessenger(array(
                                                                'danger' => $tonerErrorMessage
                                                           ));
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
                    $this->_helper->flashMessenger(array(
                                                        'danger' => $e->getMessage()
                                                   ));
                }
            }
            else
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $this->_helper->redirector('index');
            }
        }

        // Display filterd list of toners
        $paginator = new Zend_Paginator(new My_Paginator_MapperAdapter(Proposalgen_Model_Mapper_Toner::getInstance(), $where));

        // Set the current page we're on
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Set how many items to show
        $paginator->setItemCountPerPage(125);

        // Add form to page
        $form->setDecorators(array(
                                  array(
                                      'ViewScript',
                                      array(
                                          'viewScript'     => 'devicesetup/forms/createdevice.phtml',
                                          'manufacturers'  => $manufacturers,
                                          'assignedToners' => $assignedToners,
                                          'paginator'      => $paginator,
                                          'viewfilter'     => $view_filter,
                                          'search_filter'  => $filter,
                                          'cboCriteria'    => $cboCriteria,
                                          'txtCriteria'    => $txtCriteria
                                      )
                                  )
                             ));
        $this->view->form = $form;
    }

    /**
     * Edit device details
     */
    public function editAction ()
    {
        $masterDeviceId = $this->_getParam('id', false);
        $this->view->id = $masterDeviceId;

        // If they haven't provided an id, send them back to the view all masterDevice
        // page
        if (!$masterDeviceId)
        {
            $this->_helper->flashMessenger(array(
                                                'warning' => 'Please select a master device to edit first.'
                                           ));
            $this->_helper->redirector('index');
        }

        // Get the masterDevice
        $mapper                 = new Proposalgen_Model_Mapper_MasterDevice();
        $masterDevice           = $mapper->find($masterDeviceId);
        $this->view->devicename = $masterDevice->getFullDeviceName();

        // If the masterDevice doesn't exist, send them back t the view all masterDevices page
        if (!$masterDevice)
        {
            $this->_helper->flashMessenger(array(
                                                'danger' => 'There was an error selecting the master device to edit.'
                                           ));
            $this->_helper->redirector('index');
        }

        // Create a new form with the mode and roles set
        $form = new Quotegen_Form_DeviceSetup();

        // Prepare the data for the form
        $form->populate($masterDevice->toArray());

        // Populate SKU
        $oemSku                     = null;
        $devicemapper               = new Quotegen_Model_Mapper_Device();
        $device                     = $devicemapper->find($masterDeviceId);
        $this->view->quotegendevice = $device;
        if ($device)
        {
            $oemSku = $device->oemSku;
            $form->getElement('can_sell')->setValue(true);
            $form->getElement('oemSku')->setValue($oemSku);
            $form->getElement('dealerSku')->setValue($device->dealerSku);

            $description = $device->description;
            $form->getElement('description')->setValue($description);
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

                        /*
                         * In order to use populate, we need to make sure the toner_config_id value is set Since it's
                         * disabled in edit mode, we need to assign it the value from the hidden field
                         */
                        // If checkbox for can sell is checked, if not delete.
                        if ($formValues ['can_sell'])
                        {
                            // Save Device SKU
                            $device       = new Quotegen_Model_Device();
                            $devicevalues = array(
                                'masterDeviceId' => $masterDeviceId,
                                'oemSku'         => $values ['oemSku'],
                                'dealerSku'      => $values ['dealerSku'],
                                'description'    => $values ['description']
                            );
                            $device->populate($devicevalues);

                            // If $oemSku set above, then record exists to update
                            if ($oemSku)
                            {
                                $deviceId = $devicemapper->save($device, $masterDeviceId);
                            }

                            // Else no record, so insert it
                            else
                            {
                                $deviceId = $devicemapper->insert($device);
                            }

                            $this->view->quotegendevice = $device;
                        }
                        else
                        {
                            $devicemapper->delete($device);
                            $this->view->quotegendevice = null;
                        }

                        // Save Master Device
                        $mapper       = new Proposalgen_Model_Mapper_MasterDevice();
                        $masterDevice = new Proposalgen_Model_MasterDevice();
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
                        $masterDeviceId = $mapper->save($masterDevice, $masterDeviceId);

                        $this->_helper->flashMessenger(array(
                                                            'success' => "The device has been updated sucessfully."
                                                       ));
                    }

                    // Error
                    else
                    {
                        throw new InvalidArgumentException("Please correct the errors below");
                    }
                }
                catch (InvalidArgumentException $e)
                {
                    $this->_helper->flashMessenger(array(
                                                        'danger' => $e->getMessage()
                                                   ));
                }
            }
            else
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $this->_helper->redirector('index');
            }
        }
        $this->view->form = $form;
    }

    /**
     * Deletes a device
     */
    public function deleteAction ()
    {
        $deviceId = $this->_getParam('id', false);

        if (!$deviceId)
        {
            $this->_helper->flashMessenger(array(
                                                'warning' => 'Please select a device to delete first.'
                                           ));
            $this->_helper->redirector('index');
        }

        $device = Proposalgen_Model_Mapper_MasterDevice::getInstance()->find($deviceId);
        if (!$device)
        {
            $this->_helper->flashMessenger(array(
                                                'danger' => 'There was an error selecting the device to delete.'
                                           ));
            $this->_helper->redirector('index');
        }

        // Get the device name
        $deviceName = $device->getFullDeviceName();

        $message = "Are you sure you want to delete {$deviceName}?";
        $form    = new Application_Form_Delete($message);

        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (!isset($values ['cancel']))
            {
                // delete device from database
                if ($form->isValid($values))
                {
                    // Delete device options, configurations and configuration options
                    Quotegen_Model_Mapper_DeviceConfiguration::getInstance()->deleteConfigurationByDeviceId($deviceId);

                    // Delete the device options and device
                    Quotegen_Model_Mapper_DeviceOption::getInstance()->deleteOptionsByDeviceId($deviceId);

                    // Get the Quotegen Device Object
                    $quotegenDeviceMapper = Quotegen_Model_Mapper_Device::getInstance();
                    $quotegenDevice       = $quotegenDeviceMapper->find($deviceId);
                    Quotegen_Model_Mapper_Device::getInstance()->delete($quotegenDevice);

                    // Delete toners for Master Device
                    $deviceToners = Proposalgen_Model_Mapper_DeviceToner::getInstance()->fetchAll("master_device_id = {$deviceId}");
                    foreach ($deviceToners as $toner)
                    {
                        $tonerId = $toner->tonerId;
                        Proposalgen_Model_Mapper_DeviceToner::getInstance()->delete("toner_id = {$tonerId}");
                    }

                    // Delete Master Device
                    Proposalgen_Model_Mapper_MasterDevice::getInstance()->delete($device);

                    // Display Message and return
                    $this->_helper->flashMessenger(array(
                                                        'success' => "{$deviceName} was deleted successfully."
                                                   ));
                    $this->_helper->redirector('index');
                }
            }
            else
            {
                $this->_helper->redirector('index');
            }
        }
        $this->view->form = $form;
    }

    /**
     * Edit device toners
     */
    public function tonersAction ()
    {
        $where          = array();
        $tonerId        = null;
        $txtCriteria    = null;
        $cboCriteria    = null;
        $masterDeviceId = $this->_getParam('id', false);

        // Pass values back to view
        $this->view->id = $masterDeviceId;

        // If they haven't provided an id, send them back to the view all master device page
        if (!$masterDeviceId)
        {
            $this->_helper->flashMessenger(array(
                                                'warning' => 'Please select a master device to edit first.'
                                           ));
            $this->_helper->redirector('index');
        }

        // Get the master device
        $mapper                 = new Proposalgen_Model_Mapper_MasterDevice();
        $masterDevice           = $mapper->find($masterDeviceId);
        $this->view->devicename = $masterDevice->getFullDeviceName();

        // If the master device doesn't exist, send them back to the view all master devices page
        if (!$masterDevice)
        {
            $this->_helper->flashMessenger(array(
                                                'danger' => 'There was an error selecting the master device to edit.'
                                           ));
            $this->_helper->redirector('index');
        }
        $tonerConfig = $masterDevice->getTonerConfig()->tonerConfigName;

        // Get the toner colors that we require
        $requiredTonerColors = Proposalgen_Model_TonerConfig::getRequiredTonersForTonerConfig($masterDevice->tonerConfigId);

        // Get quotegen device
        $device                     = Quotegen_Model_Mapper_Device::getInstance()->find($masterDeviceId);
        $this->view->quotegendevice = $device;

        // Populate manufacturers dropdown
        $manufacturers             = Proposalgen_Model_Mapper_Manufacturer::getInstance()->fetchAll();
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
                    // Get Toner Id
                    $tonerId = $values ['tonerid'];

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
                        // Save if tonerid and device id
                        if ($tonerId && $masterDeviceId)
                        {
                            // Get toner
                            $toner = Proposalgen_Model_Mapper_Toner::getInstance()->find($tonerId);

                            $validToner = in_array($toner->tonerColorId, $requiredTonerColors);

                            if ($validToner)
                            {
                                // Save device toner
                                $deviceTonerMapper           = new Proposalgen_Model_Mapper_DeviceToner();
                                $deviceToner                 = new Proposalgen_Model_DeviceToner();
                                $deviceToner->tonerId        = $tonerId;
                                $deviceToner->masterDeviceId = $masterDeviceId;
                                $deviceTonerMapper->save($deviceToner);

                                $this->_helper->flashMessenger(array(
                                                                    'success' => "The toner was assigned successfully."
                                                               ));
                            }
                            else
                            {
                                $this->_helper->flashMessenger(array(
                                                                    'danger' => "The toner is an invalid toner for this device."
                                                               ));
                            }
                        }
                    }

                    // Unassign Toner
                    else if (isset($values ['btnUnassign']))
                    {
                        // An array for counting each color
                        $tonerCounts = array(
                            Proposalgen_Model_TonerColor::BLACK       => 0,
                            Proposalgen_Model_TonerColor::CYAN        => 0,
                            Proposalgen_Model_TonerColor::MAGENTA     => 0,
                            Proposalgen_Model_TonerColor::YELLOW      => 0,
                            Proposalgen_Model_TonerColor::THREE_COLOR => 0,
                            Proposalgen_Model_TonerColor::FOUR_COLOR  => 0
                        );

                        $tonerErrorMessage = "";
                        $safeToDelete      = true;
                        $tonersByPartType  = Proposalgen_Model_Mapper_Toner::getInstance()->getTonersForDevice($masterDeviceId);

                        // Count the toners
                        foreach ($tonersByPartType as $partTypeId => $tonersByColor)
                        {
                            foreach ($tonersByColor as $tonerColorId => $toners)
                            {
                                $tonerCounts [$tonerColorId] += count($toners);
                            }
                        }

                        $toner = Proposalgen_Model_Mapper_Toner::getInstance()->find($tonerId);

                        // Make sure we're not dropping below one valid toner for the color
                        if ($tonerCounts [$toner->tonerColorId] < 2)
                        {
                            $safeToDelete = false;
                        }

                        // If it's safe to delete, do so.
                        if ($safeToDelete)
                        {
                            $devicetonerMapper = new Proposalgen_Model_Mapper_DeviceToner();
                            $devicetonerMapper->delete(array(
                                                            'toner_id = ?'         => $tonerId,
                                                            'master_device_id = ?' => $masterDeviceId
                                                       ));

                            $this->_helper->flashMessenger(array(
                                                                'success' => "The toner was unassigned successfully."
                                                           ));
                        }
                        else
                        {
                            $this->_helper->flashMessenger(array(
                                                                'danger' => 'You must have at least 1 complete set of toners for this device. If you must unassign this toner you will need to assign a new one before being able to unassign this one.'
                                                           ));
                        }
                    }

                    // Filter
                    else if (isset($values ['btnSearch']))
                    {
                        // Get Device Toners List
                        $deviceToners   = Proposalgen_Model_Mapper_DeviceToner::getInstance()->getDeviceToners($masterDeviceId);
                        $assignedToners = array(
                            ''
                        );
                        foreach ($deviceToners as $toner)
                        {
                            $assignedToners [] = $toner->id;
                        }

                        if ($view == "assigned")
                        {
                            $where = array(
                                'id IN ( ? )' => $assignedToners
                            );
                        }
                        else if ($view == "unassigned")
                        {
                            $where = array(
                                'id NOT IN ( ? )' => $assignedToners
                            );
                        }

                        else if ($filter == 'oemSku')
                        {
                            $where = array_merge((array)$where, array(
                                                                     'oemSku LIKE ( ? )' => '%' . $txtCriteria . '%'
                                                                ));
                        }
                        else
                        {
                            $where = array_merge((array)$where, array(
                                                                     'manufacturerId = ?' => $cboCriteria
                                                                ));
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
                    $this->_helper->flashMessenger(array(
                                                        'danger' => $e->getMessage()
                                                   ));
                }
            }
            else
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $this->_helper->redirector('index');
            }
        }

        $deviceToners   = Proposalgen_Model_Mapper_DeviceToner::getInstance()->getDeviceToners($masterDeviceId);
        $assignedToners = array();
        foreach ($deviceToners as $toner)
        {
            $assignedToners [] = $toner->id;
        }
        $this->view->assignedToners = $assignedToners;

        // Display filterd list of toners
        switch ($tonerConfig)
        {
            case "3 COLOR - COMBINED" :
                $validTonerColors = array(
                    1,
                    5
                );
                break;
            case "3 COLOR - SEPARATED" :
                $validTonerColors = array(
                    1,
                    2,
                    3,
                    4
                );
                break;
            case "4 COLOR - COMBINED" :
                $validTonerColors = array(
                    6
                );
                break;
            case "BLACK ONLY" :
                $validTonerColors = array(
                    1
                );
                break;
        }

        $where = array_merge((array)$where, array(
                                                 'tonerColorId IN ( ? )' => $validTonerColors
                                            ));

        $paginator = new Zend_Paginator(new My_Paginator_MapperAdapter(Proposalgen_Model_Mapper_Toner::getInstance(), $where));

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
        $view           = "assigned";
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
            $this->_helper->flashMessenger(array(
                                                'warning' => 'Please select a master device to edit first.'
                                           ));
            $this->_helper->redirector('index');
        }

        // Get the device and assigned options
        $quoteDevice            = Quotegen_Model_Mapper_Device::getInstance()->find($masterDeviceId);
        $this->view->devicename = $quoteDevice->getMasterDevice()->getFullDeviceName();

        // Get device options list
        $assignedOptions = array();
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
                    // Get Option Id
                    $optionId           = $values ['optionid'];
                    $deviceOptionMapper = new Quotegen_Model_Mapper_DeviceOption();
                    $deviceOption       = new Quotegen_Model_DeviceOption();

                    // Save if option and device id
                    if ($optionId && $masterDeviceId)
                    {
                        $deviceOption->masterDeviceId   = $masterDeviceId;
                        $deviceOption->optionId         = $optionId;
                        $deviceOption->includedQuantity = 0;

                        if (isset($values ['btnAssign']))
                        {
                            // Save device option
                            $deviceOptionMapper->insert($deviceOption);

                            $assignedOptions [] = $deviceOption->optionId;

                            $this->_helper->flashMessenger(array(
                                                                'success' => "The option was assigned successfully."
                                                           ));
                        }

                        // Unassign Option
                        else if (isset($values ['btnUnassign']))
                        {
                            // Delete device option
                            $deviceOptionMapper->delete($deviceOption);

                            // Delete all occurences of this option from the array
                            $arrayKeys = array_keys($assignedOptions, $deviceOption->optionId);
                            foreach ($arrayKeys as $key)
                            {
                                unset($assignedOptions [$key]);
                            }

                            $this->_helper->flashMessenger(array(
                                                                'success' => "The option was unassigned successfully."
                                                           ));
                        }
                    }

                    // Filter
                    else if (isset($values ['btnSearch']))
                    {
                        // Nothing to do here?
                        // Options Search Filter
                        if (isset($values ['txtCriteria']))
                        {
                            $filterWhere = array(
                                "{$values ['criteria_filter']} LIKE ( ? )" => "%{$values ['txtCriteria']}%"
                            );
                        }
                    }

                    // Clear Filter
                    else if (isset($values ['btnClearSearch']))
                    {
                        $this->view->view_filter = "all";
                        $view                    = "assigned";
                    }
                }
                catch (Exception $e)
                {
                    $this->_helper->flashMessenger(array(
                                                        'error' => "An error has occurred."
                                                   ));
                }
                catch (InvalidArgumentException $e)
                {
                    $this->_helper->flashMessenger(array(
                                                        'danger' => $e->getMessage()
                                                   ));
                }
            }
            else
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $this->_helper->redirector('index');
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
                    $where = array(
                        'id IN ( ? )' => $assignedOptions
                    );
                }
                else
                {
                    $where = array(
                        'id IN ( ? )' => "NULL"
                    );
                }
                break;
            case self::OPTION_FILTER_UNASSIGNED :
                if (count($assignedOptions) > 0)
                {
                    $where = array(
                        'id NOT IN ( ? )' => $assignedOptions
                    );
                }
                break;
            case self::OPTION_FILTER_ALL :
                break;
        }

        if (is_array($filterWhere))
        {
            $where = array_merge((array)$where, $filterWhere);
        }

        $this->view->view_filter = $view;

        // Display filterd list of options
        $paginator = new Zend_Paginator(new My_Paginator_MapperAdapter(Quotegen_Model_Mapper_Option::getInstance(), $where));

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
        $where = array(
            'masterDeviceId = ?' => $masterDeviceId
        );

        // If they haven't provided an id, send them back to the view all masterDevice page
        if (!$masterDeviceId)
        {
            $this->_helper->flashMessenger(array(
                                                'warning' => 'Please select a master device to edit first.'
                                           ));
            $this->_helper->redirector('index');
        }

        // Get the device
        $device                 = Quotegen_Model_Mapper_Device::getInstance()->find($masterDeviceId);
        $this->view->devicename = $device->getMasterDevice()->getFullDeviceName();

        // Get device configurations list
        $deviceConfiguration    = Quotegen_Model_Mapper_DeviceConfiguration::getInstance()->fetchAll(array(
                                                                                                          'masterDeviceId = ?' => $masterDeviceId
                                                                                                     ));
        $assignedConfigurations = array();
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
                    $this->_helper->flashMessenger(array(
                                                        'error' => "An error has occurred."
                                                   ));
                }
                catch (InvalidArgumentException $e)
                {
                    $this->_helper->flashMessenger(array(
                                                        'danger' => $e->getMessage()
                                                   ));
                }
            }
            else
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $this->_helper->redirector('index');
            }
        }

        // Display all of the devices
        $paginator = new Zend_Paginator(new My_Paginator_MapperAdapter(Quotegen_Model_Mapper_DeviceConfiguration::getInstance(), $where));

        // Set the current page we're on
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Set how many items to show
        $paginator->setItemCountPerPage(15);

        // Pass the view the paginator
        $this->view->paginator = $paginator;
    }
}
