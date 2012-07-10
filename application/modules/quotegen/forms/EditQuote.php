<?php

class Quotegen_Form_EditQuote extends EasyBib_Form
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
        $this->setAttrib('class', 'form-horizontal form-center-actions');
        
        $this->addElement('text', 'clientDisplayName', array (
                'label' => 'Display Name:', 
                'filters' => array (
                        'StringTrim', 
                        'StripTags' 
                ) 
        ));
        
        $this->addElement('text', 'leaseTerm', array (
                'label' => 'Lease Term:' 
        ));
        
        $this->addElement('text', 'leaseRate', array (
                'label' => 'Lease Rate:' 
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
        
        $this->addElement('text', 'quoteDate', array (
                'label' => 'Quote Date:' 
        ));
        
        $minYear = (int)date('Y') - 2;
        $maxYear = $minYear + 4;
        $quoteDate = new My_Form_Element_DateTimePicker('quoteDate');
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
        
        $this->addElement($quoteDate);
        
        // Add the submit button and cancel button
        $this->addElement('submit', 'submit', array (
                'ignore' => true, 
                'label' => 'Save & Continue' 
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
                                'viewScript' => 'quote/settings/form/quote.phtml' 
                        ) 
                ) 
        ));
    }
}