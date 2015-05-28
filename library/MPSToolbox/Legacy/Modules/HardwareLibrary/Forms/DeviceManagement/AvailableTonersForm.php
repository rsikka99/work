<?php

namespace MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement;

use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\ManufacturerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerColorMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerColorModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerModel;
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
    protected $tonerModel;

    /**
     * @var string
     */
    protected $viewScript = 'forms/hardware-library/device-management/available-toners-form.phtml';

    /**
     * @param int        $dealerId
     * @param TonerModel $tonerModel
     * @param array|null $options
     */
    public function __construct ($dealerId = null, $tonerModel = null, $options = null)
    {


        $this->dealerId   = $dealerId;
        $this->tonerModel = $tonerModel;

        parent::__construct($options);

        if ($tonerModel instanceof TonerModel)
        {
            $data               = $tonerModel->toArray();
            $data['dealerSku']  = $tonerModel->getDealerTonerAttribute($dealerId)->dealerSku;
            $data['dealerCost'] = $tonerModel->getDealerTonerAttribute($dealerId)->cost;

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
            'required'   => true,
            'validators' => [$manufacturerValidator],
        ]);

        /**
         * Toner Color
         */
        $tonerColorValidator = new \Zend_Validate_Db_RecordExists([
            'table' => 'toner_colors',
            'field' => 'id',
        ]);

        $tonerColorValidator->setMessage("Invalid toner color selected", \Zend_Validate_Db_Abstract::ERROR_NO_RECORD_FOUND);

        $this->addElement('text', 'tonerColorId', [
            'label'      => 'Color',
            'required'   => true,
            'validators' => [$tonerColorValidator],
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
        $tonerMapper = TonerMapper::getInstance();

        $dbNoRecordExistsValidator = $tonerMapper->getDbNoRecordExistsValidator($this->tonerModel);
        $dbNoRecordExistsValidator->setMessage("VPN is already in use");

        $dbNoRecordExistsValidator = new UniqueTonerVpn('manufacturerId', ($this->tonerModel) ? $this->tonerModel->id : null);

        $this->addElement('text', 'sku', [
            'label'      => 'VPN (Vendor Product Number/SKU)',
            'required'   => true,
            'maxlength'  => 255,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                [
                    'validator' => 'StringLength',
                    'options'   => [1, 255],
                ],
                $dbNoRecordExistsValidator
            ],
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
        ]);

        /**
         * Yield
         */
        $this->addElement('text_int', 'yield', [
            'label'      => 'Yield',
            'required'   => true,
            'maxlength'  => 255,
            'validators' => [
                [
                    'validator' => 'greaterThan',
                    'options'   => ['min' => 0]
                ],
                'Int'
            ],
        ]);

        /**
         * Dealer Cost
         */
        $this->addElement('text_currency', 'dealerCost', [
            'label'      => 'Your Cost',
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
            'required'   => true,
            'maxlength'  => 255,
            'validators' => [
                [
                    'validator' => 'greaterThan',
                    'options'   => ['min' => 0],
                ],
                'Float',
            ]
        ]);

        /* image */
        $this->addElement('text', 'imageUrl', [
            'label'    => 'Image URL',
            'maxlength'  => 255,
            'filters'    => ['StringTrim', 'StripTags'],
        ]);
        $this->addElement('text', 'imageFile', [
            'label'    => 'Upload Image',
        ]);

        /**
         * Save and approve
         *
         * TODO lrobert: Why is this hidden field here?
         */
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