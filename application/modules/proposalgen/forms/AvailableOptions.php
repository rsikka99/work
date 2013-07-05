<?php

/**
 * Class Proposalgen_Form_AvailableOptions
 */
class Proposalgen_Form_AvailableOptions extends Twitter_Bootstrap_Form_Horizontal
{

    public function init ()
    {
        $this->setMethod('post');

        $this->addElement('text', 'availableOptionsname', array (
                'label' => 'Name:', 
                'class' => 'span3', 
                'required' => true,
                'maxlength' => 255, 
                'filters' => array (
                        'StringTrim', 
                        'StripTags' 
                ), 
                'validator' => 'StringLength', 
                'options' => array (
                        1, 
                        255 
                ) 
        ));
        
        $this->addElement('textarea', 'availableOptionsdescription', array (
                'label' => 'Description:', 
                'class' => 'span3', 
                'required' => true,
                'style' => 'height: 100px', 
                'maxlength' => 255, 
                'filters' => array (
                        'StringTrim', 
                        'StripTags' 
                ), 
                'validator' => 'StringLength', 
                'options' => array (
                        1, 
                        255 
                ) 
        ));
        
        $availableOptionsCostElement = $this->createElement('text', 'availableOptionscost', array (
                'label' => 'Price:', 
                'class' => 'span2',
                'required' => true,
                'maxlength' => 15,
                'filters' => array (
                        'StringTrim', 
                        'StripTags' 
                ), 
                'validators' => array (
                        'Float' 
                ) 
        ))->setAttrib('onkeypress', 'javascript: return numbersonly(this, event)');;
        $this->addElement($availableOptionsCostElement);
        
        $this->addElement('text', 'availableOptionsoemSku', array (
                'label' => 'OEM SKU:', 
                'class' => 'span3', 
                'required' => true,
                'maxlength' => 255, 
                'filters' => array (
                        'StringTrim', 
                        'StripTags' 
                ), 
                'validator' => 'StringLength', 
                'options' => array (
                        1, 
                        255 
                ) 
        ));
        
        $this->addElement('text', 'availableOptionsdealerSku', array (
                'label' => 'Dealer SKU:',
                'class' => 'span3',
                'maxlength' => 255,
                'filters' => array (
                        'StringTrim',
                        'StripTags'
                ),
                'validator' => 'StringLength',
                'options' => array (
                        1,
                        255
                )
        ));
        $this->addElement('hidden', 'availableOptionsid', array ());
    }
}