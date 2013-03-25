<?php
class Dealermanagement_Form_User extends Twitter_Bootstrap_Form_Horizontal
{
    /**
     * @var Admin_Model_Role[]
     */
    protected $_roles;

    protected $_createMode = false;

    /**
     * @param null $roles
     * @param bool $createMode Whether or not we're creating a user
     * @param null $options
     */
    public function __construct ($roles = null, $createMode = false, $options = null)
    {
        $this->_createMode = $createMode;
        $this->_roles      = $roles;

        parent::__construct($options);
    }

    public function init ()
    {
        $this->setMethod('POST');

        // Filters
        $alphaNumericWithSpaces = new Zend_Filter_Alnum(true);

        // Validators
        $datetimeValidator = new My_Validate_DateTime();

        if ($this->_createMode)
        {
            $this->addElement('text', 'username', array(
                                                       'label'      => 'Username:',
                                                       'required'   => true,
                                                       'filters'    => array(
                                                           'StringTrim',
                                                           'StripTags',
                                                           'Alnum'
                                                       ),
                                                       'validators' => array(
                                                           array(
                                                               'validator' => 'StringLength',
                                                               'options'   => array(
                                                                   4,
                                                                   30
                                                               ),
                                                               'Alnum'
                                                           )
                                                       )
                                                  ));
        }


        $this->addElement('text', 'firstname', array(
                                                    'label'      => 'First Name:',
                                                    'required'   => true,
                                                    'filters'    => array(
                                                        'StringTrim',
                                                        'StripTags',
                                                        $alphaNumericWithSpaces
                                                    ),
                                                    'validators' => array(
                                                        array(
                                                            'validator' => 'StringLength',
                                                            'options'   => array(
                                                                2,
                                                                30
                                                            )
                                                        )
                                                    )
                                               ));

        $this->addElement('text', 'lastname', array(
                                                   'label'      => 'Last Name:',
                                                   'required'   => true,
                                                   'filters'    => array(
                                                       'StringTrim',
                                                       'StripTags',
                                                       $alphaNumericWithSpaces
                                                   ),
                                                   'validators' => array(
                                                       array(
                                                           'validator' => 'StringLength',
                                                           'options'   => array(
                                                               2,
                                                               30
                                                           )
                                                       )
                                                   )
                                              ));

        $this->addElement('text', 'email', array(
                                                'label'         => 'Email:',
                                                'required'      => true,
                                                'filters'       => array(
                                                    'StringTrim',
                                                    'StripTags'
                                                ),
                                                'validators'    => array(
                                                    array(
                                                        'validator' => 'StringLength',
                                                        'options'   => array(
                                                            4,
                                                            200
                                                        )
                                                    ),
                                                    array(
                                                        'validator' => 'EmailAddress',
                                                        'allow'     => Zend_Validate_Hostname::ALLOW_DNS
                                                    )
                                                ),
                                                'errorMessages' => array(
                                                    'EmailAddress' => 'Invalid Email Address'
                                                )
                                           ));

        if (count($this->_roles) > 0)
        {
            $userRoles = new Zend_Form_Element_MultiCheckbox('userRoles');
            $userRoles->setLabel("User Roles:");
            $userRoles->setRequired(true);

            foreach ($this->_roles as $role)
            {
                $userRoles->addMultiOption($role->id, $role->name);
            }
            $this->addElement($userRoles);
        }

        // No need to edit this when creating a user
        if ($this->_createMode === false)
        {

            $this->addElement('text', 'loginAttempts', array(
                                                            'label'    => 'Login Attempts:',
                                                            'disabled' => true
                                                       ));

            $this->addElement('checkbox', 'resetLoginAttempts', array(
                                                                     'label'    => 'Reset Login Attempts:',
                                                                     'required' => true
                                                                ));

            $minYear     = (int)date('Y') - 2;
            $maxYear     = $minYear + 4;
            $frozenUntil = new My_Form_Element_DateTimePicker('frozenUntil');
            $frozenUntil->setLabel('Frozen Until:')
                ->addValidator($datetimeValidator)
                ->setRequired(false)
                ->setDescription('yyyy-mm-dd hh:mm')
                ->setJQueryParam('dateFormat', 'yy-mm-dd')
                ->setJqueryParam('timeFormat', 'hh:mm')
                ->setJQueryParam('changeYear', 'true')
                ->setJqueryParam('changeMonth', 'true')
                ->setJqueryParam('yearRange', "{$minYear}:{$maxYear}");
            $frozenUntil->addFilters(array(
                                          'StringTrim',
                                          'StripTags'
                                     ));

            $this->addElement($frozenUntil);

            $this->addElement('checkbox', 'locked', array(
                                                         'label'    => 'Locked:',
                                                         'filters'  => array(
                                                             new Zend_Filter_Boolean(Zend_Filter_Boolean::ALL)
                                                         ),
                                                         'required' => false
                                                    ));

            $this->addElement('checkbox', 'reset_password', array(
                                                                 'label'    => 'Reset Password:',
                                                                 'required' => true
                                                            ));
        }


        $password = new Zend_Form_Element_Password('password', array(
                                                                    'label'      => 'Password:',
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

        $passwordConfirm = new Zend_Form_Element_Password('password_confirm', array(
                                                                                   'label'         => 'Confirm Password:',
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

        $password->setRequired(false);
        $passwordConfirm->setRequired(false);

        $this->addElement($password);
        $this->addElement($passwordConfirm);

        $this->addElement('checkbox', 'resetPasswordOnNextLogin', array(
                                                                       'label' => 'Require Password Change On Next Login:'
                                                                  ));


        // Add the submit button
        $this->addElement('submit', 'submit', array(
                                                   'ignore' => true,
                                                   'label'  => 'Save'
                                              ));

        EasyBib_Form_Decorator::setFormDecorator($this, EasyBib_Form_Decorator::BOOTSTRAP, 'submit');
    }
}