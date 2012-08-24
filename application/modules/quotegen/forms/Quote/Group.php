<?php

class Quotegen_Form_Quote_Group extends Twitter_Bootstrap_Form_Horizontal
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
        
        $this->addElement('text', 'quantity', array (
                'label' => 'Quantity', 
                'class' => 'span1', 
                'value' => 1 
        ));
        
        /* @var $quoteDeviceGroup Quotegen_Model_QuoteDeviceGroup */
        foreach ( $this->getQuote()->getQuoteDeviceGroups() as $quoteDeviceGroup )
        {
            /* @var $quoteDeviceGroupDevice Quotegen_Model_QuoteDeviceGroupDevice */
            foreach ( $quoteDeviceGroup->getQuoteDeviceGroupDevices() as $quoteDeviceGroupDevice )
            {
                $this->addElement('text', "quantity-{$quoteDeviceGroupDevice->getQuoteDeviceGroupId()}-{$quoteDeviceGroupDevice->getQuoteDeviceId()}", array (
                        'label' => 'Quantity', 
                        'class' => 'span1', 
                        'value' => 1, 
                        'decorators' => array (
                                'FieldSize', 
                                'ViewHelper', 
                                'Addon', 
                                'ElementErrors', 
                                'Wrapper' 
                        ) 
                ));
            }
        }
        
        $deviceDropdown = new Zend_Form_Element_Select('devices', array (
                'label' => 'Devices:' 
        ));
        
        $quoteDevices = $this->_quote->getQuoteDevices();
        
        /* @var $quoteDevice Quotegen_Model_QuoteDevice */
        foreach ( $quoteDevices as $quoteDevice )
        {
            $deviceDropdown->addMultiOption($quoteDevice->getId(), $quoteDevice->getName());
        }
        
        $this->addElement($deviceDropdown);
        
        $groupDropdown = new Zend_Form_Element_Select('groups', array (
                'label' => 'Groups:' 
        ));
        
        $groupDropdown->addMultiOption('1', 'Default Group (Ungrouped)');
        $this->addElement($groupDropdown);
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