<?php

namespace MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement;

use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DealerTonerAttributeMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\ManufacturerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerColorMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DealerTonerAttributeModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerColorModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerModel;
use MPSToolbox\Services\CurrencyService;
use My_Brand;
use Tangent\Validate\UniqueTonerVpn;
use Zend_Form;

/**
 * Class AvailableTonersForm
 *
 * @package MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement
 */
class AvailableTonersForm extends \My_Form_Form
{
    /**
     * @var int
     */
    protected $dealerId;

    /**
     * @var TonerModel
     */
    protected $toner;

    public $images;

    /**
     * @var string
     */
    protected $viewScript = 'forms/hardware-library/device-management/available-toners-form.phtml';

    public $_isAllowedToEditFields = false;

    public $distributors = [];

    /**
     * @param null $dealerId
     * @param null $tonerModel
     * @param null $options
     * @param bool $isAllowedToEditFields
     */
    public function __construct ($dealerId = null, TonerModel $toner = null, $options = null, $isAllowedToEditFields = false)
    {
        $this->_isAllowedToEditFields = $isAllowedToEditFields;

        $this->dealerId   = $dealerId;
        $this->toner = $toner;

        parent::__construct($options);

        if (!empty($toner))
        {
            //$a = $tonerModel->getDealerTonerAttribute($dealerId);
            $attr = DealerTonerAttributeMapper::getInstance()->find([ $toner->id, $dealerId]);
            if (empty($attr)) $attr = new DealerTonerAttributeModel();

            $data               = $toner->toArray();
            $data['cost']       = number_format(CurrencyService::getInstance()->getObjectValue($toner, 'base_printer_consumable', 'cost'),2);
            $data['dealerSku']  = $attr->dealerSku;
            $data['dealerCost'] = $attr->cost;
            $data['sellPrice'] = $attr->sellPrice;

            $this->setDefaults($data);
        }
    }

    public function init ()
    {
        $this->setMethod('post');
        $this->setAttrib('id', 'toner-form');

        /**
         * Toner Id
         */
        $this->addElement('hidden', 'id', []);

        /**
         * Manufacturer
         */
        $manufacturerValidator = new \Zend_Validate_Db_RecordExists([
            'table' => 'manufacturers',
            'field' => 'id',
        ]);

        $manufacturerValidator->setMessage("Invalid manufacturer selected", \Zend_Validate_Db_Abstract::ERROR_NO_RECORD_FOUND);

        $this->addElement('text', 'manufacturerId', [
            'label'      => 'Manufacturer',
            'required'   => $this->_isAllowedToEditFields,
            'validators' => [$manufacturerValidator],
            'disabled' => !$this->_isAllowedToEditFields,
        ]);

        /**
         * Toner Color
         *
        $tonerColorValidator = new \Zend_Validate_Db_RecordExists([
            'table' => 'toner_colors',
            'field' => 'id',
        ]);
        $tonerColorValidator->setMessage("Invalid toner color selected", \Zend_Validate_Db_Abstract::ERROR_NO_RECORD_FOUND);
         */

        $colors    = TonerColorMapper::getInstance()->fetchAll();
        $colorList = ['' => ''];
        foreach ($colors as $color)
        {
            $colorList[$color->id] = $color->name;
        }

        $this->addElement('select', 'tonerColorId', [
            'label'        => 'Color',
            'required'     => false,
            'validators' => [],
            'disabled' => !$this->_isAllowedToEditFields,
            'multiOptions' => $colorList
        ]);

        $this->addElement('text', 'colorStr', [
            'label'      => 'Color Description',
            'required'   => false,
            'disabled' => !$this->_isAllowedToEditFields,
        ]);

        $typeList = [];
        $db = \Zend_Db_Table::getDefaultAdapter();
        foreach ($db->query('select distinct(`type`) as t from base_printer_consumable order by t') as $row) $typeList[$row['t']] = $row['t'];
        $this->addElement('select', 'type', [
            'label'        => 'Type',
            'required'     => $this->_isAllowedToEditFields,
            'validators' => [],
            'disabled' => !$this->_isAllowedToEditFields,
            'multiOptions' => $typeList
        ]);

        /**
         * Dealer SKU
         */
        $this->addElement('text', 'dealerSku', [
            'label'      => My_Brand::$dealerSku,
            'required'   => false,
            'maxlength'  => 255,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                [
                    'validator' => 'StringLength',
                    'options'   => [1, 255],
                ],
            ],
        ]);

