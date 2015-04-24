<?php
use MPSToolbox\Legacy\Forms\DeleteConfirmationForm;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Forms\DeviceForm;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Forms\SelectOptionsForm;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\OptionMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\GlobalDeviceConfigurationMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceOptionMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceConfigurationOptionMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceConfigurationMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\UserDeviceConfigurationMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceConfigurationModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceOptionModel;
use Tangent\Controller\Action;

/**
 * Class Quotegen_DeviceController
 */
class Quotegen_DeviceController extends Action
{

    public function init ()
    {
        /* Initialize action controller here */
    }

    /**
     * Gets a device from the database
     *
     * @param int $id
     *
     * @return \MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceModel
     */
    public function getDevice ($id)
    {
        return $this->getDeviceMapper()->find($id);
    }

    /**
     * Gets the mapper
     *
     * @return DeviceMapper
     */
    public function getDeviceMapper ()
    {
        return DeviceMapper::getInstance();
    }

    /**
     * Displays all devices
     */
    public function indexAction ()
    {
        // Display all of the devices
        $paginator = new Zend_Paginator(new My_Paginator_MapperAdapter($this->getDeviceMapper()));

        // Set the current page we're on
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Set how many items to show
        $paginator->setItemCountPerPage(15);

        // Pass the view the paginator
        $this->view->paginator = $paginator;
    }

