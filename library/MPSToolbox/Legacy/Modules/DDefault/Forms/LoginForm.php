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
        $this->addElement('text', 'email', [
            'label'         => 'Email:',
            'placeholder'   => 'Email Address',
            'required'      => true,
            'filters'       => ['StringTrim', 'StripTags'],
            'validators'    => [
                [
                    'validator' => 'StringLength',
                    'options'   => [
                        'min' => 4,
                        'max' => 255,
                    ],
                ],
                ['validator' => 'EmailAddress',],
            ],
            'errorMessages' => ['Invalid email address'],
        ]);

        // Add the password element
        $this->addElement('password', 'password', [
            'label'       => 'Password:',
            'placeholder' => 'Password',
            'required'    => true,
            'filters'     => ['StringTrim'],
            'validators'  => [
                [
                    'validator' => 'StringLength',
                    'options'   => [
                        'min' => 1,
                        'max' => 255,
                    ],
                ],
            ],
        ]);

        $this->addElement('submit', 'login', [
            'label'          => 'Sign In',
            'ignore'         => true,
            'class'          => 'btn btn-success btn-lg btn-block',
            'formnovalidate' => true,
        ]);
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/default/login-form.phtml']]]);
    }
}
