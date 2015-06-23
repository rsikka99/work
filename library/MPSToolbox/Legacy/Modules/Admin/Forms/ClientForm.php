<?php

namespace MPSToolbox\Legacy\Modules\Admin\Forms;

use MPSToolbox\Legacy\Mappers\DealerMapper;
use MPSToolbox\Legacy\Models\Acl\AdminAclModel;
use MPSToolbox\Legacy\Models\Acl\AppAclModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\CountryMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\CountryModel;
use Zend_Form;
use Zend_Validate_Int;
use Zend_Validate_Regex;
use Zend_Validate_StringLength;

/**
 * Class ClientForm
 *
 * @package MPSToolbox\Legacy\Modules\Admin\Forms
 */
class ClientForm extends \My_Form_Form
{
    /**
     * @var bool
     */
    protected $_dealerManagement;

    /**
     * @param bool       $dealerManagement
     * @param null|array $options
     */
    public function __construct ($dealerManagement = false, $options = null)
    {
        $this->_dealerManagement = $dealerManagement;

        parent::__construct($options);
    }

    public function init ()
    {
        $this->setMethod('POST');

        /**
         * If we are a system administrator we should be able to create clients for dealers.
         * Therefore we need to be able to assign the client to a dealership
         */
        if ($this->getView()->IsAllowed(AdminAclModel::RESOURCE_ADMIN_CLIENT_WILDCARD, AppAclModel::PRIVILEGE_ADMIN) && $this->_dealerManagement == false)
        {
            $this->addElement('text', 'dealerId', [
                'label'      => 'Dealer',
                'required'   => true,
                'validators' => [
                    [
                        'validator' => 'Db_RecordExists',
                        'options'   => ['table' => 'dealers', 'field' => 'id'],
                    ]
                ],
            ]);
        }

        /**
         * Account Number
         */
        $this->addElement('text', 'accountNumber', [
            'label'   => 'Account Number',
            'filters' => ['StringTrim'],
        ]);

        /**
         * Company Display Name
         */
        $this->addElement('text', 'companyName', [
            'label'    => 'Company Name',
            'required' => true,
            'filters'  => ['StringTrim'],
        ]);

        /**
         * Number of employees
         */
        $this->addElement('text_int', 'employeeCount', [
            'label'    => '# of employees',
            'required' => 'true',
            'filters'  => ['StringTrim'],
        ]);

        /**
         * Company Legal Name
         */
        $this->addElement('text', 'legalName', [
            'label'   => 'Legal Name',
            'filters' => ['StringTrim'],
        ]);

        /**
         * Contact First Name
         */
        $this->addElement('text', 'firstName', [
            'label'      => 'First Name',
            'allowEmpty' => true,
            'filters'    => ['StringTrim'],
        ]);

        /**
         * Contact Last Name
         */
        $this->addElement('text', 'lastName', [
            'label'      => 'Last Name',
            'allowEmpty' => true,
            'filters'    => ['StringTrim'],
        ]);

        /**
         * Contact Phone Number
         */
        $this->addElement('text', 'phoneNumber', [
            'label'       => 'Phone Number',
            'placeholder' => '111-222-3333',
            'filters'     => ['StringTrim'],
        ]);

        /**
         * Address Line 1
         */
        $this->addElement('text', 'addressLine1', [
            'label'    => 'Address 1',
            'required' => true,
            'filters'  => ['StringTrim'],
        ]);

        /**
         * Address Line 2
         */
        $this->addElement('text', 'addressLine2', [
            'label'   => 'Address 2',
            'filters' => ['StringTrim'],
        ]);

        /**
         * City
         */
        $this->addElement('text', 'city', [
            'label'    => 'City',
            'required' => true,
            'filters'  => ['StringTrim'],
        ]);

        /**
         * Region
         */
        $this->addElement('text', 'region', [
            'label'    => 'State or Province',
            'required' => true,
            'filters'  => ['StringTrim'],
        ]);

        /**
         * Post Code
         */
        $this->addElement('text', 'postCode', [
            'label'       => 'Post/Zip Code',
            'description' => 'Format: Zip Code: 12345 Postal Code: A1B2C3',
            'required'    => true,
            'filters'     => ['StringToUpper', 'StringTrim'],
        ]);

        /**
         * Country
         */
        $this->addElement('text', 'countryId', [
            'label'      => 'Country',
            'required'   => true,
            'validators' => [
                [
                    'validator' => 'Db_RecordExists',
                    'options'   => ['table' => 'countries', 'field' => 'country_id'],
                ]
            ],
        ]);


        $this->addElement('submit', 'Cancel', [
            'ignore'          => true,
            'formnovalidate ' => true,
            'label'           => 'Cancel',
        ]);

        $this->addElement('submit', 'Save', [
            'ignore' => true,
            'label'  => 'Save',
        ]);
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/admin/client-form.phtml']]]);
    }
}
