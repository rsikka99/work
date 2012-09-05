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
        Quotegen_Form_Quote_Navigation::addFormActionsToForm(Quotegen_Form_Quote_Navigation::BUTTONS_ALL, $this);
    }

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');
        $this->_addClassNames('form-center-actions');
        
        // Validation variables 
        $minQuantityPages = 0;
        $maxQuantityPages = 100000;
        
        /* @var $quoteDeviceGroup Quotegen_Model_QuoteDeviceGroup */
        foreach ( $this->_quote->getQuoteDeviceGroups() as $quoteDeviceGroup )
        {
          
            /* @var $quoteDeviceGroupDevice Quotegen_Model_QuoteDeviceGroupDevice */
            foreach ( $quoteDeviceGroup->getQuoteDeviceGroupDevices() as $quoteDeviceGroupDevice )
            {
                // quantity_monochrome_<quoteDeviceGroupId>_<quoteDeviceId> : Quotegen_Model_QuoteDeviceGroupDevice->monochromePagesQuantity
                // quantity_monochrome_<quoteDeviceGroupId>_<quoteDeviceId> is used to store the amount of pages allocated per device
                $this->addElement('text', "quantity_monochrome_{$quoteDeviceGroupDevice->getQuoteDeviceGroupId()}_{$quoteDeviceGroupDevice->getQuoteDeviceId()}", array (
                        'label' => 'Quantity', 
                        'required' => true, 
                        'class' => 'span1', 
                        'value' => $quoteDeviceGroupDevice->getMonochromePagesQuantity(), 
                        'validators' => array (
                                'Int', 
                                array (
                                        'validator' => 'Between', 
                                        'options' => array (
                                                'min' => $minQuantityPages, 
                                                'max' => $maxQuantityPages 
                                        ) 
                                ) 
                        ) 
                ));
                
                // quantity_color_<quoteDeviceGroupId>_<quoteDeviceId> : Quotegen_Model_QuoteDeviceGroupDevice->colorPagesQuantity
                // quantity_color_<quoteDeviceGroupId>_<quoteDeviceId> is used to store the amount of pages allocated per device
                $this->addElement('text', "quantity_color_{$quoteDeviceGroupDevice->getQuoteDeviceGroupId()}_{$quoteDeviceGroupDevice->getQuoteDeviceId()}", array (
                        'label' => 'Quantity', 
                        'required' => true, 
                        'class' => 'span1', 
                        'value' => $quoteDeviceGroupDevice->getColorPagesQuantity(), 
                        'validators' => array (
                                'Int', 
                                array (
                                        'validator' => 'Between', 
                                        'options' => array (
                                                'min' => $minQuantityPages, 
                                                'max' => $maxQuantityPages 
                                        ) 
                                ) 
                        ) 
                ));
            }
        }
        
        // pageMargin : Quotegen_Model_Quote->pageMargin
        // pageMargin is used to determine margin on pages for the entire quote
        $this->addElement('text', 'monochromePageMargin', array (
                'value' => $this->_quote->getMonochromePageMargin(),
                'required' => true,
                'class' => 'input-mini', 
                'validators' => array (
                        'Float', 
                        array (
                                'validator' => 'Between', 
                                'options' => array (
                                        'min' => - 100, 
                                        'max' => 100, 
                                        'inclusive' => false 
                                ) 
                        ) 
                ) 
        ));
        
        // colorPageMargin : Quotegen_Model_Quote->colorPageMargin
        // colorPageMargin is used to set a page margin for all of the color pages on the quote
        $this->addElement('text', 'colorPageMargin', array (
                'value' => $this->_quote->getColorPageMargin(),
                'required' => true,
                'class' => 'input-mini', 
                'validators' => array (
                        'Float', 
                        array (
                                'validator' => 'Between', 
                                'options' => array (
                                        'min' => - 100, 
                                        'max' => 100, 
                                        'inclusive' => false 
                                ) 
                        ) 
                ) 
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