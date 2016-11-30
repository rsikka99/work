<?php

namespace MPSToolbox\Legacy\Modules\Admin\Forms;

use MPSToolbox\Entities\DealerEntity;
use MPSToolbox\Legacy\Mappers\DealerMapper;
use MPSToolbox\Legacy\Models\Acl\AdminAclModel;
use MPSToolbox\Legacy\Models\Acl\AppAclModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\CountryMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\CountryModel;
use Zend_Form;
use Zend_Validate_Int;
use Zend_Validate_Regex;
use Zend_Validate_StringLength;

class ClientEmailValidator implements \Zend_Validate_Interface {

    public $clientId;

    public function __construct($clientId) {
        $this->clientId = $clientId;
    }

    private $message = null;
    public function isValid($value) {
        $dealerId = DealerEntity::getDealerId();
        $db = \Zend_Db_Table::getDefaultAdapter();
        $exists = $db->query('select clientId from contacts where email=? and clientId in (select clientId from clients where dealerId=?)', [$value, $dealerId])->fetchColumn(0);
        if ($exists) {
            if (!$this->clientId) {
                $this->message = 'This e-mail address is already registered by one of your other clients: '.$exists;
                return false;
            }
            if ($exists!=$this->clientId) {
                $this->message = 'You cannot change the email address to that of another client: '.$exists.' ('.$this->clientId.')';
                return false;
            }
        }
        return true;
    }

    public function getMessages() {
        $result = $this->message ? ['email'=>$this->message] : null;
        return $result;
    }
}

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

    public $clientId = false;

    /**
     * @param bool       $dealerManagement
     * @param null|array $options
     */
    public function __construct ($dealerManagement = false, $options = null, $clientId = null)
    {
        $this->_dealerManagement = $dealerManagement;
        $this->clientId = $clientId;
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

        $this->addElement('text', 'id', [
            'label'   => 'ID',
        ]);

        $this->addElement('text', 'deviceGroup', [
            'label'   => 'RMS Client Identifier',
            'filters' => ['StringTrim'],
        ]);

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

        $this->addElement('text', 'industry', [
            'label'    => 'Industry',
            'required' => 'false',
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

        $this->addElement('text', 'email', [
            'label'       => 'E-mail address',
            'placeholder' => '',
            'filters'     => ['StringTrim'],
            'validators' => [
                [
                    new ClientEmailValidator($this->clientId)
                ]
            ],
        ]);

        $this->addElement('text', 'website', [
            'label'    => 'Website',
            'filters'  => ['StringTrim'],
        ]);

        $this->addElement('text', 'emailSupply', [
            'label'       => 'Supply E-mail address',
            'placeholder' => '',
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
