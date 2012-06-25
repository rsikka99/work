<?php

class Quotegen_Form_QuoteSetting extends EasyBib_Form
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
        parent::__construct();
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
        $this->setAttrib('class', 'form-horizontal');
        
        $pageCoverageMonochrome = $this->createElement('text', 'pageCoverageMonochrome', array (
                'label' => 'Page Coverage Mono:', 
                'required' => true, 
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
                                        'max' => 100 
                                ) 
                        ), 
                        'Int' 
                ) 
        ));
        $this->addElement($pageCoverageMonochrome);
        
        $pageCoverageColor = $this->createElement('text', 'pageCoverageColor', array (
                'label' => 'Page Coverage Color:', 
                'required' => true, 
                
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
                                        'max' => 100 
                                ) 
                        ), 
                        'Int' 
                ) 
        ));
        $this->addElement($pageCoverageColor);
        
        $deviceMargin = $this->createElement('text', 'deviceMargin', array (
                'label' => 'Device Margin:', 
                
                'required' => true, 
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
                                        'max' => 99 
                                ) 
                        ), 
                        'Int' 
                ) 
        ));
        $this->addElement($deviceMargin);
        
        $pageMargin = $this->createElement('text', 'pageMargin', array (
                'label' => 'Page Margin:', 
                
                'required' => true, 
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
                                        'max' => 99 
                                ) 
                        ), 
                        'Int' 
                ) 
        ));
        $this->addElement($pageMargin);
        
        $pricingConfigDropdown = new Zend_Form_Element_Select('pricingConfigId', array (
                'label' => 'Toner Preference:' 
        ));
        
        /* @var $princingConfig Proposalgen_Model_PricingConfig */
        foreach ( Proposalgen_Model_Mapper_PricingConfig::getInstance()->fetchAll() as $pricingConfig )
        {
            $pricingConfigDropdown->addMultiOption($pricingConfig->getPricingConfigId(), $pricingConfig->getConfigName());
        }
        $this->addElement($pricingConfigDropdown);
        
        /*
         * Set the defaults if the flag is enabled
         */
        
        if ($this->_showSystemDefaults)
        {
            $systemDefaultQuoteSetting = Quotegen_Model_Mapper_QuoteSetting::getInstance()->find(Quotegen_Model_QuoteSetting::SYSTEM_ROW_ID);
            $pageCoverageMonochrome->setDescription($systemDefaultQuoteSetting->getPageCoverageMonochrome());
            $pageCoverageColor->setDescription($systemDefaultQuoteSetting->getPageCoverageColor());
            $deviceMargin->setDescription($systemDefaultQuoteSetting->getDeviceMargin());
            $pageMargin->setDescription($systemDefaultQuoteSetting->getPageMargin());
        }
        
        // Add the submit button
        $this->addElement('submit', 'submit', array (
                'ignore' => true, 
                'label' => 'Save' 
        ));
        
        // Add the cancel button
        $this->addElement('submit', 'cancel', array (
                'ignore' => true, 
                'label' => 'Cancel' 
        ));
        
        EasyBib_Form_Decorator::setFormDecorator($this, EasyBib_Form_Decorator::BOOTSTRAP, 'submit', 'cancel');
    }

    public function loadDefaultDecorators ()
    {
        // Only show the custom view script if we are showing defaults
        if ($this->_showSystemDefaults)
        {
            $this->setDecorators(array (
                    array (
                            'ViewScript', 
                            array (
                                    'viewScript' => 'quotesetting/form/quotesetting.phtml' 
                            ) 
                    ) 
            ));
        }
    }
}
