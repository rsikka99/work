<?php

/**
 * Class Quotegen_Quote_GroupsController
 */
class Quotegen_Quote_GroupsController extends Quotegen_Library_Controller_Quote
{

    public function init ()
    {
        parent::init();
        Quotegen_View_Helper_Quotemenu::setActivePage(Quotegen_View_Helper_Quotemenu::GROUPS_CONTROLLER);
    }

    /**
     * This function takes care of editing quote settings
     */
    public function indexAction ()
    {
        $this->view->headTitle('Quote');
        $this->view->headTitle('Group Devices');
        $form = new Quotegen_Form_Quote_Group($this->_quote);

        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();

            if (isset($values ['goBack']))
            {
                $this->redirector('index', 'quote_devices', null, array(
                    'quoteId' => $this->_quoteId
                ));
            }
            else
            {
                // What are we doing?
                if (isset($values ['addGroup']))
                {
                    // Adding a new group
                    $addGroupSubForm = $form->getSubForm('addGroup');

                    if ($addGroupSubForm->isValid($values))
                    {
                        // Add the new group
                        $quoteDeviceGroup            = new Quotegen_Model_QuoteDeviceGroup();
                        $quoteDeviceGroup->quoteId   = $this->_quoteId;
                        $quoteDeviceGroup->name      = $addGroupSubForm->getValue('name');
                        $quoteDeviceGroup->isDefault = 0;

                        Quotegen_Model_Mapper_QuoteDeviceGroup::getInstance()->insert($quoteDeviceGroup);

                        $this->_flashMessenger->addMessage(array(
                            'success' => "Group '{$quoteDeviceGroup->name}' successfully created."
                        ));

                        // Redirect to ourselves
                        $this->redirector(null, null, null, array(
                            'quoteId' => $this->_quoteId
                        ));
                    }
                    else
                    {
                        $this->_flashMessenger->addMessage(array(
                            'danger' => 'Please correct the errors below:'
                        ));
                    }
                }
                else if (isset($values ['deleteGroup']))
                {
                    if ($form->isValidPartial(array(
                        'deleteGroup' => $values ['deleteGroup']
                    ))
                    )
                    {
                        Quotegen_Model_Mapper_QuoteDeviceGroup::getInstance()->delete($form->getSubForm('deviceQuantity')
                                                                                           ->getValue('deleteGroup'));

                        $this->_flashMessenger->addMessage(array(
                            'success' => 'Group Deleted.'
                        ));

                        // Redirect to ourselves
                        $this->redirector(null, null, null, array(
                            'quoteId' => $this->_quoteId
                        ));
                    }
                    else
                    {
                        $this->_flashMessenger->addMessage(array(
                            'danger' => 'You cannot delete this group.'
                        ));
                    }
                }
                else if (isset($values ['addDevice']))
                {
                    // Adding a device to a group
                    $addDeviceToGroupSubform = $form->getSubForm('addDeviceToGroup');
                    if ($addDeviceToGroupSubform->isValid($values))
                    {
                        $quoteDeviceGroupId = $addDeviceToGroupSubform->getValue('quoteDeviceGroupId');
                        $quoteDeviceId      = $addDeviceToGroupSubform->getValue('quoteDeviceId');
                        $quantity           = $addDeviceToGroupSubform->getValue('quantity');

                        $quoteDeviceGroupDeviceMapper = Quotegen_Model_Mapper_QuoteDeviceGroupDevice::getInstance();
                        $quoteDeviceGroupDevice       = $quoteDeviceGroupDeviceMapper->find(array(
                            $quoteDeviceId,
                            $quoteDeviceGroupId
                        ));

                        // If we found one, update it, otherwise insert a new one
                        if ($quoteDeviceGroupDevice)
                        {
                            // Quantity should never reach over 999
                            $newQuantity = $quoteDeviceGroupDevice->quantity + $quantity;
                            if ($newQuantity > 999)
                            {
                                $newQuantity = 999;
                            }
                            $quoteDeviceGroupDevice->quantity = $newQuantity;
                            Quotegen_Model_Mapper_QuoteDeviceGroupDevice::getInstance()->save($quoteDeviceGroupDevice);
                        }
                        else
                        {
                            $quoteDeviceGroupDevice                          = new Quotegen_Model_QuoteDeviceGroupDevice();
                            $quoteDeviceGroupDevice->monochromePagesQuantity = 0;
                            $quoteDeviceGroupDevice->colorPagesQuantity      = 0;
                            $quoteDeviceGroupDevice->quantity                = $quantity;
                            $quoteDeviceGroupDevice->quoteDeviceId           = $quoteDeviceId;
                            $quoteDeviceGroupDevice->quoteDeviceGroupId      = $quoteDeviceGroupId;

                            Quotegen_Model_Mapper_QuoteDeviceGroupDevice::getInstance()->insert($quoteDeviceGroupDevice);
                        }

                        $this->_flashMessenger->addMessage(array(
                            'success' => "Added the devices successfully."
                        ));

                        // Redirect to ourselves
                        $this->redirector(null, null, null, array(
                            'quoteId' => $this->_quoteId
                        ));
                    }
                    else
                    {
                        $this->_flashMessenger->addMessage(array(
                            'danger' => 'Please correct the errors below:'
                        ));
                    }
                }
                else if (isset($values ['deleteDeviceFromGroup']))
                {
                    $deviceQuantitySubform = $form->getSubForm('deviceQuantity');
                    if ($deviceQuantitySubform->isValid($values))
                    {
                        $keyPair = explode('_', $deviceQuantitySubform->getValue('deleteDeviceFromGroup'));

                        Quotegen_Model_Mapper_QuoteDeviceGroupDevice::getInstance()->delete($keyPair);

                        $this->_flashMessenger->addMessage(array(
                            'success' => 'Device deleted successfully.'
                        ));

                        // Redirect to ourselves
                        $this->redirector(null, null, null, array(
                            'quoteId' => $this->_quoteId
                        ));
                    }
                    else
                    {
                        $this->_flashMessenger->addMessage(array(
                            'danger' => 'You cannot delete this device.'
                        ));
                    }
                }
                else
                {
                    // Check to see if our device quantity subform is valid
                    if ($form->getSubForm('deviceQuantity')->isValid($values))
                    {
                        $db = Zend_Db_Table::getDefaultAdapter();
                        try
                        {
                            // Start of the first transaction.
                            $db->beginTransaction();

                            $deviceQuantitySubform = $form->getSubForm('deviceQuantity');

                            $quantityUpdates = 0;

                            $quoteDeviceGroupDeviceMapper = Quotegen_Model_Mapper_QuoteDeviceGroupDevice::getInstance();
                            /* @var $quoteDeviceGroup Quotegen_Model_QuoteDeviceGroup */
                            foreach ($this->_quote->getQuoteDeviceGroups() as $quoteDeviceGroup)
                            {
                                // If the group name has been changed then save the name 
                                if ($values ["groupName_{$quoteDeviceGroup->id}"] !== $quoteDeviceGroup->name)
                                {
                                    $quoteDeviceGroup->name = $values ["groupName_{$quoteDeviceGroup->id}"];
                                    Quotegen_Model_Mapper_QuoteDeviceGroup::getInstance()->save($quoteDeviceGroup);
                                }
                                /* @var $quoteDeviceGroupDevice Quotegen_Model_QuoteDeviceGroupDevice */
                                foreach ($quoteDeviceGroup->getQuoteDeviceGroupDevices() as $quoteDeviceGroupDevice)
                                {
                                    $newQuantity = $deviceQuantitySubform->getValue("quantity_{$quoteDeviceGroupDevice->quoteDeviceGroupId}_{$quoteDeviceGroupDevice->quoteDeviceId}");
                                    if ((int)$newQuantity !== (int)$quoteDeviceGroupDevice->quantity)
                                    {
                                        $quoteDeviceGroupDevice->quantity = $newQuantity;
                                        $quoteDeviceGroupDeviceMapper->save($quoteDeviceGroupDevice);
                                        $quantityUpdates++;
                                    }
                                }
                            }

                            $this->saveQuote();

                            $db->commit();

                            if ($quantityUpdates > 0)
                            {
                                $this->_flashMessenger->addMessage(array(
                                    'success' => 'Your changes to the device quantities have been saved.'
                                ));
                            }

                            // Redirect?
                            if (isset($values ['saveAndContinue']))
                            {
                                $this->redirector('index', 'quote_pages', null, array(
                                    'quoteId' => $this->_quoteId
                                ));
                            }
                            else
                            {
                                $form->populate($values);
                            }
                        }
                        catch (Exception $e)
                        {
                            $db->rollBack();

                            // Log the error
                            Tangent_Log::logException($e);

                            $this->_flashMessenger->addMessage(array(
                                'danger' => 'There was an error saving your changes. Please try again or contact your system administrator.'
                            ));
                        }
                    }
                    else
                    {
                        $this->_flashMessenger->addMessage(array(
                            'danger' => 'Please correct the errors below:'
                        ));
                    }
                }
            }
        }

        $this->view->form = $form;
    }
}

