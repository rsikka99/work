<?php
/**
 * Class Assessment_Form_Assessment_Settings
 */
class Assessment_Form_Assessment_Settings extends Twitter_Bootstrap_Form_Vertical
{
    /**
     * The default settings
     *
     * @var Proposalgen_Model_Assessment_Setting
     */
    protected $_defaultSettings;

    /**
     * @var int
     */
    protected $_reportSettingId;
    /**
     * @var Assessment_Model_Assessment_Setting
     */
    protected $_reportSetting;

    /**
     * Groups of elements
     *
     * @var array
     */
    protected $_formElementGroups;

    /**
     * @param Assessment_Model_Assessment_Setting $defaultSettings
     * @param array|null                          $options
     */
    public function __construct (Assessment_Model_Assessment_Setting $defaultSettings, $options = null)
    {
        $this->_reportSetting   = $defaultSettings->id;
        $this->_defaultSettings = $defaultSettings;
        parent::__construct($options);
    }

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');
        $this->_addClassNames('form-center-actions');

        $this->setMethod("POST");

        // What does this currency regex validate?
        $currencyRegex     = '/^\d+(?:\.\d{0,2})?$/';
        $currencyValidator = new Zend_Validate_Regex($currencyRegex);
        $currencyValidator->setMessage("Please enter a valid dollar amount.");
        $datetimeValidator = new My_Validate_DateTime('/\d{2}\/\d{2}\/\d{4}/');

        $generalGroup               = new stdClass();
        $generalGroup->title        = "Report Settings";
        $generalGroup->elements     = array();
        $this->_formElementGroups[] = $generalGroup;

        $customerGroup               = new stdClass();
        $customerGroup->title        = "Customer Facing Settings";
        $customerGroup->elements     = array();
        $this->_formElementGroups [] = $customerGroup;

        $dealerGroup                 = new stdClass();
        $dealerGroup->title          = "Dealer Settings (Not for customer eyes)";
        $dealerGroup->elements       = array();
        $this->_formElementGroups [] = $dealerGroup;

        //*****************************************************************
        // GENERAL SETTING FIELDS
        //*****************************************************************
        $minYear     = (int)date('Y') - 2;
        $maxYear     = $minYear + 4;
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
        $report_date->addFilters(array(
                                      'StringTrim',
                                      'StripTags'
                                 ));

        $this->addElement($report_date);

        $generalGroup->elements [] = $report_date;

        $report_name = new Zend_Form_Element_Text('name');
        $report_name->setLabel('Report Name')
        ->setAttrib('data-defaultvalue', "assessment" . date('Ymd'))
        ->setAttrib('class', 'span2')
        ->setAttrib('style', 'text-align: right');
        $report_name->addFilters(array(
                                      'StringTrim',
                                      'StripTags'
                                 ));

        $this->addElement($report_name);
        $generalGroup->elements [] = $report_name;

