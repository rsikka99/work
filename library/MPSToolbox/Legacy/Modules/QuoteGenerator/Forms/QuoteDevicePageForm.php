<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Forms;

use Zend_Form;

/**
 * Class QuoteDevicePageForm
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Forms
 */
class QuoteDevicePageForm extends Zend_Form
{

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');

        $this->addElement('text', 'costPerPageMonochrome', array(
            'label'      => 'CPP Monochrome:',
            'required'   => true,
            'filters'    => array(
                'StringTrim',
                'StripTags'
            ),
            'validators' => array(
                array(
                    'validator' => 'StringLength',
                    'options'   => array(
                        1,
                        255
                    )
                )
            )
        ));

        $this->addElement('text', 'costPerPageColor', array(
            'label'      => 'CPP Color:',
            'required'   => true,
            'filters'    => array(
                'StringTrim',
                'StripTags'
            ),
            'validators' => array(
                array(
                    'validator' => 'StringLength',
                    'options'   => array(
                        1,
                        255
                    )
                )
            )
        ));

        $this->addElement('select', 'pageBillingPreference', array(
            'label'    => 'Page Billing Preference:',
            'required' => true,
            'filters'  => array(
                'StringTrim',
                'StripTags'
            )
        ));

        $this->addElement('text', 'margin', array(
            'label'      => 'Margin:',
            'required'   => true,
            'filters'    => array(
                'StringTrim',
                'StripTags'
            ),
            'validators' => array(
                array(
                    'validator' => 'StringLength',
                    'options'   => array(
                        1,
                        255
                    )
                )
            )
        ));

        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore' => true,
            'label'  => 'Save'
        ));

        // Add the cancel button
        $this->addElement('submit', 'cancel', array(
            'ignore' => true,
            'label'  => 'Cancel'
        ));

    }
}
