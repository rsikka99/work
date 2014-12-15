<?php
use MPSToolbox\Legacy\Modules\QuoteGenerator\Forms\QuoteGroupForm;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\QuoteDeviceGroupDeviceMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\QuoteDeviceGroupMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteDeviceGroupDeviceModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteDeviceGroupModel;

/**
 * Class Quotegen_Quote_GroupsController
 */
class Quotegen_Quote_GroupsController extends Quotegen_Library_Controller_Quote
{

    public function init ()
    {
        parent::init();
        $this->_navigation->setActiveStep(\MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteStepsModel::STEP_GROUP_DEVICES);
    }

    /**
     * This function takes care of editing quote settings
     */
    public function indexAction ()
    {
        $this->_pageTitle = array('Quote', 'Group Devices');
        $form             = new QuoteGroupForm($this->_quote);

        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();

            if (isset($values ['goBack']))
            {
                $this->redirectToRoute('quotes', array(
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
                        $quoteDeviceGroup            = new QuoteDeviceGroupModel();
                        $quoteDeviceGroup->quoteId   = $this->_quoteId;
                        $quoteDeviceGroup->name      = $addGroupSubForm->getValue('name');
                        $quoteDeviceGroup->isDefault = 0;

                        QuoteDeviceGroupMapper::getInstance()->insert($quoteDeviceGroup);

                        $this->_flashMessenger->addMessage(array(
                            'success' => "Group '{$quoteDeviceGroup->name}' successfully created."
                        ));

                        // Redirect to ourselves
                        $this->redirectToRoute(null, array(
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
                        QuoteDeviceGroupMapper::getInstance()->delete($form->getSubForm('deviceQuantity')
                                                                           ->getValue('deleteGroup'));

                        $this->_flashMessenger->addMessage(array(
                            'success' => 'Group Deleted.'
                        ));

                        // Redirect to ourselves
                        $this->redirectToRoute(null, array(
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

                        $quoteDeviceGroupDeviceMapper = QuoteDeviceGroupDeviceMapper::getInstance();
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
                            QuoteDeviceGroupDeviceMapper::getInstance()->save($quoteDeviceGroupDevice);
                        }
                        else
                        {
                            $quoteDeviceGroupDevice                          = new QuoteDeviceGroupDeviceModel();
                            $quoteDeviceGroupDevice->monochromePagesQuantity = 0;
                            $quoteDeviceGroupDevice->colorPagesQuantity      = 0;
                            $quoteDeviceGroupDevice->quantity                = $quantity;
                            $quoteDeviceGroupDevice->quoteDeviceId           = $quoteDeviceId;
                            $quoteDeviceGroupDevice->quoteDeviceGroupId      = $quoteDeviceGroupId;

                            QuoteDeviceGroupDeviceMapper::getInstance()->insert($quoteDeviceGroupDevice);
                        }

                        $this->_flashMessenger->addMessage(array(
                            'success' => "Added the devices successfully."
                        ));

                        // Redirect to ourselves
                        $this->redirectToRoute(null, array(
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

                        QuoteDeviceGroupDeviceMapper::getInstance()->delete($keyPair);

                        $this->_flashMessenger->addMessage(array(
                            'success' => 'Device deleted successfully.'
                        ));

                        // Redirect to ourselves
                        $this->redirectToRoute(null, array(
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

                            $quoteDeviceGroupDeviceMapper = QuoteDeviceGroupDeviceMapper::getInstance();
                            /* @var $quoteDeviceGroup QuoteDeviceGroupModel */
                            foreach ($this->_quote->getQuoteDeviceGroups() as $quoteDeviceGroup)
                            {
                                // If the group name has been changed then save the name 
                                if ($values ["groupName_{$quoteDeviceGroup->id}"] !== $quoteDeviceGroup->name)
                                {
                                    $quoteDeviceGroup->name = $values ["groupName_{$quoteDeviceGroup->id}"];
                                    QuoteDeviceGroupMapper::getInstance()->save($quoteDeviceGroup);
                                }
                                /* @var $quoteDeviceGroupDevice QuoteDeviceGroupDeviceModel */
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
                                $this->updateQuoteStepName();
                                $this->saveQuote();
                                $this->redirectToRoute('quotes.manage-pages', array('quoteId' => $this->_quoteId));
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
                            \Tangent\Logger\Logger::logException($e);

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

