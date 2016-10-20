<?php

namespace MPSToolbox\Forms;

use My_Brand;
use Tangent\Validate\FieldDependsOnValue;
use Zend_Form;
use Zend_Validate_Float;
use Zend_Validate_GreaterThan;
use Zend_Validate_NotEmpty;

class SkuQuoteForm extends \My_Form_Form
{

    public function init ()
    {
        $this->setMethod('post');
        $this->setAttrib('id', 'skuQuote');

        /*
         * Dealer SKU
         */
        $this->addElement('text', 'dealerSku', [
            'label'      => My_Brand::$dealerSku,
            'maxlength'  => 255,
            'required'   => false,
            'filters'    => ['StringTrim', 'StripTags'],
            'allowEmpty' => true,
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

        /*
        * Cost
        */
        $this->addElement('text_currency', 'fixedPrice', [
            'label'      => 'Sell price',
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
        $this->addElement('text', 'tags', [
            'label'      => 'Tags',
            'maxlength'  => 255,
            'required'   => false,
            'allowEmpty' => true,
        ]);
        $this->addElement('checkbox', 'online', [
            'label'      => 'Is visible online',
            'required'   => false,
        ]);
        $this->addElement('textarea', 'onlineDescription', [
            'label'    => 'Online description',
            'required' => false,
            'filters'  => ['StringTrim', 'StripTags'],
        ]);
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/hardware-library/sku-quote-form.phtml']]]);
    }
}