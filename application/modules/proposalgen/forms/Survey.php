<?php

/**
 * Survey form:  Used to ask dealer about current state of thier print fleet
 * The main survey form will contain a collection of subforms.  This allows
 * each subform to be validated seporately.  The main form will not be valid
 * until all of the subforms it contains are valid.
 *
 * @author	Chris Garrah
 * @version v1.0
 */
class Proposalgen_Form_Survey extends Zend_Form
{
    public function init ()
    {
        $session = new Zend_Session_Namespace('report');
        $currencyRegex = '/^\d+(?:\.\d{0,2})?$/';
        $currencyValidator = new Zend_Validate_Regex($currencyRegex);
        
        //*********************************************************************
        // COMPANY FORM
        //*********************************************************************
        $companyInfo = new Zend_Form_SubForm();
        
        $companyInfo->addPrefixPath('Tangent_Form_Element', 'Tangent/Form/Element/', 'element');
        $companyInfo->addElement('RawText', 'OpenDiv', array ( 'value' => '<div id="elements_container">' ));
        
        $company_name = new Zend_Form_Element_Text('company_name');
        $company_name->setAttrib('style', 'width: 245px;')
            ->setAttrib('maxlength', 40)
            ->setRequired(true)
            ->setAutoInsertNotEmptyValidator(true)
            ->setAttrib('tmtw', 'text')
            ->setAttrib('id', '4')
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'id' => 'company_name' ) ), 
                array ( 'Label' ) 
            ));
        $companyQst = "Company name:";
        $company_name->setLabel($companyQst);
        $companyInfo->addElement($company_name);
        
        $company_address = new Zend_Form_Element_Textarea('company_address');
        $company_address->setAttrib('maxlength', 200)
            ->setRequired(true)
            ->setAutoInsertNotEmptyValidator(true)
            ->setAttrib('tmtw', 'text')
            ->setAttrib('id', '30')
            ->setAttrib('cols', '40')
            ->setAttrib('rows', '5')
            ->setAttrib('style', 'resize: none;')
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'id' => 'company_address' ) ), 
                array ( 'Label' ) 
            ));
        $companyAddressQst = "Address:";
        $company_address->setLabel($companyAddressQst);
        $companyInfo->addElement($company_address);
        
        $companyInfo->addElement('RawText', 'CloseDiv', array ( 'value' => '</div>' ));
        
        //*********************************************************************
        // GENERAL FORM
        //*********************************************************************
        $general = new Zend_Form_SubForm();
        
        $general->addPrefixPath('Tangent_Form_Element', 'Tangent/Form/Element/', 'element');
        $general->addElement('RawText', 'OpenDiv', array ( 'value' => '<div id="elements_container">' ));
        
        $numb_employees = new Zend_Form_Element_Text('numb_employees');
        $numb_employees->setAttrib('style', 'width: 35px;')
            ->setAttrib('maxlength', 4)
            ->setRequired(true)
            ->addValidator('digits')
            ->setAutoInsertNotEmptyValidator(true)
            ->setAttrib('tmtw', 'numeric')
            ->setAttrib('id', '5')
            ->addValidator('greaterThan', true, array ( 'min' => 0 ) )
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'id' => 'numb_employees-element' ) ), 
                array ( 'Label' ) 
            ));
        $numb_employees->getValidator('digits')->setMessage('Please enter a number. (With no decimal places)');
        $emplQst = "How many office employees do you have at the site(s) to be covered by managed print services?";
        $numb_employees->setLabel($emplQst);
        $general->addElement($numb_employees);
        
        $general->addElement('hidden', 'textScale', array (
            'description' => 'Rate the following goals for managed print services from highest to lowest, with 1 being the most important goal and 5 being the least important goal.', 
            'ignore' => true, 
            'decorators' => array ( array ( 'Description', array ( 'escape' => false ) ) ) 
        ));
        
        $general->getElement('textScale')
            ->addDecorator('ViewHelper')
            ->addDecorator('Errors')
            ->addDecorator('Description', array ( 'tag' => 'p', 'class' => 'description' ))
            ->addDecorator('HtmlTag', array ( 'id' => $this->getName() . '-element' ))
            ->addDecorator('Label');
        $descriptionDecorator = $general->getElement('textScale')->getDecorator('Description');
        $descriptionDecorator->setEscape(false);
        
        $goals_hardware = new Zend_Form_Element_Radio('goals_hardware');
        $goals_hardware->setRequired(true)
            ->setAutoInsertNotEmptyValidator(true)
            ->setAttrib('tmtw', 'numeric')
            ->setAttrib('id', '6')
            ->addPrefixPath('Tangent_Validate', 'Tangent/Validate/', 'validate')
            ->addValidator('Range')
            ->addMultiOptions(array (
                "1" => "1", 
                "2" => "2", 
                "3" => "3", 
                "4" => "4", 
                "5" => "5" 
            ))
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'id' => 'goals_hardware-element' ) ), 
                array ( 'Label' ) 
            ))
            ->setSeparator(' ');
        $goals_hardwareQst = "Ensure your printing hardware matches your print volume needs. ";
        $goals_hardware->getDecorator('Label')->setOption('escape', false);
        $label = $goals_hardware->getDecorator('label');
        $goals_hardware->setLabel($goals_hardwareQst);
        $general->addElement($goals_hardware);
        
        $goals_employee = new Zend_Form_Element_Radio('goals_employee');
        $goals_employee->setRequired(true)
            ->setAutoInsertNotEmptyValidator(true)
            ->setAttrib('tmtw', 'numeric')
            ->setAttrib('id', '7')
            ->addPrefixPath('Tangent_Validate', 'Tangent/Validate/', 'validate')
            ->addValidator('Range')
            ->addMultiOptions(array (
                "1" => "1", 
                "2" => "2", 
                "3" => "3", 
                "4" => "4", 
                "5" => "5" 
            ))
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'id' => 'goals_employee-element' ) ), 
                array ( 'Label' ) 
            ))
            ->setSeparator(' ');
        $goals_employeeQst = "Increase uptime and productivity for your employees.";
        $goals_employee->setLabel($goals_employeeQst);
        $label = $goals_employee->getDecorator('label');
        $general->addElement($goals_employee);
        
        $goals_logistics = new Zend_Form_Element_Radio('goals_logistics');
        $goals_logistics->setRequired(true)
            ->setAutoInsertNotEmptyValidator(true)
            ->setAttrib('tmtw', 'numeric')
            ->setAttrib('id', '8')
            ->addPrefixPath('Tangent_Validate', 'Tangent/Validate/', 'validate')
            ->addValidator('Range')
            ->addMultiOptions(array (
                "1" => "1", 
                "2" => "2", 
                "3" => "3", 
                "4" => "4", 
                "5" => "5" 
            ))
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'id' => 'goals_logistics-element' ) ), 
                array ( 'Label' ) 
            ))
            ->setSeparator(' ');
        $goals_logisticsQst = "Streamline logistics for supplies, service and hardware acquisition.";
        $goals_logistics->setLabel($goals_logisticsQst);
        $label = $goals_logistics->getDecorator('label');
        $general->addElement($goals_logistics);
        
        $goals_enviroment = new Zend_Form_Element_Radio('goals_enviroment');
        $goals_enviroment->setRequired(true)
            ->setAutoInsertNotEmptyValidator(true)
            ->setAttrib('tmtw', 'numeric')
            ->setAttrib('id', '9')
            ->addPrefixPath('Tangent_Validate', 'Tangent/Validate/', 'validate')
            ->addValidator('Range')
            ->addMultiOptions(array (
                "1" => "1", 
                "2" => "2", 
                "3" => "3", 
                "4" => "4", 
                "5" => "5" 
            ))
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'id' => 'goals_enviroment-element' ) ), 
                array ( 'Label' ) 
            ))
            ->setSeparator(' ');
        $goals_enviromentQst = "Reduce environmental impact";
        $goals_enviroment->setLabel($goals_enviromentQst);
        $label = $goals_enviroment->getDecorator('label');
        $general->addElement($goals_enviroment);
        
        $goals_cost = new Zend_Form_Element_Radio('goals_costs');
        $goals_cost->setRequired(true)
            ->setAutoInsertNotEmptyValidator(true)
            ->setAttrib('tmtw', 'numeric')
            ->setAttrib('id', '10')
            ->addPrefixPath('Tangent_Validate', 'Tangent/Validate/', 'validate')
            ->addValidator('Range')
            ->addMultiOptions(array (
                "1" => "1", 
                "2" => "2", 
                "3" => "3", 
                "4" => "4", 
                "5" => "5" 
            ))
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'id' => 'goals_costs-element' ) ), 
                array ( 'Label' ) 
            ))
            ->setSeparator(' ');
        $goals_costQst = "Reduce costs";
        $goals_cost->setLabel($goals_costQst);
        $label = $goals_cost->getDecorator('label');
        $general->addElement($goals_cost);
        
        $general->addElement('RawText', 'CloseDiv', array ( 'value' => '</div>' ));
        
        //*********************************************************************
        // FINANCE FORM
        //*********************************************************************
        $finance = new Zend_Form_SubForm();
        
        $finance->addPrefixPath('Tangent_Form_Element', 'Tangent/Form/Element/', 'element');
        $finance->addElement('RawText', 'OpenDiv', array ( 'value' => '<div id="elements_container">' ));
        
        $toner_cost_radio = new Zend_Form_Element_Radio('toner_cost_radio');
        $toner_cost_radio
        ->addMultiOption("guess", "I don't know")
        ->addMultiOption('I know the exact amount', 'I know the exact amount')
        ->setAttrib('tmtw', 'textual')
        ->setAttrib('id', '11a')
        ->setValue('I know the exact amount');
        $finance->addElement($toner_cost_radio);
        
        $toner_cost = new Zend_Form_Element_Text('toner_cost');
        $toner_cost
	        ->setAttrib('style', 'width: 55px;')
	        ->setAttrib('maxlength', 7)
	        ->setAttrib('tmtw', 'numeric')
	        ->setAttrib('id', '11')
	        ->setDescription('$')
	        ->setAllowEmpty(false)
	        ->addValidator(new Custom_Validate_FieldDependsOnValue('toner_cost_radio', 'I know the exact amount', array ( new Zend_Validate_NotEmpty(), new Zend_Validate_Float() )), true)
	        ->setDecorators(array (
	                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ),
	                'ViewHelper',
	                'Errors',
	                array ( 'HtmlTag', array ( 'id' => 'toner_cost-element' ) ),
	                array ( 'Label' )
	        ));
        $tonerQst = "How much did you spend last year on ink and toner for your printer fleet (excluding the cost of leased copiers)?";
        $toner_cost->setLabel($tonerQst);
        $finance->addElement($toner_cost);
        
        $labor_cost_radio = new Zend_Form_Element_Radio('labor_cost_radio');
        $labor_cost_radio
        ->addMultiOption("guess", "I don't know")
        ->addMultiOption('I know the exact amount', 'I know the exact amount')
        ->setAttrib('tmtw', 'textual')
        ->setAttrib('id', '12a')
        ->setValue('I know the exact amount');
        $finance->addElement($labor_cost_radio);
        
        $labor_cost = new Zend_Form_Element_Text('labor_cost');
        $labor_cost
            ->setAttrib('style', 'width: 55px;')
            ->setAttrib('maxlength', 7)
            ->setAttrib('tmtw', 'numeric')
            ->setAttrib('id', '12')
            ->setDescription('$')
            ->setAllowEmpty(false)
            ->addValidator(new Custom_Validate_FieldDependsOnValue('labor_cost_radio', 'I know the exact amount', array ( new Zend_Validate_NotEmpty(), new Zend_Validate_Float() )), true)
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'id' => 'labor_cost-element' ) ), 
                array ( 'Label' ) 
            ));
        $laborQst = "How much did you spend last year on service from outside vendors for your printer fleet, including maintenance kits, parts and labor (excluding the cost of leased copiers)? If you select \"I don't know\", an average of $200 per printer per year will be used.";
        $labor_cost->setLabel($laborQst);
        $finance->addElement($labor_cost);

        /* REMOVING QUESTION
        $hardware_costs = new Zend_Form_Element_Text('hardware_costs');
        $hardware_costs->setRequired(true)
            ->setAttrib('style', 'width: 55px;')
            ->setRequired(true)
            ->setAttrib('maxlength', 7)
            ->addValidator('regex', false, array (
                'pattern' => $currencyRegex, 
                'messages' => array ( 'regexNotMatch' => "Please enter a valid dollar amount." ) 
            ))
            ->setAttrib('tmtw', 'numeric')
            ->setAttrib('id', '13')
            ->setValue(money_format('%i', 1000))
            ->setDescription('$')
            ->addValidator('greaterThan', true, array ( 'min' => 0 ))
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'id' => 'hardware_costs-element' ) ), 
                array ( 'Label' ) 
            ));
        $hardwareQst = "How much in total did you spend last year on printer hardware purchases? The average purchase price is $1,000 per device.";
        $hardware_costs->setLabel($hardwareQst);
        $finance->addElement($hardware_costs);
        */
        
        $avg_purchase = new Zend_Form_Element_Text('avg_purchase');
        $avg_purchase->setRequired(true)
            ->setAttrib('style', 'width: 55px;')
            ->setRequired(true)
            ->setAttrib('maxlength', 7)
            ->addValidator('regex', false, array (
                'pattern' => $currencyRegex, 
                'messages' => array ( 'regexNotMatch' => "Please enter a valid dollar amount." ) 
            ))
            ->setAttrib('tmtw', 'numeric')
            ->setAttrib('id', '14')
            ->setValue(money_format('%i', 50))
            ->setDescription('$')
            ->addValidator('greaterThan', true, array ( 'min' => 0 ))
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'id' => 'avg_purchase-element' ) ), 
                array ( 'Label' ) 
        ));
        $purchaseQst = "What is the average cost for you to execute a supplies purchase order, including labor for purchasing and administrative personnel? The average cost is $50 per transaction.";
        $avg_purchase->setLabel($purchaseQst);
        $finance->addElement($avg_purchase);
        
        $it_hourlyRate = new Zend_Form_Element_Text('it_hourlyRate');
        $it_hourlyRate->setRequired(true)
            ->setAttrib('style', 'width: 55px;')
            ->setAttrib('maxlength', 6)
            ->setRequired(true)
            ->addValidator('regex', false, array (
                'pattern' => $currencyRegex, 
                'messages' => array ( 'regexNotMatch' => "Please enter a valid dollar amount." ) 
            ))
            ->setAttrib('tmtw', 'numeric')
            ->setAttrib('id', '15')
            ->setValue(money_format('%i', 40))
            ->setDescription('$')
            ->addValidator('greaterThan', true, array ( 'min' => 0 ))
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'id' => 'parts_cost-element' ) ), 
                array ( 'Label' ) 
            ));
        $itQst = "What is the average hourly rate for IT personnel involved in managing printing devices? The average rate is $40/hour.";
        $it_hourlyRate->setLabel($itQst);
        $finance->addElement($it_hourlyRate);
        
        $finance->addElement('RawText', 'CloseDiv', array ( 'value' => '</div>' ));
        
        //*********************************************************************
        // PURCHASING FORM        
        //********************************************************************* 
        $purchasing = new Zend_Form_SubForm();
        
        $purchasing->addPrefixPath('Tangent_Form_Element', 'Tangent/Form/Element/', 'element');
        $purchasing->addElement('RawText', 'OpenDiv', array ( 'value' => '<div id="elements_container">' ));
        
        $numb_vendors = new Zend_Form_Element_Text('numb_vendors');
        $numb_vendors->setRequired(true)
            ->setAttrib('style', 'width: 30px;')
            ->setAttrib('maxlength', 3)
            ->setAutoInsertNotEmptyValidator(false)
            ->setAttrib('tmtw', 'numeric')
            ->addValidator('greaterThan', true, array ( 'min' => 0 ))
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'id' => 'numb_vendors-element' ) ), 
                array ( 'Label' ) 
            ))
            ->setAttrib('id', '16');
        $vendorsQst = "How many vendors do you currently deal with for printer supplies, service and hardware?";
        $numb_vendors->setLabel($vendorsQst);
        $numb_vendors->getValidator('greaterThan')->setMessage('Must be greater than zero');
        $purchasing->addElement($numb_vendors);
        
        //**********************************
        $inkTonerOrderRadio = new Zend_Form_Element_Radio('inkTonerOrderRadio');
        $inkTonerOrderRadio->addMultiOption('Daily', 'Daily')
            ->addMultiOption('Weekly', 'Weekly')
            ->addMultiOption('Times per month', '');
        $inkTonerOrderRadioQst = "How many times per month does your organization order printer supplies?";
        $inkTonerOrderRadio->setLabel($inkTonerOrderRadioQst);
        
        $purchasing->addElement('hidden', 'inkTonerOrderText', array (
            'description' => 'How many times per month does your organization order printer supplies?', 
            'ignore' => true, 
            'decorators' => array ( array ( 'Description', array ( 'escape' => false ) ) ) 
        ));
        
        $purchasing->getElement('inkTonerOrderText')
            ->addDecorator('ViewHelper')
            ->addDecorator('Errors')
            ->addDecorator('Description', array ( 'tag' => 'p', 'class' => 'description' ))
            ->addDecorator('HtmlTag', array ( 'id' => $this->getName() . '-element' ))
            ->addDecorator('Label');
            
        $purchasing->addElement($inkTonerOrderRadio);
        $purchasing->getElement('inkTonerOrderRadio')
            ->setAttrib('tmtw', 'textual')
            ->setAttrib('id', '17a')
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'id' => 'inkTonerOrderRadio-element' ) ), 
                array ( 'Label' ) 
            ));
            
        $ink_toner_order = new Zend_Form_Element_Text('numb_monthlyOrders');
        $ink_toner_order
            ->setAttrib('style', 'width: 30px;')
            ->setAttrib('maxlength', 7)
            ->setAttrib('tmtw', 'numeric')
            ->setAttrib('id', '17')
            ->setAllowEmpty(false)
            ->addValidator(new Custom_Validate_FieldDependsOnValue('inkTonerOrderRadio', 'Times per month', array ( new Zend_Validate_NotEmpty(), new Zend_Validate_Digits() )), true)
            ->setDescription(' times per month')
            ->setDecorators(array (
                'ViewHelper', 
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                array ( 'HtmlTag', array ( 'id' => 'numb_monthlyOrders-element' ) ), 
                'Errors', 
                array ( 'Label' ) 
            ));
        $purchasing->addElement($ink_toner_order);
        //**********************************
        
        $purchasing->addElement('RawText', 'CloseDiv', array ( 'value' => '</div>' ));
        
        //*********************************************************************
        // IT FORM
        //*********************************************************************
        $it = new Zend_Form_SubForm();
        
        $it->addPrefixPath('Tangent_Form_Element', 'Tangent/Form/Element/', 'element');
        $it->addElement('RawText', 'OpenDiv', array ( 'value' => '<div id="elements_container">' ));
        
        $itHoursRadio = new Zend_Form_Element_Radio('itHoursRadio');
        $itHoursRadio
            ->addMultiOption('guess', 'I don\'t know')
            ->addMultiOption('I know the exact amount', 'I know the exact amount');
        
        $it->addElement($itHoursRadio);
        $it->getElement('itHoursRadio')
            ->setValue('I know the exact amount')
            ->setAttrib('tmtw', 'textual')
            ->setAttrib('id', '18a');
            
        $itHours = new Zend_Form_Element_Text('itHours');
        $itHours
            ->setAttrib('style', 'width: 30px;')
            ->setAttrib('maxlength', 3)
            ->setAttrib('tmtw', 'numeric')
            ->setAttrib('id', '18')
            ->setAllowEmpty(false)
            ->addValidator(new Custom_Validate_FieldDependsOnValue('itHoursRadio', 'I know the exact amount', array ( new Zend_Validate_NotEmpty(), new Zend_Validate_Float() )), true)
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'id' => 'itHours-element' ) ), 
                array ( 'Label' ) 
            ));
        $itHoursQst = "How many hours per week do IT personnel spend servicing and supporting printers? If you select \"I don't know\", an average of 15 minutes per week per printer will be used.";
        $itHours->setLabel($itHoursQst);
        $it->addElement($itHours);
        
        $monthlyBreakdown = new Zend_Form_Element_Text('monthlyBreakdown');
        $monthlyBreakdown
            ->setAttrib('style', 'width: 55px;')
            ->setAttrib('maxlength', 7)
            ->setAttrib('tmtw', 'numeric')
            ->setAttrib('id', '24')
            ->setDescription('$')
            ->setAllowEmpty(false)
            ->addValidator(new Custom_Validate_FieldDependsOnValue('monthlyBreakdownRadio', 'I know the exact amount', array ( new Zend_Validate_NotEmpty(), new Zend_Validate_Float() )), true)
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'id' => 'labor_cost-element' ) ), 
                array ( 'Label' ) 
            ));
        $monthlyBreakdownQst = "How many times per month, on average, does your internal IT staff or an external service company need to be called to repair a broken printer in your fleet? If you select \"I don't know\", an average of 1 repair per month for every 20 printers will be used.";
        $monthlyBreakdown->setLabel($monthlyBreakdownQst);
        $it->addElement($monthlyBreakdown);
        
        $monthlyBreakdownRadio = new Zend_Form_Element_Radio('monthlyBreakdownRadio');
        $monthlyBreakdownRadio
            ->addMultiOption('guess', 'I don\'t know')
            ->addMultiOption('I know the exact amount', 'I know the exact amount');
        $monthlyBreakdownRadio->setLabel($monthlyBreakdownQst);
        
        $it->addElement($monthlyBreakdownRadio);
        $it->getElement('monthlyBreakdownRadio')
            ->setValue('I know the exact amount')
            ->setAttrib('tmtw', 'textual')
            ->setAttrib('id', '24a')
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'id' => 'monthlyBreakdownRadio-element' ) ), 
                array ( 'Label' ) 
            ));
            
        $location_trackingQst = "Do you currently have a tracking mechanism for the location of printing devices based on their IP address?";
        $location_tracking = new Zend_Form_Element_Select('location_tracking');
        $location_tracking->setAttrib('class', 'select_yesno')
            ->setMultiOptions(array ( 'Y' => 'Yes', 'N' => 'No' ))
            ->setAttrib('width', 45)
            ->setAttrib('tmtw', 'textual')
            ->setAttrib('id', '19')
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'id' => 'location_tracking-element' ) ), 
                array ( 'Label' ) 
            ));
        $location_tracking->setLabel($location_trackingQst);
        $it->addElement($location_tracking);
        
        $it->addElement('RawText', 'CloseDiv', array ( 'value' => '</div>' ));
        
        //*********************************************************************
        // USERS FORM
        //*********************************************************************
        $users = new Zend_Form_SubForm();
        
        $users->addPrefixPath('Tangent_Form_Element', 'Tangent/Form/Element/', 'element');
        $users->addElement('RawText', 'OpenDiv', array ( 'value' => '<div id="elements_container">' ));
        
        $users->setDecorators(array ( 'FormElements', array ( 'HtmlTag', array ( 'tag' => 'table' ) ), 'Form' ));
        
        $pageCoverageQst = "Page coverages range from 5% to 10% for monochrome and 15% to 25% for color. Estimate your own average page coverages.";
        
        //Add a note element to the form.  This element is there for the
        //sole purpose of displaying this text within the form
        /*
        $users->addPrefixPath('Tangent_Form_Element', 'Tangent/Form/Element/', 'element');
        $users->addElement(
            'Note',
            'myElementId',
            array( 'value'=>'<p>'.$pageCoverageQst.'</p>', 'disableLoadDefaultDecorators' => true,
        ));

        $users->getElement('myElementId')->setDecorators(array( 'ViewHelper')); */
                                                    
        $users->addElement('hidden', 'pageCoverage', array (
            'description' => "Page coverages range from 5% to 10% for monochrome and 15% to 25% for color. Estimate your own average page coverages.", 
            'ignore' => true, 
            'decorators' => array ( array ( 'Description', array ( 'escape' => false ) ) ) 
        ));
        
        $users->getElement('pageCoverage')
            ->addDecorator('ViewHelper')
            ->addDecorator('Errors')
            ->addDecorator('Description', array ( 'tag' => 'span', 'class' => 'description' ))
            ->addDecorator('HtmlTag', array ( 'id' => $this->getName() . '-element' ))
            ->addDecorator('Label');
        
        $descriptionDecorator = $users->getElement('pageCoverage')->getDecorator('Description');
        $descriptionDecorator->setEscape(false);
        
        $pageCoverage_BW = new Zend_Form_Element_Text('pageCoverage_BW');
        $pageCoverage_BW
            ->setRequired(true)
            ->setAttrib('style', 'width: 35px;')
            ->setAttrib('maxlength', 5)
            ->setAttrib('tmtw', 'numeric')
            ->setDescription('%') 
            ->setValue($session->pageCoverageBW)
            ->addValidator(new Zend_Validate_Between(1,100))
            ->setDecorators(array (
                'ViewHelper', 
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'Errors', 
                array ( 'HtmlTag', array ( 'id' => 'page_coverage-element' ) ), 
                array ( 'Label' ) 
            ));
        $pageCoverage_BW->getValidator('Between')->setMessage('Must be between 1 and 100');
        $pageCoverage_BW->setAttrib('id', '21');
        
        $coverageBWQst = "Monochrome Coverage:";
        $pageCoverage_BW->setLabel($coverageBWQst);
        
        $users->addElement($pageCoverage_BW);
        
        $pageCoverage_Colour = new Zend_Form_Element_Text('pageCoverage_Colour');
        $pageCoverage_Colour
            ->setRequired(true)
            ->setAttrib('style', 'width: 35px;')
            ->setAttrib('maxlength', 5)
            ->addValidator('float')
            ->setAttrib('tmtw', 'numeric')
            ->setDescription('%')
            ->setAttrib('id', '22')
            ->setValue($session->pageCoverageColor)
            ->addValidator(new Zend_Validate_Between(1,100))
            ->setDecorators(array (
                'ViewHelper', 
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'Errors', 
                array ( 'HtmlTag', array ( 'id' => 'page_coverage-element' ) ), 
                array ( 'Label' ) 
            ));
        $pageCoverage_Colour->getValidator('Between')->setMessage('Must be between 1 and 100');
        $coverageColour = "Color Coverage:";
        $pageCoverage_Colour->setLabel($coverageColour);
        $users->addElement($pageCoverage_Colour);
        
        $volumeOptions = array (
            5 => 'Less than 10%', 
            18 => '10% to 25%', 
            38 => '26% to 50%', 
            75 => 'More than 50%' 
        );
        
        $volumeQst = "What percent of your print volume is done on inkjet and other desktop printers?";
        
        $users->addElements(array (
            new Zend_Form_Element_Radio('printVolume', array (
                'label' => $volumeQst, 
                'multiOptions' => $volumeOptions, 
                'required' => true, 
                'filters' => array ( 'StringTrim' ), 
                'validators' => array ( array ( 'InArray', false, array ( array_keys($volumeOptions) ) ) ) 
            )) 
        ));
        
        $users->getElement('printVolume')
            ->setAttrib('tmtw', 'numeric')
            ->setAttrib('id', '23')
            ->setOptions(array ( 'class' => 'radiobuttons' ))
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'id' => 'printVolume-element' ) ), 
                array ( 'Label' ) 
            ));
            
        $users->getElement('printVolume')->class = "radiobuttons";
        
        $repairTimeOptions = array (
            '0.5' => 'Less than a day', 
            1 => 'One day', 
            2 => 'Two days', 
            3 => 'Three to five days', 
            5 => 'More than five days' 
        );
        
        $repairTimeQst = "How long does it take, on average, for a printer to be fixed after it has broken down?";
        
        $users->addElements(array (
            new Zend_Form_Element_Radio('repairTime', array (
                'label' => $repairTimeQst, 
                'multiOptions' => $repairTimeOptions, 
                'required' => true, 
                'filters' => array ( 'StringTrim' ), 
                'validators' => array ( array ( 'InArray', false, array ( array_keys($repairTimeOptions) ) ) ) 
            )) 
        ));
        
        $users->getElement('repairTime')
            ->setAttrib('tmtw', 'numeric')
            ->setAttrib('id', '20')
            ->setOptions(array ( 'class' => 'radiobuttons' ))
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'id' => 'repairTime-element' ) ), 
                array ( 'Label' ) 
            ));
        $users->getElement('repairTime')->class = "radiobuttons";
        
        $users->addElement('RawText', 'CloseDiv', array ( 'value' => '</div>' ));
        
        //*********************************************************************
        // Attach sub forms to main form
        //*********************************************************************
        $this->addSubForms(array (
            'company' => $companyInfo, 
            'general' => $general, 
            'finance' => $finance, 
            'purchasing' => $purchasing, 
            'it' => $it, 
            'users' => $users 
        ));
        
        $general->setDecorators(array ( array ( 'HtmlTag', array ( 'tag' => 'dl', 'class' => 'survey_form' ) ), 'Form' ));
    
    }

    /**
     * Prepare a sub form for display
     *
     * @param  string|Zend_Form_SubForm $spec
     * @return Zend_Form_SubForm
     */
    public function prepareSubForm ($spec)
    {
        if (is_string($spec))
        {
            $subForm = $this->{$spec};
        
        }
        else if ($spec instanceof Zend_Form_SubForm)
        {
            $subForm = $spec;
        
        }
        else
        {
            throw new Exception('Invalid argument passed to ' . __FUNCTION__ . '()');
        
        }
        
        $this->setSubFormDecorators($subForm)
            ->addSubmitButton($subForm)
            ->addSubFormActions($subForm);
        return $subForm;
    }

    /**
     * Add form decorators to an individual sub form
     *
     * @param  Zend_Form_SubForm $subForm
     * @return My_Form_Registration
     */
    public function setSubFormDecorators (Zend_Form_SubForm $subForm)
    {
        $subForm->setDecorators(array (
            'FormElements', 
            array ( 'HtmlTag', array ( 'tag' => 'dl', 'class' => 'zend_form' ) ), 
            'Form' 
        ));
        return $this;
    }

    /**
     * Add back and save buttoms to the bottom of a subform.
     * Both buttons will be wrapped in a div tag, so they can be styled.
     *
     * @param  Zend_Form_SubForm $subForm
     * @return My_Form_Registration
     */
    public function addSubmitButton (Zend_Form_SubForm $subForm)
    {
        //place the following buttons in a div tag
        $subForm->addPrefixPath('Tangent_Form_Element', 'Tangent/Form/Element/', 'element');
        $subForm->addElement('RawText', 'DivStart', array ( 'value' => '<div class="submitBar">' ));
        
        $savebtn = new Zend_Form_Element_Submit('save', array (
            'label' => 'Save and continue', 
            'required' => false, 
            'ignore' => true, 
            'disableLoadDefaultDecorators' => true 
        ));
        $savebtn->setAttrib('class','btn btn-primary');
        
        $savebtn->setDecorators(array (
            'ViewHelper', 
            'Errors', 
            array ( array ( 'span' => 'HtmlTag' ), array ( 'tag' => 'span', 'class' => '' ) ) 
        ));
        $subForm->addElement($savebtn);
        
        $backbtn = new Zend_Form_Element_Button('back_button', array ( 'disableLoadDefaultDecorators' => true ));
        $backbtn->setAttrib('class','btn');
        $backbtn->setLabel('Back')->setDecorators(array (
            'ViewHelper', 
            'Errors', 
            array ( array ( 'span' => 'HtmlTag' ), array ( 'tag' => 'span', 'class' => '' ) ) 
        ));
        $subForm->addElement($backbtn);
        
        //end the div tag container for the buttons
        $subForm->addPrefixPath('Tangent_Form_Element', 'Tangent/Form/Element/', 'element');
        $subForm->addElement('RawText', 'DivEnd', array ( 'value' => '</div>' ));
        
        return $this;
    }

    /**
     * Add action and method to sub form
     *
     *
     * @param  Zend_Form_SubForm $subForm
     * @return My_Form_Registration
     */
    public function addSubFormActions (Zend_Form_SubForm $subForm)
    {
        //NOTE:  Not currently being used.  Form actions are currently set
        // set in the processForm action in the SurveyController.
        

        //$subForm->setAction('/survey/process')
        //        ->setMethod('post');
        
        return $this;
    }

    //function checks if any of the goals questions contain duplicate values.
    public function set_validation ($data)
    {
        $form = new Proposalgen_Form_Survey();
        $usedValues = array ();
        $used = false;
        $data = reset($data);
        foreach ( $data as $key => $value )
        {
            if (strstr($key, 'goal')){
    			if(in_array($value, $usedValues)){
    				return false; 
    			} else {
    				$usedValues[] = $value;
    			}
    		}
    	}
    	return true;
    }
    
    public function fetchValue($data) {
    }
    
} // end Survey

?>