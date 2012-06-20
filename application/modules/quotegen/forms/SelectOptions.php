<?php

class Quotegen_Form_SelectOptions extends EasyBib_Form
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
        $this->setAttrib('class', 'form-horizontal');
        
        $optionList = array ();
        
        
        $options = array ();
        for($i = 1; $i < 20; $i ++)
        {
            $options [] = new Quotegen_Model_Option(array (
                    'id' => $i, 
                    'name' => 'Option Name ' . $i 
            ));
        }
        
        /* @var $option Quotegen_Model_Option */
//         foreach ( Quotegen_Model_Mapper_Option::getInstance()->fetchAll() as $option )
        foreach ( $options as $option )
        {
            $optionList [$option->getId()] = $option->getName();
            
        }
        
        $this->addElement('multiCheckbox', 'optionId', array (
                'label' => 'Options',
                'multiOptions' => $optionList
        ));
        
        // Add the submit button
        $this->addElement('submit', 'submit', array (
                'ignore' => true, 
                'label' => 'Create' 
        ));
        
        // Add the cancel button
        $this->addElement('submit', 'cancel', array (
                'ignore' => true, 
                'label' => 'Cancel' 
        ));
        
        EasyBib_Form_Decorator::setFormDecorator($this, EasyBib_Form_Decorator::BOOTSTRAP, 'submit', 'cancel');
    }
}
