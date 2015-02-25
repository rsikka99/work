<?php

namespace MPSToolbox\Legacy\Modules\Admin\Forms;

use MPSToolbox\Legacy\Mappers\DealerMapper;
use MPSToolbox\Legacy\Models\Acl\AppAclModel;
use MPSToolbox\Legacy\Modules\Admin\Mappers\RoleMapper;
use My_Form_Element_DateTimePicker;
use My_Validate_DateTime;
use Zend_Filter_Alnum;
use Zend_Filter_Boolean;
use Zend_Form;
use Zend_Form_Element_MultiCheckbox;
use Zend_Validate_Hostname;

/**
 * Class UserForm
 *
 * @package MPSToolbox\Legacy\Modules\Admin\Forms
 */
class UserForm extends Zend_Form
{
    const MODE_CREATE    = 0;
    const MODE_EDIT      = 1;
    const MODE_USER_EDIT = 2;

    /**
     * The mode of the form.
     * This allows us to only display certain elements when in each mode
     *
     * @var integer
     */
    protected $_formMode = self::MODE_CREATE;

    protected $_defaultDealerId = 0;


    /**
     * @param null|int   $formMode
     * @param bool|int   $dealerId
     * @param null|array $options
     */
    public function __construct ($formMode = null, $dealerId = false, $options = null)
    {
        if (null !== $formMode)
        {
            $this->_formMode = $formMode;
        }

        if ($dealerId > 0)
        {
            $this->_defaultDealerId = $dealerId;
        }

        parent::__construct($options);
    }

    public function init ()
    {
        // Set the method for the display form to POST
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
                    'options'   => [4, 200]
                ],
                [
                    'validator' => 'EmailAddress',
                    'allow'     => Zend_Validate_Hostname::ALLOW_DNS
                ],
            ],
            'errorMessages' => [
                'EmailAddress' => 'Invalid Email Address'
            ],
        ]);

        // No need to edit this when creating a user
        if ($this->getFormMode() !== self::MODE_USER_EDIT)
        {
            $roles = RoleMapper::getInstance()->fetchAll();
            if (count($roles) > 0)
            {
                $userRoles = new Zend_Form_Element_MultiCheckbox('userRoles');
                $userRoles->setLabel("User Roles:");

                foreach ($roles as $role)
                {
                    if ($role->id != AppAclModel::ROLE_SYSTEM_ADMIN || ($role->id == AppAclModel::ROLE_SYSTEM_ADMIN && $this->_dealerManagement == false))
                    {
                        $roleName = ($role->systemRole) ? $role->name . " (System Role)" : $role->name;
                        $userRoles->addMultiOption($role->id, $roleName);
                    }
                }
                $this->addElement($userRoles);
            }

            $firstDealerId = null;
            $dealers       = [];
            foreach (DealerMapper::getInstance()->fetchAll() as $dealer)
            {
                // Use this to grab the first id in the leasing schema dropdown
                if (!$firstDealerId)
                {
                    $firstDealerId = $dealer->id;
                }
                $dealers [$dealer->id] = $dealer->dealerName;
            }

            if ($dealers)
            {
                $this->addElement('select', 'dealerId', [
                    'label'        => 'Dealer:',
                    'class'        => 'input-medium',
                    'multiOptions' => $dealers,
                    'required'     => true,
                    'value'        => ($this->_defaultDealerId > 0) ? $this->_defaultDealerId : $firstDealerId,
                ]);
            }


            if ($this->getFormMode() === self::MODE_EDIT)
            {
                $this->addElement('text', 'loginAttempts', [
                    'label'    => 'Login Attempts:',
                    'disabled' => true
                ]);

                $this->addElement('checkbox', 'resetLoginAttempts', [
                    'label' => 'Reset Login Attempts',
                ]);


                $minYear     = (int)date('Y') - 2;
                $maxYear     = $minYear + 4;
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

                $frozenUntil->addFilters(['StringTrim', 'StripTags']);

                $this->addElement($frozenUntil);

                $this->addElement('checkbox', 'locked', [
                    'label'    => 'Locked',
                    'filters'  => [new Zend_Filter_Boolean(Zend_Filter_Boolean::ALL)],
                    'required' => false
                ]);

                $this->addElement('checkbox', 'reset_password', [
                    'label' => 'Reset Password',
                ]);
            }

            $this->addElement('password', 'password', [
                'label'         => 'New Password:',
                'required'      => true,
                'filters'       => ['StringTrim'],
                'validators'    => [
                    [
                        'validator' => 'StringLength',
                        'options'   => [6, 255]
                    ],
                ],
                'errorMessages' => [
                    'StringLength' => 'Passwords must be at least 6 characters.',
                ]
            ]);

            $this->addElement('password', 'password_confirm', [
                'required'      => true,
                'label'         => 'Confirm New Password:',
                'allowEmpty'    => false,
                'filters'       => ['StringTrim'],
                'validators'    => [
                    [
                        'validator' => 'Identical',
                        'options'   => ['token' => 'password']
                    ]
                ],
                'errorMessages' => [
                    'Identical' => 'Passwords must match.'
                ]
            ]);

            if ($this->getFormMode() === self::MODE_CREATE)
            {
                $this->addElement('checkbox', 'send_email', [
                    'label'   => 'Send Password to User via Email',
                    'checked' => true
                ]);

                $this->addElement('checkbox', 'resetPasswordOnNextLogin', [
                    'label'    => 'Require Password Change On Next Login',
                    'required' => true
                ]);
            }
            else
            {
                $this->getElement('password')->setRequired(false);
                $this->getElement('password_confirm')->setRequired(false);
            }

            $this->addElement('checkbox', 'resetPasswordOnNextLogin', [
                'label' => 'Require Password Change On Next Login'
            ]);
        }

        // Add the submit button
        $this->addElement('submit', 'submit', [
            'ignore' => true,
            'label'  => 'Save'
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
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/admin/user-form.phtml']]]);
    }

    /**
     * @return string
     */
    public function getFormMode ()
    {
        return $this->_formMode;
    }

    /**
     * @param string $formMode
     */
    public function setFormMode ($formMode)
    {
        $this->_formMode = $formMode;
    }
}
