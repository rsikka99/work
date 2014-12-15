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
        $this->addElement('submit', 'cancel', array(
            'label'        => 'Cancel',
            'data-dismiss' => 'modal'
        ));

        $this->addElement('submit', 'delete', array(
            'label'        => 'Delete',
            'data-dismiss' => 'modal'
        ));
        $this->addElement('hidden', 'deleteId', array());
        $this->addElement('hidden', 'deleteColorId', array());
        $this->addElement('hidden', 'deleteFormName', array());
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators(array(
            array(
                'ViewScript',
                array(
                    'viewScript' => 'forms/hardware-library/device-management/delete-form.phtml',
                )
            )
        ));
    }
}