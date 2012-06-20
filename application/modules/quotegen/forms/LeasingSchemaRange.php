<?php

class Quotegen_Form_LeasingSchemaRange extends EasyBib_Form
{

    public function __construct ($leasingSchemaTerms = null)
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
        $this->setAttrib('class', 'form-horizontal');
        $this->setName('leasingSchemaRange');
        $this->setAttrib('id', 'leasingSchemaRange');
        
        $this->addElement('hidden', 'hdnId', array());
        
        $this->addElement('text', 'range', array (
                'label' => 'New Range:', 
                'required' => true, 
                'class' => 'span1',
                'style' => 'position: relative; left: -4px;',
                'filters' => array (
                        'StringTrim', 
                        'StripTags' 
                ),
                'validators' => array (
                        array (
                                'validator' => 'StringLength', 
                                'options' => array (
                                        1, 
                                        6 
                                ) 
                        ) 
                ) 
        ));
        
        foreach ( $leasingSchemaTerms as $term )
        {
            $termid = $term->getId();
            
	        $this->addElement('text', "rate{$termid}", array (
	                'label' => 'Rate:', 
	                'required' => true, 
	                'filters' => array (
	                        'StringTrim', 
	                        'StripTags' 
	                ),
	                'class' => 'span1',
	                'validators' => array (
	                        array (
	                                'validator' => 'StringLength', 
	                                'options' => array (
	                                        1, 
	                                        6 
	                                )
	                        ),
	                        array (
	                                'validator' => 'Between',
	                                'options' => array (
	                                        0.0001,
	                                        1.0000
	                                )
	                        )
	                ) 
	        ));
        }
        
        // Add the submit button
        $this->addElement('submit', 'submit', array (
                'ignore' => true, 
                'label' => 'Save'
        ));
        
        // Add the cancel button
        $this->addElement('button', 'cancel', array (
                'ignore' => true, 
                'label' => 'Cancel' 
        ));
        
        EasyBib_Form_Decorator::setFormDecorator($this, EasyBib_Form_Decorator::BOOTSTRAP, 'submit', 'cancel');
    }
}
