<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Forms;

use Zend_Form;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\CategoryMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\CategoryModel;
use My_Brand;
use Zend_Auth;
use Zend_Form_Element_MultiCheckbox;

/**
 * Class OptionForm
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Forms
 */
class OptionForm extends \My_Form_Form
{

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');

        $this->addElement('text', 'name', [
            'label'      => 'Name:',
            'class'      => 'span3',
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

        $this->addElement('textarea', 'description', [
            'label'      => 'Description:',
            'class'      => 'span3',
            'id'         => 'description',
            'required'   => true,
            'style'      => 'height: 100px',
            'maxlength'  => 255,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                [
                    'validator' => 'StringLength',
                    'options'   => [1, 255],
                ],
            ],
        ]);

        $this->addElement('text_currency', 'cost', [
            'label'      => 'Price:',
            'class'      => 'span1',
            'required'   => true,
            'maxlength'  => 8,
        ]);

        $this->addElement('text', 'oemSku', [
            'label'      => 'OEM SKU:',
            'class'      => 'span3',
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

        $this->addElement('text', 'dealerSku', [
            'label'      => My_Brand::$dealerSku . ":",
            'class'      => 'span3',
            'maxlength'  => 255,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                [
                    'validator' => 'StringLength',
                    'options'   => [1, 255],
                ],
            ],
        ]);


        $optionCategoryCheckBox = new Zend_Form_Element_MultiCheckbox('categories', [
            'label' => 'Categories:',
        ]);

        $categories = CategoryMapper::getInstance()->fetchAllForDealer(Zend_Auth::getInstance()->getIdentity()->dealerId);
        /* @var $category CategoryModel */
        foreach ($categories as $category)
        {
            $optionCategoryCheckBox->addMultiOption($category->id, $category->name);
        }

        if ($categories)
        {
            $this->addElement($optionCategoryCheckBox);
        }

        // Add the submit button
        $this->addElement('submit', 'submit', [
            'ignore' => true,
            'label'  => 'Save',
        ]);

        // Add the cancel button
        $this->addElement('submit', 'cancel', [
            'ignore'         => true,
            'label'          => 'Cancel',
            'formnovalidate' => true,
        ]);

    }
}