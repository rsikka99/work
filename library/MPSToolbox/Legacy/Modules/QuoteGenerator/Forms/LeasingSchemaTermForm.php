<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Forms;

use Zend_Form;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\LeasingSchemaRangeModel;

/**
 * Class LeasingSchemaTermForm
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Forms
 */
class LeasingSchemaTermForm extends \My_Form_Form
{

    /**
     * @param null|LeasingSchemaRangeModel[] $leasingSchemaRanges
     */
    public function __construct ($leasingSchemaRanges = null)
    {

        // Set the method for the display form to POST
        $this->setMethod('POST');

        $this->setName('leasingSchemaTerm');
        $this->setAttrib('id', 'leasingSchemaTerm');

        $this->addElement('hidden', 'hdnId', []);

        $this->addElement('text', 'term', [
            'label'      => 'New Term:',
            'required'   => true,
            'class'      => 'span2',
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                [
                    'validator' => 'StringLength',
                    'options'   => [1, 3],
                ],
                [
                    'validator' => 'Digits',
                    'message'   => 'Please enter a numeric value.'
                ],
            ],
        ]);
        foreach ($leasingSchemaRanges as $leasingSchemaRange)
        {
            $leasingSchemaRangeId = $leasingSchemaRange->id;

            $this->addElement('text_float', "rate{$leasingSchemaRangeId}", [
                'label'      => 'Rate:',
                'required'   => true,
                'class'      => 'span2 text-right',
                'validators' => [
                    [
                        'validator' => 'Between',
                        'options'   => [0.00001, 1.00000],
                    ],
                ],
            ]);
        }

        // Add the submit button
        $this->addElement('submit', 'submit', [
            'ignore' => true,
            'label'  => 'Save',
        ]);

        // Add the cancel button
        $this->addElement('submit', 'cancel', [
            'ignore'         => true,
            'label'          => 'Back',
            'formnovalidate' => true,
        ]);
    }
}
