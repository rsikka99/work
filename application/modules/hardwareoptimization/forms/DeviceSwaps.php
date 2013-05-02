<?php
class Hardwareoptimization_Form_DeviceSwaps extends Twitter_Bootstrap_Form_Horizontal
{
    public function init ()
    {
        $this->setMethod("POST");
        $this->_addClassNames('reportSettingsForm form-center-actions');

        $this->addElement("text", "deviceName", array("label" => "Device Name"));

        $this->addElement("text", "maximumPageCountThreshold", array("label" => "Max Page Count Volume"));
        $this->addElement("text", "minimumPageCountThreshold", array("label" => "Min Page Count Volume"));

        Hardwareoptimization_Form_Hardware_Optimization_Navigation::addFormActionsToForm(Hardwareoptimization_Form_Hardware_Optimization_Navigation::BUTTONS_ALL, $this);
    }
}