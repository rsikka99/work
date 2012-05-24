<?php

class Default_Form_Login extends EasyBib_Form
{

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');
        
        /**
         * Add class to form for label alignment
         *
         * - Vertical .form-vertical (not required)	Stacked, left-aligned labels
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
        
        // Add an email element
        $this->addElement('text', 'username', array (
                'label' => 'Username:', 
                'required' => true, 
                'filters' => array (
                        'StringTrim', 
                        'StripTags' 
                ), 
                'validators' => array (
                        array (
                                'validator' => 'StringLength', 
                                'options' => array (
                                        4, 
                                        255 
                                ) 
                        ), 
                        'Alnum' 
                ) 
        ));
        
        // Add the comment element
        $this->addElement('password', 'password', array (
                'label' => 'Password:', 
                'required' => true, 
                'filters' => array (
                        'StringTrim' 
                ), 
                'validators' => array (
                        array (
                                'validator' => 'StringLength', 
                                'options' => array (
                                        6, 
                                        255 
                                ), 
                                'Alnum' 
                        ) 
                ) 
        ));
        
        // Add the submit button
        $this->addElement('submit', 'submit', array (
                'ignore' => true, 
                'label' => 'Login' 
        ));
        
        // Add the submit button
        $this->addElement('submit', 'forgotpassword', array (
                'ignore' => true, 
                'label' => 'Forgot Password' 
        ));
        
        EasyBib_Form_Decorator::setFormDecorator($this, EasyBib_Form_Decorator::BOOTSTRAP, 'submit', 'forgotpassword');
    }
}
