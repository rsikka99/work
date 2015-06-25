<?php

namespace MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement;

use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerConfigMapper;
use Tangent\Validate\FieldDependsOnValue;
use Zend_Form;
use Zend_Validate_Int;
use Zend_Validate_NotEmpty;
use Zend_Validate_GreaterThan;

/**
 * Class SuppliesAndServiceForm
 *
 * @package MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement
 */
class SuppliesAndServiceForm extends \My_Form_Form
{
    protected $_isAllowed;
    protected $_isQuoteDevice;

    /**
     * @param null $options
     * @param bool $isAllowed
     * @param bool $isQuoteDevice
     */
    public function __construct ($options = null, $isAllowed = false, $isQuoteDevice = false)
    {
        $this->_isAllowed     = $isAllowed;
        $this->_isQuoteDevice = $isQuoteDevice;
        parent::__construct($options);
    }

    public function init ()
    {
        $this->setMethod('post');
        $this->setAttrib('id', 'suppliesAndService');

        /**
         * Toner configuration
         */
        $this->addElement('select', 'tonerConfigId', [
            'label'        => 'Toner Configuration',
            'id'           => 'tonerConfigId',
            'description'  => 'The type of toners that fit into this device.',
            'required'     => false,
            'disabled'     => (!$this->_isAllowed),
            'multiOptions' => TonerConfigMapper::getInstance()->fetchAllForMultiOptions(),
        ]);

        /**
         * Is Leased
         */
        $this->addElement('checkbox', 'isLeased', [
            'label'    => 'Is Leased',
            'id'       => 'isLeased',
            'disabled' => (!$this->_isAllowed),
        ]);

        /**
         * Leased Toner Yield
         */
        $this->addElement('text_int', 'leasedTonerYield', [
            'label'       => 'Leased Toner Yield',
            'id'          => 'leasedTonerYield',
            'description' => 'If this device is marked as leased you must fill this out.',
            'allowEmpty'  => false,
            'disabled'    => (!$this->_isAllowed),
            'validators'  => [
                new FieldDependsOnValue('isLeased', '1', [
                        new Zend_Validate_NotEmpty(),
                        new Zend_Validate_Int(),
                        new Zend_Validate_GreaterThan(['min' => 0])
                    ]
                ),
            ],
        ]);

        /**
         * Parts Cost Per Page
         */
        $this->addElement('text_currency', 'dealerPartsCostPerPage', [
            'label'       => 'Parts CPP',
            'id'          => 'dealerParsCostPerPage',
            'description' => 'Your parts cost per page to manage this device. Overrides the default parts cost per page in reports.',
            'maxlength'   => 8,
            'required'    => $this->_isQuoteDevice,
            'validators'  => [
                [
                    'validator' => 'Between',
                    'options'   => ['min' => 0, 'max' => 5],
                ],
            ],
        ]);

        /**
         * Labor Cost Per Page
         */
        $this->addElement('text_currency', 'dealerLaborCostPerPage', [
            'label'       => 'Labor CPP',
            'id'          => 'dealerLaborCostPerPage',
            'description' => 'Your labor cost per page to manage this device. Overrides the default labor cost per page in reports.',
            'maxlength'   => 8,
            'required'    => $this->_isQuoteDevice,
        ]);

        /**
         * Lease Buyback
         */
        $this->addElement('text_currency', 'leaseBuybackPrice', [
            'label'       => 'Lease Buyback Price',
            'id'          => 'leaseBuybackPrice',
            'description' => 'Used in calculations for the lease buyback report.',
            'maxlength'   => 8,
        ]);
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/hardware-library/device-management/supplies-and-service-form.phtml']]]);
    }
}