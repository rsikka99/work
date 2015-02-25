<?php

namespace MPSToolbox\Legacy\Modules\DDefault\Forms;

use Zend_Form;
use Zend_Form_Element_Password;

/**
 * Class ChangePasswordForm
 *
 * @package MPSToolbox\Legacy\Modules\DDefault\Forms
 */
class ChangePasswordForm extends Zend_Form
{

    function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');

        // Add the password element
        $this->addElement('password', 'current_password', [
            'label'      => 'Current Password:',
            'required'   => true,
            'filters'    => ['StringTrim'],
            'validators' => [
                [
                    'validator' => 'StringLength',
                    'options'   => [6, 255]
                ],
            ],
        ]);

        $this->addElement('password', 'password', [
            'label'      => 'New Password:',
            'required'   => true,
            'filters'    => ['StringTrim'],
            'validators' => [
                [
                    'validator' => 'StringLength',
                    'options'   => [6, 80]
                ],
            ],
        ]);

        $this->addElement('password', 'password_confirm', [
            'label'         => 'Confirm New Password:',
            'required'      => true,
            'filters'       => ['StringTrim'],
            'validators'    => [
                [
                    'validator' => 'StringLength',
                    'options'   => [6, 255]
                ],
                [
                    'validator' => 'Identical',
                    'options'   => [
                        'token' => 'password'
                    ]
                ],
            ],
            'errorMessages' => [
                'Identical' => 'Passwords must match.'
            ],
        ]);

        $this->addElement('submit', 'submit', [
            'ignore' => true,
            'label'  => 'Save',
        ]);

        // Add the cancel button
        $this->addElement('submit', 'cancel', [
            'ignore'         => true,
            'label'          => 'Cancel',
            'formnovalidate' => true,
        ]);

    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/default/change-password-form.phtml']]]);
    }

}
