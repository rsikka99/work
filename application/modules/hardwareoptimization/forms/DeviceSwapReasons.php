<?php

class Hardwareoptimization_Form_DeviceSwapReasons extends Twitter_Bootstrap_Form_Horizontal
{
    public function init ()
    {
        $this->setMethod("POST");
        $this->_addClassNames('form-center-actions');
        $this->setAttrib('id', 'deviceSwapReason');

        $reasonCategoryElement = $this->createElement("select", "reasonCategory", array(
            "label" => "Reason category",
            "class" => "input-xlarge"
        ));

        $this->addElement($reasonCategoryElement);

        $reasons = array();

        foreach (Hardwareoptimization_Model_Mapper_Device_Swap_Reason_Category::getInstance()->fetchAll() as $reason)
        {
            $reasons[$reason->id] = $reason->name;
        }

        $reasonCategoryElement->setMultiOptions($reasons);

        $reasonElement = $this->createElement("text", "reason", array(
            "label" => "Reason",
            "class" => "input-xlarge"
        ));
        $this->addElement($reasonElement);

        $this->addElement('checkbox', 'isDefault', array(
            "label" => "Default Reason"
        ));

        $this->addElement('hidden', 'deviceSwapReasonId');

    }
}