<?php

/**
 * Class Quotegen_Form_LeasingSchemaRange
 */
class Quotegen_Form_LeasingSchemaRange extends EasyBib_Form
{

    /**
     * @param null|Quotegen_Model_LeasingSchemaTerm[] $leasingSchemaTerms
     */
    public function __construct ($leasingSchemaTerms = null)
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');

        $this->setAttrib('class', 'form-horizontal form-center-actions');
        $this->setName('leasingSchemaRange');
        $this->setAttrib('id', 'leasingSchemaRange');

        $this->addElement('hidden', 'hdnId', array());

        $this->addElement('text', 'range', array(
            'label'      => 'New Range:',
            'required'   => true,
            'class'      => 'span1',
            'maxlength'  => 6,
            'style'      => 'position: relative; left: -4px;',
            'filters'    => array(
                'StringTrim',
                'StripTags'
            ),
            'validators' => array(
                array(
                    'validator' => 'StringLength',
                    'options'   => array(
                        1,
                        6
                    )
                ),
                array(
                    'validator' => 'Digits',
                    'message'   => 'Please enter a numeric value.'
                )
            )
        ));

        foreach ($leasingSchemaTerms as $leasingSchemaTerm)
        {
            $leasingSchemaTermId = $leasingSchemaTerm->id;

            $this->addElement('text', "rate{$leasingSchemaTermId}", array(
                'label'      => 'Rate:',
                'required'   => true,
                'maxlength'  => 6,
                'filters'    => array(
                    'StringTrim',
                    'StripTags'
                ),
                'class'      => 'span1',
                'validators' => array(
                    array(
                        'validator' => 'StringLength',
                        'options'   => array(
                            1,
                            6
                        )
                    ),
                    array(
                        'validator' => 'Between',
                        'options'   => array(
                            0.0001,
                            1.0000
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
            'ignore' => true,
            'label'  => 'Back'
        ));


        EasyBib_Form_Decorator::setFormDecorator($this, EasyBib_Form_Decorator::BOOTSTRAP, 'submit', 'cancel');
    }
}
