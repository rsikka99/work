<?php

class Quotegen_Form_Quote_Page extends Twitter_Bootstrap_Form_Horizontal
{
    /**
     * This represent the current quote being in use
     *
     * @var Quotegen_Model_Quote
     */
    private $_quote;

    public function __construct ($quote = null, $options = null)
    {
        $this->_quote = $quote;
        parent::__construct($options);
        Quotegen_Form_Quote_Navigation::addFormActionsToForm(Quotegen_Form_Quote_Navigation::BUTTONS_ALL, $this);
    }

    public function init ()
    {
        $inlineDecorators = array (
                'FieldSize', 
                'ViewHelper', 
                'Addon', 
                'PopoverElementErrors', 
                'Wrapper' 
        );
        
        // Set the method for the display form to POST
        $this->setMethod('POST');
        $this->_addClassNames('form-center-actions');
        
        // Validation variables 
        $minQuantityPages = 0;
        $maxQuantityPages = 100000;
        
        /* @var $quoteDeviceGroup Quotegen_Model_QuoteDeviceGroup */
        foreach ( $this->_quote->getQuoteDeviceGroups() as $quoteDeviceGroup )
        {
            
            /* @var $quoteDeviceGroupDevice Quotegen_Model_QuoteDeviceGroupDevice */
            foreach ( $quoteDeviceGroup->getQuoteDeviceGroupDevices() as $quoteDeviceGroupDevice )
            {
                // quantity_monochrome_<quoteDeviceGroupId>_<quoteDeviceId> : Quotegen_Model_QuoteDeviceGroupDevice->monochromePagesQuantity
                // quantity_monochrome_<quoteDeviceGroupId>_<quoteDeviceId> is used to store the amount of pages allocated per device
                $this->addElement('text', "quantity_monochrome_{$quoteDeviceGroupDevice->getQuoteDeviceGroupId()}_{$quoteDeviceGroupDevice->getQuoteDeviceId()}", array (
                        'label' => 'Quantity', 
                        'required' => true, 
                        'class' => 'span1', 
                        'value' => $quoteDeviceGroupDevice->getMonochromePagesQuantity(), 
                        'decorators' => $inlineDecorators, 
                        'validators' => array (
                                'Int', 
                                array (
                                        'validator' => 'Between', 
                                        'options' => array (
                                                'min' => $minQuantityPages, 
                                                'max' => $maxQuantityPages 
                                        ) 
                                ) 
                        ) 
                ));
                
                if ($quoteDeviceGroupDevice->getQuoteDevice()->isColorCapable())
                {
                    // quantity_color_<quoteDeviceGroupId>_<quoteDeviceId> : Quotegen_Model_QuoteDeviceGroupDevice->colorPagesQuantity
                    // quantity_color_<quoteDeviceGroupId>_<quoteDeviceId> is used to store the amount of pages allocated per device
                    $this->addElement('text', "quantity_color_{$quoteDeviceGroupDevice->getQuoteDeviceGroupId()}_{$quoteDeviceGroupDevice->getQuoteDeviceId()}", array (
                            'label' => 'Quantity', 
                            'required' => true, 
                            'class' => 'span1', 
                            'decorators' => $inlineDecorators, 
                            'value' => $quoteDeviceGroupDevice->getColorPagesQuantity(), 
                            'validators' => array (
                                    'Int', 
                                    array (
                                            'validator' => 'Between', 
                                            'options' => array (
                                                    'min' => $minQuantityPages, 
                                                    'max' => $maxQuantityPages 
                                            ) 
                                    ) 
                            ) 
                    ));
                }
            }
        }
        
        // monochromePageMargin : Quotegen_Model_Quote->monochromePageMargin
        // monochromePageMargin is used to determine margin on pages for the entire quote
        $this->addElement('text', 'monochromePageMargin', array (
                'value' => $this->_quote->monochromePageMargin,
                'required' => true, 
                'decorators' => $inlineDecorators, 
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
                ) 
        ));
        
