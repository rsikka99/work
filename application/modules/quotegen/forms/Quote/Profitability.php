<?php

class Quotegen_Form_Quote_Profitability extends Twitter_Bootstrap_Form_Inline
{
    /**
     * This represents the current quote being worked on
     *
     * @var Quotegen_Model_Quote
     */
    protected $_quote;

    public function __construct ($quote = null, $options = null)
    {
        $this->_quote = $quote;
        parent::__construct($options);
        Quotegen_Form_Quote_Navigation::addFormActionsToForm(Quotegen_Form_Quote_Navigation::BUTTONS_ALL, $this);
    }

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');
        $this->_addClassNames('form-center-actions');
        
        // ----------------------------------------------------------------------
        // Validation Varaibles
        // ----------------------------------------------------------------------
        $minDeviceQuantity = 0;
        $maxDeviceQuantity = 999;
        
        // ----------------------------------------------------------------------
        // Add device to group subform
        // ----------------------------------------------------------------------
        $addDeviceToGroupSubform = new Twitter_Bootstrap_Form_Inline();
        $addDeviceToGroupSubform->setElementDecorators(array (
                'FieldSize', 
                'ViewHelper', 
                'Addon', 
                'PopoverElementErrors', 
                'Wrapper' 
        ));
        
        $this->addSubForm($addDeviceToGroupSubform, 'addDeviceToGroup');
        
        // Quantity of the new device
        $addDeviceToGroupSubform->addElement('text', 'quantity', array (
                'label' => 'Quantity', 
                'class' => 'span1', 
                'value' => 1, 
                'validators' => array (
                        'Int', 
                        array (
                                'validator' => 'Between', 
                                'options' => array (
                                        'min' => $minDeviceQuantity, 
                                        'max' => $maxDeviceQuantity 
                                ) 
                        ) 
                ) 
        ));
        
        // Available devices
        $deviceDropdown = $addDeviceToGroupSubform->createElement('select', 'quoteDeviceId', array (
                'label' => 'Devices:' 
        ));
        
        /* @var $quoteDevice Quotegen_Model_QuoteDevice */
        foreach ( $this->_quote->getQuoteDevices() as $quoteDevice )
        {
            $deviceDropdown->addMultiOption($quoteDevice->getId(), $quoteDevice->getName());
        }
        
        $addDeviceToGroupSubform->addElement($deviceDropdown);
        
        // Groups
        $groupDropdown = $addDeviceToGroupSubform->createElement('select', 'quoteDeviceGroupId', array (
                'label' => 'Groups:' 
        ));
        
        foreach ( $this->_quote->getQuoteDeviceGroups() as $quoteDeviceGroup )
        {
            $groupDropdown->addMultiOption("{$quoteDeviceGroup->getId()}", $quoteDeviceGroup->getName());
        }
        $addDeviceToGroupSubform->addElement($groupDropdown);
        
        // Add button
        $addDeviceToGroupSubform->addElement('button', 'addDevice', array (
                'buttonType' => Twitter_Bootstrap_Form_Element_Button::BUTTON_SUCCESS, 
                'type' => 'submit', 
                'label' => 'Add' 
        ));
        
        // ----------------------------------------------------------------------
        // Add group subform
        // ----------------------------------------------------------------------
        $addGroupSubform = new Twitter_Bootstrap_Form_Inline();
        $addGroupSubform->setElementDecorators(array (
                'FieldSize', 
                'ViewHelper', 
                'Addon', 
                'PopoverElementErrors', 
                'Wrapper' 
        ));
        $this->addSubForm($addGroupSubform, 'addGroup');
        
        // The add group button
        $addGroupButton = $addGroupSubform->createElement('button', 'addGroup', array (
                'buttonType' => Twitter_Bootstrap_Form_Element_Button::BUTTON_SUCCESS, 
                'type' => 'submit', 
                'label' => 'Add Group' 
        ));
        
