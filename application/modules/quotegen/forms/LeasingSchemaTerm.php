<?php

/**
 * Class Quotegen_Form_LeasingSchemaTerm
 */
class Quotegen_Form_LeasingSchemaTerm extends EasyBib_Form
{

    /**
     * @param null|Quotegen_Model_LeasingSchemaRange[] $leasingSchemaRanges
     */
    public function __construct ($leasingSchemaRanges = null)
    {

        // Set the method for the display form to POST
        $this->setMethod('POST');

        /**
         * Add class to form for label alignment
         *
         * - Vertical .form-vertical (not required)    Stacked, left-aligned labels
         * over controls (default)
         * - Inline .form-inline Left-aligned label and inline-block controls
         * for compact style
         * - Search .form-search Extra-rounded text input for a typical search
         * aesthetic
         * - Horizontal .form-horizontal
         *
         * Use .form-horizontal to have same experience as with Bootstrap v1!
         */
        $this->setAttrib('class', 'form-horizontal form-center-actions');
        $this->setName('leasingSchemaTerm');
        $this->setAttrib('id', 'leasingSchemaTerm');

        $this->addElement('hidden', 'hdnId', array());

        $this->addElement('text', 'term', array(
                                               'label'      => 'New Term:',
                                               'required'   => true,
                                               'class'      => 'span1',
                                               'maxlength'  => 3,
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
