<?php

class Quotegen_Form_SelectQuote extends EasyBib_Form
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
        $this->setAttrib('class', 'form-vertical button-tabbed-styled');
        
        $quoteList = array ();
        $quoteListValidator = array ();
        /* @var $quote Quotegen_Model_Quote */
        foreach ( Quotegen_Model_Mapper_Quote::getInstance()->fetchAll() as $quote )
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
        $quotes->setAttrib('class', 'span5');
        $this->addElement($quotes);
        
        // Add the submit button
        $this->addElement('submit', 'submit', array (
                'ignore' => true, 
                'label' => 'Continue' 
        ));
        
        // Add the cancel button
        $this->addElement('submit', 'cancel', array (
                'ignore' => true, 
                'label' => 'Cancel' 
        ));
//         $this->addElement('hash', 'csrf', array (
//                 'ignore' => true
//         ));
        
        EasyBib_Form_Decorator::setFormDecorator($this, EasyBib_Form_Decorator::BOOTSTRAP, 'submit', 'cancel');
    }
}
