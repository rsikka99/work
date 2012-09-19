<?php

class Quotegen_Form_SelectQuote extends EasyBib_Form
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
        $this->setAttrib('class', 'form-vertical form-center-actions');
        

        $clientList = array ();
        /* @var $client Quotegen_Model_Client */
        foreach ( Quotegen_Model_Mapper_Client::getInstance()->fetchAll() as $client )
        {
            $clientList [$client->getId()] = $client->getName();
        }
        $clientSelect = new Zend_Form_Element_Select('clientId');
        $clientSelect->addMultiOptions($clientList);
        $this->addElement($clientSelect);
        
        $quoteList = array ();
        $quoteListValidator = array ();
        /* @var $quote Quotegen_Model_Quote */
        foreach ( Quotegen_Model_Mapper_Quote::getInstance()->fetchAllForUser($this->_userId) as $quote )
        {
            $clientName = $quote->getClient()->getName();
            $dateCreated = $quote->getDateCreated();
            $quoteList [$quote->getId()] = "$clientName - $dateCreated";
            $quoteListValidator [] = $quote->getId();
        }
        
        $quotes = new Zend_Form_Element_Select('quoteId');
        $quotes->addMultiOptions($quoteList);
        $quotes->addValidator('InArray', false, array (
                $quoteListValidator 
        ));
        
        $this->addElement($quotes);
        
        // Add the submit button
        $this->addElement('submit', 'submit', array (
                'ignore' => true, 
                'label' => 'Continue' 
        ));
        
        EasyBib_Form_Decorator::setFormDecorator($this, EasyBib_Form_Decorator::BOOTSTRAP);
    }
}
