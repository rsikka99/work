<?php

class Quotegen_Form_QuoteDevices extends EasyBib_Form
{
    
    /**
     * If this is set to false it the form will display a dropdown to select a device.
     *
     * @var Quotegen_Model_Quote
     */
    protected $_quote;
    
    /**
     * Contains all the information to edit multiple quote devices on the fly
     *
     * @var mixed
     */
    protected $_elementSets = array ();

    public function __construct ($quote, $options = null)
    {
        $this->_quote = $quote;
        parent::__construct($options);
    }

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');
        /**
         * Add class to form for label alignment
         *
         * - Vertical .form-vertical (not required)	Stacked, left-aligned labels
         * over controls (default)
         * - Inline .form-inline Left-aligned label and inline-block controls
         * for compact style
         * - Search .form-search Extra-rounded text input for a typical search
         * aesthetic
         * - Horizontal .form-horizontal
         *
         * Use .form-horizontal to have same experience as with Bootstrap v1!
         */
        $this->setAttrib('class', 'form-horizontal form-center-actions');
        
        /* @var $quoteDevice Quotegen_Model_QuoteDevice */
        foreach ( $this->_quote->getQuoteDevices() as $quoteDevice )
        {
            $quoteDeviceId = $quoteDevice->getId();
            $elementSet = new stdClass();
            $elementSet->quoteDevice = $quoteDevice;
            
            $elementSet->packagePrice = $this->createElement('text', "packagePrice-{$quoteDeviceId}", array (
                    'label' => 'Package Price:', 
                    'class' => 'input-mini', 
                    'value' => $quoteDevice->getPackagePrice(), 
                    'validators' => array (
                            'Float', 
                            array (
                                    'validator' => 'Between', 
                                    'options' => array (
                                            'min' => 0, 
                                            'max' => 250000,
                                            'inclusive' => false
                                    ) 
                            ) 
                    ) 
            ));
            
            $lessThanElementValidator = new My_Validate_LessThanFormValue($elementSet->packagePrice);
            $elementSet->residual = $this->createElement('text', "residual-{$quoteDeviceId}", array (
                    'label' => 'Residual:',
                    'class' => 'input-mini',
                    'value' => $quoteDevice->getResidual(),
                    'validators' => array (
                            'Float',
                            array (
                                    'validator' => 'Between',
                                    'options' => array (
                                            'min' => 0,
                                            'max' => 250000
                                    )
                            ),
                            $lessThanElementValidator
                    )
            ));
            
            $elementSet->margin = $this->createElement('text', "margin-{$quoteDeviceId}", array (
                    'label' => 'Margin:', 
                    'class' => 'input-mini', 
                    'value' => $quoteDevice->getMargin(), 
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
            
            $elementSet->quantity = $this->createElement('text', "quantity-{$quoteDeviceId}", array (
                    'label' => 'Quantity:', 
                    'class' => 'span1', 
                    'value' => $quoteDevice->getQuantity(), 
                    'validators' => array (
                            'Float', 
                            array (
                                    'validator' => 'Between', 
                                    'options' => array (
                                            'min' => 0, 
                                            'max' => 10000 
                                    ) 
                            ) 
                    ) 
            ));
            
            
            // Add all our elements
            $this->addElement($elementSet->packagePrice);
            $this->addElement($elementSet->margin);
            $this->addElement($elementSet->quantity);
            $this->addElement($elementSet->residual);
            
            $this->_elementSets [] = $elementSet;
        }
        
        EasyBib_Form_Decorator::setFormDecorator($this, EasyBib_Form_Decorator::BOOTSTRAP, 'submit', 'cancel');
    }

    public function loadDefaultDecorators ()
    {
        // Only show the custom view script if we are showing defaults
        if ($this->_quote)
        {
            $this->setDecorators(array (
                    array (
                            'ViewScript', 
                            array (
                                    'viewScript' => 'quote/devices/form/quotedevices.phtml' 
                            ) 
                    ) 
            ));
        }
    }

    /**
     * Gets all the elements grouped by quote device
     *
     * @return array
     */
    public function getElementSets ()
    {
        return $this->_elementSets;
    }
}