        //*****************************************************************
        // PROPOSAL SETTING FIELDS
        //*****************************************************************
        // Page Pricing Margin
        $pricing_margin = new Zend_Form_Element_Text('assessmentReportMargin');
        $pricing_margin->setLabel('Pricing Margin')
        ->addValidator(new Zend_Validate_Float())
        ->addValidator(new Zend_Validate_Between(array(
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
        $customerGroup->elements [] = $pricing_margin;

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
        ->addValidator('greaterThan', true, array(
                                                 'min' => 0
                                            ));
        $element->getValidator('Float')->setMessage('Please enter a number.');

        $this->addElement($element);
        $customerGroup->elements [] = $element;

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
        ->addValidator('greaterThan', true, array(
                                                 'min' => 0
                                            ));
        $element->getValidator('Float')->setMessage('Please enter a number.');

        $this->addElement($element);
        $customerGroup->elements [] = $element;

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
        ->addValidator('greaterThan', true, array(
                                                 'min' => 0
                                            ));
        $element->getValidator('Float')->setMessage('Please enter a number.');

        $this->addElement($element);
        $customerGroup->elements [] = $element;

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
        ->addValidator('greaterThan', true, array(
                                                 'min' => 0
                                            ));
        $element->getValidator('Float')->setMessage('Please enter a number.');

        $this->addElement($element);
        $customerGroup->elements [] = $element;

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
        ->addValidator('greaterThan', true, array(
                                                 'min' => 0
                                            ));
        $element->getValidator('Float')->setMessage('Please enter a number.');

        $this->addElement($element);
        $customerGroup->elements [] = $element;

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
        ->addValidator('greaterThan', true, array(
                                                 'min' => 0
                                            ));
        $element->getValidator('Float')->setMessage('Please enter a number.');

        $this->addElement($element);
        $customerGroup->elements [] = $element;

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
        ->addValidator('greaterThan', true, array(
                                                 'min' => 0
                                            ));
        $element->getValidator('Float')->setMessage('Please enter a number.');

        $this->addElement($element);
        $customerGroup->elements [] = $element;

        $customer_monochrome_vendor = new Zend_Form_Element_Multiselect('customerMonochromeRankSetArray');
        $customer_monochrome_vendor->setAttrib('class', 'tonerMultiselect')
        ->setMultiOptions(Proposalgen_Model_Mapper_TonerVendorManufacturer::getInstance()->fetchAllForDropdown());
        $this->addElement($customer_monochrome_vendor);
        $customerGroup->elements [] = $customer_monochrome_vendor;

        $customer_color_vendor = new Zend_Form_Element_Multiselect('customerColorRankSetArray');
        $customer_color_vendor->setAttrib('class', 'tonerMultiselect')
        ->setMultiOptions(Proposalgen_Model_Mapper_TonerVendorManufacturer::getInstance()->fetchAllForDropdown());
        $this->addElement($customer_color_vendor);
        $customerGroup->elements [] = $customer_color_vendor;

        //*****************************************************************
        // GROSS MARGIN SETTING FIELDS
        //*****************************************************************

        // Actual Page Coverage (Monochrome)
        $actual_page_coverage = new Zend_Form_Element_Text('actualPageCoverageMono');
        $actual_page_coverage->setLabel('Page Coverage Monochrome')
        ->addValidator(new Zend_Validate_Float())
        ->addValidator(new Zend_Validate_Between(0, 100), false)
        ->addValidator('greaterThan', true, array(
                                                 'min' => 0
                                            ))
        ->setAttrib('class', 'span1')
        ->setAttrib('maxlength', 10)
        ->setAttrib('style', 'text-align: right')
        ->setDescription('%')
        ->setAttrib('data-defaultvalue', number_format($this->_defaultSettings->actualPageCoverageMono, 2))
        ->setAttrib('inputappend', '%');

        $this->addElement($actual_page_coverage);
        $dealerGroup->elements [] = $actual_page_coverage;

        // Actual Page Coverage (Color)
        $actual_page_coverage_color = new Zend_Form_Element_Text('actualPageCoverageColor');
        $actual_page_coverage_color->setLabel('Page Coverage Color')
        ->addValidator(new Zend_Validate_Float())
        ->addValidator(new Zend_Validate_Between(0, 100), false)
        ->addValidator('greaterThan', true, array(
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
        $dealerGroup->elements [] = $actual_page_coverage_color;

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
        ->addValidator('greaterThan', true, array(
                                                 'min' => 0
                                            ));
        $admin_charge->getValidator('Float')->setMessage('Please enter a number.');

        $this->addElement($admin_charge);
        $dealerGroup->elements [] = $admin_charge;

        // Service Cost Per Page
        $labor_cost = new Zend_Form_Element_Text('laborCostPerPage');
        $labor_cost->setLabel('Labor Cost')
        ->addValidator(new Zend_Validate_Float())
        ->setAttrib('class', 'input-mini')
        ->setAttrib('maxlength', 10)
        ->setAttrib('style', 'text-align: right')
        ->setDescription('$')
        ->setAttrib('data-defaultvalue', number_format($this->_defaultSettings->laborCostPerPage, 4))
        ->setAttrib('inputprepend', '$')
        ->setAttrib('inputappend', ' / page')
        ->addValidator('greaterThan', true, array(
                                                 'min' => 0
                                            ));
        $labor_cost->getValidator('Float')->setMessage('Please enter a number.');

        $this->addElement($labor_cost);
        $dealerGroup->elements [] = $labor_cost;

        // Service Cost Per Page
        $parts_cost = new Zend_Form_Element_Text('partsCostPerPage');
        $parts_cost->setLabel('Parts Cost')
        ->addValidator(new Zend_Validate_Float())
        ->setAttrib('class', 'input-mini')
        ->setAttrib('maxlength', 10)
        ->setAttrib('style', 'text-align: right')
        ->setDescription('$')
        ->setAttrib('data-defaultvalue', number_format($this->_defaultSettings->partsCostPerPage, 4))
        ->setAttrib('inputprepend', '$')
        ->setAttrib('inputappend', ' / page')
        ->addValidator('greaterThan', true, array(
                                                 'min' => 0
                                            ));
        $parts_cost->getValidator('Float')->setMessage('Please enter a number.');

        $this->addElement($parts_cost);
        $dealerGroup->elements [] = $parts_cost;


        $dealer_monochrome_vendor = new Zend_Form_Element_Multiselect('dealerMonochromeRankSetArray');
        $dealer_monochrome_vendor->setAttrib('class', 'tonerMultiselect')
        ->setMultiOptions(Proposalgen_Model_Mapper_TonerVendorManufacturer::getInstance()->fetchAllForDropdown());
        $this->addElement($dealer_monochrome_vendor);
        $dealerGroup->elements [] = $dealer_monochrome_vendor;

        $dealer_color_vendor = new Zend_Form_Element_Multiselect('dealerColorRankSetArray');
        $dealer_color_vendor->setAttrib('class', 'tonerMultiselect')
        ->setMultiOptions(Proposalgen_Model_Mapper_TonerVendorManufacturer::getInstance()->fetchAllForDropdown());
        $this->addElement($dealer_color_vendor);
        $dealerGroup->elements [] = $dealer_color_vendor;

        Assessment_Form_Assessment_Navigation::addFormActionsToForm(Assessment_Form_Assessment_Navigation::BUTTONS_ALL, $this);
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

    /**
     * @return Assessment_Model_Assessment_Setting
     */
    public function getReportSetting ()
    {
        if (!isset($this->_reportSetting))
        {
            $this->_reportSetting = Assessment_Model_Mapper_Assessment_Setting::getInstance()->find($this->_reportSettingId);
        }

        return $this->_reportSetting;
    }
}