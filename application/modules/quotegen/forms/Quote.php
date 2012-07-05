<?php

class Quotegen_Form_Quote extends EasyBib_Form
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
        $this->setAttrib('class', 'form-horizontal button-styled');
        
        $clientList = array ();
        $clientListValidator = array ();
        /* @var $client Quotegen_Model_Client */
        foreach ( Quotegen_Model_Mapper_Client::getInstance()->fetchAll() as $client )
        {
            $clientList [$client->getId()] = $client->getName();
            $clientListValidator [] = $client->getId();
        }
        
        $clients = new Zend_Form_Element_Select('clientId');
        $clients->setLabel('Select Client:');
        $clients->addMultiOptions($clientList);
        $clients->addValidator('InArray', false, array (
                $clientListValidator 
        ));
        $this->addElement($clients);
        
        $this->addElement('text', 'clientDisplayName', array (
                'label' => 'Client Display Name:', 
                'required' => true, 
                'filters' => array (
                        'StringTrim', 
                        'StripTags' 
                ), 
                'validators' => array (
                        array (
                                'validator' => 'StringLength', 
                                'options' => array (
                                        1, 
                                        255 
                                ) 
                        ) 
                ) 
        ));
        
        $this->addElement('checkbox', 'isLeased', array (
                'label' => 'Leased Quote', 
                'required' => true 
        ));
        
        // Add the submit button
        $this->addElement('submit', 'submit', array (
                'ignore' => true, 
                'label' => 'Save & Continue' 
        ));
        
        // Add the cancel button
        $this->addElement('submit', 'cancel', array (
                'ignore' => true, 
                'label' => 'Cancel' 
        ));        

        
        EasyBib_Form_Decorator::setFormDecorator($this, EasyBib_Form_Decorator::BOOTSTRAP, 'submit', 'cancel');
    }
}
