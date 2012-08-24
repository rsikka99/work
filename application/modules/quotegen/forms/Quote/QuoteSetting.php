<?php

class Quotegen_Form_Quote_QuoteSetting extends Twitter_Bootstrap_Form_Horizontal
{
    protected $_leasingSchemaId;

    public function __construct ($options = null)
    {
        $this->addPrefixPath('My_Form_Element', 'My/Form/Element', 'element');
        
        parent::__construct($options);
        Quotegen_Form_Quote_Navigation::addFormActionsToForm(Quotegen_Form_Quote_Navigation::BUTTONS_SAVE_NEXT, $this);
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
                ) 
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
            ->setDescription('yyyy-mm-dd hh:mm')
            ->addValidator(new My_Validate_DateTime())
            ->setRequired(false);
        $quoteDate->addFilters(array (
                'StringTrim', 
                'StripTags' 
        ));
        
        $quoteDate->setRequired(true);
        $this->addElement($quoteDate);
        
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
        
        /**
         * ------------------------------------------------------------------
         * LEASING DETAILS
         * ------------------------------------------------------------------
         */
        $this->addElement('select', 'leasingSchemaId', array (
                'label' => 'Leasing Schema:', 
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
                $leasingSchemaTerms [$leasingSchemaTerm->getId()] = $leasingSchemaTerm->getMonths();
            }
        }
        
        $this->addElement('select', 'leasingSchemaTermId', array (
                'label' => 'Lease Term:', 
                'multiOptions' => $leasingSchemaTerms, 
                'required' => true 
        ));
        
        /**
         * ------------------------------------------------------------------
         * PAGE COVERAGE
         * ------------------------------------------------------------------
         */
        $pageCoverageColor = $this->createElement('text', 'pageCoverageColor', array (
                'label' => 'Page Covereage Color:', 
                'class' => 'span1', 
                'filters' => array (
                        'StringTrim', 
                        'StripTags' 
                ), 
                'validators' => array (
                        array (
                                'validator' => 'Between', 
                                'options' => array (
                                        'min' => 0, 
                                        'max' => 100, 
                                        'inclusive' => false 
                                ) 
                        ), 
                        'Float' 
                ), 
                'append' => 'Default: ' . number_format($quoteSetting->getPageCoverageColor(), 2) . "%" 
        ));
        $this->addElement($pageCoverageColor);
        
        $pageCoverageMonochrome = $this->createElement('text', 'pageCoverageMonochrome', array (
                'label' => 'Page Coverage Monochrome:', 
                'class' => 'span1', 
                'filters' => array (
                        'StringTrim', 
                        'StripTags' 
                ), 
                'validators' => array (
                        array (
                                'validator' => 'Between', 
                                'options' => array (
                                        'min' => 0, 
                                        'max' => 100, 
                                        'inclusive' => false 
                                ) 
                        ), 
                        'Float' 
                ), 
                'append' => 'Default: ' . number_format($quoteSetting->getPageCoverageMonochrome(), 2) . '%' 
        ));
        $this->addElement($pageCoverageMonochrome);
        
        /**
         * ------------------------------------------------------------------
         * COST PER PAGE
         * ------------------------------------------------------------------
         */
        $adminCostPerPage = $this->createElement('text', 'adminCostPerPage', array (
                'label' => 'Admin Cost Per Page:', 
                'class' => 'input-mini', 
                'filters' => array (
                        'StringTrim', 
                        'StripTags' 
                ), 
                'validators' => array (
                        array (
                                'validator' => 'Between', 
                                'options' => array (
                                        'min' => 0, 
                                        'max' => 5, 
                                        'inclusive' => false 
                                ) 
                        ), 
                        'Float' 
                ), 
                'append' => 'Default: ' . $this->getView()
                    ->currency((float)$quoteSetting->getAdminCostPerPage()) 
        ));
        $this->addElement($adminCostPerPage);
        
        $serviceCostPerPage = $this->createElement('text', 'serviceCostPerPage', array (
                'label' => 'Service Cost Per Page:', 
                'class' => 'input-mini', 
                'filters' => array (
                        'StringTrim', 
                        'StripTags' 
                ), 
                'validators' => array (
                        array (
                                'validator' => 'Between', 
                                'options' => array (
                                        'min' => 0, 
                                        'max' => 5, 
                                        'inclusive' => false 
                                ) 
                        ), 
                        'Float' 
                ), 
                'append' => 'Default: ' . $this->getView()
                    ->currency((float)$quoteSetting->getServiceCostPerPage()) 
        ));
        $this->addElement($serviceCostPerPage);
        
        /**
         * ------------------------------------------------------------------
         * Pricing Configuration
         * ------------------------------------------------------------------
         */
        $pricingConfigDropdown = $this->createElement('select', 'pricingConfigId', array (
                'label' => 'Toner Preference:', 
                'append' => 'Default: ' . $quoteSetting->getPricingConfig()
                    ->getConfigName() 
        ));
        
        /* @var $princingConfig Proposalgen_Model_PricingConfig */
        foreach ( Proposalgen_Model_Mapper_PricingConfig::getInstance()->fetchAll() as $pricingConfig )
        {
            $pricingConfigDropdown->addMultiOption($pricingConfig->getPricingConfigId(), $pricingConfig->getConfigName());
        }
        $this->addElement($pricingConfigDropdown);
    }
}