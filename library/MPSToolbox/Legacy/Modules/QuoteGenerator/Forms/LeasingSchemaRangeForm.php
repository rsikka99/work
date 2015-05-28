<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Forms;

use Zend_Form;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\LeasingSchemaTermModel;

/**
 * Class LeasingSchemaRangeForm
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Forms
 */
class LeasingSchemaRangeForm extends \My_Form_Form
{

    /**
     * @param null|LeasingSchemaTermModel[] $leasingSchemaTerms
     */
    public function __construct ($leasingSchemaTerms = null)
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');

        $this->setName('leasingSchemaRange');
        $this->setAttrib('id', 'leasingSchemaRange');

        $this->addElement('hidden', 'hdnId', []);

        $this->addElement('text', 'range', [
            'label'      => 'New Range:',
            'required'   => true,
            'class'      => 'span2 text-right',
            'style'      => 'position: relative; left: -4px;',
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                [
                    'validator' => 'Digits',
                    'message'   => 'Please enter a numeric value.',
                ],
            ],
        ]);

        foreach ($leasingSchemaTerms as $leasingSchemaTerm)
        {
            $leasingSchemaTermId = $leasingSchemaTerm->id;

            $this->addElement('text', "rate{$leasingSchemaTermId}", [
                'label'      => 'Rate:',
                'required'   => true,
                'filters'    => ['StringTrim', 'StripTags'],
                'class'      => 'span2 text-right',
                'validators' => [
                    [
                        'validator' => 'Between',
                        'options'   => [0.00001, 1.00000]
                    ],
                ],
            ]);
        }

        // Add the submit button
        $this->addElement('submit', 'submit', [
            'ignore' => true,
            'label'  => 'Save'
        ]);

        // Add the cancel button
        $this->addElement('submit', 'cancel', [
            'ignore'         => true,
            'label'          => 'Back',
            'formnovalidate' => true,
        ]);
    }
}
