<?php

class Quotegen_Form_Quote_Profitability extends Twitter_Bootstrap_Form_Inline
{
    /**
     * This represents the current quote being worked on
     *
     * @var Quotegen_Model_Quote
     */
    protected $_quote;

    public function __construct ($quote = null, $options = null)
    {
        $this->_quote = $quote;
        parent::__construct($options);
        Quotegen_Form_Quote_Navigation::addFormActionsToForm(Quotegen_Form_Quote_Navigation::BUTTONS_ALL, $this);
    }

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');
        $this->_addClassNames('form-center-actions');
        
        // Setup the element decorators
        $this->setElementDecorators(array (
                'FieldSize', 
                'ViewHelper', 
                'Addon', 
                'PopoverElementErrors', 
                'Wrapper' 
        ));
        
        if ($this->_quote->isLeased())
        {
            $leasingSchemas = array ();
            $leasingSchemaId = null;
            /* @var $leasingSchema Quotegen_Model_LeasingSchema */
            foreach ( Quotegen_Model_Mapper_LeasingSchema::getInstance()->fetchAll() as $leasingSchema )
            {
                if (! $leasingSchemaId)
                {
                    $leasingSchemaId = $leasingSchema->getId();
                }
                $leasingSchemas [$leasingSchema->getId()] = $leasingSchema->getName();
            }
            
            if ($this->_leasingSchemaId)
            {
                $leasingSchemaId = $this->_leasingSchemaId;
            }
            
            $this->addElement('select', 'leasingSchemaId', array (
                    'label' => 'Leasing Schema:', 
                    'class' => 'input-medium',
                    'multiOptions' => $leasingSchemas, 
                    'required' => true, 
                    'value' => $leasingSchemaId 
            ));
            
            $leasingSchema = Quotegen_Model_Mapper_LeasingSchema::getInstance()->find($leasingSchemaId);
            $leasingSchemaTerms = array ();
            if ($leasingSchema)
            {
                /* @var $leasingSchemaTerm Quotegen_Model_LeasingSchemaTerm */
                foreach ( $leasingSchema->getTerms() as $leasingSchemaTerm )
                {
                    $leasingSchemaTerms [$leasingSchemaTerm->getId()] = number_format($leasingSchemaTerm->getMonths()) . " months";
                }
            }
            
            $this->addElement('select', 'leasingSchemaTermId', array (
                    'label' => 'Lease Term:',
                    'class' => 'input-medium', 
                    'multiOptions' => $leasingSchemaTerms, 
                    'required' => true, 
                    'value' => $this->getQuote()
                        ->getLeasingSchemaTerm()
                        ->getId() 
            ));
        }
        
        // ----------------------------------------------------------------------
        // Form elements for devices
        // ----------------------------------------------------------------------        
        /* @var $quoteDevice Quotegen_Model_QuoteDevice */
        foreach ( $this->getQuote()->getQuoteDevices() as $quoteDevice )
        {
            // Package Markup
            $this->addElement('text', "packageMarkup_{$quoteDevice->getId()}", array (
                    'label' => 'Markup', 
                    'required' => true, 
                    'class' => 'input-mini rightAlign', 
                    'value' => $quoteDevice->getPackageMarkup(), 
                    'validators' => array (
                            'Float', 
                            array (
                                    'validator' => 'Between', 
                                    'options' => array (
                                            'min' => 0, 
                                            'max' => 99999 
                                    ) 
                            ) 
                    ) 
            ));
            
            // Margin
            $this->addElement('text', "margin_{$quoteDevice->getId()}", array (
                    'label' => 'Margin', 
                    'required' => true, 
                    'class' => 'input-mini rightAlign', 
                    'value' => $quoteDevice->getMargin(), 
                    'validators' => array (
                            'Float', 
                            array (
                                    'validator' => 'Between', 
                                    'options' => array (
                                            'min' => - 100, 
                                            'max' => 100, 
                                            'inclusive' => false 
                                    ) 
                            ) 
                    ) 
            ));
            
            if ($this->_quote->isLeased())
            {
                // Residual
                $this->addElement('text', "residual_{$quoteDevice->getId()}", array (
                        'label' => 'Residual', 
                        'required' => true, 
                        'class' => 'input-mini rightAlign', 
                        'value' => $quoteDevice->getResidual(), 
                        'validators' => array (
                                'Float', 
                                array (
                                        'validator' => 'Between', 
                                        'options' => array (
                                                'min' => 0, 
                                                'max' => 30000, 
                                                'inclusive' => true 
                                        ) 
                                ) 
                        ) 
                ));
            }
        }
    }

    /**
     * Overrides the loadDefaultDecorators and allows us to use a view script to render the form elements.
     *
     * @see Zend_Form::loadDefaultDecorators()
     */
    public function loadDefaultDecorators ()
    {
        $this->setDecorators(array (
                array (
                        'ViewScript', 
                        array (
                                'viewScript' => 'quote/profitability/form/profitability.phtml' 
                        ) 
                ), 
                'Form' 
        ));
    }

    /**
     * Gets the quote
     *
     * @return Quotegen_Model_Quote
     */
    public function getQuote ()
    {
        return $this->_quote;
    }

    /**
     * Sets the quote
     *
     * @param Quotegen_Model_Quote $_quote            
     */
    public function setQuote ($_quote)
    {
        $this->_quote = $_quote;
        return this;
    }
}

?>