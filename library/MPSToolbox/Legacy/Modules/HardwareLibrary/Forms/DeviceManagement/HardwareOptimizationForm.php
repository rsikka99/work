<?php

namespace MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement;

use Tangent\Validate\FieldDependsOnValue;
use Tangent\Validate\LessThanFormValue;
use Zend_Form;
use Zend_Validate_Between;
use Zend_Validate_Int;
use Zend_Validate_NotEmpty;

/**
 * Class HardwareOptimizationForm
 *
 * @package MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement
 */
class HardwareOptimizationForm extends Zend_Form
{

    public function init ()
    {
        $this->setMethod('post');
        $this->setAttrib('id', 'hardwareOptimization');

        $this->addElement('checkbox', 'isDeviceSwap', array(
            'label'      => 'Is a device swap',
            'validators' => array(
                'int',
                array(
                    'validator' => 'Between',
                    'options'   => array('min' => 0, 'max' => 1)),
            ),
        ));
        /*
         * Parts Cost Per Page
         */
        $this->addElement('text', 'minimumPageCount', array(
            'label'      => 'Minimum Page Count',
            'maxlength'  => 8,
            'allowEmpty' => false,
            'filters'    => array('StringTrim', 'StripTags'),
        ));

        /*
        * Labor Cost Per Page
        */
        $this->addElement('text', 'maximumPageCount', array(
            'label'      => 'Maximum Page Count',
            'maxlength'  => 8,
            'allowEmpty' => false,
            'filters'    => array('StringTrim', 'StripTags'),
            'validators' => array(
                new FieldDependsOnValue('isDeviceSwap', '1', array(
                        new Zend_Validate_NotEmpty(),
                        new Zend_Validate_Int(),
                        new Zend_Validate_Between(array('min' => 1, 'max' => 9223372036854775807)),
                    )
                )
            ),
        ));


        /**
         * Add "depends" validator on minimum page count
         */
        $this->getElement('minimumPageCount')->addValidators(array(new FieldDependsOnValue('isDeviceSwap', '1', array(
            new Zend_Validate_NotEmpty(),
            new Zend_Validate_Int(),
            new Zend_Validate_Between(array('min' => 0, 'max' => 9223372036854775807)),
            new LessThanFormValue($this->getElement('maximumPageCount')->getName()),
        ))));
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators(array(
            array(
                'ViewScript',
                array(
                    'viewScript' => 'forms/hardware-library/device-management/hardware-optimization-form.phtml'
                )
            )
        ));
    }
}