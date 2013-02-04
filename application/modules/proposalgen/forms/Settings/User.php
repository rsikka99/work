<?php

/**
 * User Form: Used for managing user settings
 *
 * @version v1.0
 */
class Proposalgen_Form_Settings_User extends Zend_Form
{

    /**
     * Constructor builds the form
     *
     * @param $options -
     *                 not used (required)
     *
     * @return \Proposalgen_Form_Settings_User markup for the from is automatically returned by zend_form
     */
    public function __construct ($options = null)
    {
        //call parent contsructor
        parent::__construct($options);

        $elements       = array();
        $elementCounter = 0;
        $formElements   = new Zend_Form();
        $this->setName('settings_form');

        $config         = Zend_Registry::get('config');
        $MPSProgramName = $config->app->MPSProgramName;

        $this->setAction("");
        $this->setMethod("POST");

        $currencyRegex     = '/^\d+(?:\.\d{0,2})?$/';
        $currencyValidator = new Zend_Validate_Regex($currencyRegex);
        $currencyValidator->setMessage("Please enter a valid dollar amount.");
        $greaterThanZeroValidator = new Zend_Validate_GreaterThan(0);

        //*****************************************************************
        //SETTINGS FIELDS
        //*****************************************************************


        //page coverage bw
        $page_coverage = new Zend_Form_Element_Text('pageCoverageMono');
        $page_coverage->setLabel('Page Coverage Monochrome:')
            ->addValidator(new Zend_Validate_Float())
            ->addValidator(new Zend_Validate_Between(0, 100), false)
            ->addValidator('greaterThan', true, array(
                                                     'min' => 0
                                                ))
            ->setAttrib('class', 'span1')
            ->setAttrib('maxlength', 10)
            ->setAttrib('style', 'text-align: right')
            ->setOrder($elementCounter)
            ->setAttrib('id', 'page_coverage')
            ->setDescription('%')
            ->setDecorators(array(
                                 'ViewHelper',
                                 array(
                                     'Description',
                                     array(
                                         'escape' => false,
                                         'tag'    => false
                                     )
                                 ),
                                 'Errors',
                                 array(
                                     'HtmlTag',
                                     array(
                                         'tag' => 'dd',
                                         'id'  => 'page_coverage-element'
                                     )
                                 ),
                                 array(
                                     'Label',
                                     array(
                                         'tag'   => 'dt',
                                         'class' => 'forms_label'
                                     )
                                 )
                            ));

        array_push($elements, $page_coverage);
        $elementCounter++;
        $formElements->addElement($page_coverage);

        //page coverage color
        $page_coverageColor = new Zend_Form_Element_Text('pageCoverageColor');
        $page_coverageColor->setLabel('Page Coverage Color:')
            ->addValidator(new Zend_Validate_Float())
            ->addValidator(new Zend_Validate_Between(0, 100), false)
            ->addValidator('greaterThan', true, array(
                                                     'min' => 0
                                                ))
            ->setAttrib('class', 'span1')
            ->setAttrib('maxlength', 10)
            ->setAttrib('style', 'text-align: right')
            ->setOrder($elementCounter)
            ->setAttrib('id', 'page_coverage')
            ->setDescription('%')
            ->setDecorators(array(
                                 'ViewHelper',
                                 array(
                                     'Description',
                                     array(
                                         'escape' => false,
                                         'tag'    => false
                                     )
                                 ),
                                 'Errors',
                                 array(
                                     'HtmlTag',
                                     array(
                                         'tag' => 'dd',
                                         'id'  => 'page_coverage-element'
                                     )
                                 ),
                                 array(
                                     'Label',
                                     array(
                                         'tag'   => 'dt',
                                         'class' => 'forms_label'
                                     )
                                 )
                            ));

        $page_coverageColor->getValidator('Float')->setMessage('Please enter a number.');
        $page_coverageColor->getValidator('greaterThan')->setMessage('Please enter a number, cannot be zero.');
        array_push($elements, $page_coverageColor);
        $elementCounter++;
        $formElements->addElement($page_coverageColor);

        //actual page coverage black & white
        $actual_page_coverage = new Zend_Form_Element_Text('actualPageCoverageMono');
        $actual_page_coverage->setLabel('Page Coverage Monochrome:')
            ->addValidator(new Zend_Validate_Float())
            ->addValidator(new Zend_Validate_Between(0, 100), false)
            ->addValidator('greaterThan', true, array(
                                                     'min' => 0
                                                ))
            ->setAttrib('class', 'span1')
            ->setAttrib('maxlength', 10)
            ->setAttrib('style', 'text-align: right')
            ->setOrder($elementCounter)
            ->setAttrib('id', 'actual_page_coverage')
            ->setDescription('%')
            ->setDecorators(array(
                                 'ViewHelper',
                                 array(
                                     'Description',
                                     array(
                                         'escape' => false,
                                         'tag'    => false
                                     )
                                 ),
                                 'Errors',
                                 array(
                                     'HtmlTag',
                                     array(
                                         'tag' => 'dd',
                                         'id'  => 'page_coverage-element'
                                     )
                                 ),
                                 array(
                                     'Label',
                                     array(
                                         'tag'   => 'dt',
                                         'class' => 'forms_label'
                                     )
                                 )
                            ));

        array_push($elements, $actual_page_coverage);
        $elementCounter++;
        $formElements->addElement($actual_page_coverage);

        //actual page coverage color
        $actual_page_coverage_color = new Zend_Form_Element_Text('actualPageCoverageColor');
        $actual_page_coverage_color->setLabel('Page Coverage Color:')
            ->addValidator(new Zend_Validate_Float())
            ->addValidator(new Zend_Validate_Between(0, 100), false)
            ->addValidator('greaterThan', true, array(
                                                     'min' => 0
                                                ))
            ->setAttrib('class', 'span1')
            ->setAttrib('maxlength', 10)
            ->setAttrib('style', 'text-align: right')
            ->setOrder($elementCounter)
            ->setAttrib('id', 'actual_page_coverage_color')
            ->setDescription('%')
            ->setDecorators(array(
                                 'ViewHelper',
                                 array(
                                     'Description',
                                     array(
                                         'escape' => false,
                                         'tag'    => false
                                     )
                                 ),
                                 'Errors',
                                 array(
                                     'HtmlTag',
                                     array(
                                         'tag' => 'dd',
                                         'id'  => 'page_coverage-element'
                                     )
                                 ),
                                 array(
                                     'Label',
                                     array(
                                         'tag'   => 'dt',
                                         'class' => 'forms_label'
                                     )
                                 )
                            ));

        array_push($elements, $actual_page_coverage_color);
        $elementCounter++;
        $formElements->addElement($actual_page_coverage_color);

        //page pricing margin
        $pricing_margin = new Zend_Form_Element_Text('assessmentReportMargin');
        $pricing_margin->setLabel('Pricing Margin:')
            ->addValidator(new Zend_Validate_Float())
            ->addValidator(new Zend_Validate_Between(array(
                                                          'min' => 0,
                                                          'max' => 99
                                                     )))
            ->setAttrib('class', 'span1')
            ->setAttrib('maxlength', 10)
            ->setAttrib('style', 'text-align: right')
            ->setOrder($elementCounter)
            ->setAttrib('id', 'pricing_margin')
            ->setDescription('%')
            ->setValue('20')
            ->setDecorators(array(
                                 'ViewHelper',
                                 array(
                                     'Description',
                                     array(
                                         'escape' => false,
                                         'tag'    => false
                                     )
                                 ),
                                 'Errors',
                                 array(
                                     'HtmlTag',
                                     array(
                                         'tag' => 'dd',
                                         'id'  => 'pricing_margin-element'
                                     )
                                 ),
                                 array(
                                     'Label',
                                     array(
                                         'tag'   => 'dt',
                                         'class' => 'forms_label'
                                     )
                                 )
                            ));
        $pricing_margin->getValidator('Float')->setMessage('Please enter a number.');
        $pricing_margin->getValidator('Between')->setMessage('Must be greater than 0 and less than 100.');
        array_push($elements, $pricing_margin);
        $elementCounter++;
        $formElements->addElement($pricing_margin);

        //page service cost
        $service_cost = new Zend_Form_Element_Text('serviceCostPerPage');
        $service_cost->setLabel('Service Cost:')
            ->addValidator(new Zend_Validate_Float())
            ->setAttrib('class', 'span2')
            ->setAttrib('service', 'service')
            ->setAttrib('maxlength', 10)
            ->setAttrib('style', 'text-align: right')
            ->setOrder($elementCounter)
            ->setAttrib('id', 'service_cost')
            ->setDescription('$')
            ->addValidator('greaterThan', true, array(
                                                     'min' => 0
                                                ))
            ->setDecorators(array(
                                 array(
                                     'Description',
                                     array(
                                         'escape' => false,
                                         'tag'    => false
                                     )
                                 ),
                                 'ViewHelper',
                                 'Errors',
                                 array(
                                     'HtmlTag',
                                     array(
                                         'tag' => 'dd',
                                         'id'  => 'service_cost-element'
                                     )
                                 ),
                                 array(
                                     'Label',
                                     array(
                                         'tag'   => 'dt',
                                         'class' => 'forms_label'
                                     )
                                 )
                            ));

        $service_cost->getValidator('Float')->setMessage('Please enter a number.');
        array_push($elements, $service_cost);
        $elementCounter++;
        $formElements->addElement($service_cost);

        //page admin charge
        $admin_charge = new Zend_Form_Element_Text('adminCostPerPage');
        $admin_charge->setLabel('Admin Charge:')
            ->addValidator(new Zend_Validate_Float())
            ->setAttrib('class', 'span2')
            ->setAttrib('page', 'page')
            ->setAttrib('maxlength', 10)
            ->setAttrib('style', 'text-align: right')
            ->setOrder($elementCounter)
            ->setAttrib('id', 'admin_charge')
            ->setDescription('$')
            ->addValidator('greaterThan', true, array(
                                                     'min' => 0
                                                ))
            ->setDecorators(array(
                                 array(
                                     'Description',
                                     array(
                                         'escape' => false,
                                         'tag'    => false
                                     )
                                 ),
                                 'ViewHelper',
                                 'Errors',
                                 array(
                                     'HtmlTag',
                                     array(
                                         'tag' => 'dd',
                                         'id'  => 'admin_charge-element'
                                     )
                                 ),
                                 array(
                                     'Label',
                                     array(
                                         'tag'   => 'dt',
                                         'class' => 'forms_label'
                                     )
                                 )
                            ));

        $admin_charge->getValidator('Float')->setMessage('Please enter a number.');
        array_push($elements, $admin_charge);
        $elementCounter++;
        $formElements->addElement($admin_charge);

        //monthly lease payment
        $element = new Zend_Form_Element_Text('monthlyLeasePayment');
        $element->setLabel('Average Monthly Lease Payment:')
            ->addValidator(new Zend_Validate_Float())
            ->setAttrib('class', 'span2')
            ->setAttrib('maxlength', 10)
            ->setAttrib('style', 'text-align: right')
            ->setOrder($elementCounter)
            ->setAttrib('id', 'monthly_lease_payment')
            ->setDescription('$')
            ->addValidator('greaterThan', true, array(
                                                     'min' => 0
                                                ))
            ->setDecorators(array(
                                 array(
                                     'Description',
                                     array(
                                         'escape' => false,
                                         'tag'    => false
                                     )
                                 ),
                                 'ViewHelper',
                                 'Errors',
                                 array(
                                     'HtmlTag',
                                     array(
                                         'tag' => 'dd',
                                         'id'  => 'monthly_lease_payment-element'
                                     )
                                 ),
                                 array(
                                     'Label',
                                     array(
                                         'tag'   => 'dt',
                                         'class' => 'forms_label'
                                     )
                                 )
                            ));

        $element->getValidator('Float')->setMessage('Please enter a number.');
        array_push($elements, $element);
        $elementCounter++;
        $formElements->addElement($element);

        //average non-leased printer cost
        $element = new Zend_Form_Element_Text('defaultPrinterCost');
        $element->setLabel('Default Printer Cost:');

        $element->addValidator(new Zend_Validate_Float())
            ->setAttrib('class', 'span2')
            ->setAttrib('maxlength', 10)
            ->setAttrib('style', 'text-align: right')
            ->setOrder($elementCounter)
            ->setAttrib('id', 'average_nonlease_printer_cost')
            ->setDescription('$')
            ->addValidator('greaterThan', true, array(
                                                     'min' => 0
                                                ))
            ->setDecorators(array(
                                 array(
                                     'Description',
                                     array(
                                         'escape' => false,
                                         'tag'    => false
                                     )
                                 ),
                                 'ViewHelper',
                                 'Errors',
                                 array(
                                     'HtmlTag',
                                     array(
                                         'tag' => 'dd',
                                         'id'  => 'average_nonlease_printer_cost-element'
                                     )
                                 ),
                                 array(
                                     'Label',
                                     array(
                                         'tag'   => 'dt',
                                         'class' => 'forms_label'
                                     )
                                 )
                            ));

        $element->getValidator('Float')->setMessage('Please enter a number.');
        array_push($elements, $element);
        $elementCounter++;
        $formElements->addElement($element);

        //leased bw per page
        $element = new Zend_Form_Element_Text('leasedBwCostPerPage');
        $element->setLabel('Leased Monochrome Cost:')
            ->addValidator(new Zend_Validate_Float())
            ->setAttrib('class', 'span2')
            ->setAttrib('maxlength', 10)
            ->setAttrib('page', 'page')
            ->setAttrib('style', 'text-align: right')
            ->setOrder($elementCounter)
            ->setAttrib('id', 'leased_bw_per_page')
            ->setDescription('$')
            ->addValidator('greaterThan', true, array(
                                                     'min' => 0
                                                ))
            ->setDecorators(array(
                                 array(
                                     'Description',
                                     array(
                                         'escape' => false,
                                         'tag'    => false
                                     )
                                 ),
                                 'ViewHelper',
                                 'Errors',
                                 array(
                                     'HtmlTag',
                                     array(
                                         'tag' => 'dd',
                                         'id'  => 'leased_bw_per_page-element'
                                     )
                                 ),
                                 array(
                                     'Label',
                                     array(
                                         'tag'   => 'dt',
                                         'class' => 'forms_label'
                                     )
                                 )
                            ));

        $element->getValidator('Float')->setMessage('Please enter a number.');
        array_push($elements, $element);
        $elementCounter++;
        $formElements->addElement($element);

        //leased color per page
        $element = new Zend_Form_Element_Text('leasedColorCostPerPage');
        $element->setLabel('Leased Color Cost:')
            ->addValidator(new Zend_Validate_Float())
            ->setAttrib('class', 'span2')
            ->setAttrib('maxlength', 10)
            ->setAttrib('page', 'page')
            ->setAttrib('style', 'text-align: right')
            ->setOrder($elementCounter)
            ->setAttrib('id', 'leased_color_per_page')
            ->setDescription('$')
            ->addValidator('greaterThan', true, array(
                                                     'min' => 0
                                                ))
            ->setDecorators(array(
                                 array(
                                     'Description',
                                     array(
                                         'escape' => false,
                                         'tag'    => false
                                     )
                                 ),
                                 'ViewHelper',
                                 'Errors',
                                 array(
                                     'HtmlTag',
                                     array(
                                         'tag' => 'dd',
                                         'id'  => 'leased_color_per_page-element'
                                     )
                                 ),
                                 array(
                                     'Label',
                                     array(
                                         'tag'   => 'dt',
                                         'class' => 'forms_label'
                                     )
                                 )
                            ));

        $element->getValidator('Float')->setMessage('Please enter a number.');
        array_push($elements, $element);
        $elementCounter++;
        $formElements->addElement($element);

        //kilowatts per hour
        $element = new Zend_Form_Element_Text('kilowattsPerHour');
        $element->setLabel('Energy Cost:')
            ->addValidator(new Zend_Validate_Float())
            ->setAttrib('class', 'span2')
            ->setAttrib('KW', 'KW')
            ->setAttrib('maxlength', 10)
            ->setAttrib('style', 'text-align: right')
            ->setOrder($elementCounter)
            ->setAttrib('id', 'kilowatts_per_hour')
            ->setDescription('$')
            ->addValidator('greaterThan', true, array(
                                                     'min' => 0
                                                ))
            ->setDecorators(array(
                                 array(
                                     'Description',
                                     array(
                                         'escape' => false,
                                         'tag'    => false
                                     )
                                 ),
                                 'ViewHelper',
                                 'Errors',
                                 array(
                                     'HtmlTag',
                                     array(
                                         'tag' => 'dd',
                                         'id'  => 'kilowatts_per_hour-element'
                                     )
                                 ),
                                 array(
                                     'Label',
                                     array(
                                         'tag'   => 'dt',
                                         'class' => 'forms_label'
                                     )
                                 )
                            ));

        $element->getValidator('Float')->setMessage('Please enter a number.');
        array_push($elements, $element);
        $elementCounter++;
        $formElements->addElement($element);

        //BW cost per page
        $element = new Zend_Form_Element_Text('mpsBwCostPerPage');
        $element->setLabel($MPSProgramName . ' Monochrome Cost:')
            ->addValidator(new Zend_Validate_Float())
            ->setAttrib('class', 'span2')
            ->setAttrib('page', 'page')
            ->setAttrib('maxlength', 10)
            ->setAttrib('style', 'text-align: right')
            ->setOrder($elementCounter)
            ->setAttrib('id', 'mps_bw_per_page')
            ->setDescription('$')
            ->addValidator('greaterThan', true, array(
                                                     'min' => 0
                                                ))
            ->setDecorators(array(
                                 array(
                                     'Description',
                                     array(
                                         'escape' => false,
                                         'tag'    => false
                                     )
                                 ),
                                 'ViewHelper',
                                 'Errors',
                                 array(
                                     'HtmlTag',
                                     array(
                                         'tag' => 'dd',
                                         'id'  => 'mps_bw_per_page'
                                     )
                                 ),
                                 array(
                                     'Label',
                                     array(
                                         'tag'   => 'dt',
                                         'class' => 'forms_label'
                                     )
                                 )
                            ));

        $element->getValidator('Float')->setMessage('Please enter a number.');
        array_push($elements, $element);
        $elementCounter++;
        $formElements->addElement($element);

        //BW cost per page
        $element = new Zend_Form_Element_Text('mpsColorCostPerPage');
        $element->setLabel($MPSProgramName . ' Color Cost:')
            ->addValidator(new Zend_Validate_Float())
            ->setAttrib('class', 'span2')
            ->setAttrib('page', 'page')
            ->setAttrib('maxlength', 10)
            ->setAttrib('style', 'text-align: right')
            ->setOrder($elementCounter)
            ->setAttrib('id', 'mps_color_per_page')
            ->setDescription('$')
            ->addValidator('greaterThan', true, array(
                                                     'min' => 0
                                                ))
            ->setDecorators(array(
                                 array(
                                     'Description',
                                     array(
                                         'escape' => false,
                                         'tag'    => false
                                     )
                                 ),
                                 'ViewHelper',
                                 'Errors',
                                 array(
                                     'HtmlTag',
                                     array(
                                         'tag' => 'dd',
                                         'id'  => 'mps_color_per_page'
                                     )
                                 ),
                                 array(
                                     'Label',
                                     array(
                                         'tag'   => 'dt',
                                         'class' => 'forms_label'
                                     )
                                 )
                            ));

        $element->getValidator('Float')->setMessage('Please enter a number.');
        array_push($elements, $element);
        $elementCounter++;
        $formElements->addElement($element);

        //pricing config
        $pricing_config = new Zend_Form_Element_Select('assessmentPricingConfigId');
        $pricing_config->setLabel('Toner Preference:')
            ->setOrder($elementCounter)
            ->setAttrib('id', 'pricing_config_id')
            ->setAttrib('class', 'wide-select')
            ->setAttribs(array(
                              'style' => 'width: 130px;'
                         ))
            ->setDecorators(array(
                                 'ViewHelper',
                                 array(
                                     'Description',
                                     array(
                                         'escape' => false,
                                         'tag'    => false
                                     )
                                 ),
                                 'Errors',
                                 array(
                                     'HtmlTag',
                                     array(
                                         'tag' => 'dd',
                                         'id'  => 'pricing_config_id-element'
                                     )
                                 ),
                                 array(
                                     'Label',
                                     array(
                                         'tag'   => 'dt',
                                         'class' => 'forms_label'
                                     )
                                 )
                            ));
        array_push($elements, $pricing_config);
        $elementCounter++;
        $formElements->addElement($pricing_config);

        $cost_threshold = new Zend_Form_Element_Text('costThreshold');
        $cost_threshold->setLabel('Cost Threshold')
            ->addValidator(new Zend_Validate_Float())
            ->setDecorators(array(
                                 array(
                                     'Description',
                                     array(
                                         'escape' => false,
                                         'tag'    => false
                                     )
                                 ),
                                 'ViewHelper',
                                 'Errors',
                                 array(
                                     'HtmlTag',
                                     array(
                                         'tag' => 'dd',
                                         'id'  => 'mps_color_per_page'
                                     )
                                 ),
                                 array(
                                     'Label',
                                     array(
                                         'tag'   => 'dt',
                                         'class' => 'forms_label'
                                     )
                                 )
                            ));
        array_push($elements, $cost_threshold);
        $elementCounter++;
        $formElements->addElement($cost_threshold);

        $target_monochrome = new Zend_Form_Element_Text('targetMonochrome');
        $target_monochrome->setLabel('Cost Threshold')
            ->addValidator(new Zend_Validate_Float())
            ->setDecorators(array(
                                 array(
                                     'Description',
                                     array(
                                         'escape' => false,
                                         'tag'    => false
                                     )
                                 ),
                                 'ViewHelper',
                                 'Errors',
                                 array(
                                     'HtmlTag',
                                     array(
                                         'tag' => 'dd',
                                         'id'  => 'mps_color_per_page'
                                     )
                                 ),
                                 array(
                                     'Label',
                                     array(
                                         'tag'   => 'dt',
                                         'class' => 'forms_label'
                                     )
                                 )
                            ));
        array_push($elements, $target_monochrome);
        $elementCounter++;
        $formElements->addElement($target_monochrome);

        $target_color = new Zend_Form_Element_Text('targetColor');
        $target_color->setLabel('Cost Threshold')
            ->addValidator(new Zend_Validate_Float())
            ->setDecorators(array(
                                 array(
                                     'Description',
                                     array(
                                         'escape' => false,
                                         'tag'    => false
                                     )
                                 ),
                                 'ViewHelper',
                                 'Errors',
                                 array(
                                     'HtmlTag',
                                     array(
                                         'tag' => 'dd',
                                         'id'  => 'mps_color_per_page'
                                     )
                                 ),
                                 array(
                                     'Label',
                                     array(
                                         'tag'   => 'dt',
                                         'class' => 'forms_label'
                                     )
                                 )
                            ));
        array_push($elements, $target_color);
        $elementCounter++;
        $formElements->addElement($target_color)
            ->setAttrib('class', 'span1');
        $target_monochrome->setLabel('Target Monochrome')
            ->setAttrib('class', 'span1');
        $target_color->setLabel('Target Color')
            ->setAttrib('class', 'span1');
        //save button
        $element = new Zend_Form_Element_Submit('save_settings', array(
                                                                      'disableLoadDefaultDecorators' => true
                                                                 ));
        $element->setLabel('Save')
            ->setOrder($elementCounter)
            ->setAttrib('class', 'btn btn-primary')
            ->setDecorators(array(
                                 'ViewHelper',
                                 'Errors'
                            ));
        array_push($elements, $element);
        $elementCounter++;

        //back button
        $element = new Zend_Form_Element_Button('back_button');
        $element->setLabel('Done')
            ->setOrder($elementCounter)
            ->setAttrib('class', 'btn')
            ->setAttrib('onClick', 'javascript: document.location.href="/proposalgen";')
            ->setDecorators(array(
                                 'ViewHelper',
                                 'Errors'
                            ));
        array_push($elements, $element);
        $elementCounter++;

        //add all defined elements to the form
        $this->setDecorators(array(
                                  'FormElements',
                                  array(
                                      'HtmlTag',
                                      array(
                                          'tag'         => 'table',
                                          'cellspacing' => '10'
                                      )
                                  ),
                                  'Form'
                             ));

        $this->addElements($elements);
    }
}
?>