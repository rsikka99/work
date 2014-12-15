<?php

namespace MPSToolbox\Legacy\Modules\HardwareLibrary\Forms;

use Zend_Form;

/**
 * Class ManufacturerForm
 *
 * @package MPSToolbox\Legacy\Modules\HardwareLibrary\Forms
 */
class ManufacturerForm extends Zend_Form
{
    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');

        $this->addElement('text', 'fullname', array(
            'label'      => 'Full Name:',
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags'),
            'validators' => array(
                array(
                    'validator' => 'StringLength',
                    'options'   => array(1, 255),
                )
            )
        ));

        $this->addElement('text', 'displayname', array(
            'label'      => 'Display Name:',
            'required'   => false,
            'filters'    => array('StringTrim', 'StripTags'),
            'validators' => array(
                array(
                    'validator' => 'StringLength',
                    'options'   => array(1, 255),
                )
            )
        ));

        $this->addElement('checkbox', 'isDeleted', array(
            'label'   => 'Deleted',
            'filters' => array('Boolean'),
        ));

        $this->addElement('checkbox', 'isTonerVendor', array(
            'label'   => 'Is Toner Vendor',
            'filters' => array('Boolean'),
        ));

        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore' => true,
            'label'  => 'Save'
        ));

        // Add the cancel button
        $this->addElement('submit', 'cancel', array(
            'ignore'          => true,
            'formnovalidate ' => true,
            'label'           => 'Cancel'
        ));

    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators(array(
            array(
                'ViewScript',
                array(
                    'viewScript' => 'forms/hardware-library/manufacturer-form.phtml'
                )
            )
        ));
    }
}
