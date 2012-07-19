<?php

class Quotegen_Form_Configuration extends EasyBib_Form
{

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');
        /**
         * Add class to form for label alignment
         *
         * - Vertical .form-vertical (not required)	Stacked, left-aligned labels
         * over controls (default)
         * - Inline .form-inline Left-aligned label and inline-block controls
         * for compact style
         * - Search .form-search Extra-rounded text input for a typical search
         * aesthetic
         * - Horizontal .form-horizontal
         *
         * Use .form-horizontal to have same experience as with Bootstrap v1!
         */
        $this->setAttrib('class', 'form-horizontal form-center-actions');
        
        $this->addElement('text', 'name', array (
                'label' => 'Name:', 
                'required' => true, 
                
                'filters' => array (
                        'StringTrim', 
                        'StripTags' 
                ) 
        ));
        
        $this->addElement('textarea', 'description', array (
                'label' => 'Description:', 
                'required' => true, 
                'style' => 'height: 100px', 
                'filters' => array (
                        'StringTrim', 
                        'StripTags' 
                ) 
        ));
        
        // Add the submit button
        $this->addElement('submit', 'submit', array (
                'ignore' => true, 
                'label' => 'Save' 
        ));
        
        // Add the cancel button
        $this->addElement('submit', 'cancel', array (
                'ignore' => true, 
                'label' => 'Cancel' 
        ));
        
        EasyBib_Form_Decorator::setFormDecorator($this, EasyBib_Form_Decorator::BOOTSTRAP, 'submit', 'cancel');
    }
}

?>