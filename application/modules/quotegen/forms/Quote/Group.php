<?php

class Quotegen_Form_Quote_Group extends Twitter_Bootstrap_Form_Inline
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
        $maxDeviceQuantity = 100;
        
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
        
        $deviceDropdown = $addDeviceToGroupSubform->createElement('select', 'devices', array (
                'label' => 'Devices:' 
        ));
        
        $quoteDevices = $this->_quote->getQuoteDevices();
        
        /* @var $quoteDevice Quotegen_Model_QuoteDevice */
        foreach ( $quoteDevices as $quoteDevice )
        {
            $deviceDropdown->addMultiOption($quoteDevice->getId(), $quoteDevice->getName());
        }
        
        $addDeviceToGroupSubform->addElement($deviceDropdown);
        
        $groupDropdown = $addDeviceToGroupSubform->createElement('select', 'groups', array (
                'label' => 'Groups:' 
        ));
        
        $groupDropdown->addMultiOption('1', 'Default Group (Ungrouped)');
        $addDeviceToGroupSubform->addElement($groupDropdown);
        
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
        
        $addGroupButton = $addGroupSubform->createElement('button', 'addGroup', array (
                'buttonType' => Twitter_Bootstrap_Form_Element_Button::BUTTON_SUCCESS, 
                'type' => 'submit', 
                'label' => 'Add Group' 
        ));
        
        $addGroupSubform->addElement('text', 'name', array (
                'required' => true, 
                'prepend' => $addGroupButton 
        ));
        
        // ----------------------------------------------------------------------
        // Quantity subform
        // ----------------------------------------------------------------------
        $deviceQuantitySubform = new Twitter_Bootstrap_Form_Inline();
        $this->addSubForm($addDeviceToGroupSubform, 'deviceQuantity');
        
        /* @var $quoteDeviceGroup Quotegen_Model_QuoteDeviceGroup */
        foreach ( $this->getQuote()->getQuoteDeviceGroups() as $quoteDeviceGroup )
        {
            /* @var $quoteDeviceGroupDevice Quotegen_Model_QuoteDeviceGroupDevice */
            foreach ( $quoteDeviceGroup->getQuoteDeviceGroupDevices() as $quoteDeviceGroupDevice )
            {
                $addDeviceToGroupSubform->addElement('text', "quantity_{$quoteDeviceGroupDevice->getQuoteDeviceGroupId()}_{$quoteDeviceGroupDevice->getQuoteDeviceId()}", array (
                        'label' => 'Quantity', 
                        'required' => true, 
                        'class' => 'span1', 
                        'value' => $quoteDeviceGroupDevice->getQuantity(), 
                        'decorators' => array (
                                'FieldSize', 
                                'ViewHelper', 
                                'Addon', 
                                'ElementErrors', 
                                'PopoverElementErrors' 
                        ) 
                ));
            }
        }
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