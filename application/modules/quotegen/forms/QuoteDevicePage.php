<?php

/**
 * Class Quotegen_Form_QuoteDevicePage
 */
class Quotegen_Form_QuoteDevicePage extends EasyBib_Form
{

    public function init ()
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

        $this->addElement('text', 'costPerPageMonochrome', array(
                                                                'label'      => 'CPP Monochrome:',
                                                                'required'   => true,
                                                                'filters'    => array(
                                                                    'StringTrim',
                                                                    'StripTags'
                                                                ),
                                                                'validators' => array(
                                                                    array(
                                                                        'validator' => 'StringLength',
                                                                        'options'   => array(
                                                                            1,
                                                                            255
                                                                        )
                                                                    )
                                                                )
                                                           ));

        $this->addElement('text', 'costPerPageColor', array(
                                                           'label'      => 'CPP Color:',
                                                           'required'   => true,
                                                           'filters'    => array(
                                                               'StringTrim',
                                                               'StripTags'
                                                           ),
                                                           'validators' => array(
                                                               array(
                                                                   'validator' => 'StringLength',
                                                                   'options'   => array(
                                                                       1,
                                                                       255
                                                                   )
                                                               )
                                                           )
                                                      ));

        $this->addElement('select', 'pageBillingPreference', array(
                                                                  'label'    => 'Page Billing Preference:',
                                                                  'required' => true,
                                                                  'filters'  => array(
                                                                      'StringTrim',
                                                                      'StripTags'
                                                                  )
                                                             ));

        $this->addElement('text', 'margin', array(
                                                 'label'      => 'Margin:',
                                                 'required'   => true,
                                                 'filters'    => array(
                                                     'StringTrim',
                                                     'StripTags'
                                                 ),
                                                 'validators' => array(
                                                     array(
                                                         'validator' => 'StringLength',
                                                         'options'   => array(
                                                             1,
                                                             255
                                                         )
                                                     )
                                                 )
                                            ));

        // Add the submit button
        $this->addElement('submit', 'submit', array(
                                                   'ignore' => true,
                                                   'label'  => 'Save'
                                              ));

        // Add the cancel button
        $this->addElement('submit', 'cancel', array(
                                                   'ignore' => true,
                                                   'label'  => 'Cancel'
                                              ));

        EasyBib_Form_Decorator::setFormDecorator($this, EasyBib_Form_Decorator::BOOTSTRAP, 'submit', 'cancel');
    }
}
