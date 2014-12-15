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

        $this->addElement('checkbox', 'isSelling', array(
            'label' => 'Sell This Device'
        ));

        /*
         * Your SKU
         */
        $this->addElement('text', 'oemSku', array(
            'label'      => 'OEM SKU',
            'maxlength'  => 255,
            'required'   => false,
            'filters'    => array('StringTrim', 'StripTags'),
            'allowEmpty' => false,
            'validators' => array(
                new FieldDependsOnValue('isSelling', '1', array(
                    new Zend_Validate_NotEmpty()
                ), array(
                    'validator' => 'StringLength',
                    'options'   => array(1, 255)
                ))
            )
        ));
        /*
         * Dealer SKU
         */
        $this->addElement('text', 'dealerSku', array(
            'label'      => My_Brand::$dealerSku,
            'maxlength'  => 255,
            'required'   => false,
            'filters'    => array('StringTrim', 'StripTags'),
            'allowEmpty' => false,
            'validators' => array(
                new FieldDependsOnValue('isSelling', '1', array(
                    new Zend_Validate_NotEmpty()
                ), array(
                    'validator' => 'StringLength',
                    'options'   => array(1, 255)
                ))
            )
        ));
        /*
        * Cost
        */
        $this->addElement('text', 'cost', array(
            'label'      => 'Your cost',
            'maxlength'  => 255,
            'required'   => false,
            'filters'    => array('StringTrim', 'StripTags'),
            'allowEmpty' => false,
            'validators' => array(
                new FieldDependsOnValue('isSelling', '1', array(
                    new Zend_Validate_NotEmpty(),
                    new Zend_Validate_Float(),
                    new Zend_Validate_GreaterThan(0)
                )),

            )
        ));
        /*
         * Description of standard features
         */
        $this->addElement('textarea', 'description', array(
            'label'    => 'Standard Features',
            'required' => false,
            'filters'  => array('StringTrim', 'StripTags'),
        ));
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators(array(
            array(
                'ViewScript',
                array(
                    'viewScript' => 'forms/hardware-library/device-management/hardware-quote-form.phtml'
                )
            )
        ));
    }
}