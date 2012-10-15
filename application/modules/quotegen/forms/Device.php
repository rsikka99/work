<?php

class Quotegen_Form_Device extends EasyBib_Form
{
    /**
     * Device Id
     *
     * @var int
     */
    protected $_deviceId;
    
    /**
     * An array of elements that repeneds Quotegen_Model_Option objects
     *
     * @var array
     */
    protected $_deviceOptionElements = array ();
    
    /**
     * Options for the devices
     *
     * @var Quotegen_Model_Option array
     */
    protected $_deviceOptions;

    public function __construct ($deviceId = null, $options = null)
    {
        $this->_deviceId = $deviceId;
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
        
        if ($this->_deviceId)
        {
            $device = Quotegen_Model_Mapper_Device::getInstance()->find($this->_deviceId);
            $deviceName = new My_Form_Element_Paragraph('deviceName');
            $deviceName->setValue($device->getMasterDevice()
                ->getFullDeviceName());
            $deviceName->setName('deviceName');
            
            $this->addElement($deviceName);
            
            $this->addElement('text', 'oemSku', array (
                    'label' => 'OEM SKU:', 
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
            
            /* @var $deviceOption Quotegen_Model_DeviceOption */
            foreach ( $device->getDeviceOptions() as $deviceOption )
            {
                $object = new stdClass();

                // Create a unique element with a unique id

                if (! $deviceOption->getIncludedQuantity())
                    $deviceOption->setIncludedQuantity(0);
                $optionElement = $this->createElement('text', "option-{$deviceOption->getOption()->getId()}", array (
                        'label' => $deviceOption->getOption()->getName(), 
                        'value' => $deviceOption->getIncludedQuantity(), 
                        'class' => 'span1' 
                ));
                
                // Add the elements to the table
                $this->addElement($optionElement);
                
                $object->deviceOptionElement = $optionElement;
                $object->deviceOption = $deviceOption;
                $object->option = $deviceOption->getOption();
                
                // Add the elements to the options array
                $this->_deviceOptionElements [] = $object;
            }
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
        if ($this->_deviceId)
        {
            $this->setDecorators(array (
                    array (
                            'ViewScript', 
                            array (
                                    'viewScript' => 'device/form/edit.phtml' 
                            ) 
                    ) 
            ));
        }
    }

    /**
     *
     * @return the $_deviceOptionElements
     */
    public function getDeviceOptionElements ()
    {
        return $this->_deviceOptionElements;
    }

    /**
     *
     * @param multitype: $_deviceOptionElements            
     */
    public function setDeviceOptionElements ($_deviceOptionElements)
    {
        $this->_deviceOptionElements = $_deviceOptionElements;
    }

    /**
     *
     * @return the $_deviceOptions
     */
    public function getDeviceOptions ()
    {
        return $this->_deviceOptions;
    }

    /**
     *
     * @param field_type $_deviceOptions            
     */
    public function setDeviceOptions ($_deviceOptions)
    {
        $this->_deviceOptions = $_deviceOptions;
    }
}
