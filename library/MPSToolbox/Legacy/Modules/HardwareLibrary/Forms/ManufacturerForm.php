<?php

namespace MPSToolbox\Legacy\Modules\HardwareLibrary\Forms;

use Zend_Form;

/**
 * Class ManufacturerForm
 *
 * @package MPSToolbox\Legacy\Modules\HardwareLibrary\Forms
 */
class ManufacturerForm extends Zend_Form
{
    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');

        $this->addElement('text', 'fullname', [
            'label'      => 'Full Name:',
            'required'   => true,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                [
                    'validator' => 'StringLength',
                    'options'   => [1, 255],
                ],
            ],
        ]);

        $this->addElement('text', 'displayname', [
            'label'      => 'Display Name:',
            'required'   => false,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                [
                    'validator' => 'StringLength',
                    'options'   => [1, 255],
                ],
            ],
        ]);

        $this->addElement('checkbox', 'isDeleted', [
            'label'   => 'Deleted',
            'filters' => ['Boolean'],
        ]);

        $this->addElement('checkbox', 'isTonerVendor', [
            'label'   => 'Is Toner Vendor',
            'filters' => ['Boolean'],
        ]);

        // Add the submit button
        $this->addElement('submit', 'submit', [
            'ignore' => true,
            'label'  => 'Save'
        ]);

        // Add the cancel button
        $this->addElement('submit', 'cancel', [
            'ignore'          => true,
            'formnovalidate ' => true,
            'label'           => 'Cancel'
        ]);

    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/hardware-library/manufacturer-form.phtml']]]);
    }
}
