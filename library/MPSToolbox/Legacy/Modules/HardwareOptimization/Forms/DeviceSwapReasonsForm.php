<?php

namespace MPSToolbox\Legacy\Modules\HardwareOptimization\Forms;

use MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers\DeviceSwapReasonCategoryMapper;
use Zend_Form;

/**
 * Class DeviceSwapReasonsForm
 *
 * @package MPSToolbox\Legacy\Modules\HardwareOptimization\Forms
 */
class DeviceSwapReasonsForm extends Zend_Form
{
    public function init ()
    {
        $this->setMethod("POST");
        $this->setAttrib('id', 'deviceSwapReason');

        $reasonCategoryElement = $this->createElement("select", "reasonCategory", array(
            "label"    => "Reason category",
            "required" => true,
            "id"       => "reasonCategory"
        ));

        $this->addElement($reasonCategoryElement);

        $reasons = array();

        foreach (DeviceSwapReasonCategoryMapper::getInstance()->fetchAll() as $reason)
        {
            $reasons[$reason->id] = $reason->name;
        }

        $reasonCategoryElement->setMultiOptions($reasons);

        $this->addElement("text", "reason", array(
            "label"    => "Reason",
            "id"       => "reason",
            "required" => true
        ));

        $this->addElement('checkbox', 'isDefault', array(
            "label" => "Default Reason",
            "id"    => "isDefault"
        ));

        $this->addElement('hidden', 'deviceSwapReasonId');

    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators(array(
            array(
                'ViewScript',
                array(
                    'viewScript' => 'forms/hardwareoptimization/device-swap-reason-form.phtml'
                )
            )
        ));
    }
}