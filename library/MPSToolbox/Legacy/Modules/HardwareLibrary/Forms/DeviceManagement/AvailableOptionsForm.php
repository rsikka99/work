<?php

namespace MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement;

use My_Brand;
use Zend_Form;

/**
 * Class AvailableOptionsForm
 *
 * @package MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement
 */
class AvailableOptionsForm extends Zend_Form
{
    public function init ()
    {
        $this->setMethod('post');

        $this->addElement('text', 'name', [
            'label'      => 'Name',
            'required'   => true,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [['validator' => 'StringLength', 'options' => [1, 255],],],
        ]);

        $this->addElement('textarea', 'description', [
            'label'      => 'Description',
            'required'   => true,
            'rows'       => 5,
            'filters'    => array('StringTrim', 'StripTags'),
            'validators' => [['validator' => 'StringLength', 'options' => [1, 255],],],
        ]);

        $this->addElement('text', 'cost', [
            'label'      => 'Cost',
            'required'   => true,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => ['Float'],
        ]);

        $this->addElement('text', 'oemSku', [
            'label'      => 'Manufacturer VPN/SKU',
            'required'   => true,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [['validator' => 'StringLength', 'options' => [1, 255],],],
        ]);

        $this->addElement('text', 'dealerSku', [
            'label'      => My_Brand::$dealerSku,
            'class'      => 'span3',
            'maxlength'  => 255,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [['validator' => 'StringLength', 'options' => [1, 255],],],
        ]);

        $this->addElement('hidden', 'id', []);
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/hardware-library/device-management/available-options-form.phtml']]]);
    }
}