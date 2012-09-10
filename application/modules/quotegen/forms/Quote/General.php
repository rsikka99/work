<?php

class Quotegen_Form_Quote_General extends Twitter_Bootstrap_Form_Vertical
{
    
    /**
     * This represents the current quote being worked on
     *
     * @var Quotegen_Model_Quote
     */
    protected $_quote;

    public function __construct (Quotegen_Model_Quote $quote, $options = null)
    {
        $this->_quote = $quote;
        $this->addPrefixPath('My_Form_Element', 'My/Form/Element', 'element');
        
        parent::__construct($options);
        //         Quotegen_Form_Quote_Navigation::addFormActionsToForm(Quotegen_Form_Quote_Navigation::BUTTONS_NEXT, $this);
    }

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');
        
        $this->_addClassNames('form-center-actions');
        
        // Get resolved system settings
        $quoteSetting = Quotegen_Model_Mapper_QuoteSetting::getInstance()->fetchSystemQuoteSetting();
        $userSetting = Quotegen_Model_Mapper_UserQuoteSetting::getInstance()->fetchUserQuoteSetting(Zend_Auth::getInstance()->getIdentity()->id);
        $quoteSetting->applyOverride($userSetting);
        
        $this->addElement('text', 'clientDisplayName', array (
                'label' => 'Display Name:', 
                'filters' => array (
                        'StringTrim', 
                        'StripTags' 
                ), 
                'placeholder' => $this->_quote->getClient()
                    ->getName(), 
                'description' => 'Display Name' 
        ));
        
        $minYear = (int)date('Y') - 2;
        $maxYear = $minYear + 4;
        $quoteDate = $this->createElement('DateTimePicker', 'quoteDate');
        $quoteDate->setLabel('Quote Date:')
            ->setJQueryParam('dateFormat', 'yy-mm-dd')
            ->setJqueryParam('timeFormat', 'hh:mm')
            ->setJQueryParam('changeYear', 'true')
            ->setJqueryParam('changeMonth', 'true')
            ->setJqueryParam('yearRange', "{$minYear}:{$maxYear}")
            ->setDescription('Quote Date (yyyy-mm-dd hh:mm)')
            ->addValidator(new My_Validate_DateTime())
            ->setRequired(false);
        $quoteDate->addFilters(array (
                'StringTrim', 
                'StripTags' 
        ));
        
        $quoteDate->setRequired(true);
        $this->addElement($quoteDate);
        
        $this->addElement('button', 'submit', array (
                'buttonType' => Twitter_Bootstrap_Form_Element_Button::BUTTON_SUCCESS,
                'label' => 'Update',
                'type' => 'submit',
                'icon' => 'check',
                'whiteIcon' => true
        ));
    
//         $leasingSchemas = array ();
        //         $leasingSchemaId = null;
        //         /* @var $leasingSchema Quotegen_Model_LeasingSchema */
        //         foreach ( Quotegen_Model_Mapper_LeasingSchema::getInstance()->fetchAll() as $leasingSchema )
        //         {
        //             if (! $leasingSchemaId)
        //             {
        //                 $leasingSchemaId = $leasingSchema->getId();
        //             }
        //             $leasingSchemas [$leasingSchema->getId()] = $leasingSchema->getName();
        //         }
    

//         if ($this->_leasingSchemaId)
        //         {
        //             $leasingSchemaId = $this->_leasingSchemaId;
        //         }
    

//         /**
        //          * ------------------------------------------------------------------
        //          * LEASING DETAILS
        //          * ------------------------------------------------------------------
        //          */
        //         $this->addElement('select', 'leasingSchemaId', array (
        //                 'label' => 'Leasing Schema:', 
        //                 'multiOptions' => $leasingSchemas, 
        //                 'required' => true, 
        //                 'value' => $leasingSchemaId 
        //         ));
    

//         $leasingSchema = Quotegen_Model_Mapper_LeasingSchema::getInstance()->find($leasingSchemaId);
        //         $leasingSchemaTerms = array ();
        //         if ($leasingSchema)
        //         {
        //             /* @var $leasingSchemaTerm Quotegen_Model_LeasingSchemaTerm */
        //             foreach ( $leasingSchema->getTerms() as $leasingSchemaTerm )
        //             {
        //                 $leasingSchemaTerms [$leasingSchemaTerm->getId()] = $leasingSchemaTerm->getMonths();
        //             }
        //         }
    

//         $this->addElement('select', 'leasingSchemaTermId', array (
        //                 'label' => 'Lease Term:', 
        //                 'multiOptions' => $leasingSchemaTerms, 
        //                 'required' => true 
        //         ));
    

    

//         $this->addDisplayGroup(array (
        //                 'clientDisplayName', 
        //                 'quoteDate' 
        //         ), 'generalGroup', array (
        //                 'legend' => 'General' 
        //         ));
    

//         $this->addDisplayGroup(array (
        //                 'leasingSchemaId', 
        //                 'leasingSchemaTermId' 
        //         ), 'leasingGroup', array (
        //                 'legend' => 'Leasing' 
        //         ));
    

//         $this->addDisplayGroup(array (
        //                 'pricingConfigId', 
        //                 'pageCoverageMonochrome', 
        //                 'pageCoverageColor', 
        //                 'adminCostPerPage', 
        //                 'serviceCostPerPage', 
        //                 'monochromeOverageRatePerPage', 
        //                 'colorOverageRatePerPage' 
        //         ), 'pagesGroup', array (
        //                 'legend' => 'Pages' 
        //         ));
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
}