        /**
         * OEM SKU
         */
        $dbNoRecordExistsValidator = new UniqueTonerVpn('manufacturerId', ($this->toner) ? $this->toner->id : null);
        $dbNoRecordExistsValidator->setMessage("VPN is already in use");

        $this->addElement('text', 'sku', [
            'label'      => 'VPN (Vendor Product Number/SKU)',
            'required'   => $this->_isAllowedToEditFields,
            'maxlength'  => 255,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                [
                    'validator' => 'StringLength',
                    'options'   => [1, 255],
                ],
                $dbNoRecordExistsValidator
            ],
            'disabled' => !$this->_isAllowedToEditFields,
        ]);

        $this->addElement('text', 'name', [
            'label'      => 'Product Name',
            'required'   => false,
            'maxlength'  => 255,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                [
                    'validator' => 'StringLength',
                    'options'   => [1, 255],
                ]
            ],
            'disabled' => !$this->_isAllowedToEditFields,
        ]);

        /**
         * Yield
         */
        $this->addElement('text_int', 'yield', [
            'label'      => 'Yield',
            'required'   => false,
            'maxlength'  => 255,
            'validators' => [
                'Int'
            ],
            'disabled' => !$this->_isAllowedToEditFields,
        ]);
        $this->addElement('text_int', 'mlYield', [
            'label'      => 'Yield',
            'required'   => false,
            'maxlength'  => 255,
            'validators' => [
                'Int'
            ],
            'disabled' => !$this->_isAllowedToEditFields,
        ]);

        /**
         * Weight
         */
        $this->addElement('text_float', 'weight', [
            'label'      => 'Weight (KG)',
            'required'   => false,
            'validators' => [
                'Float'
            ],
            'disabled' => !$this->_isAllowedToEditFields,
        ]);

        /**
         * UPC
         */
        $this->addElement('text', 'UPC', [
            'label'      => 'UPC',
            'required'   => false,
            'disabled' => !$this->_isAllowedToEditFields,
        ]);

        /**
         * Dealer Cost
         */
        $this->addElement('text_currency', 'dealerCost', [
            'label'      => 'Your MPS Cost',
            'required'   => false,
            'maxlength'  => 255,
            'validators' => [
                [
                    'validator' => 'greaterThan',
                    'options'   => ['min' => 0],
                ],
                'Float',
            ]
        ]);

        /**
         * Dealer sell price
         */
        $this->addElement('text_currency', 'sellPrice', [
            'label'      => 'Online Sell Price',
            'required'   => false,
            'maxlength'  => 255,
            'validators' => [
                [
                    'validator' => 'greaterThan',
                    'options'   => ['min' => 0],
                ],
                'Float',
            ]
        ]);

        /**
         * Cost
         */
        $this->addElement('text_currency', 'cost', [
            'label'      => "Typical Dealer Cost",
            'required'   => $this->_isAllowedToEditFields,
            'maxlength'  => 255,
            'validators' => [
                [
                    'validator' => 'greaterThan',
                    'options'   => ['min' => 0],
                ],
                'Float',
            ],
            'disabled' => !$this->_isAllowedToEditFields,
        ]);

        /* image */
        $this->addElement('text', 'imageUrl', [
            'label'    => 'Image URL',
            'maxlength'  => 255,
            'filters'    => ['StringTrim', 'StripTags'],
            'disabled' => !$this->_isAllowedToEditFields,
        ]);

        $this->addElement('text', 'imageFile', [
            'label'    => 'Upload Image',
            'disabled' => !$this->_isAllowedToEditFields,
        ]);

        $this->addElement('hidden', 'saveAndApproveHdn', [
            'value' => 0,
        ]);

        /**
         * Save and approve
         */
        $this->addElement('submit', 'saveAndApprove', [
            'label' => 'Save and Approve',
        ]);
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators([
            ['ViewScript', ['viewScript' => $this->viewScript]]
        ]);
    }
}