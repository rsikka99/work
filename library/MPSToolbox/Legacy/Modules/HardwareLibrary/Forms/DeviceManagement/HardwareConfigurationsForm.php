<?php

namespace MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement;

use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceConfigurationOptionMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceOptionModel;
use Zend_Form;
use Zend_Auth;
use Zend_Form_Element;

/**
 * Class HardwareConfigurationsForm
 *
 * @package MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement
 */
class HardwareConfigurationsForm extends Zend_Form
{
    /**
     * If this is set to false it the form will display a dropdown to select a device.
     *
     * @var string
     */
    protected $_deviceConfigurationId;

    /**
     * If this is set to false it the form will display a dropdown to select a device.
     *
     * @var string
     */
    protected $_masterDeviceId;

    /**
     * An array of elements used to display options
     *
     * @var Zend_Form_Element
     */
    protected $_optionElements = [];

    /**
     * @param int  $deviceConfigurationId
     * @param int  $masterDeviceId
     * @param null $options
     *
     * @internal param int $id
     */
    public function __construct ($deviceConfigurationId = 0, $masterDeviceId = 0, $options = null)
    {
        $this->_deviceConfigurationId = $deviceConfigurationId;
        $this->_masterDeviceId        = $masterDeviceId;
        parent::__construct($options);
    }

    public function init ()
    {
        $this->setMethod('post');

        $this->addElement('text', 'hardwareConfigurationsname', [
            'label'      => 'Name:',
            'required'   => true,
            'maxlength'  => 255,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                [
                    'validator' => 'StringLength',
                    'options'   => [1, 255],
                ],
            ],
        ]);

        $this->addElement('textarea', 'hardwareConfigurationsdescription', [
            'label'      => 'Description:',
            'required'   => true,
            'style'      => 'height: 100px',
            'cols'       => '40',
            'maxlength'  => 255,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                [
                    'validator' => 'StringLength',
                    'options'   => [1, 255],
                ],
            ],
        ]);

        /**
         * Options
         */
        if ($this->_masterDeviceId > 0)
        {
            $device = DeviceMapper::getInstance()->find([$this->_masterDeviceId, Zend_Auth::getInstance()->getIdentity()->dealerId]);

            if ($device)
            {
                $deviceConfigurationOptionMapper = DeviceConfigurationOptionMapper::getInstance();

                /* @var $deviceOption DeviceOptionModel */
                foreach ($device->getDeviceOptions() as $deviceOption)
                {
                    $deviceConfigurationOption = $deviceConfigurationOptionMapper->find([$this->_deviceConfigurationId, $deviceOption->getOption()->id]);

                    $optionElement = $this->createElement('text', "hardwareConfigurationsoption{$deviceOption->optionId}", [
                        'label'      => $deviceOption->getOption()->name,
                        'value'      => ($deviceConfigurationOption ? $deviceConfigurationOption->quantity : 0),
                        'class'      => 'span4',
                        'maxlength'  => 8,
                        'required'   => false,
                        'allowEmpty' => true,
                        'filters'    => ['StringTrim', 'StripTags'],
                        'validators' => [
                            [
                                'validator' => 'Between',
                                'options'   => ['min' => 0, 'max' => 1000]
                            ],
                            'Int',
                        ],
                    ]);
                    $optionElement->setAttrib('class', 'span1');
                    $optionElement->setAttrib('onkeypress', 'javascript: return numbersonly(this, event)');
                    $this->_optionElements [] = $optionElement;
                }

                if (count($this->_optionElements) > 0)
                {
                    $this->addDisplayGroup($this->_optionElements, "optionsGroup");
                    $optionsGroup = $this->getDisplayGroup("optionsGroup");
                    $optionsGroup->setDescription("Quantity");
                    $optionsGroup->setDecorators([
                        'FormElements',
                        ['HtmlTag',
                         ['tag'   => 'div',
                          'class' => 'myClass']],
                        ['Description',
                         ['tag'       => 'h3',
                          'placement' => 'prepend',
                          'class'     => 'text-center']]
                    ]);
                }
            }
        }
        $this->addElement('hidden', 'hardwareConfigurationsid', []);
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/hardware-library/device-management/hardware-configurations-form.phtml', 'masterDeviceId' => $this->_masterDeviceId]]]);
    }

    public function getOptionElements ()
    {
        return $this->_optionElements;
    }
}