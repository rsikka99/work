<?php

/**
 * Class Default_Form_ChangePassword
 */
class Default_Form_ChangePassword extends EasyBib_Form
{

    function init ()
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

        $this->setAttrib('class', 'form-horizontal');

        // Add the password element
        $currentPassword = new Zend_Form_Element_Password('current_password', array(
            'label'      => 'Current Password:',
            'required'   => true,
            'filters'    => array(
                'StringTrim'
            ),
            'validators' => array(
                array(
                    'validator' => 'StringLength',
                    'options'   => array(
                        6,
                        255
                    )
                )
            )
        ));

        $this->addElement($currentPassword);

        $newPassword = new Zend_Form_Element_Password('password', array(
                'label'      => 'New Password:',
                'required'   => true,
                'filters'    => array(
                    'StringTrim'
                ),
                'validators' => array(
                    array(
                        'validator' => 'StringLength',
                        'options'   => array(
                            6,
                            80
                        )
                    )
                )
            )
        );
        $this->addElement($newPassword);

        $newPasswordConfirm = new Zend_Form_Element_Password('password_confirm', array(
            'label'         => 'Confirm New Password:',
            'required'      => true,
            'filters'       => array(
                'StringTrim'
            ),
            'validators'    => array(
                array(
                    'validator' => 'StringLength',
                    'options'   => array(
                        6,
                        255
                    )
                ),
                array(
                    'validator' => 'Identical',
                    'options'   => array(
                        'token' => 'password'
                    )
                )
            ),
            'errorMessages' => array(
                'Identical' => 'Passwords must match.'
            )
        ));
        $this->addElement($newPasswordConfirm);

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
