<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Forms;

use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceConfigurationMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\QuoteDeviceMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteDeviceModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteDeviceOptionModel;
use My_Brand;
use My_Form_Element_Paragraph;
use stdClass;
use Zend_Form_Element;
use Zend_Form;

/**
 * Class QuoteDeviceForm
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Forms
 */
class QuoteDeviceForm extends \My_Form_Form
{
    /**
     * If this is set to false it the form will display a drop down to select a device.
     *
     * @var string
     */
    protected $_id;

    /**
     * The quote device
     *
     * @var QuoteDeviceModel
     */
    protected $_quoteDevice;

    /**
     * An array of elements used to display options
     *
     * @var Zend_Form_Element
     */
    protected $_optionElements = [];

    /**
     * @param int        $id      The quote device id
     * @param null|array $options The form options
     */
    public function __construct ($id = 0, $options = null)
    {
        $this->_id = $id;
        parent::__construct($options);
        QuoteNavigationForm::addFormActionsToForm(QuoteNavigationForm::BUTTONS_ALL, $this);
    }

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');

        if ($this->_id > 0)
        {
            $quoteDevice = $this->getQuoteDevice();

            $deviceName = new My_Form_Element_Paragraph('deviceName');
            $deviceName->setValue($quoteDevice->name);
            $deviceName->setIgnore(true);
            $this->addElement($deviceName);

            $this->addElement('text', 'name', [
                'label'    => 'Device Name',
                'disabled' => true,
                'ignore'   => true,
            ]);

            $this->addElement('text', 'oemSku', [
                'label'    => 'OEM SKU',
                'disabled' => true,
                'ignore'   => true,
            ]);

            $this->addElement('text', 'dealerSku', [
                'label'    => My_Brand::$dealerSku,
                'disabled' => true,
                'ignore'   => true,
            ]);

            $this->addElement('text_currency', 'totalPrice', [
                'label'    => 'Total Package Price',
                'disabled' => true,
                'ignore'   => true,
            ]);

            $this->addElement('text_float', 'margin', [
                'label'      => 'Margin',
                'class'      => 'span1',
                'validators' => [
                    'Float',
                    [
                        'validator' => 'Between',
                        'options'   => ['min' => -100, 'max' => 100, 'inclusive' => false],
                    ],
                ],
            ]);

            $this->addElement('text_currency', 'cost', [
                'label'    => 'Cost',
                'disabled' => true,
                'ignore'   => true,
            ]);

            // For each option that is linked with the device
            /* @var $quoteDeviceOption QuoteDeviceOptionModel */
            foreach ($quoteDevice->getQuoteDeviceOptions() as $quoteDeviceOption)
            {
                $object                    = new stdClass();
                $object->quoteDeviceOption = $quoteDeviceOption;

                // Create and text element with its name as option-{id}-quantity
                $optionElement = $this->createElement('text', "option-{$quoteDeviceOption->id}-quantity", [
                    'label'       => $quoteDeviceOption->name,
                    'value'       => $quoteDeviceOption->quantity,
                    'description' => $quoteDeviceOption->id,
                ]);
                $optionElement->setAttrib('class', 'span1');

                // Add the optionElement to the form
                $this->addElement($optionElement);

                // Create a quantity namespace inside object and store the element
                $object->quantity = $optionElement;
                /* @var $quoteDeviceOption QuoteDeviceOptionModel */
                $optionElement = $this->createElement('text', "option-{$quoteDeviceOption->id}-cost", [
                    'label'       => $quoteDeviceOption->name,
                    'value'       => $quoteDeviceOption->cost,
                    'description' => $quoteDeviceOption->id,
                ]);
                $this->addElement($optionElement);

                $object->cost = $optionElement;

                // Set the attribute of the form optionElements to the object array
                $this->_optionElements [] = $object;
            }

            // Add the add option
            $this->addElement('submit', 'add', [
                'ignore' => true,
                'label'  => 'Add',
            ]);
        }
        else
        {
            $quoteDeviceList = [];
            /* @var $device DeviceModel */
            foreach (DeviceMapper::getInstance()->fetchAll() as $quoteDevice)
            {
                $quoteDeviceList [$quoteDevice->masterDeviceId] = $quoteDevice->getMasterDevice()->getFullDeviceName();
            }
            $this->addElement('select', 'masterDeviceId', [
                'label'        => 'Available Devices',
                'multiOptions' => $quoteDeviceList,
            ]);
        }

        $this->addElement('text_currency', 'packageCost', [
            'label'    => 'Package Cost per Unit',
            'value'    => $this->getQuoteDevice()->calculatePackageCost(),
            'disabled' => true,
            'ignore'   => true,
        ]);
    }

    public function loadDefaultDecorators ()
    {
        // Only show the custom view script if we are showing defaults
        if ($this->_id)
        {
            $this->setDecorators([['ViewScript', [
                'viewScript'     => 'forms/quotegen/quote/quote-device-form.phtml',
                'configurations' => DeviceConfigurationMapper::getInstance()->fetchAllDeviceConfigurationByDeviceIdAndDealerId($this->_quoteDevice->getDevice()->masterDeviceId)
            ]]]);
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
     * @return QuoteDeviceModel
     */
    public function getQuoteDevice ()
    {
        if (!isset($this->_quoteDevice) && isset($this->_id))
        {
            $this->_quoteDevice = QuoteDeviceMapper::getInstance()->find($this->_id);
        }

        return $this->_quoteDevice;
    }
}
