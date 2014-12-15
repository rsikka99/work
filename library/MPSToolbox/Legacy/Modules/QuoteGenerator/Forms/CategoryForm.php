<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Forms;

use Zend_Form;

/**
 * Class CategoryForm
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Forms
 */
class CategoryForm extends Zend_Form
{

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');

        $this->addElement('text', 'name', array(
            'label'    => 'Name:',
            'required' => true,

            'filters'  => array(
                'StringTrim',
                'StripTags'
            )
        ));

        $this->addElement('textarea', 'description', array(
            'label'    => 'Description:',
            'required' => true,
            'style'    => 'height: 100px',
            'filters'  => array(
                'StringTrim',
                'StripTags'
            )
        ));

        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore' => true,
            'label'  => 'Save'
        ));

        // Add the cancel button
        $this->addElement('submit', 'cancel', array(
            'ignore'         => true,
            'label'          => 'Cancel',
            'formnovalidate' => true
        ));
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators(array(
            array(
                'ViewScript',
                array(
                    'viewScript' => 'forms/quotegen/category-options-form.phtml'
                )
            )
        ));
    }

}