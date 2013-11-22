<?php

/**
 * Class Quotegen_Form_QuoteDeviceGroupPage
 */
class Quotegen_Form_QuoteDeviceGroupPage extends EasyBib_Form
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

        $this->addElement('text', 'name', array(
                                               'label'      => 'Name:',
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

        $this->addElement('text', 'oemSku', array(
                                                 'label'      => 'OEM Sku	:',
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

        $this->addElement('text', 'pricePerPage', array(
                                                       'label'      => 'Price Per Page:',
                                                       'required'   => true,
                                                       'filters'    => array(
                                                           'StringTrim',
                                                           'StripTags'
                                                       ),
                                                       'validators' => array(
                                                           'Float',
                                                           array(
                                                               'validator' => 'Between',
                                                               'options'   => array(
                                                                   'min'       => 0,
                                                                   'max'       => 5,
                                                                   'inclusive' => false
                                                               )
                                                           )
                                                       )
                                                  ));

        $this->addElement('text', 'includedPrice', array(
                                                        'label'      => 'Included Price:',
                                                        'required'   => true,
                                                        'filters'    => array(
                                                            'StringTrim',
                                                            'StripTags'
                                                        ),
                                                        'validators' => array(
                                                            'Float',
                                                            array(
                                                                'validator' => 'Between',
                                                                'options'   => array(
                                                                    'min'       => 0,
                                                                    'max'       => 5000,
                                                                    'inclusive' => true
                                                                )
                                                            )
                                                        )
                                                   ));

        $this->addElement('text', 'includedQuantity', array(
                                                           'label'      => 'Included Quantity:',
                                                           'required'   => true,
                                                           'filters'    => array(
                                                               'StringTrim',
                                                               'StripTags'
                                                           ),
                                                           'validators' => array(
                                                               'Int',
                                                               array(
                                                                   'validator' => 'Between',
                                                                   'options'   => array(
                                                                       'min'       => 0,
                                                                       'max'       => 50000,
                                                                       'inclusive' => true
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
