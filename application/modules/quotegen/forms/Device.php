<?php

/**
 * Class Quotegen_Form_Device
 */
class Quotegen_Form_Device extends EasyBib_Form
{
    /**
     * Device Id
     *
     * @var int
     */
    protected $_deviceId;

    /**
     * An array of elements that represents Quotegen_Model_Option objects
     *
     * @var array
     */
    protected $_deviceOptionElements = array();

    /**
     * Options for the devices
     *
     * @var Quotegen_Model_Option[]
     */
    protected $_deviceOptions;

    /**
     * @param null|int   $deviceId
     * @param null|array $options
     */
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
         * - Vertical .form-vertical (not required)    Stacked, left-aligned labels
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
            $device     = Quotegen_Model_Mapper_Device::getInstance()->find($this->_deviceId);
            $deviceName = new My_Form_Element_Paragraph('deviceName');
            $deviceName->setValue($device->getMasterDevice()
                ->getFullDeviceName());
            $deviceName->setName('deviceName');

            $this->addElement($deviceName);

            $this->addElement('text', 'oemSku', array(
                                                     'label'      => 'OEM SKU:',
                                                     'required'   => true,
                                                     'filters'    => array(
                                                         'StringTrim',
                                                         'StripTags'
                                                     ),
                                                     'validators' => array(
                                                         array(
                                                             'validator' => 'StringLength',
                                                             'options'   => array(
                                                                 1,
                                                                 255
                                                             )
                                                         )
                                                     )
                                                ));

            /* @var $deviceOption Quotegen_Model_DeviceOption */
            foreach ($device->getDeviceOptions() as $deviceOption)
            {
                $object = new stdClass();

                // Create a unique element with a unique id

                if (!$deviceOption->includedQuantity)
                {
                    $deviceOption->includedQuantity = 0;
                }
                $optionElement = $this->createElement('text', "option-{$deviceOption->getOption()->id}", array(
                                                                                                              'label' => $deviceOption->getOption()->name,
                                                                                                              'value' => $deviceOption->includedQuantity,
                                                                                                              'class' => 'span1'
                                                                                                         ));

                // Add the elements to the table
                $this->addElement($optionElement);

                $object->deviceOptionElement = $optionElement;
                $object->deviceOption        = $deviceOption;
                $object->option              = $deviceOption->getOption();

                // Add the elements to the options array
                $this->_deviceOptionElements [] = $object;
            }
        }
        else
        {
            $masterDeviceList = array();
            /* @var $masterDevice Proposalgen_Model_MasterDevice */
            foreach (Proposalgen_Model_Mapper_MasterDevice::getInstance()->fetchAllAvailableMasterDevices() as $masterDevice)
            {
                $masterDeviceList [$masterDevice->id] = $masterDevice->getFullDeviceName();
            }
            $this->addElement('select', 'masterDeviceId', array(
                                                               'label'        => 'Master Device',
                                                               'multiOptions' => $masterDeviceList
                                                          ));
        }


        // Add the submit button
        $this->addElement('submit', 'submit', array(
                                                   'ignore' => true,
                                                   'label'  => 'Save'
                                              ));

        // Add the cancel button
        $this->addElement('submit', 'cancel', array(
                                                   'ignore' => true,
                                                   'label'  => 'Cancel'
                                              ));

        EasyBib_Form_Decorator::setFormDecorator($this, EasyBib_Form_Decorator::BOOTSTRAP, 'submit', 'cancel');
    }

    public function loadDefaultDecorators ()
    {
        if ($this->_deviceId)
        {
            $this->setDecorators(array(
                                      array(
                                          'ViewScript',
                                          array(
                                              'viewScript' => 'device/form/edit.phtml'
                                          )
                                      )
                                 ));
        }
    }

    /**
     *
     * @return Zend_Form_Element[]
     */
    public function getDeviceOptionElements ()
    {
        return $this->_deviceOptionElements;
    }

    /**
     *
     * @param Zend_Form_Element[] $_deviceOptionElements
     */
    public function setDeviceOptionElements ($_deviceOptionElements)
    {
        $this->_deviceOptionElements = $_deviceOptionElements;
    }

    /**
     * @return Quotegen_Model_Option[]
     */
    public function getDeviceOptions ()
    {
        return $this->_deviceOptions;
    }

    /**
     * @param Quotegen_Model_Option[] $_deviceOptions
     *
     * @return $this
     */
    public function setDeviceOptions ($_deviceOptions)
    {
        $this->_deviceOptions = $_deviceOptions;

        return $this;
    }
}
