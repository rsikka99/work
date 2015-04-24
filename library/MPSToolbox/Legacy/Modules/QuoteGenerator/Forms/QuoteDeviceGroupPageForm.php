<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Forms;

use Zend_Form;

/**
 * Class QuoteDeviceGroupPageForm
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Forms
 */
class QuoteDeviceGroupPageForm extends Zend_Form
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

        $this->addElement('text', 'pricePerPage', [
            'label'      => 'Price Per Page:',
            'required'   => true,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                'Float',
                [
                    'validator' => 'Between',
                    'options'   => ['min' => 0, 'max' => 5, 'inclusive' => false],
                ],
            ],
        ]);

        $this->addElement('text', 'includedPrice', [
            'label'      => 'Included Price:',
            'required'   => true,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                'Float',
                [
                    'validator' => 'Between',
                    'options'   => ['min' => 0, 'max' => 5000, 'inclusive' => true],
                ],
            ],
        ]);

        $this->addElement('text', 'includedQuantity', [
            'label'      => 'Included Quantity:',
            'required'   => true,
            'filters'    => ['StringTrim', 'StripTags'],
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
