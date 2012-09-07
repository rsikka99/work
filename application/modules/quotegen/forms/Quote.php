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
            $clientList [$client->getId()] = $client->getName();
            $clientListValidator [] = $client->getId();
        }
        
        $clients = $this->createElement('select','clientId');
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
        
        // Get resolved system settings
        $quoteSetting = Quotegen_Model_Mapper_QuoteSetting::getInstance()->fetchSystemQuoteSetting();
        $userSetting = Quotegen_Model_Mapper_UserQuoteSetting::getInstance()->fetchUserQuoteSetting(Zend_Auth::getInstance()->getIdentity()->id);
        $quoteSetting->applyOverride($userSetting);
        
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
                'append' => sprintf("System Default: %s%%", number_format($quoteSetting->getPageCoverageMonochrome(),2))
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
                'append' => sprintf("System Default: %s%%", number_format($quoteSetting->getPageCoverageColor(),2))
        ));
        
        $this->addElement($pageCoverageMonochrome);
        
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
                                        'max' => 5 
                                ) 
                        ), 
                        'Float' 
                ), 
                'append' => sprintf("System Default: %s%%", number_format($quoteSetting->getAdminCostPerPage(),2)) 
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
                                        'max' => 5 
                                ) 
                        ), 
                        'Float' 
                ), 
                'append' => sprintf("System Default: %s%%", number_format($quoteSetting->getServiceCostPerPage(),2))
        ));
        $this->addElement($serviceCostPerPage);
        
        $monochromeOverageRatePerPage = $this->createElement('text', 'monochromeOverageRatePerPage', array (
                'label' => 'Monochrome Overate Rate Per Page:',
                'class' => 'input-mini',
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
                ),
                'append' => sprintf("System Default: $%s", number_format($quoteSetting->getMonochromeOverageRatePerPage(),2))
        ));
        $this->addElement($monochromeOverageRatePerPage);
        
        $colorOverageRatePerPage = $this->createElement('text', 'colorOverageRatePerPage', array (
                'label' => 'Color Overate Rate Per Page:',
                'class' => 'input-mini',
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
                ),
                'append' => sprintf("System Default: $%s", number_format($quoteSetting->getColorOverageRatePerPage(),2))
        ));
        $this->addElement($colorOverageRatePerPage);
        
        $pricingConfigDropdown = $this->createElement('select', 'pricingConfigId', array (
                'label' => 'Toner Preference:' 
        ));
        
        /* @var $princingConfig Proposalgen_Model_PricingConfig */
        foreach ( Proposalgen_Model_Mapper_PricingConfig::getInstance()->fetchAll() as $pricingConfig )
        {
            $pricingConfigDropdown->addMultiOption($pricingConfig->getPricingConfigId(), $pricingConfig->getConfigName());
        }
        $this->addElement($pricingConfigDropdown);
        
        $pricingConfigDropdown->setAttrib('append', "System Default: {$quoteSetting->getPricingConfig()->getConfigName()}");
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