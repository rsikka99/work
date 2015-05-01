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
class HardwareOptimizationForm extends \My_Form_Form
{

    public function init ()
    {
        $this->setMethod('post');
        $this->setAttrib('id', 'hardwareOptimization');

        $this->addElement('checkbox', 'isDeviceSwap', [
            'label'      => 'Is a device swap',
            'validators' => [
                'int',
                [
                    'validator' => 'Between',
                    'options'   => ['min' => 0, 'max' => 1]],
            ],
        ]);
        /*
         * Parts Cost Per Page
         */
        $this->addElement('text_int', 'minimumPageCount', [
            'label'      => 'Minimum Page Count',
            'maxlength'  => 8,
            'allowEmpty' => false,
            'validators' => [new FieldDependsOnValue('isDeviceSwap', '1', [
                new Zend_Validate_NotEmpty(),
                new Zend_Validate_Int(),
                new Zend_Validate_Between(['min' => 0, 'max' => 9223372036854775807]),
                new LessThanFormValue('maximumPageCount'),
            ])],
        ]);

        /*
        * Labor Cost Per Page
        */
        $this->addElement('text_int', 'maximumPageCount', [
            'label'      => 'Maximum Page Count',
            'maxlength'  => 8,
            'allowEmpty' => false,
            'validators' => [
                new FieldDependsOnValue('isDeviceSwap', '1', [
                    new Zend_Validate_NotEmpty(),
                    new Zend_Validate_Int(),
                    new Zend_Validate_Between(['min' => 1, 'max' => 9223372036854775807]),
                ]),
            ],
        ]);
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/hardware-library/device-management/hardware-optimization-form.phtml']]]);
    }
}