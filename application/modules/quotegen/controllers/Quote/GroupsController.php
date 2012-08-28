<?php

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
        $form = new Quotegen_Form_Quote_Group($this->_quote);
        
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            
            if (isset($values ['goBack']))
            {
                $this->_helper->redirector('index', 'quote_devices', null, array (
                        'quoteId' => $this->_quoteId 
                ));
            }
            else
            {
                // What are we doing?
                

                if (isset($values ['addGroup']))
                {
                    // Adding a new group
                    $addGroupSubform = $form->getSubForm('addGroup');
                    
                    $addDeviceToGroupSubform = $form->getSubForm('addDeviceToGroup');
                    if ($addGroupSubform->isValid($values))
                    {
                        // TODO: Add the new group
                        $quoteDeviceGroup = new Quotegen_Model_QuoteDeviceGroup();
                        $quoteDeviceGroup->setQuoteId($this->_quoteId);
                        $quoteDeviceGroup->setName($addGroupSubform->getValue('name'));
                        
                        $quoteDeviceGroup->setIsDefault(0);
                        $quoteDeviceGroup->setGroupPages(0);
                        
                        Quotegen_Model_Mapper_QuoteDeviceGroup::getInstance()->insert($quoteDeviceGroup);
                        
                        $this->_helper->flashMessenger(array (
                                'success' => "Group '{$quoteDeviceGroup->getName()}' successfully created." 
                        ));
                        
                        // Redirect to ourselves
                        $this->_helper->redirector(null, null, null, array (
                                'quoteId' => $this->_quoteId 
                        ));
                    }
                    else
                    {
                        $this->_helper->flashMessenger(array (
                                'danger' => 'Please correct the errors below:' 
                        ));
                    }
                }
                else if (isset($values ['deleteGroup']))
                {
                    if ($form->isValidPartial(array (
                            'deleteGroup' => $values ['deleteGroup'] 
                    )))
                    {
                        Quotegen_Model_Mapper_QuoteDeviceGroup::getInstance()->delete($form->getSubForm('deviceQuantity')
                            ->getValue('deleteGroup'));
                        
                        $this->_helper->flashMessenger(array (
                                'success' => 'Group Deleted.' 
                        ));
                        
                        // Redirect to ourselves
                        $this->_helper->redirector(null, null, null, array (
                                'quoteId' => $this->_quoteId 
                        ));
                    }
                    else
                    {
                        $this->_helper->flashMessenger(array (
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
                        $quoteDeviceId = $addDeviceToGroupSubform->getValue('quoteDeviceId');
                        $quantity = $addDeviceToGroupSubform->getValue('quantity');
                        
                        $quoteDeviceGroupDeviceMapper = Quotegen_Model_Mapper_QuoteDeviceGroupDevice::getInstance();
                        $quoteDeviceGroupDevice = $quoteDeviceGroupDeviceMapper->find(array($quoteDeviceId, $quoteDeviceGroupId));
                        if ($quoteDeviceGroupDevice)
                        {
                            $quoteDeviceGroupDevice->setQuantity($quoteDeviceGroupDevice->getQuantity() + $quantity);
                            Quotegen_Model_Mapper_QuoteDeviceGroupDevice::getInstance()->save($quoteDeviceGroupDevice);
                        }
                        else
                        {
                            $quoteDeviceGroupDevice = new Quotegen_Model_QuoteDeviceGroupDevice();
                            $quoteDeviceGroupDevice->setMonochromePagesQuantity(0);
                            $quoteDeviceGroupDevice->setColorPagesQuantity(0);
                            $quoteDeviceGroupDevice->setQuantity($quantity);
                            $quoteDeviceGroupDevice->setQuoteDeviceId($quoteDeviceId);
                            $quoteDeviceGroupDevice->setQuoteDeviceGroupId($quoteDeviceGroupId);
                            
                            Quotegen_Model_Mapper_QuoteDeviceGroupDevice::getInstance()->insert($quoteDeviceGroupDevice);
                        }
                        
                        $this->_helper->flashMessenger(array (
                                'success' => "Added the devices successfully." 
                        ));
                    }
                    else
                    {
                        $this->_helper->flashMessenger(array (
                                'danger' => 'Please correct the errors below:' 
                        ));
                    }
                }
                else
                {
                    
                    // TODO: Switch this to validate subforms instead?
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
                            foreach ( $this->_quote->getQuoteDeviceGroups() as $quoteDeviceGroup )
                            {
                                /* @var $quoteDeviceGroupDevice Quotegen_Model_QuoteDeviceGroupDevice */
                                foreach ( $quoteDeviceGroup->getQuoteDeviceGroupDevices() as $quoteDeviceGroupDevice )
                                {
                                    $newQuantity = $deviceQuantitySubform->getValue("quantity_{$quoteDeviceGroupDevice->getQuoteDeviceGroupId()}_{$quoteDeviceGroupDevice->getQuoteDeviceId()}");
                                    if ((int)$newQuantity !== (int)$quoteDeviceGroupDevice->getQuantity())
                                    {
                                        $quoteDeviceGroupDevice->setQuantity($newQuantity);
                                        $quoteDeviceGroupDeviceMapper->save($quoteDeviceGroupDevice);
                                        $quantityUpdates ++;
                                    }
                                }
                            }
                            
                            $this->saveQuote();
                            
                            $db->commit();
                            
                            if ($quantityUpdates > 0)
                            {
                                $this->_helper->flashMessenger(array (
                                        'success' => 'Your changes to the device quantities have been saved.' 
                                ));
                            }
                            
                            // Redirect?
                            if (isset($values ['saveAndContinue']))
                            {
                                $this->_helper->redirector('index', 'quote_pages', null, array (
                                        'quoteId' => $this->_quoteId 
                                ));
                            }
                        }
                        catch ( Exception $e )
                        {
                            $db->rollBack();
                            
                            // Log the error
                            My_Log::logException($e);
                            
                            $this->_helper->flashMessenger(array (
                                    'danger' => 'There was an error saving your changes. Please try again or contact your system administrator.' 
                            ));
                        }
                    }
                    else
                    {
                        $this->_helper->flashMessenger(array (
                                'danger' => 'Please correct the errors below:' 
                        ));
                    }
                }
            }
        }
        
        $this->view->form = $form;
    }
}

