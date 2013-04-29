<?php

class Quotegen_Form_Quote_Profitability extends Twitter_Bootstrap_Form_Inline
{
    protected $_leasingSchemaId;
    /**
     * This represents the current quote being worked on
     *
     * @var Quotegen_Model_Quote
     */
    protected $_quote;

    public function __construct ($quote = null, $leasingSchemaId = null, $options = null)
    {
        $this->_leasingSchemaId = $leasingSchemaId;
        $this->_quote           = $quote;
        parent::__construct($options);
        Quotegen_Form_Quote_Navigation::addFormActionsToForm(Quotegen_Form_Quote_Navigation::BUTTONS_ALL, $this);
    }

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');
        $this->_addClassNames('form-center-actions');

        // Setup the element decorators
        $this->setElementDecorators(array(
                                         'FieldSize',
                                         'ViewHelper',
                                         'Addon',
                                         'PopoverElementErrors',
                                         'Wrapper'
                                    ));

        if ($this->_quote->isLeased())
        {
            $leasingSchemas  = array();
            $leasingSchemaId = null;
            /* @var $leasingSchema Quotegen_Model_LeasingSchema */
            foreach (Quotegen_Model_Mapper_LeasingSchema::getInstance()->getSchemasForDealer(Zend_Auth::getInstance()->getIdentity()->dealerId) as $leasingSchema)
            {
                // Use this to grab the first id in the leasing schema dropdown
                if (!$leasingSchemaId)
                {
                    $leasingSchemaId = $leasingSchema->id;
                }
                $leasingSchemas [$leasingSchema->id] = $leasingSchema->name;
            }

            /**
             * If the quote has a leasing schema term already selected we should grab the values from the schema it's from
             */
            if ($this->getQuote()->getLeasingSchemaTerm() && $this->getQuote()->getLeasingSchemaTerm()->getLeasingSchema()->id && $this->getQuote()->getLeasingSchemaTerm()->getLeasingSchema()->dealerId == Zend_Auth::getInstance()->getIdentity()->dealerId)
            {
                $leasingSchemaId = $this->getQuote()->getLeasingSchemaTerm()->getLeasingSchema()->id;
            }

            /**
             * Finally if we've manually specified a new schema, we should grab those values since they will be used for validation
             */
            if ($this->_leasingSchemaId)
            {
                $leasingSchemaId = $this->_leasingSchemaId;
            }

            $this->addElement('select', 'leasingSchemaId', array(
                                                                'label'        => 'Leasing Schema:',
                                                                'class'        => 'input-medium',
                                                                'multiOptions' => $leasingSchemas,
                                                                'required'     => true,
                                                                'value'        => $leasingSchemaId));

            $leasingSchema      = Quotegen_Model_Mapper_LeasingSchema::getInstance()->find($leasingSchemaId);
            $leasingSchemaTerms = array();
            $firstId            = null;
            if ($leasingSchema && $leasingSchemas)
            {
                /* @var $leasingSchemaTerm Quotegen_Model_LeasingSchemaTerm */
                foreach ($leasingSchema->getTerms() as $leasingSchemaTerm)
                {
                    $leasingSchemaTerms [$leasingSchemaTerm->id] = number_format($leasingSchemaTerm->months) . " months";
                    if ($firstId == null)
                    {
                        $firstId = $leasingSchemaTerm->id;
                    }
                }
            }

            if (!$this->getQuote()->getLeasingSchemaTerm() && $firstId != null)
            {
                $termId = $firstId;
            }
            else
            {
                $termId = $this->getQuote()->getLeasingSchemaTerm()->id;
            }
            $this->addElement('select', 'leasingSchemaTermId', array(
                                                                    'label'        => 'Lease Term:',
                                                                    'class'        => 'input-medium',
                                                                    'multiOptions' => $leasingSchemaTerms,
                                                                    'required'     => true,
                                                                    'value'        => $termId
                                                               ));
        }

        // ----------------------------------------------------------------------
        // Form elements for devices
        // ----------------------------------------------------------------------        
        /* @var $quoteDevice Quotegen_Model_QuoteDevice */
        foreach ($this->getQuote()->getQuoteDevices() as $quoteDevice)
        {
            if ($quoteDevice->calculateTotalQuantity() > 0)
            {
                // Package Markup
                $this->addElement('text', "packageMarkup_{$quoteDevice->id}", array(
                                                                                   'label'      => 'Markup',
                                                                                   'required'   => true,
                                                                                   'class'      => 'input-mini rightAlign',
                                                                                   'value'      => $quoteDevice->packageMarkup,
                                                                                   'validators' => array(
                                                                                       'Float',
                                                                                       array(
                                                                                           'validator' => 'Between',
                                                                                           'options'   => array(
                                                                                               'min' => 0,
                                                                                               'max' => 99999
                                                                                           )
                                                                                       )
                                                                                   )
                                                                              ));

                // Margin
                $this->addElement('text', "margin_{$quoteDevice->id}", array(
                                                                            'label'      => 'Margin',
                                                                            'required'   => true,
                                                                            'class'      => 'input-mini rightAlign',
                                                                            'value'      => $quoteDevice->margin,
                                                                            'validators' => array(
                                                                                'Float',
                                                                                array(
                                                                                    'validator' => 'Between',
                                                                                    'options'   => array(
                                                                                        'min'       => -100,
                                                                                        'max'       => 100,
                                                                                        'inclusive' => false
                                                                                    )
                                                                                )
                                                                            )
                                                                       ));

                if ($this->_quote->isLeased())
                {
                    // Residual
                    $this->addElement('text', "residual_{$quoteDevice->id}", array(
                                                                                  'label'      => 'Residual',
                                                                                  'required'   => true,
                                                                                  'class'      => 'input-mini rightAlign',
                                                                                  'value'      => $quoteDevice->residual,
                                                                                  'validators' => array(
                                                                                      'Float',
                                                                                      array(
                                                                                          'validator' => 'Between',
                                                                                          'options'   => array(
                                                                                              'min'       => 0,
                                                                                              'max'       => 30000,
                                                                                              'inclusive' => true
                                                                                          )
                                                                                      )
                                                                                  )
                                                                             ));
                }
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
        $this->setDecorators(array(
                                  array(
                                      'ViewScript',
                                      array(
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
     *
     * @return $this
     */
    public function setQuote ($_quote)
    {
        $this->_quote = $_quote;

        return $this;
    }
}