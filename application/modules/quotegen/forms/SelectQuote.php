<?php

class Quotegen_Form_SelectQuote extends Twitter_Bootstrap_Form_Inline
{
    /**
     * The user id to get quotes for.
     *
     * @var int
     */
    protected $_userId;

    public function __construct ($userId, $options = null)
    {
        $this->_userId = $userId;
        parent::__construct($options);
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
        $this->_addClassNames('form-center-actions');
        
        $clientList = array ();
        /* @var $client Quotegen_Model_Client */
        foreach ( Quotegen_Model_Mapper_Client::getInstance()->fetchAll() as $client )
        {
            $clientList [$client->id] = $client->companyName;
        }
        $clientSelect = new Zend_Form_Element_Select('clientId');
        $clientSelect->addMultiOptions($clientList);
        $clientSelect->setLabel('Company Name');
        $this->addElement($clientSelect);
        
        $quoteList = array ();
        $quoteListValidator = array ();
        /* @var $quote Quotegen_Model_Quote */
        foreach ( Quotegen_Model_Mapper_Quote::getInstance()->fetchAllForUser($this->_userId) as $quote )
        {
            $clientName = $quote->getClient()->companyName;
            $dateCreated = $quote->getDateCreated();
            $quoteList [$quote->getId()] = "$clientName - $dateCreated";
            $quoteListValidator [] = $quote->getId();
        }
        
        // Create a select element to hold quotes for the user
        $quotes = new Zend_Form_Element_Select('quoteId');
        
        // Quotes element setup vars
        $quotes->addMultiOptions($quoteList);
        $quotes->addValidator('InArray', false, array (
                $quoteListValidator 
        ));
        $quotes->setLabel('Quote Date');
        
        // Add the quote element to the form
        $this->addElement($quotes);
        
        // Add the submit button
        $this->addElement('submit', 'submit', array (
                'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY, 
                'ignore' => true, 
                'label' => 'Continue', 
                'decorators' => array (
                        'ViewHelper', 
                        array (
                                'HtmlTag', 
                                array (
                                        'tag' => 'div', 
                                        'class' => 'form-actions' 
                                ) 
                        ) 
                ) 
        ));
    }
}
