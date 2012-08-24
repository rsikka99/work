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
        
        // Get configurations from database
        $deviceConfigurations = Quotegen_Model_Mapper_DeviceConfiguration::getInstance()->fetchAll();
        
        // Populate array with configuraions
        $data = array ();
        $data ['-1'] = 'New configuration';
        
        /* @var $deviceConfiguration Quotegen_Model_DeviceConfiguration */
        foreach ( $deviceConfigurations as $deviceConfiguration )
        {
            $data [$deviceConfiguration->getId()] = $deviceConfiguration->getName();
        }
        
        // This is a list of favorite devices that the user can add
        $this->addElement('select', 'deviceConfigurationId', array (
                'label' => 'Device Configuration', 
                'multiOptions' => $data, 
                'prepend' => $submitButton 
        ));
    }
}

?>
