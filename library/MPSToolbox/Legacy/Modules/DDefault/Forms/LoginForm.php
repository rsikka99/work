<?php

namespace MPSToolbox\Legacy\Modules\DDefault\Forms;

use Zend_Form;

/**
 * Class LoginForm
 *
 * @package MPSToolbox\Legacy\Modules\DDefault\Forms
 */
class LoginForm extends Zend_Form
{

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');

        // Add an email element
        $this->addElement('text', 'email', array(
            'label'         => 'Email:',
            'placeholder'   => 'Email Address',
            'required'      => true,
            'filters'       => array(
                'StringTrim',
                'StripTags',
            ),
            'validators'    => array(
                array(
                    'validator' => 'StringLength',
                    'options'   => array(
                        'min' => 4,
                        'max' => 255,
                    ),
                ),
                array('validator' => 'EmailAddress',),
            ),
            'errorMessages' => array('Invalid email address'),
        ));

        // Add the password element
        $this->addElement('password', 'password', array(
            'label'       => 'Password:',
            'placeholder' => 'Password',
            'required'    => true,
            'filters'     => array(
                'StringTrim',
            ),
            'validators'  => array(
                array(
                    'validator' => 'StringLength',
                    'options'   => array(
                        'min' => 1,
                        'max' => 255,
                    ),
                ),
            ),
        ));

        $this->addElement('submit', 'login', array(
            'label'          => 'Sign In',
            'ignore'         => true,
            'class'          => 'btn btn-success btn-lg btn-block',
            'formnovalidate' => true,
        ));
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators(array(
            array(
                'ViewScript',
                array(
                    'viewScript' => 'forms/default/login-form.phtml'
                )
            )
        ));
    }
}
