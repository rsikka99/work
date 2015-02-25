<?php

namespace MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement;

use Zend_Form;

/**
 * Class DeleteForm
 *
 * @package MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement
 */
class DeleteForm extends Zend_Form
{

    /**
     * @param null $options
     */
    public function __construct ($options = null)
    {
        parent::__construct($options);
    }

    public function init ()
    {
        $this->addElement('submit', 'cancel', [
            'label'        => 'Cancel',
            'data-dismiss' => 'modal',
        ]);

        $this->addElement('submit', 'delete', [
            'label'        => 'Delete',
            'data-dismiss' => 'modal',
        ]);
        $this->addElement('hidden', 'deleteId', []);
        $this->addElement('hidden', 'deleteColorId', []);
        $this->addElement('hidden', 'deleteFormName', []);
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/hardware-library/device-management/delete-form.phtml',]]]);
    }
}