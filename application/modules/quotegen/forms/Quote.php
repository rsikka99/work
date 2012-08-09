<?php

class Quotegen_Form_Quote extends EasyBib_Form
{
    protected $_leasingSchemaId;

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
                'label' => 'Display Name:', 
                'filters' => array (
                        'StringTrim', 
                        'StripTags' 
                ) 
        ));
        
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
                ) 
        ));
        
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
                ) 
        ));
        
        $this->addElement($pageCoverageColor);
        $this->addElement($pageCoverageMonochrome);
        
        // Get resolved system settings
        $quoteSetting = Quotegen_Model_Mapper_QuoteSetting::getInstance()->fetchSystemQuoteSetting();
        $userSetting = Quotegen_Model_Mapper_UserQuoteSetting::getInstance()->fetchUserQuoteSetting(Zend_Auth::getInstance()->getIdentity()->id);
        $quoteSetting->applyOverride($userSetting);
        
        $pageCoverageColor->setDescription($quoteSetting->getPageCoverageColor());
        $pageCoverageMonochrome->setDescription($quoteSetting->getPageCoverageMonochrome());
        
        $pricingConfigDropdown = new Zend_Form_Element_Select('pricingConfigId', array (
                'label' => 'Toner Preference:' 
        ));
        
        /* @var $princingConfig Proposalgen_Model_PricingConfig */
        foreach ( Proposalgen_Model_Mapper_PricingConfig::getInstance()->fetchAll() as $pricingConfig )
        {
            $pricingConfigDropdown->addMultiOption($pricingConfig->getPricingConfigId(), $pricingConfig->getConfigName());
        }
        $this->addElement($pricingConfigDropdown);
        
        $pricingConfigDropdown->setDescription($quoteSetting->getPricingConfigId());
        
        // Add the submit button and cancel button
        $this->addElement('submit', 'submit', array (
                'ignore' => true, 
                'label' => 'Create' 
        ));
        $this->addElement('submit', 'cancel', array (
                'ignore' => true, 
                'label' => 'Cancel' 
        ));
        EasyBib_Form_Decorator::setFormDecorator($this, EasyBib_Form_Decorator::BOOTSTRAP, 'submit', 'cancel');
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