<?php

class Quotegen_Form_AddDevice extends Twitter_Bootstrap_Form_Inline
{

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');
        
        
        // An add configuration button for the favorite devices
        $submitButton = $this->createElement('submit', 'addConfiguration', array (
                'ignore' => true, 
                'label' => 'Add', 
                'class' => 'btn btn-success' 
        ));
        
        // This is a list of favorite devices that the user can add
        $this->addElement('select', 'deviceConfigurationId', array (
                'label' => 'Device Configuration', 
                'multiOptions' => array (
                        '-1' => 'New Configuration', 
                        '1' => 'test2', 
                        '2' => 'test2', 
                        '3' => 'test3' 
                ), 
                'prepend' => $submitButton 
        ));
    }
}

?>
