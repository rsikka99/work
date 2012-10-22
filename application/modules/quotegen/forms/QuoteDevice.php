<?php

class Quotegen_Form_QuoteDevice extends Twitter_Bootstrap_Form_Horizontal
{
    
    /**
     * If this is set to false it the form will display a dropdown to select a device.
     *
     * @var string
     */
    protected $_id;
    
    /**
     * The quote device
     *
     * @var Quotegen_Model_QuoteDevice
     */
    protected $_quoteDevice;
    
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
        Quotegen_Form_Quote_Navigation::addFormActionsToForm(Quotegen_Form_Quote_Navigation::BUTTONS_ALL, $this);
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
        
        if ($this->_id > 0)
        {
            $quoteDevice = $this->getQuoteDevice();
            
            $deviceName = new My_Form_Element_Paragraph('deviceName');
            $deviceName->setValue($quoteDevice->getName());
            $deviceName->setIgnore(true);
            $this->addElement($deviceName);
            
            $this->addElement('text', 'name', array (
                    'label' => 'Device Name:', 
                    'disabled' => true, 
                    'ignore' => true 
            ));
            
            $this->addElement('text', 'oemSku', array (
                    'label' => 'OEM Sku:', 
                    'disabled' => true, 
                    'ignore' => true 
            ));
            
            $this->addElement('text', 'dealerSku', array (
                    'label' => 'Dealer Sku:',
                    'disabled' => true,
                    'ignore' => true
            ));
            
            $this->addElement('text', 'totalPrice', array (
                    'label' => 'Total Package Price', 
                    'disabled' => true, 
                    'ignore' => true 
            ));
            
            $this->addElement('text', 'margin', array (
                    'label' => 'Margin:', 
                    'class' => 'span1', 
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
            
            $this->addElement('text', 'cost', array (
                    'label' => 'Cost:', 
                    'disabled' => true, 
                    'ignore' => true 
            ));
            
            // For each option that is linked with the device
            /* @var $deviceConfigurationOption Quotegen_Model_QuoteDeviceOption */
            foreach ( $quoteDevice->getQuoteDeviceOptions() as $quoteDeviceOption )
            {
                $object = new stdClass();
                $object->quoteDeviceOption = $quoteDeviceOption;
                
                // Create and text element with its name as option-{id}-quantity
                $optionElement = $this->createElement('text', "option-{$quoteDeviceOption->getId()}-quantity", array (
                        'label' => $quoteDeviceOption->getName(), 
                        'value' => $quoteDeviceOption->getQuantity(), 
                        'description' => $quoteDeviceOption->getId() 
                ));
                $optionElement->setAttrib('class', 'span1');
                
                // Add the optionElement to the form
                $this->addElement($optionElement);
                
                // Create a quantity namespace inside object and store the element
                $object->quantity = $optionElement;
                /* @var $quoteDeviceOption Quotegen_Model_QuoteDeviceOption */
                $optionElement = $this->createElement('text', "option-{$quoteDeviceOption->getId()}-cost", array (
                        'label' => $quoteDeviceOption->getName(), 
                        'value' => $quoteDeviceOption->getCost(), 
                        'description' => $quoteDeviceOption->getId() 
                ));
                $optionElement->setAttrib('class', 'span1');
                $this->addElement($optionElement);
                
                $object->cost = $optionElement;
                
                // Set the attribute of the form optionElements to the object array
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
                                    'viewScript' => 'quote/devices/form/quotedevice.phtml' 
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

    /**
     * Gets the quote device
     *
     * @return Quotegen_Model_QuoteDevice
     */
    public function getQuoteDevice ()
    {
        if (! isset($this->_quoteDevice) && isset($this->_id))
        {
            $this->_quoteDevice = Quotegen_Model_Mapper_QuoteDevice::getInstance()->find($this->_id);
        }
        return $this->_quoteDevice;
    }
}
