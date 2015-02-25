<?php

namespace MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement;

use My_Brand;
use Tangent\Validate\FieldDependsOnValue;
use Zend_Form;
use Zend_Validate_Float;
use Zend_Validate_GreaterThan;
use Zend_Validate_NotEmpty;

/**
 * Class HardwareQuoteForm
 *
 * @package MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement
 */
class HardwareQuoteForm extends Zend_Form
{

    public function init ()
    {
        $this->setMethod('post');
        $this->setAttrib('id', 'hardwareQuote');

        $this->addElement('checkbox', 'isSelling', [
            'label' => 'Sell This Device'
        ]);

        /*
         * Your SKU
         */
        $this->addElement('text', 'oemSku', [
            'label'      => 'OEM SKU',
            'maxlength'  => 255,
            'required'   => false,
            'filters'    => ['StringTrim', 'StripTags'],
            'allowEmpty' => false,
            'validators' => [
                new FieldDependsOnValue('isSelling', '1', [
                    new Zend_Validate_NotEmpty()
                ], [
                    'validator' => 'StringLength',
                    'options'   => [1, 255]
                ]),
            ],
        ]);
        /*
         * Dealer SKU
         */
        $this->addElement('text', 'dealerSku', [
            'label'      => My_Brand::$dealerSku,
            'maxlength'  => 255,
            'required'   => false,
            'filters'    => ['StringTrim', 'StripTags'],
            'allowEmpty' => false,
            'validators' => [
                new FieldDependsOnValue('isSelling', '1', [
                    new Zend_Validate_NotEmpty()
                ], [
                    'validator' => 'StringLength',
                    'options'   => [1, 255]
                ]),
            ],
        ]);
        /*
        * Cost
        */
        $this->addElement('text', 'cost', [
            'label'      => 'Your cost',
            'maxlength'  => 255,
            'required'   => false,
            'filters'    => ['StringTrim', 'StripTags'],
            'allowEmpty' => false,
            'validators' => [
                new FieldDependsOnValue('isSelling', '1', [
                    new Zend_Validate_NotEmpty(),
                    new Zend_Validate_Float(),
                    new Zend_Validate_GreaterThan(0),
                ]),
            ],
        ]);
        /*
         * Description of standard features
         */
        $this->addElement('textarea', 'description', [
            'label'    => 'Standard Features',
            'required' => false,
            'filters'  => ['StringTrim', 'StripTags'],
        ]);
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/hardware-library/device-management/hardware-quote-form.phtml']]]);
    }
}