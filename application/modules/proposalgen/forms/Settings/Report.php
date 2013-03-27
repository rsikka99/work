<?php

/**
 * User Form: Used to manage Report settings
 *
 * @version v1.0
 */
class Proposalgen_Form_Settings_Report extends Twitter_Bootstrap_Form_Vertical
{
    /**
     * The default settings
     *
     * @var Proposalgen_Model_Report_Setting
     */
    protected $_defaultSettings;
    
    /**
     * Groups of elements
     *
     * @var array
     */
    protected $_formElementGroups;

    public function __construct (Proposalgen_Model_Report_Setting $defaultSettings, $options = null)
    {
        $this->_defaultSettings = $defaultSettings;
        parent::__construct($options);
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
        
        $this->setMethod("POST");
        
        // What does this currency regex validate?
        $currencyRegex = '/^\d+(?:\.\d{0,2})?$/';
        $currencyValidator = new Zend_Validate_Regex($currencyRegex);
        $currencyValidator->setMessage("Please enter a valid dollar amount.");
        $greaterThanZeroValidator = new Zend_Validate_GreaterThan(0);
        $datetimeValidator = new My_Validate_DateTime('/\d{2}\/\d{2}\/\d{4}/');
        
        // Setup some form element groups
        $generalGroup = new stdClass();
        $generalGroup->title = "General";
        $generalGroup->elements = array ();
        $this->_formElementGroups [] = $generalGroup;
        
        $proposalGroup = new stdClass();
        $proposalGroup->title = "Assessment / Solution";
        $proposalGroup->elements = array ();
        $this->_formElementGroups [] = $proposalGroup;
        
        $grossMarginGroup = new stdClass();
        $grossMarginGroup->title = "Gross Margin";
        $grossMarginGroup->elements = array ();
        $this->_formElementGroups [] = $grossMarginGroup;
        
        //*****************************************************************
        // GENERAL SETTING FIELDS
        //*****************************************************************
        

        $minYear = (int)date('Y') - 2;
        $maxYear = $minYear + 4;
        $report_date = new ZendX_JQuery_Form_Element_DatePicker('reportDate');
        //$report_date = new My_Form_Element_DateTimePicker('reportDate');
        $report_date->setLabel('Report Date')
            ->setJQueryParam('dateFormat', 'mm/dd/yy')
            ->setJQueryParam('changeYear', 'true')
            ->setJqueryParam('changeMonth', 'true')
            ->setJqueryParam('yearRange', "{$minYear}:{$maxYear}")
            ->addValidator($datetimeValidator)
            ->setAttrib('placeholder', 'mm/dd/yyyy')
            ->setAttrib('class', 'span2')
            ->setAttrib('style', 'text-align: right')
            ->setRequired(true);
        $report_date->addFilters(array (
                'StringTrim', 
                'StripTags' 
        ));
        
        $this->addElement($report_date);
        
        $generalGroup->elements [] = $report_date;
        
        //*****************************************************************
        // PROPOSAL SETTING FIELDS
        //*****************************************************************


        // Page Pricing Margin
        $pricing_margin = new Zend_Form_Element_Text('assessmentReportMargin');
        $pricing_margin->setLabel('Pricing Margin')
            ->addValidator(new Zend_Validate_Float())
            ->addValidator(new Zend_Validate_Between(array (
                'min' => 0,
                'max' => 99
        )))
            ->setAttrib('class', 'span1')
            ->setAttrib('maxlength', 10)
            ->setAttrib('style', 'text-align: right')
            ->setDescription('%')
            ->setValue('20')
            ->setAttrib('data-defaultvalue', number_format($this->_defaultSettings->assessmentReportMargin, 2))
            ->setAttrib('inputappend', '%');
        $pricing_margin->getValidator('Float')->setMessage('Please enter a number.');
        $pricing_margin->getValidator('Between')->setMessage('Must be greater than 0 and less than 100.');

        $this->addElement($pricing_margin);
        $proposalGroup->elements [] = $pricing_margin;

        // Average Monthly Lease Payment
        $element = new Zend_Form_Element_Text('monthlyLeasePayment');
        $element->setLabel('Average Monthly Lease Payment')
            ->addValidator(new Zend_Validate_Float())
            ->setAttrib('class', 'input-mini')
            ->setAttrib('maxlength', 10)
            ->setAttrib('style', 'text-align: right')
            ->setDescription('$')
            ->setAttrib('data-defaultvalue', number_format($this->_defaultSettings->monthlyLeasePayment, 2))
            ->setAttrib('inputprepend', '$')
            ->setAttrib('inputappend', ' / device')
            ->addValidator('greaterThan', true, array (
                'min' => 0 
        ));
        $element->getValidator('Float')->setMessage('Please enter a number.');
        
        $this->addElement($element);
        $proposalGroup->elements [] = $element;
        
        // Default Printer Cost
        $element = new Zend_Form_Element_Text('defaultPrinterCost');
        $element->setLabel('Default Printer Cost')
            ->addValidator(new Zend_Validate_Float())
            ->setAttrib('class', 'input-mini')
            ->setAttrib('maxlength', 10)
            ->setAttrib('style', 'text-align: right')
            ->setDescription('$')
            ->setAttrib('data-defaultvalue', number_format($this->_defaultSettings->defaultPrinterCost, 2))
            ->setAttrib('inputprepend', '$')
            ->setAttrib('inputappend', ' / device')
            ->addValidator('greaterThan', true, array (
                'min' => 0 
        ));
        $element->getValidator('Float')->setMessage('Please enter a number.');
        
        $this->addElement($element);
        $proposalGroup->elements [] = $element;
        
        // Leased Cost Per Page (Monochrome)
        $element = new Zend_Form_Element_Text('leasedBwCostPerPage');
        $element->setLabel('Leased Monochrome Cost')
            ->addValidator(new Zend_Validate_Float())
            ->setAttrib('class', 'input-mini')
            ->setAttrib('maxlength', 10)
            ->setAttrib('style', 'text-align: right')
            ->setDescription('$')
            ->setAttrib('data-defaultvalue', number_format($this->_defaultSettings->leasedBwCostPerPage, 4))
            ->setAttrib('inputprepend', '$')
            ->setAttrib('inputappend', ' / page')
            ->addValidator('greaterThan', true, array (
                'min' => 0 
        ));
        $element->getValidator('Float')->setMessage('Please enter a number.');
        
        $this->addElement($element);
        $proposalGroup->elements [] = $element;
        
        // Leased Cost Per Page (Color)
        $element = new Zend_Form_Element_Text('leasedColorCostPerPage');
        $element->setLabel('Leased Color Cost')
            ->addValidator(new Zend_Validate_Float())
            ->setAttrib('class', 'input-mini')
            ->setAttrib('maxlength', 10)
            ->setAttrib('style', 'text-align: right')
            ->setDescription('$')
            ->setAttrib('data-defaultvalue', number_format($this->_defaultSettings->leasedColorCostPerPage, 4))
            ->setAttrib('inputprepend', '$')
            ->setAttrib('inputappend', ' / page')
            ->addValidator('greaterThan', true, array (
                'min' => 0 
        ));
        $element->getValidator('Float')->setMessage('Please enter a number.');
        
        $this->addElement($element);
        $proposalGroup->elements [] = $element;
        
        // MPS Cost Per Page (Monochrome)
        $element = new Zend_Form_Element_Text('mpsBwCostPerPage');
        $element->setLabel('MPS Monochrome Cost')
            ->addValidator(new Zend_Validate_Float())
            ->setAttrib('class', 'input-mini')
            ->setAttrib('maxlength', 10)
            ->setAttrib('style', 'text-align: right')
            ->setDescription('$')
            ->setAttrib('data-defaultvalue', number_format($this->_defaultSettings->mpsBwCostPerPage, 4))
            ->setAttrib('inputprepend', '$')
            ->setAttrib('inputappend', ' / page')
            ->addValidator('greaterThan', true, array (
                'min' => 0 
        ));
        $element->getValidator('Float')->setMessage('Please enter a number.');
        
        $this->addElement($element);
        $proposalGroup->elements [] = $element;
        
        // MPS Cost Per Page (Color)
        $element = new Zend_Form_Element_Text('mpsColorCostPerPage');
        $element->setLabel('MPS Color Cost')
            ->addValidator(new Zend_Validate_Float())
            ->setAttrib('class', 'input-mini')
            ->setAttrib('page', 'page')
            ->setAttrib('maxlength', 10)
            ->setAttrib('style', 'text-align: right')
            ->setAttrib('id', 'mps_color_per_page')
            ->setDescription('$')
            ->setAttrib('data-defaultvalue', number_format($this->_defaultSettings->mpsColorCostPerPage, 4))
            ->setAttrib('inputprepend', '$')
            ->setAttrib('inputappend', ' / page')
            ->addValidator('greaterThan', true, array (
                'min' => 0 
        ));
        $element->getValidator('Float')->setMessage('Please enter a number.');
        
        $this->addElement($element);
        $proposalGroup->elements [] = $element;
        
        // Energy Cost ($/KW/H)
        $element = new Zend_Form_Element_Text('kilowattsPerHour');
        $element->setLabel('Energy Cost')
            ->addValidator(new Zend_Validate_Float())
            ->setAttrib('class', 'input-mini')
            ->setAttrib('maxlength', 10)
            ->setAttrib('style', 'text-align: right')
            ->setDescription('$')
            ->setAttrib('data-defaultvalue', number_format($this->_defaultSettings->kilowattsPerHour, 2))
            ->setAttrib('inputprepend', '$')
            ->setAttrib('inputappend', ' / KWh')
            ->addValidator('greaterThan', true, array (
                'min' => 0 
        ));
        $element->getValidator('Float')->setMessage('Please enter a number.');
        
        $this->addElement($element);
        $proposalGroup->elements [] = $element;
        
        // Toner preference for the assessment
        $pricing_config = new Zend_Form_Element_Select('assessmentPricingConfigId');
        $pricing_config->setLabel('Toner Preference')
            ->setAttrib('class', 'span2')
            ->setAttrib('data-defaultvalue', $this->_defaultSettings->getAssessmentPricingConfig()
            ->configName)
            ->setMultiOptions(Proposalgen_Model_PricingConfig::$ConfigNames);
        
        $this->addElement($pricing_config);
        $proposalGroup->elements [] = $pricing_config;
        
        //*****************************************************************
        // GROSS MARGIN SETTING FIELDS
        //*****************************************************************
        
        // Actual Page Coverage (Monochrome)
        $actual_page_coverage = new Zend_Form_Element_Text('actualPageCoverageMono');
        $actual_page_coverage->setLabel('Page Coverage Monochrome')
            ->addValidator(new Zend_Validate_Float())
            ->addValidator(new Zend_Validate_Between(0, 100), false)
            ->addValidator('greaterThan', true, array (
                'min' => 0 
        ))
            ->setAttrib('class', 'span1')
            ->setAttrib('maxlength', 10)
            ->setAttrib('style', 'text-align: right')
            ->setDescription('%')
            ->setAttrib('data-defaultvalue', number_format($this->_defaultSettings->actualPageCoverageMono, 2))
            ->setAttrib('inputappend', '%');
        
        $this->addElement($actual_page_coverage);
        $grossMarginGroup->elements [] = $actual_page_coverage;
        
        // Actual Page Coverage (Color)
        $actual_page_coverage_color = new Zend_Form_Element_Text('actualPageCoverageColor');
        $actual_page_coverage_color->setLabel('Page Coverage Color')
            ->addValidator(new Zend_Validate_Float())
            ->addValidator(new Zend_Validate_Between(0, 100), false)
            ->addValidator('greaterThan', true, array (
                'min' => 0 
        ))
            ->setAttrib('class', 'span1')
            ->setAttrib('maxlength', 10)
            ->setAttrib('style', 'text-align: right')
            ->setAttrib('id', 'actualPageCoverageColor')
            ->setDescription('%')
            ->setAttrib('data-defaultvalue', number_format($this->_defaultSettings->actualPageCoverageColor, 2))
            ->setAttrib('inputappend', '%');
        
        $this->addElement($actual_page_coverage_color);
        $grossMarginGroup->elements [] = $actual_page_coverage_color;
        
        // Admin Cost Per Page
        $admin_charge = new Zend_Form_Element_Text('adminCostPerPage');
        $admin_charge->setLabel('Admin Charge')
            ->addValidator(new Zend_Validate_Float())
            ->setAttrib('class', 'input-mini')
            ->setAttrib('maxlength', 10)
            ->setAttrib('style', 'text-align: right')
            ->setDescription('$')
            ->setAttrib('data-defaultvalue', number_format($this->_defaultSettings->adminCostPerPage, 4))
            ->setAttrib('inputprepend', '$')
            ->setAttrib('inputappend', ' / page')
            ->addValidator('greaterThan', true, array (
                'min' => 0 
        ));
        $admin_charge->getValidator('Float')->setMessage('Please enter a number.');
        
        $this->addElement($admin_charge);
        $grossMarginGroup->elements [] = $admin_charge;
        
        // Service Cost Per Page
        $service_cost = new Zend_Form_Element_Text('serviceCostPerPage');
        $service_cost->setLabel('Service Cost')
            ->addValidator(new Zend_Validate_Float())
            ->setAttrib('class', 'input-mini')
            ->setAttrib('maxlength', 10)
            ->setAttrib('style', 'text-align: right')
            ->setDescription('$')
            ->setAttrib('data-defaultvalue', number_format($this->_defaultSettings->serviceCostPerPage, 4))
            ->setAttrib('inputprepend', '$')
            ->setAttrib('inputappend', ' / page')
            ->addValidator('greaterThan', true, array (
                'min' => 0 
        ));
        $service_cost->getValidator('Float')->setMessage('Please enter a number.');
        
        $this->addElement($service_cost);
        $grossMarginGroup->elements [] = $service_cost;
        
        // Toner preference for the gross margin
        $gross_margin_pricing_config = new Zend_Form_Element_Select('grossMarginPricingConfigId');
        $gross_margin_pricing_config->setLabel('Toner Preference')
            ->setAttrib('class', 'span2')
            ->setAttrib('data-defaultvalue', $this->_defaultSettings->getGrossMarginPricingConfig()
            ->configName)
            ->setMultiOptions(Proposalgen_Model_PricingConfig::$ConfigNames);
        
        $this->addElement($gross_margin_pricing_config);
        $grossMarginGroup->elements [] = $gross_margin_pricing_config;
        
        //*****************************************************************
        // BUTTONS
        //*****************************************************************
        

        // Save button
        $element = new Zend_Form_Element_Submit('save_settings', array (
                'disableLoadDefaultDecorators' => true
        ));
        $element->setLabel('Save and continue')->setAttrib('class', 'btn btn-primary');
        $this->addElement($element);
        
        //back button
        $element = new Zend_Form_Element_Button('back_button');
        $element->setLabel('Back')
            ->setAttrib('class', 'btn')
            ->setAttrib('onClick', 'javascript: document.location.href="../data/deviceleasing";');
        $this->addElement($element);
        
        // Add the submit button
//        $this->addElement('submit', 'submit', array (
//                'ignore' => true,
//                'label' => 'Save & Continue'
//        ));
//
//        // Add the cancel button
//        $this->addElement('submit', 'cancel', array (
//                'ignore' => true,
//                'label' => 'Back'
//        ));
        
        //EasyBib_Form_Decorator::setFormDecorator($this, EasyBib_Form_Decorator::BOOTSTRAP, 'submit', 'cancel');
        Proposalgen_Form_Assessment_Navigation::addFormActionsToForm(Proposalgen_Form_Assessment_Navigation::BUTTONS_ALL, $this);
    }

    /**
     * Gets the forms element groups to assist the view renderer
     *
     * @return array
     */
    public function getFormElementGroups ()
    {
        return $this->_formElementGroups;
    }
}
?>