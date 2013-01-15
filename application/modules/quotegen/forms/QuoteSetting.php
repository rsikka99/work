<?php

class Quotegen_Form_QuoteSetting extends Twitter_Bootstrap_Form_Horizontal
{
    protected $_showSystemDefaults;

    /**
     * Constructor for QuoteSetting form
     *
     * @param boolean $showDefaults
     *            When set to true, defaults will be appended to each of the form elements so that users know what the
     *            default is.
     * @param unknown_type $options            
     */
    public function __construct ($showDefaults = false, $options = null)
    {
        $this->_showSystemDefaults = $showDefaults;
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
        
        $pageCoverageMonochrome = $this->createElement('text', 'pageCoverageMonochrome', array (
                'label' => 'Page Coverage Mono:', 
                'required' => true, 
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
                                        'max' => 100, 
                                        'inclusive' => false 
                                ) 
                        ), 
                        'Float' 
                ) 
        ));
        $this->addElement($pageCoverageMonochrome);
        
        $pageCoverageColor = $this->createElement('text', 'pageCoverageColor', array (
                'label' => 'Page Coverage Color:', 
                'required' => true, 
                
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
                                        'max' => 100, 
                                        'inclusive' => false 
                                ) 
                        ), 
                        'Float' 
                ) 
        ));
        $this->addElement($pageCoverageColor);
                
        $deviceMargin = $this->createElement('text', 'deviceMargin', array (
                'label' => 'Device Margin:', 
                'required' => true, 
                'class' => 'input-mini', 
                'filters' => array (
                        'StringTrim', 
                        'StripTags' 
                ), 
                'validators' => array (
                        array (
                                'validator' => 'Between', 
                                'options' => array (
                                        'min' => - 100, 
                                        'max' => 100 
                                ) 
                        ), 
                        'Float' 
                ) 
        ));
        $this->addElement($deviceMargin);
        
        $pageMargin = $this->createElement('text', 'pageMargin', array (
                'label' => 'Page Margin:', 
                'required' => true, 
                'class' => 'input-mini', 
                'filters' => array (
                        'StringTrim', 
                        'StripTags' 
                ), 
                'validators' => array (
                        array (
                                'validator' => 'Between', 
                                'options' => array (
                                        'min' => - 100, 
                                        'max' => 100 
                                ) 
                        ), 
                        'Float' 
                ) 
        ));
        $this->addElement($pageMargin);
        
        $adminCostPerPage = $this->createElement('text', 'adminCostPerPage', array (
                'label' => 'Admin Cost Per Page:', 
                
                'required' => true, 
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
                                        'inclusive' => true 
                                ) 
                        ), 
                        'Float' 
                ) 
        ));
        $this->addElement($adminCostPerPage);
        
        $serviceCostPerPage = $this->createElement('text', 'serviceCostPerPage', array (
                'label' => 'Service Cost Per Page:', 
                
                'required' => true, 
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
                                        'inclusive' => true 
                                ) 
                        ), 
                        'Float' 
                ) 
        ));
        $this->addElement($serviceCostPerPage);
        
        $pricingConfigDropdown = $this->createElement('select', 'pricingConfigId', array (
                'label' => 'Toner Preference:' 
        ));
        
        /* @var $princingConfig Proposalgen_Model_PricingConfig */
        foreach ( Proposalgen_Model_Mapper_PricingConfig::getInstance()->fetchAll() as $pricingConfig )
        {
            if ($pricingConfig->getPricingConfigId() === Proposalgen_Model_PricingConfig::NONE && ! $this->_showSystemDefaults)
            {
                continue;
            }
            $pricingConfigDropdown->addMultiOption($pricingConfig->getPricingConfigId(), $pricingConfig->getConfigName());
        }
        $this->addElement($pricingConfigDropdown);
        
        /*
         * Set the defaults if the flag is enabled
         */
        if ($this->_showSystemDefaults)
        {
            $systemDefaultQuoteSetting = Quotegen_Model_Mapper_QuoteSetting::getInstance()->find(Quotegen_Model_QuoteSetting::SYSTEM_ROW_ID);
            $pageCoverageMonochrome->setAttrib('append', sprintf("System Default: %s%%", number_format($systemDefaultQuoteSetting->getPageCoverageMonochrome(), 2)));
            $pageCoverageMonochrome->setRequired(false);
            $pageCoverageColor->setAttrib('append', sprintf("System Default: %s%%", number_format($systemDefaultQuoteSetting->getPageCoverageColor(), 2)));
            $pageCoverageColor->setRequired(false);
            $adminCostPerPage->setAttrib('append', sprintf("System Default: %s", $this->getView()
                ->currency((float)$systemDefaultQuoteSetting->getAdminCostPerPage())));
            $adminCostPerPage->setRequired(false);
            $serviceCostPerPage->setAttrib('append', sprintf("System Default: %s", $this->getView()
                ->currency((float)$systemDefaultQuoteSetting->getServiceCostPerPage())));
            $serviceCostPerPage->setRequired(false);
            
            $deviceMargin->setAttrib('append', sprintf("System Default: %s%%", number_format($systemDefaultQuoteSetting->getDeviceMargin(), 2)));
            $deviceMargin->setRequired(false);
            $pageMargin->setAttrib('append', sprintf("System Default: %s%%", number_format($systemDefaultQuoteSetting->getPageMargin(), 2)));
            $pageMargin->setRequired(false);
            $pricingConfigDropdown->setAttrib('append', sprintf("System Default: %s", $systemDefaultQuoteSetting->getPricingConfig()
                ->configName));
        }
    }
}