        // The name of the new group
        $addGroupSubform->addElement('text', 'name', array (
                'required' => true, 
                'prepend' => $addGroupButton, 
                'filters' => array (
                        'StringTrim', 
                        'StripTags' 
                ), 
                'validators' => array (
                        array (
                                'validator' => 'StringLength', 
                                'options' => array (
                                        3, 
                                        40 
                                ) 
                        ) 
                ) 
        ));
        
        // ----------------------------------------------------------------------
        // Quantity subform
        // ----------------------------------------------------------------------
        $deviceQuantitySubform = new Twitter_Bootstrap_Form_Inline();
        $this->addSubForm($deviceQuantitySubform, 'deviceQuantity');
        
        $deviceQuantitySubform->setElementDecorators(array (
                'FieldSize', 
                'ViewHelper', 
                'Addon', 
                'PopoverElementErrors', 
                'Wrapper' 
        ));
        
        // Setup all the boxes
        

        $validQuoteGroupId_DeviceId_Combinations = array ();
        $validQuoteGroupIds = array ();
        /* @var $quoteDeviceGroup Quotegen_Model_QuoteDeviceGroup */
        foreach ( $this->getQuote()->getQuoteDeviceGroups() as $quoteDeviceGroup )
        {
            
            $validQuoteGroupIds [] = "{$quoteDeviceGroup->getId()}";
            /* @var $quoteDeviceGroupDevice Quotegen_Model_QuoteDeviceGroupDevice */
            foreach ( $quoteDeviceGroup->getQuoteDeviceGroupDevices() as $quoteDeviceGroupDevice )
            {
                $deviceQuantitySubform->addElement('text', "quantity_{$quoteDeviceGroupDevice->getQuoteDeviceGroupId()}_{$quoteDeviceGroupDevice->getQuoteDeviceId()}", array (
                        'label' => 'Quantity', 
                        'required' => true, 
                        'class' => 'span1', 
                        'value' => $quoteDeviceGroupDevice->getQuantity(), 
                        'validators' => array (
                                'Int', 
                                array (
                                        'validator' => 'Between', 
                                        'options' => array (
                                                'min' => $minDeviceQuantity, 
                                                'max' => $maxDeviceQuantity 
                                        ) 
                                ) 
                        ) 
                ));
                
                $validQuoteGroupId_DeviceId_Combinations [] = "{$quoteDeviceGroupDevice->getQuoteDeviceId()}_{$quoteDeviceGroupDevice->getQuoteDeviceGroupId()}";
            }
        }
        
        // Delete group button
        $deviceQuantitySubform->addElement('button', 'deleteGroup', array (
                'buttonType' => Twitter_Bootstrap_Form_Element_Button::BUTTON_DANGER, 
                'label' => ' ', 
                'icon' => 'trash', 
                'validators' => array (
                        array (
                                'validator' => 'InArray', 
                                'options' => array (
                                        'haystack' => $validQuoteGroupIds 
                                ) 
                        ) 
                ), 
                'value' => '1' 
        ));
        
        // Delete device from group button
        $deviceQuantitySubform->addElement('button', 'deleteDeviceFromGroup', array (
                'buttonType' => Twitter_Bootstrap_Form_Element_Button::BUTTON_DANGER, 
                'label' => ' ', 
                'icon' => 'trash', 
                'validators' => array (
                        array (
                                'validator' => 'InArray', 
                                'options' => array (
                                        'haystack' => $validQuoteGroupId_DeviceId_Combinations 
                                ) 
                        ) 
                ), 
                'value' => '1' 
        ));
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators(array (
                array (
                        'ViewScript', 
                        array (
                                'viewScript' => 'quote/groups/form/group.phtml' 
                        ) 
                ) 
        ));
    }

    /**
     * Gets the quote
     *
     * @return Quotegen_Model_Quote
     */
    public function getQuote ()
    {
        return $this->_quote;
    }

    /**
     * Sets the quote
     *
     * @param Quotegen_Model_Quote $_quote            
     */
    public function setQuote ($_quote)
    {
        $this->_quote = $_quote;
        return this;
    }
}

?>