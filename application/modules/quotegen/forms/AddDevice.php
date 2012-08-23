<?php

class Quotegen_Form_AddDevice extends EasyBib_Form
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
        $this->setAttrib('class', 'form-inline');
        
        $this->setElementDecorators(array('ViewHelper'));
        
        $this->addElement('submit', 'addConfiguration', array (
                'ignore' => true,
                'label' => 'Add',
                'class' => 'btn btn-success'
        ));
        
        
        $this->addElement('select', 'deviceConfigurationId', array (
                'label' => 'Device Configuration', 
                'multiOptions' => array (
                        '-1' => 'New Configuration',
                        '1' => 'test2', 
                        '2' => 'test2', 
                        '3' => 'test3' 
                ) 
        ));
        
        // Add the submit button
        

        
    }
}

?>
