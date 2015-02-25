<?php

namespace MPSToolbox\Legacy\Modules\Admin\Forms;

use Zend_Form;
use MPSToolbox\Legacy\Mappers\DealerMapper;
use MPSToolbox\Legacy\Models\Acl\AdminAclModel;
use MPSToolbox\Legacy\Models\Acl\AppAclModel;

/**
 * Class LeasingSchemaForm
 *
 * @package MPSToolbox\Legacy\Modules\Admin\Forms
 */
class LeasingSchemaForm extends Zend_Form
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
        // Set the method for the display form to POST
        $this->setMethod('POST');

        $this->addElement('text', 'name', [
            'label'      => 'Name:',
            'required'   => true,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                [
                    'validator' => 'StringLength',
                    'options'   => [1, 255]
                ],
            ],
        ]);

        $dealerSelect = null;
        $isAdmin      = $this->getView()->IsAllowed(AdminAclModel::RESOURCE_ADMIN_LEASINGSCHEMA_WILDCARD, AppAclModel::PRIVILEGE_ADMIN);
        if ($isAdmin && $this->_dealerManagement == false)
        {
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
                    'value'        => $firstDealerId,
                ]);
            }
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
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/admin/leasing-schema-form.phtml']]]);
    }
}
