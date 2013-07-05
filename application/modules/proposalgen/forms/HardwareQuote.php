<?php

/**
 * Class Proposalgen_Form_HardwareQuote
 */
class Proposalgen_Form_HardwareQuote extends Twitter_Bootstrap_Form_Horizontal
{

    public function init ()
    {
        $this->setMethod('post');

        $this->addElement('checkbox', 'isSelling', array(
                                                        'label' => 'Sell This Device'
                                                   ));
        $this->getElement("isSelling")->setDecorators(array("ViewHelper",
                                                           array(array('wrapper' => 'HtmlTag'),array('tag' => 'div','class' => 'switch','data-on-label' => 'Yes','data-off-label' => 'No','data-off' => 'danger','data-on' => 'success')),
                                                           array(array('donkeyKong' => 'HtmlTag'),array('tag' => 'div','class' => 'controls')),
                                                           array("label",array('class' => 'control-label')),
                                                           array(array('controls' => 'HtmlTag'),array('tag' => 'div','class' => 'control-group'))));
        /*
         * Your SKU
         */
        $this->addElement('text', 'oemSku', array(
                                                 'label'      => 'OEM Sku',
                                                 'class'      => 'span2',
                                                 'maxlength'  => 255,
                                                 'required'   => false,
                                                 'filters'    => array(
                                                     'StringTrim',
                                                     'StripTags'
                                                 ),
                                                 'allowEmpty' => false,
                                                 'validators' => array(
                                                     new Tangent_Validate_FieldDependsOnValue('isSelling', '1', array(
                                                                                                                     new Zend_Validate_NotEmpty()
                                                                                                                ), array(
                                                                                                                        'validator' => 'StringLength',
                                                                                                                        'options'   => array(
                                                                                                                            1,
                                                                                                                            255
                                                                                                                        )
                                                                                                                   ))
                                                 )
                                            ));
        /*
         * Dealer SKU
         */
        $this->addElement('text', 'dealerSku', array(
                                                    'label'      => 'Your Sku',
                                                    'class'      => 'span2',
                                                    'maxlength'  => 255,
                                                    'required'   => false,
                                                    'filters'    => array(
                                                        'StringTrim',
                                                        'StripTags'
                                                    ),
                                                    'allowEmpty' => false,
                                                    'validators' => array(
                                                        new Tangent_Validate_FieldDependsOnValue('isSelling', '1', array(
                                                                                                                        new Zend_Validate_NotEmpty()
                                                                                                                   ), array(
                                                                                                                           'validator' => 'StringLength',
                                                                                                                           'options'   => array(
                                                                                                                               1,
                                                                                                                               255
                                                                                                                           )
                                                                                                                      ))
                                                    )
                                               ));
        /*
        * Cost
        */
        $this->addElement('text', 'cost', array(
                                               'label'      => 'Your cost',
                                               'class'      => 'span2',
                                               'maxlength'  => 255,
                                               'required'   => false,
                                               'filters'    => array(
                                                   'StringTrim',
                                                   'StripTags'
                                               ),
                                               'allowEmpty' => false,
                                               'validators' => array(
                                                   new Tangent_Validate_FieldDependsOnValue('isSelling', '1', array(
                                                                                                                   new Zend_Validate_NotEmpty(),
                                                                                                                   new Zend_Validate_Float(),
                                                                                                                   new Zend_Validate_GreaterThan(0)
                                                                                                              )),

                                               )
                                          ));
        /*
         * Description of standard features
         */
        $this->addElement('textarea', 'description', array(
                                                          'label'    => 'Standard Features',
                                                          'style'    => 'height: 100px',
                                                          'required' => false,
                                                          'filters'  => array(
                                                              'StringTrim',
                                                              'StripTags'
                                                          )
                                                     ));
    }
}