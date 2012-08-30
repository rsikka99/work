<?php

class Quotegen_Form_Quote_Page extends Twitter_Bootstrap_Form_Inline
{
    /**
     * This represent the current quote being in use
     *
     * @var Quotegen_Model_Quote
     */
    private $_quote;

    public function __construct ($quote = null, $options = null)
    {
        $this->_quote = $quote;
        parent::__construct($options);
        Quotegen_Form_Quote_Navigation::addFormActionsToForm(Quotegen_Form_Quote_Navigation::BUTTONS_NEXT, $this);
    }

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');
        $this->_addClassNames('form-center-actions');
        
        /* @var $quoteDeviceGroup Quotegen_Model_QuoteDeviceGroup */
        foreach ( $this->_quote->getQuoteDeviceGroups() as $quoteDeviceGroup )
        {
            if (! $quoteDeviceGroup->getIsDefault())
            {
                $this->addElement('checkbox', "groupPages_{$quoteDeviceGroup->getId()}", array (
                        'label' => 'Group pages', 
                        'decorators' => array (
                                'FieldSize', 
                                'ViewHelper', 
                                'Addon', 
                                'ElementErrors', 
                                array (
                                        'Label', 
                                        array (
                                                'class' => 'control-label' 
                                        ) 
                                ), 
                                'Wrapper' 
                        ) 
                ));
            }
            /* @var $quoteDeviceGroupDevice Quotegen_Model_QuoteDeviceGroupDevice */
            foreach ( $quoteDeviceGroup->getQuoteDeviceGroupDevices() as $quoteDeviceGroupDevice )
            {
                $this->addElement('text', "quantity_black_{$quoteDeviceGroupDevice->getQuoteDeviceGroupId()}_{$quoteDeviceGroupDevice->getQuoteDeviceId()}", array (
                        'label' => 'Quantity', 
                        'required' => true, 
                        'class' => 'span1', 
                        'value' => $quoteDeviceGroupDevice->getMonochromePagesQuantity()
                ));
                
                $this->addElement('text', "quantity_color_{$quoteDeviceGroupDevice->getQuoteDeviceGroupId()}_{$quoteDeviceGroupDevice->getQuoteDeviceId()}", array (
                        'label' => 'Quantity', 
                        'required' => true, 
                        'class' => 'span1', 
                        'value' => $quoteDeviceGroupDevice->getColorPagesQuantity()
                ));
            }
        }
        
        $this->addElement('text', 'pageMargin', array (
                'label' => 'Page Margin',
                'value' => $this->_quote->getPageMargin() 
        ));
        
        $this->addElement('button', 'save', array (
                'buttonType' => Twitter_Bootstrap_Form_Element_Button::BUTTON_SUCCESS, 
                'type' => 'submit', 
                'label' => 'Save' 
        ));
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators(array (
                array (
                        'ViewScript', 
                        array (
                                'viewScript' => 'quote/pages/form/pages.phtml' 
                        ) 
                ) 
        ));
    }

    /**
     *
     * @return the $_quote
     */
    public function getQuote ()
    {
        return $this->_quote;
    }
}