<?php

namespace MPSToolbox\Forms;

class ComputersAttributesForm extends HardwareAttributesForm
{

    public function init ()
    {
        parent::init();

        $this->addElement('checkbox', 'webcam', [
            'label'      => 'Webcam',
        ]);

        $this->addElement('checkbox', 'mediaDrive', [
            'label'      => 'CD/DVD Drive',
        ]);

        $this->addElement('checkbox', 'usb3', [
            'label'      => 'USB3',
        ]);

        $this->addElement('text', 'usbDescription', [
            'label'      => 'USB Description',
            'maxlength'  => 255,
            'required'   => false,
            'allowEmpty' => true,
        ]);

        $this->addElement('text', 'os', [
            'label'      => 'OS',
            'maxlength'  => 255,
            'required'   => false,
            'allowEmpty' => true,
        ]);

        $this->addElement('text_int', 'ram', [
            'label'      => 'RAM (GB)',
            'required'   => false,
            'allowEmpty' => true,
        ]);

        $this->addElement('text_int', 'hdd', [
            'label'      => 'Disk Size (GB)',
            'required'   => false,
            'allowEmpty' => true,
        ]);

        $this->addElement('text_float', 'screenSize', [
            'label'      => 'Screen Size (Inch)',
            'required'   => false,
            'allowEmpty' => true,
        ]);

        $this->addElement('checkbox', 'hdDisplay', [
            'label'      => 'HD Display',
        ]);

        $this->addElement('checkbox', 'ledDisplay', [
            'label'      => 'LED Display',
        ]);

        $this->addElement('text_float', 'weight', [
            'label'      => 'Weight (lbs)',
            'required'   => false,
            'allowEmpty' => true,
        ]);

        $this->addElement('text', 'processorName', [
            'label'      => 'Processor Name',
            'maxlength'  => 255,
            'required'   => false,
            'allowEmpty' => true,
        ]);

        $this->addElement('text_float', 'processorSpeed', [
            'label'      => 'Processor Speed (Ghz)',
            'required'   => false,
            'allowEmpty' => true,
        ]);

        $this->addElement('text', 'service', [
            'label'      => 'Service',
            'maxlength'  => 255,
            'required'   => false,
            'allowEmpty' => true,
        ]);

    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/hardware-library/computers-attributes-form.phtml']]]);
    }
}
