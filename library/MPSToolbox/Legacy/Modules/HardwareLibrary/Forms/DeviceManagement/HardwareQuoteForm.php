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
class HardwareQuoteForm extends \My_Form_Form
{

    public $masterDeviceId;

    public function populate(array $arr) {
        if (isset($arr['masterDeviceId'])) {
            $this->masterDeviceId = $arr['masterDeviceId'];
        }
        $result = parent::populate($arr);
        return $result;
    }

    public function isValid($arr) {
        if (is_array($arr['rent_values'])) $arr['rent_values'] = json_encode($arr['rent_values']);
        if (is_array($arr['plan_values'])) $arr['plan_values'] = json_encode($arr['plan_values']);
        if (is_array($arr['plan_page_values'])) $arr['plan_page_values'] = json_encode($arr['plan_page_values']);
        return parent::isValid($arr);
    }

    public function init ()
    {
        $this->setMethod('post');
        $this->setAttrib('id', 'hardwareQuote');

        $this->addElement('checkbox', 'isSelling', [
            'label' => 'Sell This Device'
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
        $this->addElement('text_currency', 'cost', [
            'label'      => 'Your cost',
            'maxlength'  => 255,
            'required'   => false,
            'allowEmpty' => true,
        ]);
        $this->addElement('text_currency', 'sellPrice', [
            'label'      => 'Sell price',
            'maxlength'  => 255,
            'required'   => false,
            'allowEmpty' => true,
        ]);
        $this->addElement('text_currency', 'rent', [
            'label'      => 'Rent per month',
            'maxlength'  => 255,
            'required'   => false,
            'allowEmpty' => true,
        ]);
        $this->addElement('text_int', 'pagesPerMonth', [
            'label'      => 'Pages per month',
            'maxlength'  => 255,
            'required'   => false,
            'allowEmpty' => true,
        ]);
        $this->addElement('text_currency', 'additionalCpp', [
            'label'      => 'Additional Page Cost',
            'maxlength'  => 255,
            'required'   => false,
            'allowEmpty' => true,
        ]);
        $this->addElement('text', 'dataSheetUrl', [
            'label'      => 'Data Sheet URL',
            'maxlength'  => 255,
            'required'   => false,
            'allowEmpty' => true,
        ]);
        $this->addElement('text', 'reviewsUrl', [
            'label'      => 'Reviews URL',
            'maxlength'  => 255,
            'required'   => false,
            'allowEmpty' => true,
        ]);
        $this->addElement('checkbox', 'online', [
            'label'      => 'Is visible online',
            'required'   => false,
        ]);
        $this->addElement('checkbox', 'taxable', [
            'label'      => 'Is taxable',
            'required'   => false,
        ]);
        $this->addElement('textarea', 'onlineDescription', [
            'label'    => 'Online description',
            'required' => false,
            'filters'  => ['StringTrim', 'StripTags'],
        ]);
        /*
         * Description of standard features
         */
        $this->addElement('textarea', 'description', [
            'label'    => 'Standard Features',
            'required' => false,
            'filters'  => ['StringTrim', 'StripTags'],
        ]);

        $this->addElement('textarea', 'rent_values', [
            'label'    => '',
            'required' => false,
            'filters'  => ['StringTrim', 'StripTags'],
        ]);
        $this->addElement('textarea', 'plan_values', [
            'label'    => '',
            'required' => false,
            'filters'  => ['StringTrim', 'StripTags'],
        ]);
        $this->addElement('textarea', 'plan_page_values', [
            'label'    => '',
            'required' => false,
            'filters'  => ['StringTrim', 'StripTags'],
        ]);
        $this->addElement('text', 'tags', [
            'label'    => 'Tags',
            'required' => false,
            'filters'  => ['StringTrim', 'StripTags'],
        ]);
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/hardware-library/device-management/hardware-quote-form.phtml']]]);
    }
}