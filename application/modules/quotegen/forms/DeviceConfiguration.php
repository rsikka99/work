<?php

/**
 * Class Quotegen_Form_DeviceConfiguration
 */
class Quotegen_Form_DeviceConfiguration extends EasyBib_Form
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
     * @var Zend_Form_Element
     */
    protected $_optionElements = array();

    /**
     * @param int  $id
     * @param null $options
     */
    public function __construct ($id = 0, $options = null)
    {
        $this->_id = $id;
        parent::__construct($options);
    }

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');
        $this->setAttrib('class', 'form-horizontal form-center-actions');

        if ($this->_id > 0)
        {
            $device     = Quotegen_Model_Mapper_DeviceConfiguration::getInstance()->find($this->_id);
            $deviceName = new My_Form_Element_Paragraph('deviceName');
            $deviceName->setValue($device->getDevice()
                                         ->getMasterDevice()
                                         ->getFullDeviceName());
            $this->addElement($deviceName);

            /* @var $deviceConfigurationOption Quotegen_Model_DeviceConfigurationOption */
            foreach ($device->getOptions() as $deviceConfigurationOption)
            {
                $optionElement = $this->createElement('text', "option-{$deviceConfigurationOption->optionId}", array(
                                                                                                                    'label'       => $deviceConfigurationOption->getOption()
                                                                                                                            ->name,
                                                                                                                    'value'       => $deviceConfigurationOption->quantity,
                                                                                                                    'description' => $deviceConfigurationOption->optionId
                                                                                                               )
                );
                $optionElement->setAttrib('class', 'span1');
                $this->_optionElements [] = $optionElement;
            }

            $this->addElements($this->_optionElements);

            // Add the add option
            $this->addElement('submit', 'add', array(
                                                    'ignore' => true,
                                                    'label'  => 'Add',
                                                    'class'  => 'btn btn-success btn-mini'
                                               ));
        }
        else
        {
            $quoteDeviceList = array();
            /* @var $device Quotegen_Model_Device */
            foreach (Quotegen_Model_Mapper_Device::getInstance()->fetchAll() as $device)
            {
                $quoteDeviceList [$device->masterDeviceId] = $device->getMasterDevice()->getFullDeviceName();
            }
            $this->addElement('select', 'masterDeviceId', array(
                                                               'label'        => 'Available Devices:',
                                                               'multiOptions' => $quoteDeviceList
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
        // Only show the custom view script if we are showing defaults
        if ($this->_id)
        {
            $this->setDecorators(array(
                                      array(
                                          'ViewScript',
                                          array(
                                              'viewScript' => 'deviceconfiguration/form/editdeviceconfiguration.phtml'
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
