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
        $this->addElement('password', 'current_password', array(
            'label'      => 'Current Password:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'validator' => 'StringLength',
                    'options'   => array(6, 255)
                )
            )
        ));

        $this->addElement('password', 'password', array(
                'label'      => 'New Password:',
                'required'   => true,
                'filters'    => array('StringTrim'),
                'validators' => array(
                    array(
                        'validator' => 'StringLength',
                        'options'   => array(6, 80)
                    )
                )
            )
        );
        $this->addElement('password', 'password_confirm', array(
            'label'         => 'Confirm New Password:',
            'required'      => true,
            'filters'       => array(
                'StringTrim'
            ),
            'validators'    => array(
                array(
                    'validator' => 'StringLength',
                    'options'   => array(6, 255)
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

        $this->addElement('submit', 'submit', array(
            'ignore' => true,
            'label'  => 'Save'
        ));

        // Add the cancel button
        $this->addElement('submit', 'cancel', array(
            'ignore'         => true,
            'label'          => 'Cancel',
            'formnovalidate' => true,
        ));

    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators(array(
            array(
                'ViewScript',
                array(
                    'viewScript' => 'forms/default/change-password-form.phtml'
                )
            )
        ));
    }

}
