<?php

namespace MPSToolbox\Forms;

class PeripheralsAttributesForm extends HardwareAttributesForm
{

    public function init ()
    {
        parent::init();
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/hardware-library/peripherals-attributes-form.phtml']]]);
    }
}
