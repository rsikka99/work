<?php
class Hardwareoptimization_Form_DeviceSwaps extends Twitter_Bootstrap_Form_Horizontal
{
    public function init ()
    {
        $this->setMethod("POST");
        $this->_addClassNames('reportSettingsForm form-center-actions');

        $this->addElement("text", "masterDeviceId", array("label" => "Device Name", "class" => "input-xlarge", "description" => 'Only "can sell" devices can be used as replacements.'));

        $this->addElement("text", "minimumPageCount", array("label" => "Max Page Count Volume"));
        $this->addElement("text", "maximumPageCount", array("label" => "Min Page Count Volume"));

        Hardwareoptimization_Form_Hardware_Optimization_Navigation::addFormActionsToForm(Hardwareoptimization_Form_Hardware_Optimization_Navigation::BUTTONS_SAVE, $this);
    }
}