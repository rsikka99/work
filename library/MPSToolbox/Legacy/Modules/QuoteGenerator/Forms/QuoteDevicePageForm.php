<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Forms;

use Zend_Form;

/**
 * Class QuoteDevicePageForm
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Forms
 */
class QuoteDevicePageForm extends \My_Form_Form
{

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');

        $this->addElement('text_currency', 'costPerPageMonochrome', [
            'label'      => 'CPP Monochrome:',
            'required'   => true,
            'validators' => [
                [
                    'validator' => 'StringLength',
                    'options'   => [1, 255],
                ],
            ],
        ]);

        $this->addElement('text_currency', 'costPerPageColor', [
            'label'      => 'CPP Color:',
            'required'   => true,
            'validators' => [
                [
                    'validator' => 'StringLength',
                    'options'   => [1, 255],
                ],
            ],
        ]);

        $this->addElement('select', 'pageBillingPreference', [
            'label'    => 'Page Billing Preference:',
            'required' => true,
            'filters'  => ['StringTrim', 'StripTags'],
        ]);

        $this->addElement('text_float', 'margin', [
            'label'      => 'Margin:',
            'required'   => true,
            'validators' => [
                [
                    'validator' => 'StringLength',
                    'options'   => [1, 255],
                ],
            ],
        ]);

        // Add the submit button
        $this->addElement('submit', 'submit', [
            'ignore' => true,
            'label'  => 'Save',
        ]);

        // Add the cancel button
        $this->addElement('submit', 'cancel', [
            'ignore' => true,
            'label'  => 'Cancel',
        ]);
    }
}
