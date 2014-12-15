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
class DeviceAttributesForm extends Zend_Form
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

        $this->addElement('checkbox', 'isCopier', array(
            'label'    => 'Can Copy/Scan',
            'disabled' => (!$this->_isAllowedToEditFields) ? true : false,
        ));


        $this->addElement('checkbox', 'isDuplex', array(
            'label'    => 'Can Duplex',
            'disabled' => (!$this->_isAllowedToEditFields) ? true : false,
        ));

        $this->addElement('checkbox', 'isFax', array(
            'label'    => 'Can Fax',
            'disabled' => (!$this->_isAllowedToEditFields) ? true : false,
        ));


        $this->addElement('checkbox', 'isCapableOfReportingTonerLevels', array(
            'label'    => 'Capable of Reporting Toner Levels',
            'disabled' => (!$this->_isAllowedToEditFields) ? true : false,
        ));

        $this->addElement('checkbox', 'isA3', array(
            'label'    => 'Can Print A3',
            'disabled' => (!$this->_isAllowedToEditFields) ? true : false,
        ));

        $this->addElement('checkbox', 'jitCompatibleMasterDevice', array(
            'label' => My_Brand::$jit . ' Compatible',
        ));

        /*
         * Print Speed Monochrome
         */
        $this->addElement('text', 'ppmBlack', array(
            'label'      => 'Print Speed Mono',
            'disabled'   => (!$this->_isAllowedToEditFields) ? true : false,
            'maxlength'  => 8,
            'allowEmpty' => true,
            'filters'    => array('StringTrim', 'StripTags'),
            'validators' => array(
                'Int',
                array(
                    'validator' => 'Between',
                    'options'   => array('min' => 1, 'max' => 1000)
                )
            )
        ));

        /*
         * Print Speed Color
         */
        $this->addElement('text', 'ppmColor', array(
            'label'      => 'Print Speed Color',
            'id'         => 'ppmColor',
            'disabled'   => (!$this->_isAllowedToEditFields) ? true : false,
            'maxlength'  => 8,
            'allowEmpty' => true,
            'filters'    => array('StringTrim', 'StripTags'),
            'validators' => array(
                'Int',
                array(
                    'validator' => 'Between',
                    'options'   => array('min' => 1, 'max' => 1000)
                )
            )
        ));

        /*
        * Launch Date
        */
        $minYear           = 1950;
        $maxYear           = ((int)date('Y')) + 2;
        $launchDateElement = $this->createElement('DatePicker', 'launchDate', array(
            'label'      => 'Launch Date',
            'decorators' => array('UiWidgetElement'),
            'required'   => ($this->_isAllowedToEditFields) ? true : false,
            'filters'    => array('StringTrim', 'StripTags'),
            'validators' => array(
                new My_Validate_DateTime('/\d{4}-\d{2}-\d{2}/')
            ),
        ));


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
        $this->addElement('text', 'wattsPowerNormal', array(
            'label'      => 'Operating Wattage',
            'id'         => 'wattsPowerNormal',
            'maxlength'  => 8,
            'disabled'   => (!$this->_isAllowedToEditFields) ? true : false,
            'allowEmpty' => !$this->_isAllowedToEditFields,
            'required'   => $this->_isAllowedToEditFields,
            'filters'    => array('StringTrim', 'StripTags'),
        ));

        if ($this->_isAllowedToEditFields)
        {
            $this->getElement('wattsPowerNormal')->addValidators(
                array(
                    'float',
                    array(
                        'validator' => 'Between',
                        'options'   => array('min' => 1, 'max' => 10000)
                    )
                )
            );
        }

        /*
         * Idle/Sleep Wattage
         */
        $this->addElement('text', 'wattsPowerIdle', array(
            'label'      => 'Idle/Sleep Wattage',
            'id'         => 'wattsPowerIdle',
            'disabled'   => (!$this->_isAllowedToEditFields) ? true : false,
            'maxlength'  => 8,
            'allowEmpty' => false,
            'required'   => $this->_isAllowedToEditFields,
            'filters'    => array('StringTrim', 'StripTags'),
        ));

        if ($this->_isAllowedToEditFields)
        {

            $this->getElement('wattsPowerIdle')->addValidators(
                array(
                    'float',
                    array(
                        'validator' => 'Between',
                        'options'   => array('min' => 1, 'max' => 10000)
                    )
                )
            );
        }
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators(array(
            array(
                'ViewScript',
                array(
                    'viewScript' => 'forms/hardware-library/device-management/device-attributes-form.phtml'
                )
            )
        ));
    }
}