<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Forms;

use Zend_Form;

/**
 * Class QuoteDeviceGroupPageForm
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Forms
 */
class QuoteDeviceGroupPageForm extends \My_Form_Form
{
    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');

        $this->addElement('text', 'name', [
            'label'      => 'Name:',
            'required'   => true,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                [
                    'validator' => 'StringLength',
                    'options'   => [1, 255],
                ],
            ],
        ]);

        $this->addElement('text', 'oemSku', [
            'label'      => 'OEM SKU	:',
            'required'   => true,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                [
                    'validator' => 'StringLength',
                    'options'   => [1, 255],
                ],
            ],
        ]);

        $this->addElement('text_currency', 'pricePerPage', [
            'label'      => 'Price Per Page:',
            'required'   => true,
            'validators' => [
                'Float',
                [
                    'validator' => 'Between',
                    'options'   => ['min' => 0, 'max' => 5, 'inclusive' => false],
                ],
            ],
        ]);

        $this->addElement('text_currency', 'includedPrice', [
            'label'      => 'Included Price:',
            'required'   => true,
            'validators' => [
                'Float',
                [
                    'validator' => 'Between',
                    'options'   => ['min' => 0, 'max' => 5000, 'inclusive' => true],
                ],
            ],
        ]);

        $this->addElement('text_int', 'includedQuantity', [
            'label'      => 'Included Quantity:',
            'required'   => true,
            'validators' => [
                'Int',
                [
                    'validator' => 'Between',
                    'options'   => ['min' => 0, 'max' => 50000, 'inclusive' => true],
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
