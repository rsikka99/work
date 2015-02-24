<?php
namespace MPSToolbox\Legacy\Modules\DealerManagement\Forms;

use MPSToolbox\Legacy\Modules\Admin\Models\RoleModel;
use My_Form_Element_DateTimePicker;
use My_Validate_DateTime;
use Twitter_Bootstrap_Form_Element_Submit;
use Twitter_Bootstrap_Form_Horizontal;
use Zend_Filter_Alnum;
use Zend_Filter_Boolean;
use Zend_Validate_Hostname;

/**
 * Class UserForm
 *
 * @package MPSToolbox\Legacy\Modules\DealerManagement\Forms
 */
class UserForm extends Twitter_Bootstrap_Form_Horizontal
{
    /**
     * @var RoleModel[]
     */
    public $roles;

    protected $_createMode;

    /**
     * @param null $roles
     * @param bool $createMode Whether or not we're creating a user
     * @param null $options
     */
    public function __construct ($roles = null, $createMode = false, $options = null)
    {
        $this->_createMode = $createMode;
        $this->roles       = $roles;
        $this->addPrefixPath(
            'My_Form_Element',
            'My/Form/Element',
            'element'
        );

        parent::__construct($options);
    }

    public function init ()
    {
        $this->setMethod('POST');


        // Filters
        $alphaNumericWithSpaces = new Zend_Filter_Alnum(true);

        // Validators
        $datetimeValidator = new My_Validate_DateTime();


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

        if (count($this->roles) > 0)
        {
            $roleMultiOptions = array();
            foreach ($this->roles as $role)
            {
                $roleMultiOptions[$role->id] = $role->name;
            }

            $this->addElement('multiCheckbox', 'userRoles', array(
                'label'        => 'User Roles:',
                'multiOptions' => $roleMultiOptions,
            ));
        }

        // No need to edit this when creating a user
        if ($this->_createMode === false)
        {

            $this->addElement('text', 'loginAttempts', array(
                'label'    => 'Login Attempts:',
                'disabled' => true
            ));

            $this->addElement('checkbox', 'resetLoginAttempts', array(
                'label'    => 'Reset Login Attempts'
            ));

            $minYear = (int)date('Y') - 2;
            $maxYear = $minYear + 4;
            /* @var $frozenUntil My_Form_Element_DateTimePicker */
            $frozenUntil = $this->createElement('DateTimePicker', 'frozenUntil');
            $frozenUntil->setJQueryParam('dateFormat', 'yy-mm-dd')
                        ->setJqueryParam('timeFormat', 'hh:mm')
                        ->setJQueryParam('changeYear', 'true')
                        ->setJqueryParam('changeMonth', 'true')
                        ->setJqueryParam('yearRange', "{$minYear}:{$maxYear}")
                        ->setLabel('Frozen Until:')
                        ->addValidator($datetimeValidator)
                        ->setRequired(false)
                        ->setDescription('yyyy-mm-dd hh:mm')
                        ->addFilters(array(
                            'StringTrim',
                            'StripTags'
                        ));

            $this->addElement($frozenUntil);

            $this->addElement('checkbox', 'locked', array(
                'label'    => 'Locked',
                'filters'  => array(
                    new Zend_Filter_Boolean(Zend_Filter_Boolean::ALL)
                ),
                'required' => false
            ));

            $this->addElement('checkbox', 'reset_password', array(
                'label'    => 'Reset Password'
            ));
        }


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
                        6,
                        255
                    )
                )
            )
        ));

        $this->addElement('password', 'password_confirm', array(
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

        if ($this->_createMode === true)
        {
            $this->addElement('checkbox', 'send_email', array(
                'label'   => 'Send Password to User via Email',
                'checked' => true
            ));
        }

        if (!$this->_createMode)
        {
            $this->getElement('password')->setRequired(false);
            $this->getElement('password_confirm')->setRequired(false);
        }

        $this->addElement('checkbox', 'resetPasswordOnNextLogin', array(
            'label' => 'Require Password Change On Next Login'
        ));

        /**
         * Setup Cancel Button
         */
        $this->addElement('submit', 'cancel', [
            'ignore'         => true,
            'label'          => 'Cancel',
            'formnovalidate' => 'true',
        ]);

        /**
         * Setup Save Button
         */
        $this->addElement('submit', 'submit', [
            'ignore' => true,
            'label'  => 'Save',

        ]);
    }


    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/dealermanagement/user-form.phtml']]]);
    }
}