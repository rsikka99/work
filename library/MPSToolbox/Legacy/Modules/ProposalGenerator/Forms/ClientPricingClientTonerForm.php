<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Forms;

use My_Brand;
use Twitter_Bootstrap_Form_Horizontal;

/**
 * Class ClientPricingClientTonerForm
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Forms
 */
class ClientPricingClientTonerForm extends Twitter_Bootstrap_Form_Horizontal
{

    /**
     * @param null $options
     */
    public function __construct ($options = null)
    {
        parent::__construct($options);
    }

    public function init ()
    {
        $this->setMethod('post');

        $this->addElement('hidden', 'id', [
            'label'    => 'Id',
            'class'    => 'span3',
            'required' => false,
            'disabled' => true,
            'visible'  => false,
        ]);

        $this->addElement('hidden', 'tonerId', [
            'label'    => 'Toner Id',
            'class'    => 'span3',
            'required' => false,
            'disabled' => true,
            'visible'  => false,
        ]);

        $this->addElement('text', 'systemSku', [
            'label'    => 'OEM SKU',
            'class'    => 'span3',
            'required' => false,
            'disabled' => true,
        ]);

        $this->addElement('text', 'dealerSku', [
            'label'    => My_Brand::$dealerSku,
            'class'    => 'span3',
            'required' => false,
            'disabled' => true,
        ]);

        $this->addElement('text', 'clientSku', [
            'label'      => 'Client SKU',
            'class'      => 'span3',
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

        $this->addElement('select', 'replacementTonerId', [
            'label'    => "Replacement Toner",
            'class'    => "span3",
            "required" => false,
        ]);

        $costElement = $this->createElement('text', 'cost', [
            'label'      => 'Client Cost',
            'class'      => 'span3',
            'required'   => false,
            'maxlength'  => 255,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                [
                    'validator' => 'greaterThan',
                    'options'   => ['min' => 0],
                ],
            ],
        ]);

        $costElement->setErrorMessages(["Must be greater than 0"]);
        $this->addElement($costElement);
    }
}