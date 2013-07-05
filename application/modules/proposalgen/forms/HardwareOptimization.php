<?php

/**
 * Class Proposalgen_Form_Hardware Optimization
 */
class Proposalgen_Form_HardwareOptimization extends Twitter_Bootstrap_Form_Horizontal
{

    public function init ()
    {
        $this->setMethod('post');

        $isDeviceSwapElement = $this->createElement('checkbox', 'isDeviceSwap', array(
                                                                                     'label' => 'Is a device swap',
                                                                                ));
        /*
         * Parts Cost Per Page
         */
        $minimumPageCountElement = $this->createElement('text', 'minimumPageCount', array(
                                                                                         'label'      => 'Minimum Page Count',
                                                                                         'class'      => 'span4',
                                                                                         'maxlength'  => 8,
                                                                                         'allowEmpty' => false,
                                                                                         'filters'    => array(
                                                                                             'StringTrim',
                                                                                             'StripTags'
                                                                                         ),
                                                                                         'validators' => array(
                                                                                         )
                                                                                    ))->setAttrib('onkeypress', 'javascript: return numbersonly(this, event)');

        /*
        * Labor Cost Per Page
        */
        $maximumPageCountElement = $this->createElement('text', 'maximumPageCount', array(
                                                                                         'label'      => 'Maximum Page Count',
                                                                                         'class'      => 'span4',
                                                                                         'maxlength'  => 8,
                                                                                         'allowEmpty' => false,
                                                                                         'filters'    => array(
                                                                                             'StringTrim',
                                                                                             'StripTags'
                                                                                         ),
                                                                                         'validators' => array(
                                                                                             new Tangent_Validate_FieldDependsOnValue('isDeviceSwap', '1', array(
                                                                                                                                                                new Zend_Validate_NotEmpty(),
                                                                                                                                                                new Zend_Validate_Float(),
                                                                                                                                                                new Zend_Validate_Between(array(
                                                                                                                                                                                               'min' => 0,
                                                                                                                                                                                               'max' => 9223372036854775807
                                                                                                                                                                                          ))
                                                                                                                                                           ))
                                                                                         )
                                                                                    ))->setAttrib('onkeypress', 'javascript: return numbersonly(this, event)');
        $this->addElement($isDeviceSwapElement);
        $this->addElement($minimumPageCountElement);
        $this->getElement("isDeviceSwap")->setDecorators(array("ViewHelper",
                                                                       array(array('wrapper' => 'HtmlTag'),array('tag' => 'div','class' => 'switch','data-on-label' => 'Yes','data-off-label' => 'No','data-off' => 'danger','data-on' => 'success')),
                                                                       array(array('donkeyKong' => 'HtmlTag'),array('tag' => 'div','class' => 'controls')),
                                                                       array("label",array('class' => 'control-label')),
                                                                       array(array('controls' => 'HtmlTag'),array('tag' => 'div','class' => 'control-group'))));
        $minimumPageCountElement->addValidators(array(new Tangent_Validate_FieldDependsOnValue('isDeviceSwap', '1', array(
                                                                                                                   new Zend_Validate_NotEmpty(),
                                                                                                                   new Zend_Validate_Float(),
                                                                                                                   new Zend_Validate_Between(array(
                                                                                                                                                  'min' => 0,
                                                                                                                                                  'max' => 9223372036854775807
                                                                                                                                             ))
                                                                                                                   , new My_Validate_LessThanFormValue($maximumPageCountElement)))));

        $this->addElement($maximumPageCountElement);
    }
}