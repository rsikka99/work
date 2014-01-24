<?php

/**
 * Class Default_Form_Login
 */
class Default_Form_Login extends Twitter_Bootstrap_Form_Horizontal
{

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');
        // Add an email element
        $this->addElement('text', 'email', array(
            'label'      => 'Email:',
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags'),
            'validators' => array(
                array(
                    'validator' => 'StringLength',
                    'options'   => array(
                        'min' => 4,
                        'max' => 255
                    )
                ),
                'EmailAddress'
            )
        ));

        // Add the password element
        $this->addElement('password', 'password', array(
            'label'      => 'Password:',
            'required'   => true,
            'filters'    => array(
                'StringTrim'
            ),
            'validators' => array(
                array(
                    'validator' => 'StringLength',
                    'options'   => array(
                        'min' => 1,
                        'max' => 255
                    ),
                    'Alnum'
                )
            )
        ));


        $formActions = array();

        //setup submit button
        $formActions[] = $this->createElement('submit', 'login', array(
            'label'      => 'Sign In',
            'ignore'     => true,
            'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY
        ));

        /*
         * Forgot password action
         */
        $formActions[] = $this->createElement('submit', 'forgotPassword', array(
            'label'  => 'Forgot Password',
            'ignore' => true,
        ));

        $this->addDisplayGroup($formActions, 'actions', array(
            'disableLoadDefaultDecorators' => true,
            'decorators'                   => array(
                'Actions'
            ),
            'class'                        => 'form-actions-center'
        ));
    }
}
