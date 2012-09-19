<?php

class Admin_Form_User extends EasyBib_Form
{
    const MODE_CREATE = 0;
    const MODE_EDIT = 1;
    const MODE_USER_EDIT = 2;
    
    /**
     * The mode of the form.
     * This allows us to only display certain elements when in each mode
     *
     * @var integer
     */
    protected $formMode = self::MODE_CREATE;
    protected $roles;
    
    /*
     * (non-PHPdoc) @see Zend_Form::__construct()
     */
    public function __construct ($formMode = null, $roles = null, $options = null)
    {
        if (null !== $formMode)
        {
            $this->formMode = $formMode;
        }
        
        $this->roles = $roles;
        
        parent::__construct($options);
    }

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
        $this->setAttrib('class', 'form-horizontal button-styled');
        
        // Filters
        $alnum = new Zend_Filter_Alnum(true);
        
        // Validators
        $datetimeValidator = new My_Validate_DateTime();
        
        if ($this->getFormMode() !== self::MODE_USER_EDIT)
        {
            $this->addElement('text', 'username', array (
                    'label' => 'Username:', 
                    'required' => true, 
                    'filters' => array (
                            'StringTrim', 
                            'StripTags', 
                            $alnum 
                    ), 
                    'validators' => array (
                            array (
                                    'validator' => 'StringLength', 
                                    'options' => array (
                                            4, 
                                            30 
                                    ) 
                            ) 
                    ) 
            ));
        }
        
        $this->addElement('text', 'firstname', array (
                'label' => 'First Name:', 
                'required' => true, 
                'filters' => array (
                        'StringTrim', 
                        'StripTags', 
                        $alnum 
                ), 
                'validators' => array (
                        array (
                                'validator' => 'StringLength', 
                                'options' => array (
                                        2, 
                                        30 
                                ) 
                        ) 
                ) 
        ));
        
        $this->addElement('text', 'lastname', array (
                'label' => 'Last Name:', 
                'required' => true, 
                'filters' => array (
                        'StringTrim', 
                        'StripTags', 
                        $alnum 
                ), 
                'validators' => array (
                        array (
                                'validator' => 'StringLength', 
                                'options' => array (
                                        2, 
                                        30 
                                ) 
                        ) 
                ) 
        ));
        
        $this->addElement('text', 'email', array (
                'label' => 'Email:', 
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
                                        200 
                                ) 
                        ), 
                        array (
                                'validator' => 'EmailAddress', 
                                'allow' => Zend_Validate_Hostname::ALLOW_DNS 
                        ) 
                ), 
                'errorMessages' => array (
                        'EmailAddress' => 'Invalid Email Address' 
                ) 
        ));
        
        if ($this->roles)
        {
            $userRoles = new Zend_Form_Element_MultiCheckbox('userRoles');
            $userRoles->setLabel("User Roles:");
            /* @var $role Admin_Model_Role */
            foreach ( $this->roles as $role )
            {
                $userRoles->addMultiOption($role->getId(), $role->getName());
            }
            $this->addElement($userRoles);
        }
        
        // No need to edit this when creating a user
        if ($this->getFormMode() === self::MODE_EDIT)
        {
            
            $this->addElement('text', 'loginAttempts', array (
                    'label' => 'Login Attempts:', 
                    'disabled' => true 
            ));
            
            $this->addElement('checkbox', 'resetLoginAttempts', array (
                    'label' => 'Reset Login Attempts:', 
                    'required' => true 
            ));
            
            /*
             * $frozenUntil = new Zend_Form_Element_Text('frozen_until'); $frozenUntil->setLabel('Frozen Until:');
             * $frozenUntil->setRequired(true); $frozenUntil->addValidator($datetimeValidator);
             * $this->addElement($frozenUntil);
             */
            $minYear = (int)date('Y') - 2;
            $maxYear = $minYear + 4;
            $frozenUntil = new My_Form_Element_DateTimePicker('frozenUntil');
            $frozenUntil->setLabel('Frozen Until:')
                ->setJQueryParam('dateFormat', 'yy-mm-dd')
                ->setJqueryParam('timeFormat', 'hh:mm')
                ->setJQueryParam('changeYear', 'true')
                ->setJqueryParam('changeMonth', 'true')
                ->setJqueryParam('yearRange', "{$minYear}:{$maxYear}")
                ->setDescription('yyyy-mm-dd hh:mm')
                ->addValidator($datetimeValidator)
                ->setRequired(false);
            $frozenUntil->addFilters(array (
                    'StringTrim', 
                    'StripTags' 
            ));
            
            $this->addElement($frozenUntil);
            
            $this->addElement('checkbox', 'locked', array (
                    'label' => 'Locked:', 
                    'filters' => array (
                            new Zend_Filter_Boolean(Zend_Filter_Boolean::ALL) 
                    ), 
                    'required' => false 
            ));
            
            $this->addElement('checkbox', 'reset_password', array (
                    'label' => 'Reset Password:', 
                    'required' => true 
            ));
        }
        
        if ($this->getFormMode() !== self::MODE_USER_EDIT)
        {
            
            $password = new Zend_Form_Element_Password('password', array (
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
                                    ) 
                            ) 
                    ) 
            ));
            
            $passwordConfirm = new Zend_Form_Element_Password('password_confirm', array (
                    'label' => 'Confirm Password:', 
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
                                    ) 
                            ), 
                            array (
                                    'validator' => 'Identical', 
                                    'options' => array (
                                            'token' => 'password' 
                                    ) 
                            ) 
                    ), 
                    'errorMessages' => array (
                            'Identical' => 'Passwords must match.' 
                    ) 
            ));
            
            $password->setRequired(false);
            $passwordConfirm->setRequired(false);
            
            $this->addElement($password);
            $this->addElement($passwordConfirm);
            
            if ($this->getFormMode() === self::MODE_CREATE)
            {
                $this->addElement('checkbox', 'resetPasswordOnNextLogin', array (
                        'label' => 'Require Password Change On Next Login:', 
                        'required' => true 
                ));
            }
        }
        
        // Add the submit button
        $this->addElement('submit', 'submit', array (
                'ignore' => true, 
                'label' => 'Save' 
        ));
        
        EasyBib_Form_Decorator::setFormDecorator($this, EasyBib_Form_Decorator::BOOTSTRAP, 'submit');
    }

    /**
     *
     * @return the $formMode
     */
    public function getFormMode ()
    {
        return $this->formMode;
    }

    /**
     *
     * @param string $formMode            
     */
    public function setFormMode ($formMode)
    {
        $this->formMode = $formMode;
    }
}