    /**
     * Deletes a device
     */
    public function deleteAction ()
    {
        $deviceId = $this->_getParam('id', false);

        if (!$deviceId)
        {
            $this->_flashMessenger->addMessage([
                'warning' => 'Please select a device to delete first.'
            ]);
            $this->redirectToRoute('quotes.devices');
        }

        $device = $this->getDevice($deviceId);
        if (!$device)
        {
            $this->_flashMessenger->addMessage([
                'danger' => 'There was an error selecting the device to delete.'
            ]);
            $this->redirectToRoute('quotes.devices');
        }

        // Get all the deviceConfiguration associated with the masterDeviceId
        $deviceConfigurations = DeviceConfigurationMapper::getInstance()->fetchAllDeviceConfigurationByDeviceId($deviceId);

        $message = "Are you sure you want to delete {$device->masterDeviceId}?";
        $form    = new DeleteConfirmationForm($message);

        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (!isset($values ['cancel']))
            {
                // Delete device from database
                if ($form->isValid($values))
                {
                    // Delete quote device options link 
                    DeviceOptionMapper::getInstance()->deleteOptionsByDeviceId($deviceId);

                    /* @var $deviceConfiguration DeviceConfigurationModel */
                    foreach ($deviceConfigurations as $deviceConfiguration)
                    {
                        $deviceConfigurationId = $deviceConfiguration->id;
                        // Delete user device configuration link
                        UserDeviceConfigurationMapper::getInstance()->deleteUserDeviceConfigurationByDeviceId($deviceConfigurationId);
                        // Delete global device configurations link
                        GlobalDeviceConfigurationMapper::getInstance()->delete($deviceConfigurationId);
                        // Delete the device configuration options 
                        DeviceConfigurationOptionMapper::getInstance()->deleteDeviceConfigurationOptionById($deviceConfigurationId);
                        // Delete the deviceConfiguration
                        DeviceConfigurationMapper::getInstance()->delete($deviceConfiguration);
                    }
                    $this->getDeviceMapper()->delete($device);
                    $this->_flashMessenger->addMessage([
                        'success' => "Device  '{$device->getMasterDevice()->getFullDeviceName()}' was deleted successfully."
                    ]);
                    $this->redirectToRoute('quotes.devices');
                }
            }
            else
            {
                $this->redirectToRoute('quotes.devices');
            }
        }
        $this->view->form = $form;
    }

    /**
     * Creates a new device
     */
    public function createAction ()
    {
        $request = $this->getRequest();
        $form    = new DeviceForm();

        if ($request->isPost())
        {
            $values = $request->getPost();

            if (!isset($values ['cancel']))
            {
                try
                {
                    if ($form->isValid($values))
                    {
                        // Save to the database
                        try
                        {
                            $device = new DeviceModel();
                            $device->populate($values);
                            $deviceId = $this->getDeviceMapper()->insert($device);

                            $this->_flashMessenger->addMessage([
                                'success' => "Device {$device->masterDeviceId} was added successfully."
                            ]);

                            // Redirect them here so that the form reloads
                            $this->redirectToRoute('quotes.devices.edit', [
                                'id' => $deviceId
                            ]);
                        }
                        catch (Exception $e)
                        {
                            $this->_flashMessenger->addMessage([
                                'danger' => 'There was an error processing this request.  Please try again.'
                            ]);
                            $form->populate($request->getPost());
                        }
                    }
                    else
                    {
                        throw new Zend_Validate_Exception("Form Validation Failed");
                    }
                }
                catch (Zend_Validate_Exception $e)
                {
                    $form->buildBootstrapErrorDecorators();
                }
            }
            else
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $this->redirectToRoute('quotes.devices');
            }
        }
        $this->view->form = $form;
    }

    /**
     * Edits a device
     */
    public function editAction ()
    {
        $deviceId = $this->_getParam('id', false);

        // If they haven't provided an id, send them back to the view all device page
        if (!$deviceId)
        {
            $this->_flashMessenger->addMessage([
                'warning' => 'Please select a device to edit first.'
            ]);
            $this->redirectToRoute('quotes.devices');
        }

        // Get the device
        $deviceMapper = DeviceMapper::getInstance();

        $device = $deviceMapper->find($deviceId);
        // If the device doesn't exist, send them back to the view all devices page
        if (!$device)
        {
            $this->_flashMessenger->addMessage([
                'danger' => 'There was an error selecting the device to edit.'
            ]);
            $this->redirectToRoute('quotes.devices');
        }

        // Create a new form with the mode and roles set
        $form = new DeviceForm($deviceId);

        // Prepare the data for the form
        $request = $this->getRequest();
        $form->populate($device->toArray());

        // Make sure we are posting data
        if ($request->isPost())
        {
            // Get the post data
            $values = $request->getPost();
            // Set the device name 
            $values['deviceName'] = $device->getMasterDevice()->getFullDeviceName();

            // If we cancelled we don't need to validate anything	
            if (!isset($values ['back']))
            {
                try
                {
                    // Validate the form
                    if ($form->isValid($values))
                    {
                        // Save individual option included quantities
                        $deviceOptionMapper = DeviceOptionMapper::getInstance();
                        foreach ($form->getDeviceOptionElements() as $object)
                        {
                            $includeQuantity = $object->deviceOptionElement->getValue();
                            if (!$includeQuantity)
                            {
                                $includeQuantity = 0;
                            }
                            $object->deviceOption->includedQuantity = $includeQuantity;
                            $deviceOptionMapper->save($object->deviceOption);
                        }

                        $this->_flashMessenger->addMessage([
                            'success' => "Device '" . $this->view->escape($device->getMasterDevice()->getFullDeviceName()) . "' was updated successfully."
                        ]);

                        // Save new device attributes (SKU)
                        $deviceMapper->save($device);

                        if (isset($values ['addOption']))
                        {
                            $this->redirectToRoute('quotes.devices.add-options', [
                                'deviceId' => $deviceId
                            ]);
                        }
                        $form->populate($values);
                    }
                    else
                    {
                        throw new InvalidArgumentException("Please correct the errors below");
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
                $this->redirectToRoute('quotes.devices');
            }
        }

        $form->setDecorators([
            [
                'ViewScript',
                [
                    'viewScript' => 'forms/quotegen/edit-form.phtml',
                ]
            ]
        ]);

        $this->view->form = $form;
    }

    /**
     * Adds options to a device
     */
    public function addoptionsAction ()
    {
        $deviceId = $this->_getParam('deviceId', false);

        $availableOptions = OptionMapper::getInstance()->fetchAllAvailableOptionsForDevice($deviceId);
        if (count($availableOptions) < 1)
        {
            $this->_flashMessenger->addMessage([
                'info' => "There are no more options to add to this device."
            ]);
            $this->redirectToRoute('quotes.devices.edit', [
                'id' => $deviceId
            ]);
        }

        $form = new SelectOptionsForm($availableOptions);
        // Prepare the data for the form
        $request = $this->getRequest();

        $device = $this->getDevice($deviceId);

        $form->populate($device->toArray());

        // Make sure we are posting data
        if ($request->isPost())
        {
            // Get the post data
            $values = $request->getPost();

            // If we cancelled we don't need to validate anything
            if (!isset($values ['cancel']))
            {
                try
                {
                    // Validate the form
                    if ($form->isValid($values))
                    {
                        $deviceOptionMapper           = DeviceOptionMapper::getInstance();
                        $deviceOption                 = new DeviceOptionModel();
                        $deviceOption->masterDeviceId = $device->masterDeviceId;

                        $insertedOptions = 0;

                        foreach ($values ['options'] as $optionId)
                        {
                            $deviceOption->optionId         = (int)$optionId;
                            $deviceOption->includedQuantity = 0;
                            try
                            {
                                $deviceOptionMapper->insert($deviceOption);
                                $insertedOptions++;
                            }
                            catch (Exception $e)
                            {
                                // Do nothing
                            }
                        }

                        $this->_flashMessenger->addMessage([
                            'success' => "Successfully added {$insertedOptions} options to {$device->getMasterDevice()->getFullDeviceName()} successfully."
                        ]);
                        $this->redirectToRoute('quotes.devices.edit', [
                            'id' => $deviceId
                        ]);
                    }
                    else
                    {
                        throw new InvalidArgumentException("Please correct the errors below");
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
                // User has cancelled. Go back to the edit page
                $this->redirectToRoute('quotes.devices.edit', [
                    'id' => $deviceId
                ]);
            }
        }

        $this->view->form = $form;
    }

    /**
     * Deletes an option from a device
     */
    public function deleteoptionAction ()
    {
        $id = $this->_getParam('id', false);

        $optionId = $this->_getParam('optionId', false);

        try
        {
            $deviceOption                 = new DeviceOptionModel();
            $deviceOption->masterDeviceId = $id;
            $deviceOption->optionId       = $optionId;
            DeviceOptionMapper::getInstance()->delete($deviceOption);
            $this->_flashMessenger->addMessage([
                'success' => "Option deleted successfully."
            ]);
        }
        catch (Exception $e)
        {
            $this->_flashMessenger->addMessage([
                'error' => "Could not delete that option."
            ]);
        }

        $this->redirectToRoute('quotes.devices.edit', [
            'id' => $id
        ]);
    }

    /**
     * View a device
     */
    public function viewAction ()
    {
        $this->view->device = DeviceMapper::getInstance()->find($this->_getParam('id', false));
    }
}

