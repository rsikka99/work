<?php

namespace MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement;

use My_Brand;
use My_Validate_DateTime;
use Zend_Form;

/**
 * Class DeviceAttributesForm
 *
 * @package MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement
 */
class DeviceAttributesForm extends \My_Form_Form
{
    protected $_isAllowedToEditFields = false;

    /**
     * @param null $options
     * @param bool $isAllowedToEditFields
     */
    public function __construct ($options = null, $isAllowedToEditFields = false)
    {
        $this->_isAllowedToEditFields = $isAllowedToEditFields;

        if (!$this->_prefixesInitialized)
        {
            if (null !== $this->getView())
            {
                $this->getView()->addHelperPath(
                    'ZendX/JQuery/View/Helper',
                    'ZendX_JQuery_View_Helper'
                );
            }

            $this->addPrefixPath(
                'ZendX_JQuery_Form_Element',
                'ZendX/JQuery/Form/Element',
                'element'
            );

            $this->addElementPrefixPath(
                'ZendX_JQuery_Form_Decorator',
                'ZendX/JQuery/Form/Decorator',
                'decorator'
            );

            $this->addDisplayGroupPrefixPath(
                'ZendX_JQuery_Form_Decorator',
                'ZendX/JQuery/Form/Decorator'
            );

        }

        parent::__construct($options);
    }

    public function init ()
    {
        $this->setMethod('post');
        $this->setAttrib('id', 'deviceAttributes');

        $this->addElement('checkbox', 'isCopier', [
            'label'    => 'Can Copy/Scan',
            'disabled' => !$this->_isAllowedToEditFields,
        ]);


        $this->addElement('checkbox', 'isDuplex', [
            'label'    => 'Can Duplex',
            'disabled' => !$this->_isAllowedToEditFields,
        ]);

        $this->addElement('checkbox', 'isFax', [
            'label'    => 'Can Fax',
            'disabled' => !$this->_isAllowedToEditFields,
        ]);


        $this->addElement('checkbox', 'isCapableOfReportingTonerLevels', [
            'label'    => 'Can Report Toner Levels',
            'disabled' => !$this->_isAllowedToEditFields,
        ]);

        $this->addElement('checkbox', 'isA3', [
            'label'    => 'Can Print A3',
            'disabled' => !$this->_isAllowedToEditFields,
        ]);

        $this->addElement('checkbox', 'jitCompatibleMasterDevice', [
            'label' => My_Brand::$jit . ' Compatible',
            'disabled' => !$this->_isAllowedToEditFields,
        ]);

        $this->addElement('checkbox', 'isSmartphone', [
            'label'    => 'Smartphone/tablet printing',
            'disabled' => !$this->_isAllowedToEditFields,
        ]);

        $this->addElement('text_int', 'additionalTrays', [
            'label'    => 'Additional paper trays available',
            'disabled' => !$this->_isAllowedToEditFields,
            'validators' => [
                'Int',
                [
                    'validator' => 'Between',
                    'options'   => ['min' => 0, 'max' => 10],
                ],
            ],
        ]);

        $this->addElement('checkbox', 'isPIN', [
            'label'    => 'PIN Printing',
            'disabled' => !$this->_isAllowedToEditFields,
        ]);

        $this->addElement('checkbox', 'isAccessCard', [
            'label'    => 'Access Card Printing',
            'disabled' => !$this->_isAllowedToEditFields,
        ]);

        $this->addElement('checkbox', 'isWalkup', [
            'label'    => 'Walk-up USB Printing',
            'disabled' => !$this->_isAllowedToEditFields,
        ]);

        $this->addElement('checkbox', 'isStapling', [
            'label'    => 'Stapling',
            'disabled' => !$this->_isAllowedToEditFields,
        ]);

        $this->addElement('checkbox', 'isBinding', [
            'label'    => 'Binding',
            'disabled' => !$this->_isAllowedToEditFields,
        ]);

        $this->addElement('checkbox', 'isTouchscreen', [
            'label'    => 'Has Touchscreen',
            'disabled' => !$this->_isAllowedToEditFields,
        ]);

        $this->addElement('checkbox', 'isADF', [
            'label'    => 'ADF scan possible',
            'disabled' => !$this->_isAllowedToEditFields,
        ]);

        $this->addElement('checkbox', 'isUSB', [
            'label'    => 'USB Connectivity',
            'disabled' => !$this->_isAllowedToEditFields,
        ]);

        $this->addElement('checkbox', 'isWired', [
            'label'    => 'Wired (Ethernet) Connectivity',
            'disabled' => !$this->_isAllowedToEditFields,
        ]);

        $this->addElement('checkbox', 'isWireless', [
            'label'    => 'Wireless (WiFi) Connectivity',
            'disabled' => !$this->_isAllowedToEditFields,
        ]);

        /*
         * Print Speed Monochrome
         */
        $this->addElement('text_int', 'ppmBlack', [
            'label'      => 'Print Speed Mono',
            'disabled'   => !$this->_isAllowedToEditFields,
            'maxlength'  => 8,
            'allowEmpty' => true,
            'validators' => [
                'Int',
                [
                    'validator' => 'Between',
                    'options'   => ['min' => 1, 'max' => 1000],
                ],
            ],
        ]);

        /*
         * Print Speed Color
         */
        $this->addElement('text_int', 'ppmColor', [
            'label'      => 'Print Speed Color',
            'id'         => 'ppmColor',
            'disabled'   => !$this->_isAllowedToEditFields,
            'maxlength'  => 8,
            'allowEmpty' => true,
            'validators' => [
                'Int',
                [
                    'validator' => 'Between',
                    'options'   => ['min' => 1, 'max' => 1000],
                ],
            ],
        ]);

        /*
        * Launch Date
        */
        $minYear           = 1950;
        $maxYear           = ((int)date('Y')) + 2;
        $launchDateElement = $this->createElement('DatePicker', 'launchDate', [
            'label'      => 'Launch Date',
            'decorators' => ['UiWidgetElement'],
            'required'   => $this->_isAllowedToEditFields,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                new My_Validate_DateTime('/\d{4}-\d{2}-\d{2}/'),
            ],
        ]);


