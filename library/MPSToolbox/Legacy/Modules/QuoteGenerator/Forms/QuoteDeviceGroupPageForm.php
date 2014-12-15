<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Forms;

use Zend_Form;

/**
 * Class QuoteDeviceGroupPageForm
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Forms
 */
class QuoteDeviceGroupPageForm extends Zend_Form
{
    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');

        $this->addElement('text', 'name', array(
            'label'      => 'Name:',
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

        $this->addElement('text', 'oemSku', array(
            'label'      => 'OEM SKU	:',
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

        $this->addElement('text', 'pricePerPage', array(
            'label'      => 'Price Per Page:',
            'required'   => true,
            'filters'    => array(
                'StringTrim',
                'StripTags'
            ),
            'validators' => array(
                'Float',
                array(
                    'validator' => 'Between',
                    'options'   => array(
                        'min'       => 0,
                        'max'       => 5,
                        'inclusive' => false
                    )
                )
            )
        ));

        $this->addElement('text', 'includedPrice', array(
            'label'      => 'Included Price:',
            'required'   => true,
            'filters'    => array(
                'StringTrim',
                'StripTags'
            ),
            'validators' => array(
                'Float',
                array(
                    'validator' => 'Between',
                    'options'   => array(
                        'min'       => 0,
                        'max'       => 5000,
                        'inclusive' => true
                    )
                )
            )
        ));

        $this->addElement('text', 'includedQuantity', array(
            'label'      => 'Included Quantity:',
            'required'   => true,
            'filters'    => array(
                'StringTrim',
                'StripTags'
            ),
            'validators' => array(
                'Int',
                array(
                    'validator' => 'Between',
                    'options'   => array(
                        'min'       => 0,
                        'max'       => 50000,
                        'inclusive' => true
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
