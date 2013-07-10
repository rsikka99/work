<?php
/**
 * Class Healthcheck_Form_Healthcheck_Settings
 */
class Healthcheck_Form_Healthcheck_Settings extends Twitter_Bootstrap_Form_Horizontal
{
    /**
     * The default settings
     *
     * @var Healthcheck_Model_Healthcheck_Setting
     */
    protected $_defaultSettings;

    /**
     * Groups of elements
     *
     * @var array
     */
    protected $_formElementGroups;

    /**
     * @param Healthcheck_Model_Healthcheck_Setting $defaultSettings
     * @param null|array                            $options
     */
    public function __construct (Healthcheck_Model_Healthcheck_Setting $defaultSettings, $options = null)
    {
        $this->_defaultSettings = $defaultSettings;
        parent::__construct($options);
    }

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');
        $this->_addClassNames('form-center-actions');

        $datetimeValidator = new My_Validate_DateTime('/\d{2}\/\d{2}\/\d{4}/');

        $reportGroup                 = new stdClass();
        $reportGroup->elements       = array();
        $reportGroup->title          = "Report Settings";
        $this->_formElementGroups [] = $reportGroup;

        $proposalGroup               = new stdClass();
        $proposalGroup->elements     = array();
        $proposalGroup->title        = "Health Check Settings";
        $this->_formElementGroups [] = $proposalGroup;

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
        $reportGroup->elements [] = $report_date;

        $report_name = new Zend_Form_Element_Text('name');
        $report_name->setLabel('Report Name')
        ->setAttrib('class', 'span2')
        ->setAttrib('data-defaultvalue', "healthCheck" . date('Ymd'));
        $report_name->addFilters(array(
                                      'StringTrim',
                                      'StripTags'
                                 ));
        $this->addElement($report_name);
        $reportGroup->elements [] = $report_name;

        //*****************************************************************
        // PROPOSAL SETTING FIELDS
        //*****************************************************************


