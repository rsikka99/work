<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Forms;

use Zend_Form;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\MasterDeviceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceOptionModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\OptionModel;
use My_Form_Element_Paragraph;
use stdClass;
use Zend_Form_Element;

/**
 * Class DeviceForm
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Forms
 */
class DeviceForm extends Zend_Form
{
    /**
     * Device Id
     *
     * @var int
     */
    protected $_deviceId;

    /**
     * An array of elements that represents MPSToolbox\Legacy\Modules\QuoteGenerator\Models\OptionModel objects
     *
     * @var array
     */
    protected $_deviceOptionElements = [];

    /**
     * Options for the devices
     *
     * @var OptionModel[]
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

        if ($this->_deviceId)
        {
            $device     = DeviceMapper::getInstance()->find($this->_deviceId);
            $deviceName = new My_Form_Element_Paragraph('deviceName');
            $deviceName->setValue($device->getMasterDevice()
                                         ->getFullDeviceName());
            $deviceName->setName('deviceName');

            $this->addElement($deviceName);

            $this->addElement('text', 'oemSku', [
                'label'      => 'OEM SKU:',
                'required'   => true,
                'filters'    => ['StringTrim', 'StripTags'],
                'validators' => [
                    [
                        'validator' => 'StringLength',
                        'options'   => [1, 255],
                    ],
                ],
            ]);

            /* @var $deviceOption DeviceOptionModel */
            foreach ($device->getDeviceOptions() as $deviceOption)
            {
                $object = new stdClass();

                // Create a unique element with a unique id

                if (!$deviceOption->includedQuantity)
                {
                    $deviceOption->includedQuantity = 0;
                }
                $optionElement = $this->createElement('text', "option-{$deviceOption->getOption()->id}", [
                    'label' => $deviceOption->getOption()->name,
                    'value' => $deviceOption->includedQuantity,
                    'class' => 'span1',
                ]);

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
            $masterDeviceList = [];
            /* @var $masterDevice MasterDeviceModel */
            foreach (MasterDeviceMapper::getInstance()->fetchAllAvailableMasterDevices() as $masterDevice)
            {
                $masterDeviceList [$masterDevice->id] = $masterDevice->getFullDeviceName();
            }
            $this->addElement('select', 'masterDeviceId', [
                'label'        => 'Master Device',
                'multiOptions' => $masterDeviceList,
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
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/quotegen/quote-device/device-form.phtml', 'deviceOptionElements' => $this->getDeviceOptionElements()]]]);
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
     * @return OptionModel[]
     */
    public function getDeviceOptions ()
    {
        return $this->_deviceOptions;
    }

    /**
     * @param OptionModel[] $_deviceOptions
     *
     * @return $this
     */
    public function setDeviceOptions ($_deviceOptions)
    {
        $this->_deviceOptions = $_deviceOptions;

        return $this;
    }
}
