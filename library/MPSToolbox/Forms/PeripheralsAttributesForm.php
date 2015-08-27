<?php

namespace MPSToolbox\Forms;

class PeripheralsAttributesForm extends HardwareAttributesForm
{

    private function arrToMulti($arr) {
        $result = [];
        foreach ($arr as $value) $result[$value] = $value;
        return $result;
    }

    public function init ()
    {
        parent::init();
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/hardware-library/peripherals-attributes-form.phtml']]]);
    }
}