        // Page Pricing Margin
        $pricing_margin = new Zend_Form_Element_Text('healthcheckMargin');
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
        ->setAttrib('data-defaultvalue', number_format($this->_defaultSettings->healthcheckMargin, 2))
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
        ->addValidator('greaterThan', true, array(
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
        ->addValidator('greaterThan', true, array(
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
        ->addValidator('greaterThan', true, array(
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
        ->addValidator('greaterThan', true, array(
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
        ->addValidator('greaterThan', true, array(
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
        ->addValidator('greaterThan', true, array(
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
        ->addValidator('greaterThan', true, array(
                                                 'min' => 0
                                            ));
        $element->getValidator('Float')->setMessage('Please enter a number.');

        $this->addElement($element);
        $proposalGroup->elements [] = $element;

        // Page Coverage (Monochrome)
        $actual_page_coverage = new Zend_Form_Element_Text('pageCoverageMonochrome');
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
        ->setAttrib('data-defaultvalue', number_format($this->_defaultSettings->pageCoverageMonochrome, 2))
        ->setAttrib('inputappend', '%');

        $this->addElement($actual_page_coverage);
        $proposalGroup->elements [] = $actual_page_coverage;

        // Actual Page Coverage (Color)
        $actual_page_coverage_color = new Zend_Form_Element_Text('pageCoverageColor');
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
        ->setAttrib('data-defaultvalue', number_format($this->_defaultSettings->pageCoverageColor, 2))
        ->setAttrib('inputappend', '%');

        $this->addElement($actual_page_coverage_color);
        $proposalGroup->elements [] = $actual_page_coverage_color;

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
        $proposalGroup->elements [] = $admin_charge;

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
        $proposalGroup->elements [] = $labor_cost;

        // Parts Cost Per Page
        $parts_cost = new Zend_Form_Element_Text('partsCostPerPage');
        $parts_cost->setLabel('Parts Cost Per Page')
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
        $proposalGroup->elements [] = $parts_cost;

        // Average It Hourly Rate
        $averageItHourlyRate = new Zend_Form_Element_Text('averageItHourlyRate');
        $averageItHourlyRate->setLabel('Estimated Average It Hourly Rate')
        ->addValidator(new Zend_Validate_Float())
        ->setAttrib('class', 'input-mini')
        ->setAttrib('maxlength', 10)
        ->setAttrib('style', 'text-align: right')
        ->setDescription('$ / hour')
        ->setAttrib('data-defaultvalue', number_format($this->_defaultSettings->averageItHourlyRate, 2))
        ->setAttrib('inputprepend', '$')
        ->addValidator('greaterThan', true, array(
                                                 'min' => 0
                                            ));
        $averageItHourlyRate->getValidator('Float')->setMessage('Please enter a number.');

        $this->addElement($averageItHourlyRate);
        $proposalGroup->elements [] = $averageItHourlyRate;

        // Hours Spent On It
        $hoursSpentOnIt = new Zend_Form_Element_Text('hoursSpentOnIt');
        $hoursSpentOnIt->setLabel('Estimated Hours Spent On IT')
        ->addValidator(new Zend_Validate_Float())
        ->setAttrib('class', 'input-mini')
        ->setAttrib('maxlength', 10)
        ->setAttrib('style', 'text-align: right')
        ->setDescription('$')
        ->setAttrib('data-defaultvalue', ($this->_defaultSettings->hoursSpentOnIt > 0 ? $this->_defaultSettings->hoursSpentOnIt . " hours" : "15 minutes per week per printer"))
        ->setAttrib('append', ' hours')
        ->addValidator('greaterThan', true, array(
                                                 'min' => 0
                                            ));
        $hoursSpentOnIt->getValidator('Float')->setMessage('Please enter a number.');

        $this->addElement($hoursSpentOnIt);
        $proposalGroup->elements [] = $hoursSpentOnIt;

        // Cost Of Labor
        $costOfLabor = new Zend_Form_Element_Text('costOfLabor');
        $costOfLabor->setLabel('Estimated Annual Cost Of Labor')
        ->addValidator(new Zend_Validate_Float())
        ->setAttrib('class', 'input-mini')
        ->setAttrib('maxlength', 10)
        ->setAttrib('style', 'text-align: right')
        ->setDescription('$')
        ->setAttrib('data-defaultvalue', ($this->_defaultSettings->costOfLabor > 0 ? $this->_defaultSettings->costOfLabor . " / fleet" : "200 per printer"))
        ->setAttrib('inputprepend', '$')
        ->setAttrib('append', ' / fleet')
        ->addValidator('greaterThan', true, array(
                                                 'min' => 0
                                            ));
        $costOfLabor->getValidator('Float')->setMessage('Please enter a number.');

        $this->addElement($costOfLabor);
        $proposalGroup->elements [] = $costOfLabor;

        // Cost To Execute Supplies Order
        $costToExecuteSuppliesOrder = new Zend_Form_Element_Text('costToExecuteSuppliesOrder');
        $costToExecuteSuppliesOrder->setLabel('Estimated Cost To Execute Supplies Order')
        ->addValidator(new Zend_Validate_Float())
        ->setAttrib('class', 'input-mini')
        ->setAttrib('maxlength', 10)
        ->setAttrib('style', 'text-align: right')
        ->setAttrib('prepend', '$')
        ->setDescription('$')
        ->setAttrib('data-defaultvalue', number_format($this->_defaultSettings->costToExecuteSuppliesOrder, 2))
        ->setAttrib('inputprepend', '$')
        ->setAttrib('inputappend', ' / order')
        ->addValidator('greaterThan', true, array(
                                                 'min' => 0
                                            ));
        $costToExecuteSuppliesOrder->getValidator('Float')->setMessage('Please enter a number.');

        $this->addElement($costToExecuteSuppliesOrder);
        $proposalGroup->elements [] = $costToExecuteSuppliesOrder;

        // Cost To Execute Supplies Order
        $numberOfSupplyOrdersPerMonth = new Zend_Form_Element_Text('numberOfSupplyOrdersPerMonth');
        $numberOfSupplyOrdersPerMonth->setLabel('Estimated Supply Orders Per Month')
        ->addValidator(new Zend_Validate_Float())
        ->setAttrib('class', 'input-mini')
        ->setAttrib('maxlength', 10)
        ->setAttrib('style', 'text-align: right')
        ->setDescription('$')
        ->setAttrib('data-defaultvalue', number_format($this->_defaultSettings->numberOfSupplyOrdersPerMonth, 0))
        ->setAttrib('inputappend', ' / month')
        ->addValidator('greaterThan', true, array(
                                                 'min' => 0
                                            ));
        $numberOfSupplyOrdersPerMonth->getValidator('Float')->setMessage('Please enter a number.');
        $this->addElement($numberOfSupplyOrdersPerMonth);
        $proposalGroup->elements [] = $numberOfSupplyOrdersPerMonth;

        // Toner preference for the healthcheck
        $customer_monochrome_vendor = new Zend_Form_Element_Multiselect('customerMonochromeRankSetArray');
        $customer_monochrome_vendor->setAttrib('class', 'tonerMultiselect')
        ->setMultiOptions(Proposalgen_Model_Mapper_TonerVendorManufacturer::getInstance()->fetchAllForDropdown());
        $this->addElement($customer_monochrome_vendor);
        $proposalGroup->elements [] = $customer_monochrome_vendor;

        $customer_color_vendor = new Zend_Form_Element_Multiselect('customerColorRankSetArray');
        $customer_color_vendor->setAttrib('class', 'tonerMultiselect')
        ->setMultiOptions(Proposalgen_Model_Mapper_TonerVendorManufacturer::getInstance()->fetchAllForDropdown());
        $this->addElement($customer_color_vendor);
        $proposalGroup->elements [] = $customer_color_vendor;

        Healthcheck_Form_Healthcheck_Navigation::addFormActionsToForm(Healthcheck_Form_Healthcheck_Navigation::BUTTONS_ALL, $this);
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