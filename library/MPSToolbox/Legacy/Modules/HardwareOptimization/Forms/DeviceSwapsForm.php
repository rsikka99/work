<?php

namespace MPSToolbox\Legacy\Modules\HardwareOptimization\Forms;

use Tangent\Validate\LessThanFormValue;
use Zend_Form;

/**
 * Class DeviceSwapsForm
 *
 * @package MPSToolbox\Legacy\Modules\HardwareOptimization\Forms
 */
class DeviceSwapsForm extends Zend_Form
{
    public function init ()
    {
        $this->setMethod("POST");
        $this->setAttrib('id', 'deviceSwap');

        $this->addElement("text", "masterDeviceId", array(
                "id"       => 'masterDeviceId',
                "required" => true,
                "label"    => "Device Name",
                "class"    => "input-xlarge",
                "filters"  => array(
                    'StringTrim',
                    'StripTags'
                ),
            )
        );

        $maxPageCountElement = $this->createElement("text", "maximumPageCount", array(
                "id"         => 'maximumPageCount',
                "required"   => true,
                "label"      => "Max Page Volume",
                "class"      => "span4",
                "filters"    => array(
                    'StringTrim',
                    'StripTags'
                ),
                "validators" => array(
                    array(
                        'validator' => 'Between',
                        'options'   => array(
                            'min'       => 0,
                            'max'       => PHP_INT_MAX,
                            'inclusive' => true
                        )
                    ),
                    'Int'
                ),
            )
        );

        $minPageCountElement = $this->createElement("text", "minimumPageCount", array(
                "id"         => 'minimumPageCount',
                "required"   => true,
                "label"      => "Min Page Volume",
                "class"      => "span4",
                "filters"    => array(
                    'StringTrim',
                    'StripTags'
                ),
                "validators" => array(
                    array(
                        'validator' => 'Between',
                        'options'   => array(
                            'min'       => 0,
                            'max'       => PHP_INT_MAX,
                            'inclusive' => true
                        )
                    ),
                    'Int',
                ),
            )
        );

        $this->addElement("text", "deviceType", array(
                "id"      => 'deviceType',
                "label"   => "Device Type",
                "class"   => "span4",
                "filters" => array(
                    'StringTrim',
                    'StripTags'
                ),
                'attribs' => array('disabled' => 'disabled'),
            )
        );


        $this->addElement($maxPageCountElement);
        $minPageCountElement->addValidator(new LessThanFormValue($maxPageCountElement->getName()));
        $this->addElement($minPageCountElement);

        $this->addDisplayGroup(array($minPageCountElement, $maxPageCountElement), 'devicesSwaps');
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators(array(
            array(
                'ViewScript',
                array(
                    'viewScript' => 'forms/hardwareoptimization/device-swaps-form.phtml'
                )
            )
        ));
    }
}