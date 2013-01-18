<?php

class Quotegen_Form_Quote extends Twitter_Bootstrap_Form_Horizontal
{
    protected $_leasingSchemaId;

    public function __construct ($options = null)
    {
        parent::__construct($options);
        Quotegen_Form_Quote_Navigation::addFormActionsToForm(Quotegen_Form_Quote_Navigation::BUTTONS_SAVE, $this);
    }

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
        
        $clientList = array ();
        $clientListValidator = array ();
        /* @var $client Quotegen_Model_Client */
        foreach ( Quotegen_Model_Mapper_Client::getInstance()->fetchAll() as $client )
        {
            $clientList [$client->id] = $client->companyName;
            $clientListValidator [] = $client->id;
        }
        
        $clients = $this->createElement('select', 'clientId');
        $clients->setLabel('Select Client:');
        $clients->addMultiOptions($clientList);
        $clients->addValidator('InArray', false, array (
                $clientListValidator 
        ));
        $this->addElement($clients);
        
        $this->addElement('radio', 'quoteType', array (
                'label' => 'Type Of Quote:', 
                'filters' => array (
                        'StringTrim', 
                        'StripTags' 
                ), 
                'multiOptions' => array (
                        'purchased' => 'Purchased', 
                        'leased' => 'Leased' 
                ), 
                'required' => true 
        ));
        
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators(array (
                array (
                        'ViewScript', 
                        array (
                                'viewScript' => 'index/form/quote.phtml' 
                        ) 
                ) 
        ));
    }
}