<?php

namespace MPSToolbox\Forms;

class PeripheralsAttributesForm extends HardwareAttributesForm
{

    public function init ()
    {
        parent::init();

        $this->addElement('multiselect', 'appliesTo', [
            'label'      => 'Applies To',
            'required'   => false,
            'allowEmpty' => true,
            'multiOptions' => $this->arrToMulti([
                'Laptop','Desktop','Server','Tablet'
            ])
        ]);

    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/hardware-library/peripherals-attributes-form.phtml']]]);
    }
}
