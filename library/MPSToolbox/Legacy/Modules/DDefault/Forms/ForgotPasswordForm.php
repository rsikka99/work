<?php

namespace MPSToolbox\Legacy\Modules\DDefault\Forms;

use Zend_Form;

/**
 * Class ForgotPasswordForm
 *
 * @package MPSToolbox\Legacy\Modules\DDefault\Forms
 */
class ForgotPasswordForm extends Zend_Form
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
            'filters'       => ['StringTrim', 'StripTags',],
            'validators'    => [
                ['validator' => 'StringLength', 'options' => ['min' => 4, 'max' => 255,],],
                ['validator' => 'EmailAddress',],
            ],
            'errorMessages' => ['Invalid email address'],
        ]);

        $this->addElement('submit', 'forgotPassword', [
            'label'  => 'Send Forgot Password Request',
            'ignore' => true,
        ]);

        $this->addElement('submit', 'cancel', [
            'label'          => 'Cancel',
            'ignore'         => true,
            'formnovalidate' => true,
        ]);
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/default/forgot-password-form.phtml']]]);
    }
}
