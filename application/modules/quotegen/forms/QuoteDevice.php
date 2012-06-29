<?php

class Quotegen_Form_QuoteDevice extends EasyBib_Form
{
    
    /**
     * If this is set to false it the form will display a dropdown to select a device.
     *
     * @var string
     */
    protected $_id;
    
    /**
     * An array of elements used to display options
     *
     * @var unknown_type
     */
    protected $_optionElements = array ();

    public function __construct ($id = 0, $options = null)
    {
        $this->_id = $id;
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
        $this->setAttrib('class', 'form-horizontal');
        
        if ($this->_id > 0)
        {
            $quoteDevice = Quotegen_Model_Mapper_QuoteDevice::getInstance()->find($this->_id);
            
            $deviceName = new My_Form_Element_Paragraph('deviceName');
            $deviceName->setValue($quoteDevice->getName());
            $this->addElement($deviceName);
            
            $this->addElement('text', 'margin', array (
                    'label' => 'Margin:' 
            ));
            
            $this->addElement('text', 'price', array (
                    'label' => 'Price:' 
            ));
            
            $this->addElement('text', 'quantity', array (
                    'label' => 'Quantity:' 
            ));
            
            /* @var $deviceConfigurationOption Quotegen_Model_QuoteDeviceOption */
            foreach ( $quoteDevice->getQuoteDeviceOptions() as $quoteDeviceOption )
            {
                $object = new stdClass();
                $optionElement = $this->createElement('text', "option-{$quoteDeviceOption->getId()}-quantity", array (
                        'label' => $quoteDeviceOption->getName(), 
                        'value' => $quoteDeviceOption->getQuantity(), 
                        'description' => $quoteDeviceOption->getId() 
                ));
                $optionElement->setAttrib('class', 'span1');
                $this->addElement($optionElement);
                $object->quantity = $optionElement;
                
                $optionElement = $this->createElement('text', "option-{$quoteDeviceOption->getId()}-includedQuantity", array (
                        'label' => $quoteDeviceOption->getName(), 
                        'value' => $quoteDeviceOption->getIncludedQuantity(), 
                        'description' => $quoteDeviceOption->getId() 
                ));
                $optionElement->setAttrib('class', 'span1');
                $this->addElement($optionElement);
                $object->includedQuantity = $optionElement;
                
                $this->_optionElements [] = $object;
            }
            
            // Add the add option
            $this->addElement('submit', 'add', array (
                    'ignore' => true, 
                    'label' => 'Add', 
                    'class' => 'btn btn-success btn-mini' 
            ));
        }
        else
        {
            $quoteDeviceList = array ();
            /* @var $device Quotegen_Model_Device */
            foreach ( Quotegen_Model_Mapper_Device::getInstance()->fetchAll() as $quoteDevice )
            {
                $quoteDeviceList [$quoteDevice->getMasterDeviceId()] = $quoteDevice->getMasterDevice()->getFullDeviceName();
            }
            $this->addElement('select', 'masterDeviceId', array (
                    'label' => 'Available Devices:', 
                    'multiOptions' => $quoteDeviceList 
            ));
        }
        
        // Add the submit button
        $this->addElement('submit', 'submit', array (
                'ignore' => true, 
                'label' => 'Save' 
        ));
        
        // Add the cancel button
        $this->addElement('submit', 'cancel', array (
                'ignore' => true, 
                'label' => 'Cancel' 
        ));
        
        EasyBib_Form_Decorator::setFormDecorator($this, EasyBib_Form_Decorator::BOOTSTRAP, 'submit', 'cancel');
    }

    public function loadDefaultDecorators ()
    {
        // Only show the custom view script if we are showing defaults
        if ($this->_id)
        {
            $this->setDecorators(array (
                    array (
                            'ViewScript', 
                            array (
                                    'viewScript' => 'build/form/quotedevice.phtml' 
                            ) 
                    ) 
            ));
        }
    }

    /**
     * Gets all the option elements
     *
     * @return array
     */
    public function getOptionElements ()
    {
        return $this->_optionElements;
    }
}