        // colorPageMargin : Quotegen_Model_Quote->colorPageMargin
        // colorPageMargin is used to set a page margin for all of the color pages on the quote
        $this->addElement('text', 'colorPageMargin', array (
                'value' => $this->_quote->colorPageMargin,
                'required' => true, 
                'decorators' => $inlineDecorators, 
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
                ) 
        ));
        
        // monochromeOverageMargin : Quotegen_Model_Quote->monochromeOverageMargin
        // monochromeOverageMargin is used for the calcuation of overage rate per page for pages.
        $this->addElement('text', 'monochromeOverageMargin', array (
                'value' => $this->_quote->monochromeOverageMargin,
                'required' => true, 
                'class' => 'input-mini', 
                'decorators' => $inlineDecorators, 
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
        
        // colorOverageMargin : Quotegen_Model_Quote->colorOverageMargin
        // colorOverageMargin is used for the calcuation of overage rate per page for pages.
        $this->addElement('text', 'colorOverageMargin', array (
                'value' => $this->_quote->colorOverageMargin,
                'required' => true, 
                'class' => 'input-mini', 
                'decorators' => $inlineDecorators, 
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
        
        // pageCoverageColor : Quotegen_Model_Quote->pageCoverageColor
        // pageCoverageColor is used to set the page coverage amount in the quote
        $pageCoverageColor = $this->createElement('text', 'pageCoverageColor', array (
                'label' => 'Page Coverage Color:', 
                'class' => 'input-mini', 
                'required' => true, 
                'value' => $this->_quote->pageCoverageColor,
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
        
        // pageCoverageMonochrome : Quotegen_Model_Quote->pageCoverageMonochrome
        // pageCoverageMonochrome is used to set the page coverage amount in the quote
        $pageCoverageMonochrome = $this->createElement('text', 'pageCoverageMonochrome', array (
                'label' => 'Page Coverage Monochrome:', 
                'class' => 'input-mini', 
                'required' => true, 
                'value' => $this->_quote->pageCoverageMonochrome,
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
        
        // adminCostPerPage : Quotegen_Model_Quote->adminCostPerPage
        // adminCostPerPage is a flat CPP that is used to add an additional charge per page to recoop admin relate fees
        $adminCostPerPage = $this->createElement('text', 'adminCostPerPage', array (
                'label' => 'Admin Cost Per Page:', 
                'value' => $this->_quote->adminCostPerPage,
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
                                        'max' => 5 
                                ) 
                        ), 
                        'Float' 
                ) 
        ));
        $this->addElement($adminCostPerPage);
        
        // serviceCostPerPage : Quotegen_Model_Quote->serviceCostPerPage
        // serviceCostPerPage is a flat CPP that is used to add an additional charge per page to recoop service related fees
        $serviceCostPerPage = $this->createElement('text', 'serviceCostPerPage', array (
                'label' => 'Service Cost Per Page:', 
                'value' => $this->_quote->serviceCostPerPage,
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
                                        'max' => 5 
                                ) 
                        ), 
                        'Float' 
                ) 
        ));
        $this->addElement($serviceCostPerPage);
        
        // pricingConfigId : Quotegen_Model_Quote->pricingConfigId
        // pricingConfigId is used to determine the users prefernce for toners when it comes to calculating a devices CPP
        $pricingConfigDropdown = $this->createElement('select', 'pricingConfigId', array (
                'label' => 'Toner Preference:', 
                'value' => $this->_quote->pricingConfigId,
                'required' => true 
        ));
        
        /* @var $pricingConfig Proposalgen_Model_PricingConfig */
        foreach ( Proposalgen_Model_Mapper_PricingConfig::getInstance()->fetchAll() as $pricingConfig )
        {
            $pricingConfigDropdown->addMultiOption($pricingConfig->pricingConfigId, $pricingConfig->configName);
        }
        $this->addElement($pricingConfigDropdown);
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators(array (
                array (
                        'ViewScript', 
                        array (
                                'viewScript' => 'quote/pages/form/pages.phtml' 
                        ) 
                ) 
        ));
    }

    /**
     *
     * @return the $_quote
     */
    public function getQuote ()
    {
        return $this->_quote;
    }
}