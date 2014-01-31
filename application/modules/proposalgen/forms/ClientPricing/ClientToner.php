<?php

/**
 * Class Proposalgen_Form_ClientPricing_ClientToner
 */
class Proposalgen_Form_ClientPricing_ClientToner extends Twitter_Bootstrap_Form_Horizontal
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
        $this->setMethod('post');

        $this->addElement('hidden', 'id', array(
            'label'    => 'Id',
            'class'    => 'span3',
            'required' => false,
            'disabled' => true,
            'visible'  => false
        ));

        $this->addElement('hidden', 'tonerId', array(
            'label'    => 'Toner Id',
            'class'    => 'span3',
            'required' => false,
            'disabled' => true,
            'visible'  => false
        ));

        $this->addElement('text', 'systemSku', array(
            'label'    => 'OEM SKU',
            'class'    => 'span3',
            'required' => false,
            'disabled' => true,
        ));

        $this->addElement('text', 'dealerSku', array(
            'label'    => My_Brand::$dealerSku,
            'class'    => 'span3',
            'required' => false,
            'disabled' => true,
        ));

        $this->addElement('text', 'clientSku', array(
            'label'     => 'Client SKU',
            'class'     => 'span3',
            'required'  => false,
            'maxlength' => 255,
            'filters'   => array(
                'StringTrim',
                'StripTags'
            ),
            'validator' => 'StringLength',
            'options'   => array(
                1,
                255
            )
        ));

        $this->addElement('select', 'replacementTonerId', array(
            'label'    => "Replacement Toner",
            'class'    => "span3",
            "required" => false,
        ));

        $costElement = $this->createElement('text', 'cost', array(
            'label'      => 'Client Cost',
            'class'      => 'span3',
            'required'   => false,
            'maxlength'  => 255,
            'filters'    => array(
                'StringTrim',
                'StripTags'
            ),
            'validators' => array(
                array(
                    'validator' => 'greaterThan',
                    'options'   => array(
                        'min' => 0
                    )),
            )
        ));

        $costElement->setErrorMessages(array("Must be greater than 0"));
        $this->addElement($costElement);
    }
}