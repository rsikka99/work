<?php

class Quotegen_Form_DeviceConfiguration extends EasyBib_Form
{
    
    /**
     * If this is set to false it the form will display a dropdown to select a device.
     *
     * @var string
     */
    protected $_deviceName;

    public function __construct ($deviceName = false, $options = null)
    {
        $this->_deviceName = $deviceName;
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
        
        if ($this->_deviceName)
        {
            $deviceName = new My_Form_Element_Paragraph('deviceName');
            $deviceName->setValue($this->_deviceName);
            $this->addElement($deviceName);
        }
        else
        {
            $quoteDeviceList = array ();
            /* @var $device Quotegen_Model_Device */
            foreach ( Quotegen_Model_Mapper_Device::getInstance()->fetchAll() as $device )
            {
                $quoteDeviceList [$device->getMasterDeviceId()] = $device->getMasterDevice()->getFullDeviceName();
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
}
