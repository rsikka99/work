<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Forms;

use Zend_Form;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceConfigurationMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceConfigurationOptionModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceModel;
use My_Form_Element_Paragraph;
use Zend_Form_Element;

/**
 * Class DeviceConfigurationForm
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Forms
 */
class DeviceConfigurationForm extends Zend_Form
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
     * @var Zend_Form_Element[]
     */
    protected $_optionElements = [];

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

        //if we're editing a device configuration, rather than creating one
        if ($this->_id > 0)
        {
            $device     = DeviceConfigurationMapper::getInstance()->find($this->_id);
            $deviceName = $this->createElement('text', 'deviceName');
            $deviceName->setValue($device->getDevice()
                                         ->getMasterDevice()
                                         ->getFullDeviceName());
            $deviceName->setLabel('Device Name:');
            $this->addElement($deviceName);

            /* @var $deviceConfigurationOption DeviceConfigurationOptionModel */
            foreach ($device->getOptions() as $deviceConfigurationOption)
            {
                $optionElement = $this->createElement('text', "option-{$deviceConfigurationOption->optionId}", [
                    'label'       => $deviceConfigurationOption->getOption()->name,
                    'value'       => $deviceConfigurationOption->quantity,
                    'description' => $deviceConfigurationOption->optionId,
                ]);
                $optionElement->setAttrib('class', 'span1');
                $this->_optionElements [] = $optionElement;
            }

            $this->addElements($this->_optionElements);

            // Add the add option
            $this->addElement('submit', 'add', [
                'ignore' => true,
                'label'  => 'Add',
                'class'  => 'btn btn-success btn-xs',
            ]);
        }
        // otherwise, we're creating a new device configuration
        else
        {
            $quoteDeviceList = [];
            /* @var $device DeviceModel */
            foreach (DeviceMapper::getInstance()->fetchAll() as $device)
            {
                $quoteDeviceList [$device->masterDeviceId] = $device->getMasterDevice()->getFullDeviceName();
            }
            $this->addElement('select', 'masterDeviceId', [
                'label'        => 'Available Devices:',
                'multiOptions' => $quoteDeviceList,
            ]);
        }

        // Add the submit button
        $this->addElement('submit', 'submit', [
            'ignore' => true,
            'label'  => 'Save',
        ]);

        // Add the cancel button
        $this->addElement('submit', 'cancel', [
            'ignore' => true,
            'label'  => 'Cancel',
        ]);
    }

    public function loadDefaultDecorators ()
    {
        // Only show the custom view script if we are showing defaults
        if ($this->_id)
        {
            $this->setDecorators([['ViewScript', ['viewScript' => 'forms/quotegen/edit-device-configuration-form.phtml',]]]);
        }
    }

    /**
     * Gets all the option elements
     *
     * @return Zend_Form_Element[]
     */
    public function getOptionElements ()
    {
        return $this->_optionElements;
    }
}
