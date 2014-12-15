<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Forms;

use Zend_Form;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\LeasingSchemaRangeModel;

/**
 * Class LeasingSchemaTermForm
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Forms
 */
class LeasingSchemaTermForm extends Zend_Form
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

        $this->addElement('hidden', 'hdnId', array());

        $this->addElement('text', 'term', array(
            'label'      => 'New Term:',
            'required'   => true,
            'class'      => 'span2',
            'filters'    => array(
                'StringTrim',
                'StripTags'
            ),
            'validators' => array(
                array(
                    'validator' => 'StringLength',
                    'options'   => array(
                        1,
                        3
                    )
                ),
                array(
                    'validator' => 'Digits',
                    'message'   => 'Please enter a numeric value.'
                )
            )
        ));
        foreach ($leasingSchemaRanges as $leasingSchemaRange)
        {
            $leasingSchemaRangeId = $leasingSchemaRange->id;

            $this->addElement('text', "rate{$leasingSchemaRangeId}", array(
                'label'      => 'Rate:',
                'required'   => true,
                'filters'    => array(
                    'StringTrim',
                    'StripTags'
                ),
                'class'      => 'span2 text-right',
                'validators' => array(
                    array(
                        'validator' => 'Between',
                        'options'   => array(
                            0.00001,
                            1.00000
                        )
                    )
                )
            ));
        }

        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore' => true,
            'label'  => 'Save'
        ));

        // Add the cancel button
        $this->addElement('submit', 'cancel', array(
            'ignore'         => true,
            'label'          => 'Back',
            'formnovalidate' => true,
        ));

    }
}