        $launchDateElement->setJQueryParam('dateFormat', 'yy-mm-dd')
                          ->setJQueryParam('changeYear', 'true')
                          ->setJqueryParam('changeMonth', 'true')
                          ->setJqueryParam('yearRange', "{$minYear}:{$maxYear}");

        if (!$this->_isAllowedToEditFields)
        {
            $launchDateElement->setAttrib('disabled', true);
        }

        $this->addElement($launchDateElement);

        /*
         * Operating Wattage
         */
        $this->addElement('text_float', 'wattsPowerNormal', [
            'label'      => 'Operating Wattage',
            'id'         => 'wattsPowerNormal',
            'maxlength'  => 8,
            'disabled'   => !$this->_isAllowedToEditFields,
            'allowEmpty' => !$this->_isAllowedToEditFields,
            'required'   => $this->_isAllowedToEditFields,
        ]);

        if ($this->_isAllowedToEditFields)
        {
            $this->getElement('wattsPowerNormal')->setValidators([
                'float',
                [
                    'validator' => 'Between',
                    'options'   => ['min' => 1, 'max' => 10000]
                ],
            ]);
        }

        /*
         * Idle/Sleep Wattage
         */
        $this->addElement('text_float', 'wattsPowerIdle', [
            'label'      => 'Idle/Sleep Wattage',
            'id'         => 'wattsPowerIdle',
            'maxlength'  => 8,
            'disabled'   => !$this->_isAllowedToEditFields,
            'allowEmpty' => !$this->_isAllowedToEditFields,
            'required'   => $this->_isAllowedToEditFields,
        ]);

        if ($this->_isAllowedToEditFields)
        {

            $this->getElement('wattsPowerIdle')->setValidators([
                'float',
                [
                    'validator' => 'Between',
                    'options'   => ['min' => 1, 'max' => 10000],
                ],
            ]);
        }
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/hardware-library/device-management/device-attributes-form.phtml']]]);
    }
}