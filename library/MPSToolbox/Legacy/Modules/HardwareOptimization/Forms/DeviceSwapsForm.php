<?php

namespace MPSToolbox\Legacy\Modules\HardwareOptimization\Forms;

use Tangent\Validate\LessThanFormValue;
use Zend_Form;

/**
 * Class DeviceSwapsForm
 *
 * @package MPSToolbox\Legacy\Modules\HardwareOptimization\Forms
 */
class DeviceSwapsForm extends \My_Form_Form
{
    public function init ()
    {
        $this->setMethod("POST");
        $this->setAttrib('id', 'deviceSwap');

        $this->addElement("text", "masterDeviceId", [
            "id"       => 'masterDeviceId',
            "required" => true,
            "label"    => "Device Name",
            "class"    => "input-xlarge",
            "filters"  => ['StringTrim', 'StripTags'],
        ]);

        $maxPageCountElement = $this->createElement("text_int", "maximumPageCount", [
            "id"         => 'maximumPageCount',
            "required"   => true,
            "label"      => "Max Page Volume",
            "class"      => "span4",
            "validators" => [
                [
                    'validator' => 'Between',
                    'options'   => [
                        'min'       => 0,
                        'max'       => PHP_INT_MAX,
                        'inclusive' => true
                    ]
                ],
                'Int'
            ],
        ]);

        $minPageCountElement = $this->createElement("text_int", "minimumPageCount", [
            "id"         => 'minimumPageCount',
            "required"   => true,
            "label"      => "Min Page Volume",
            "class"      => "span4",
            "validators" => [
                [
                    'validator' => 'Between',
                    'options'   => [
                        'min'       => 0,
                        'max'       => PHP_INT_MAX,
                        'inclusive' => true
                    ],
                ],
                'Int',
            ],
        ]);

        $this->addElement("text", "deviceType", [
            "id"      => 'deviceType',
            "label"   => "Device Type",
            "class"   => "span4",
            "filters" => ['StringTrim', 'StripTags'],
            'attribs' => ['disabled' => 'disabled'],
        ]);


        $this->addElement($maxPageCountElement);
        $minPageCountElement->addValidator(new LessThanFormValue($maxPageCountElement->getName()));
        $this->addElement($minPageCountElement);

        $this->addDisplayGroup([$minPageCountElement, $maxPageCountElement], 'devicesSwaps');
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/hardwareoptimization/device-swaps-form.phtml']]]);
    }
}