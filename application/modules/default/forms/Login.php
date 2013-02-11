<?php

class Default_Form_Login extends Twitter_Bootstrap_Form_Horizontal
{

    public function init ()
    {
        // Add an email element
        $this->addElement('text', 'username', array(
                                                   'label'      => 'Username:',
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
                                                       'Alnum'
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


        $formActions[] = array();

        //setup submit button
        $formActions[] = $this->createElement('submit', 'login', array(
                                                                     'label'      => 'Sign In',
                                                                     'ignore'     => true,
                                                                     'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY
                                                                ));

//        /*
//         * Forgot password action
//         */
//        if ($this->getView()->IsAllowed(Application_Model_Acl::RESOURCE_DEFAULT_AUTH_FORGOTPASSWORD, Application_Model_Acl::PRIVILEGE_VIEW))
//        {
//            $formActions[] = $this->createElement('submit', 'forgotPassword', array(
//                                                                                   'label'  => 'Forgot Password',
//                                                                                   'ignore' => true,
//                                                                              ));
//        }

        $this->addDisplayGroup($formActions, 'actions', array(
                                                             'disableLoadDefaultDecorators' => true,
                                                             'decorators'                   => array(
                                                                 'Actions'
                                                             ),
                                                             'class'                        => 'form-actions-center'
                                                        ));
    }
}
