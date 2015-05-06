<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Forms;

use Zend_Form;

/**
 * Class CategoryForm
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Forms
 */
class CategoryForm extends \My_Form_Form
{

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');

        $this->addElement('text', 'name', [
            'label'    => 'Name:',
            'required' => true,
            'filters'  => ['StringTrim', 'StripTags'],
        ]);

        $this->addElement('textarea', 'description', [
            'label'    => 'Description:',
            'required' => true,
            'style'    => 'height: 100px',
            'filters'  => ['StringTrim', 'StripTags'],
        ]);

        // Add the submit button
        $this->addElement('submit', 'submit', [
            'ignore' => true,
            'label'  => 'Save'
        ]);

        // Add the cancel button
        $this->addElement('submit', 'cancel', [
            'ignore'         => true,
            'label'          => 'Cancel',
            'formnovalidate' => true
        ]);
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/quotegen/category-options-form.phtml']]]);
    }

}