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


        $this->addElement('text', 'firstname', [
            'label'      => 'First Name:',
            'required'   => true,
            'filters'    => ['StringTrim', 'StripTags', $alphaNumericWithSpaces],
            'validators' => [
                [
                    'validator' => 'StringLength',
                    'options'   => [2, 30],
                ],
            ],
        ]);

        $this->addElement('text', 'lastname', [
            'label'      => 'Last Name:',
            'required'   => true,
            'filters'    => ['StringTrim', 'StripTags', $alphaNumericWithSpaces],
            'validators' => [
                [
                    'validator' => 'StringLength',
                    'options'   => [2, 30],
                ],
            ],
        ]);

        $this->addElement('text', 'email', [
            'label'         => 'Email:',
            'required'      => true,
            'filters'       => ['StringTrim', 'StripTags'],
            'validators'    => [
                [
                    'validator' => 'StringLength',
                    'options'   => [4, 200],
                ],
                [
                    'validator' => 'EmailAddress',
                    'allow'     => Zend_Validate_Hostname::ALLOW_DNS,
                ],
            ],
            'errorMessages' => [
                'EmailAddress' => 'Invalid Email Address',
            ],
        ]);

        if (count($this->roles) > 0)
        {
            $roleMultiOptions = [];
            foreach ($this->roles as $role)
            {
                $roleMultiOptions[$role->id] = $role->name;
            }

            $this->addElement('multiCheckbox', 'userRoles', [
                'label'        => 'User Roles:',
                'multiOptions' => $roleMultiOptions,
            ]);
        }

        // No need to edit this when creating a user
        if ($this->_createMode === false)
        {

            $this->addElement('text', 'loginAttempts', [
                'label'    => 'Login Attempts:',
                'disabled' => true,
            ]);

            $this->addElement('checkbox', 'resetLoginAttempts', [
                'label'    => 'Reset Login Attempts',
            ]);

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
                        ->addFilters(['StringTrim', 'StripTags']);

            $this->addElement($frozenUntil);

            $this->addElement('checkbox', 'locked', [
                'label'    => 'Locked',
                'filters'  => [
                    new Zend_Filter_Boolean(Zend_Filter_Boolean::ALL),
                ],
                'required' => false,
            ]);

            $this->addElement('checkbox', 'reset_password', [
                'label'    => 'Reset Password',
            ]);
        }


        $this->addElement('password', 'password', [
            'label'      => 'Password:',
            'required'   => true,
            'filters'    => ['StringTrim'],
            'validators' => [
                [
                    'validator' => 'StringLength',
                    'options'   => [6, 255],
                ],
            ],
        ]);

        $this->addElement('password', 'password_confirm', [
            'label'         => 'Confirm Password:',
            'required'      => true,
            'filters'       => ['StringTrim'],
            'validators'    => [
                [
                    'validator' => 'StringLength',
                    'options'   => [6, 255],
                ],
                [
                    'validator' => 'Identical',
                    'options'   => ['token' => 'password'],
                ],
            ],
            'errorMessages' => [
                'Identical' => 'Passwords must match.'
            ],
        ]);

        if ($this->_createMode === true)
        {
            $this->addElement('checkbox', 'send_email', [
                'label'   => 'Send Password to User via Email',
                'checked' => true,
            ]);
        }

        if (!$this->_createMode)
        {
            $this->getElement('password')->setRequired(false);
            $this->getElement('password_confirm')->setRequired(false);
        }

        $this->addElement('checkbox', 'resetPasswordOnNextLogin', [
            'label' => 'Require Password Change On Next Login',
        ]);

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