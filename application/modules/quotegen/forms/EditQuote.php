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
        $this->setAttrib('class', 'form-horizontal button-styled');
        $this->addElement('text', 'quoteDate', array (
                	'label' => 'Quote Date'
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
        
        
        $this->addElement('text', 'clientDisplayName', array (
                	'label' => 'Display Name'
                ));
        $this->addElement('text', 'leaseTerm', array (
                	'label' => 'Lease Term'
                ));
        $this->addElement('text', 'leaseRate', array (
                	'label' => 'Lease Rate'
                ));
        $this->addElement('text', 'pageCoverageColor', array (
                	'label' => 'Page Covereage Color'
                ));
        $this->addElement('text', 'pageCoverageMonochrome', array (
                	'label' => 'Page Coverage Monochrome'
                ));
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
}