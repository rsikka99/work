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

        $this->addElement('select', 'usb', [
            'label'      => 'USB Version',
            'required'   => false,
            'allowEmpty' => true,
            'multiOptions' => $this->arrToMulti([
                '','USB 1.x', 'USB 2.0','USB 3.0', 'USB 3.1', 'USB Type-C'
            ])
        ]);

        $this->addElement('select', 'os', [
            'label'      => 'OS',
            'required'   => false,
            'allowEmpty' => true,
            'multiOptions' => $this->arrToMulti([
                '','Windows 7','Windows 8','Windows 10','Mac OS X','Linux','Windows Phone','iOS','Android','Windows Server 2008','Windows Server 2012','Windows Server 2016'
            ])
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

        $this->addElement('select', 'displayType', [
            'label'      => 'Display Type',
            'required'   => false,
            'allowEmpty' => true,
            'multiOptions' => $this->arrToMulti([
                '','TFT-LCD','LED','IPS','VA','Plasma'
            ])
        ]);

        $this->addElement('text_float', 'weight', [
            'label'      => 'Weight (lbs)',
            'required'   => false,
            'allowEmpty' => true,
        ]);

        $this->addElement('text', 'processorName', [
            'label'      => 'Processor Name',
            'minlength'  => 3,
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
            'minlength'  => 3,
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
