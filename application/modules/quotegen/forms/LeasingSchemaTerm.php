<?php

class Quotegen_Form_LeasingSchemaTerm extends EasyBib_Form
{

    public function __construct ($leasingSchemaRanges = null)
    {
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
        $this->setName('leasingSchemaTerm');
        $this->setAttrib('id', 'leasingSchemaTerm');
        
        // Set the method for the display form to POST
        $this->setMethod('POST');
        
        $this->addElement('text', 'term', array (
                'label' => 'New Term:', 
                'required' => true, 
                'class' => 'span1',
                'filters' => array (
                        'StringTrim', 
                        'StripTags' 
                ), 
                'validators' => array (
                        array (
                                'validator' => 'StringLength', 
                                'options' => array (
                                        1, 
                                        3 
                                ) 
                        ) 
                ) 
        ));
        
        foreach ( $leasingSchemaRanges as $range )
        {
            $rangeid = $range->getId();
            
	        $this->addElement('text', "range{$rangeid}", array (
	                'label' => 'Range:', 
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
