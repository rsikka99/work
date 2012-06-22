<?php

class Quotegen_Form_Device extends EasyBib_Form
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
            $masterDeviceList = array ();
            /* @var $masterDevice Proposalgen_Model_MasterDevice */
            foreach ( Proposalgen_Model_Mapper_MasterDevice::getInstance()->fetchAllAvailableMasterDevices() as $masterDevice )
            {
                $masterDeviceList [$masterDevice->getId()] = $masterDevice->getFullDeviceName();
            }
            $this->addElement('select', 'masterDeviceId', array (
                    'label' => 'Master Device', 
                    'multiOptions' => $masterDeviceList 
            ));
        }
        $this->addElement('text', 'sku', array (
                'label' => 'SKU:', 
                'required' => true, 
                'filters' => array (
                        'StringTrim', 
                        'StripTags' 
                ), 
                'validators' => array (
                        array (
                                'validator' => 'StringLength', 
                                'options' => array (
                                        1, 
                                        255 
                                ) 
                        ) 
                ) 
        ));
        
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
