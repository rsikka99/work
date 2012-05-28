<?php

/**
 * Device Form: Used for Adding / Editing Devices
 *
 * @author	John Sadler
 * @version v1.0
 */

class Proposalgen_Form_Device extends Zend_Form
{

    /**
     * Constructor builds the form
     * @param $options - not used (required) 	
     * @param $type - can be set to 'edit', or null. Differnt form elements are added for editing an instructor and adding a new instructor.
     * @return HTML markup for the from is automatically returned by zend_form	 
     */
    public function __construct ($options = null, $type = null)
    {
        //call parent contsructor
        $currencyRegex = '/^\d+(?:\.\d{0,2})?$/';
        parent::__construct($options);
        $elements = array ();
        $elementCounter = 0;
        
        $this->setName('device_form');
        $this->setAttrib('class', 'outlined');
        
        //*****************************************************************
        //DEVICE FIELDS
        //*****************************************************************
        
        //hidden mode to toggle between add/edit
        $hiddenMode = new Zend_Form_Element_Hidden('form_mode');
        $hiddenMode->setValue("edit");
        $hiddenMode->setDecorators(array (
                'ViewHelper' 
        ));
        array_push($elements, $hiddenMode);
        $elementCounter ++;
        
        //hidden field for hdnID
        $element = new Zend_Form_Element_Hidden('hdnID');
        $element->setDecorators(array (
                'ViewHelper' 
        ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //hidden field for hdnItem
        $element = new Zend_Form_Element_Hidden('hdnItem');
        $element->setDecorators(array (
                'ViewHelper' 
        ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //hidden field for ticket_id when in ticket mode
        $element = new Zend_Form_Element_Hidden('ticket_id');
        $element->setDecorators(array (
                'ViewHelper' 
        ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //hidden field for devices_pf_id when in request mode
        $element = new Zend_Form_Element_Hidden('devices_pf_id');
        $element->setDecorators(array (
                'ViewHelper' 
        ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //hidden field for unknown_device_instance_id when in request mode
        $element = new Zend_Form_Element_Hidden('unknown_device_instance_id');
        $element->setDecorators(array (
                'ViewHelper' 
        ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //hidden field for saving
        $element = new Zend_Form_Element_Hidden('save_flag');
        $element->setDecorators(array (
                'ViewHelper' 
        ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //hidden field for toners_array
        $element = new Zend_Form_Element_Hidden('toner_array');
        $element->setDecorators(array (
                'ViewHelper' 
        ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //manufacturers list
        $manufacturer_id = new Zend_Form_Element_Select('manufacturer_id');
        $manufacturer_id->setLabel('* Manufacturer:')
            ->setRequired(true)
            ->setOrder($elementCounter)
            ->setAttrib('class', 'manufacturer_select')
            ->setAttrib('id', 'manufacturer_id')
            ->setAttrib('onchange', 'javascript: if(repop != 1) {empty_form();}')
            ->setDecorators(array (
                'ViewHelper', 
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'printer_manufacturer-element' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) ) 
            ));
        array_push($elements, $manufacturer_id);
        $elementCounter ++;
        
        //printer_model list
        $printer_model = new Zend_Form_Element_Select('printer_model');
        $printer_model->setLabel('* Printer Model:')
            ->setOrder($elementCounter)
            ->setRegisterInArrayValidator(false)
            ->setAttrib('id', 'printer_model')
            ->setAttrib('class', 'model_select')
            ->setAttrib('onchange', 'javascript: if(repop != 1) {empty_form();}')
            ->setDescription('<a id="add_link" href="javascript: add_printer(true);" onclick="$(\'#message_container\').html(\'\');">Add New Printer Model</a>')
            ->setDecorators(array (
                'ViewHelper', 
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'printer_model-element' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) ) 
            ));
        array_push($elements, $printer_model);
        $elementCounter ++;
        
        //add printer
        $add_printer = new Zend_Form_Element_Text('new_printer');
        $add_printer->setLabel('* Printer Model:')
            ->//->setAttrib('size',30)
        setAttrib('maxlength', 50)
            ->setAttrib('id', 'new_printer')
            ->setDescription('<a id="edit_link" href="javascript: add_printer(false);" onclick="$(\'#message_container\').html(\'\');">Edit Existing Model</a>')
            ->setDecorators(array (
                'ViewHelper', 
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'new_printer-element' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) ) 
            ))
            ->setOrder($elementCounter);
        array_push($elements, $add_printer);
        $elementCounter ++;
        
        //serial number
        $element = new Zend_Form_Element_Text('serial_number');
        $element->setLabel('Serial Number:')
            ->setAttrib('size', 30)
            ->setAttrib('maxlength', 50)
            ->setAttrib('id', 'serial_number')
            ->setOrder($elementCounter);
        array_push($elements, $element);
        $elementCounter ++;
        
        //launch_date
        $element = new Zend_Form_Element_Text('launch_date');
        $element->setLabel('* Launch Date:')
            ->setRequired(true)
            ->setAttrib('size', 20)
            ->setAttrib('maxlength', 20)
            ->setAttrib('id', 'launch_date')
            ->setDescription('mm/dd/yyyy')
            ->setDecorators(array (
                'ViewHelper', 
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'launch_date-element' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) ) 
            ))
            ->setOrder($elementCounter);
        array_push($elements, $element);
        $elementCounter ++;
        
        //device price
        $device_price = new Zend_Form_Element_Text('device_price');
        $device_price->setLabel('Price:')
            ->setAttrib('size', 6)
            ->setAttrib('maxlength', 10)
            ->setAttrib('id', 'device_price')
            ->setAttrib('style', 'text-align: right')
            ->setAttrib('onkeypress', 'javascript: return numbersonly(this, event)')
            ->setOrder($elementCounter)
            ->addValidator(new Zend_Validate_Float())
            ->addValidator(new Zend_Validate_GreaterThan(0))
            ->setDescription('$')
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'device_price-element' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) ) 
            ));
        $device_price->getValidator('Float')->setMessage('Please enter a number.');
        $device_price->getValidator('GreaterThan')->setMessage('Value must be greater then zero.');
        array_push($elements, $device_price);
        $elementCounter ++;
        
        //override price
        $override_price = new Zend_Form_Element_Text('override_price');
        $override_price->setLabel('Override Price:')
            ->setAttrib('size', 6)
            ->setAttrib('maxlength', 10)
            ->setAttrib('id', 'override_price')
            ->setAttrib('onkeypress', 'javascript: return numbersonly(this, event)')
            ->addValidator('regex', false, array (
                'pattern' => $currencyRegex, 
                'messages' => array ( 'regexNotMatch' => "Must be a dollar value." ) 
            ))
            ->setAttrib('style', 'text-align: right')
            ->setOrder($elementCounter)
            ->setDescription('$')
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'override_price-element' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) ) 
            ));
        array_push($elements, $override_price);
        $elementCounter ++;
        
        //toner_config list
        $toner_config = new Zend_Form_Element_Select('toner_config_id');
        $toner_config->setLabel('* Toner Config:')
            ->setRequired(true)
            ->setOrder($elementCounter)
            ->setAttrib('id', 'toner_config_id')
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'toner_config_id-element' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) ) 
            ));
        array_push($elements, $toner_config);
        $elementCounter ++;
        
        //copier
        $is_copier = new Zend_Form_Element_Checkbox('is_copier');
        $is_copier->setLabel('Copier:')
            ->setOrder($elementCounter)
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'is_copier-element' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) ) 
            ));
        array_push($elements, $is_copier);
        $elementCounter ++;
        
        //scanner
        $is_scanner = new Zend_Form_Element_Checkbox('is_scanner');
        $is_scanner->setLabel('Scanner:')
            ->setOrder($elementCounter)
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'is_scanner-element' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) ) 
            ));
        array_push($elements, $is_scanner);
        $elementCounter ++;
        
        //fax
        $is_fax = new Zend_Form_Element_Checkbox('is_fax');
        $is_fax->setLabel('Fax:')
            ->setOrder($elementCounter)
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'is_fax-element' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) ) 
            ));
        array_push($elements, $is_fax);
        $elementCounter ++;
        
        //duplex
        $is_duplex = new Zend_Form_Element_Checkbox('is_duplex');
        $is_duplex->setLabel('Duplex:')
            ->setOrder($elementCounter)
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'is_duplex-element' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) ) 
            ));
        array_push($elements, $is_duplex);
        $elementCounter ++;
        
        //ppm black
        $element = new Zend_Form_Element_Text('ppm_black');
        $element->setLabel('PPM Black:')
            ->setAttrib('maxlength', 4)
            ->setAttrib('size', 8)
            ->addValidator(new Zend_Validate_Float())
            ->setOrder($elementCounter)
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'ppm_black-element' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) ) 
            ));
        $element->getValidator('Float')->setMessage('Please enter a number.');
        array_push($elements, $element);
        $elementCounter ++;
        
        //ppm color
        $element = new Zend_Form_Element_Text('ppm_color');
        $element->setLabel('PPM Color:')
            ->setAttrib('maxlength', 4)
            ->setAttrib('size', 8)
            ->addValidator(new Zend_Validate_Float())
            ->setOrder($elementCounter)
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'ppm_color-element' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) ) 
            ));
        $element->getValidator('Float')->setMessage('Please enter a number.');
        array_push($elements, $element);
        $elementCounter ++;
        
        //duty cycle
        $element = new Zend_Form_Element_Text('duty_cycle');
        $element->setLabel('Duty Cycle:')
            ->setAttrib('maxlength', 8)
            ->setAttrib('size', 8)
            ->addValidator(new Zend_Validate_Float())
            ->setOrder($elementCounter)
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'duty_cycle-element' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) ) 
            ));
        $element->getValidator('Float')->setMessage('Please enter a number.');
        array_push($elements, $element);
        $elementCounter ++;
        
        //watts normal
        $watts_power_normal = new Zend_Form_Element_Text('watts_power_normal');
        $watts_power_normal->setLabel('* Power Consumption Normal:')
            ->setAttrib('maxlength', 6)
            ->setAttrib('size', 6)
            ->addValidator(new Zend_Validate_Float())
            ->addValidator(new Zend_Validate_GreaterThan( array ( 'min' => 1 ) ) )
            ->setAttrib('style', 'text-align: right')
            ->setAttrib('onkeypress', 'javascript: return numbersonly(this, event)')
            ->setDescription('Watts')
            ->setOrder($elementCounter)
            ->setDecorators(array (
                'ViewHelper', 
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'page_coverage-element' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) ) 
            ));    
        $watts_power_normal->getValidator('Float')->setMessage('Please enter a number.');
        $watts_power_normal->getValidator('GreaterThan')->setMessage('Must be greater than 0.');
        array_push($elements, $watts_power_normal);
        $elementCounter ++;
        
        //watts idle
        $watts_power_idle = new Zend_Form_Element_Text('watts_power_idle');
        $watts_power_idle->setLabel('* Power Consumption Idle:')
            ->setAttrib('maxlength', 6)
            ->setAttrib('size', 6)
            ->addValidator(new Zend_Validate_Float())
            ->addValidator(new Zend_Validate_GreaterThan(array ( 'min' => 1 ) ) )
            ->setAttrib('style', 'text-align: right')
            ->setAttrib('onkeypress', 'javascript: return numbersonly(this, event)')
            ->setDescription('Watts')
            ->setOrder($elementCounter)
            ->setDecorators(array (
                'ViewHelper', 
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'page_coverage-element' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) ) 
            ));
        $watts_power_idle->getValidator('Float')->setMessage('Please enter a number.');
        $watts_power_idle->getValidator('GreaterThan')->setMessage('Must be greater than 0.');
        array_push($elements, $watts_power_idle);
        $elementCounter ++;
        
        //*********************************************************************
        //LEASED DEVICES BELOW
        //*********************************************************************
        
        //is leased flag
        $element = new Zend_Form_Element_Checkbox('is_leased');
        $element->setLabel('Leased:')
            ->setAttrib('onclick', 'javascript: toggle_leased(this.checked)')
            ->setOrder($elementCounter)
            ->setDecorators(array (
                'ViewHelper', 
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'is_leased-element' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) )
            ) );
        array_push($elements,$element);
        $elementCounter++;
        
        //leased toner yield
        $element = new Zend_Form_Element_Text('leased_toner_yield');
        $element->setLabel('* Leased Toner Yield:')
            ->setAttrib('maxlength', 6)
            ->setAttrib('size', 6)
            ->addValidator(new Zend_Validate_GreaterThan(array ( 'min' => 1 ) ) )
            ->setAttrib('style', 'text-align: right')
            ->setAttrib('onkeypress', 'javascript: return numbersonly(this, event)')
            ->setOrder($elementCounter)
            ->setDecorators(array (
                'ViewHelper', 
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'leased_toner_yield-element' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) ) 
            ));
        $element->getValidator('GreaterThan')->setMessage('Must be greater than 0.');
        array_push($elements, $element);
        $elementCounter ++;
        
        /**********************************************************************
        //FOR REPLACEMENT DEVICES BELOW
        //*********************************************************************
        
        //is_replacement
        $element = new Zend_Form_Element_Checkbox('is_replacement_device');
        $element->setLabel('Replacement Printer:')
            ->setAttrib('onclick', 'javascript: toggle_replacement(this.checked)')
            ->setOrder($elementCounter);
        array_push($elements, $element);
        $elementCounter ++;
        
        //replacement_category
        $toner_config = new Zend_Form_Element_Select('replacement_category');
        $toner_config->setLabel('* Replacement Category:')
            ->setRequired(true)
            ->setOrder($elementCounter)
            ->setAttrib('id', 'replacement_category');
        array_push($elements, $toner_config);
        $elementCounter ++;
        
        //is_letter_legal
        $element = new Zend_Form_Element_Checkbox('is_letter_legal');
        $element->setLabel('Letter & Legal:')->setOrder($elementCounter);
        array_push($elements, $element);
        $elementCounter ++;
        
        //print_speed
        $element = new Zend_Form_Element_Text('print_speed');
        $element->setLabel('* Print Speed:')
            ->setRequired(true)
            ->setAttrib('maxlength', 6)
            ->setAttrib('size', 6)
            ->addValidator(new Zend_Validate_Float())
            ->addValidator(new Zend_Validate_GreaterThan(array ( 'min' => 1 ) ) )
            ->setAttrib('style', 'text-align: right')
            ->setAttrib('onkeypress', 'javascript: return numbersonly(this, event)')
            ->setOrder($elementCounter)
            ->setDescription('ppm')
            ->setDecorators(array (
                'ViewHelper', 
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'print_speed-element' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) ) 
            ));
        $element->getValidator('Float')->setMessage('Please enter a number.');
        $element->getValidator('GreaterThan')->setMessage('Must be greater than 0.');
        array_push($elements, $element);
        $elementCounter ++;
        
        //resolution
        $element = new Zend_Form_Element_Text('resolution');
        $element->setLabel('* Resolution:')
            ->setRequired(true)
            ->setAttrib('maxlength', 6)
            ->setAttrib('size', 6)
            ->addValidator(new Zend_Validate_Float())
            ->addValidator(new Zend_Validate_GreaterThan(array ( 'min' => 1 ) ) )
            ->setAttrib('style', 'text-align: right')
            ->setAttrib('onkeypress', 'javascript: return numbersonly(this, event)')
            ->setOrder($elementCounter)
            ->setDescription('dpi')
            ->setDecorators(array (
                'ViewHelper', 
                array ( 'Description', array ( 'escape' => false,  'tag' => false ) ), 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'resolution-element' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) ) 
            ));
        $element->getValidator('Float')->setMessage('Please enter a number.');
        $element->getValidator('GreaterThan')->setMessage('Must be greater than 0.');
        array_push($elements, $element);
        $elementCounter ++;
        
        //paper_capacity
        $element = new Zend_Form_Element_Text('paper_capacity');
        $element->setLabel('* Paper Capacity:')
            ->setRequired(true)
            ->setAttrib('maxlength', 6)
            ->setAttrib('size', 6)
            ->addValidator(new Zend_Validate_Float())
            ->addValidator(new Zend_Validate_GreaterThan(array ( 'min' => 1 ) ) )
            ->setAttrib('style', 'text-align: right')
            ->setAttrib('onkeypress', 'javascript: return numbersonly(this, event)')
            ->setOrder($elementCounter)
            ->setDescription('pages')
            ->setDecorators(array (
                'ViewHelper', 
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'paper_capacity-element' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) ) 
            ));
        $element->getValidator('Float')->setMessage('Please enter a number.');
        $element->getValidator('GreaterThan')->setMessage('Must be greater than 0.');
        array_push($elements, $element);
        $elementCounter ++;
        
        //CPP_above_ten_thousand_pages
        $element = new Zend_Form_Element_Text('cpp_above');
        $element->setLabel('* CPP Above 10000 Pages:')
            ->setRequired(true)
            ->setAttrib('maxlength', 6)
            ->setAttrib('size', 6)
            ->addValidator(new Zend_Validate_Float())
            ->addValidator(new Zend_Validate_GreaterThan( array ( 'min' => 0 ) ) )
            ->setAttrib('style', 'text-align: right')
            ->setAttrib('onkeypress', 'javascript: return numbersonly(this, event)')
            ->setOrder($elementCounter)
            ->setDescription('$')
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd',  'id' => 'cpp_above-element' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) ) 
            ));
        $element->getValidator('Float')->setMessage('Please enter a number.');
        $element->getValidator('GreaterThan')->setMessage('Must be greater than 0.');
        array_push($elements, $element);
        $elementCounter ++;
        
        //monthly rate
        $element = new Zend_Form_Element_Text('monthly_rate');
        $element->setLabel('* Monthly Rate:')
            ->setRequired(true)
            ->setAttrib('maxlength', 6)
            ->setAttrib('size', 6)
            ->addValidator(new Zend_Validate_Float())
            ->addValidator(new Zend_Validate_GreaterThan( array ('min' => 0 ) ) )
            ->setAttrib('style', 'text-align: right')
            ->setAttrib('onkeypress', 'javascript: return numbersonly(this, event)')
            ->setOrder($elementCounter)
            ->setDescription('$')
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'monthly_rate-element' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) ) 
            ));
        $element->getValidator('Float')->setMessage('Please enter a number.');
        $element->getValidator('GreaterThan')->setMessage('Must be greater than 0.');
        array_push($elements, $element);
        $elementCounter ++;
        //********************************************************************/
        
        //*********************************************************************
        //BUTTONS
        //*********************************************************************
        
        //save button
        $update = new Zend_Form_Element_Submit('save_device', array ( 'disableLoadDefaultDecorators' => true ) );
        $update->setLabel('Save')
            ->setOrder($elementCounter)
            ->setDecorators(array (
                'ViewHelper', 
                'Errors', 
                array ( array ( 'dd' => 'HtmlTag' ), array ( 'tag' => 'dd', 'class' => 'botMenu' ) ) 
            ));
        array_push($elements, $update);
        $elementCounter ++;
        
        //delete button
        $element = new Zend_Form_Element_Submit('delete_device', array ( 'disableLoadDefaultDecorators' => true ) );
        $element->setLabel('Delete')
            ->setOrder($elementCounter)
            ->setAttrib('onclick', 'javascript: return confirm("Are you sure you want to delete this printer?");')
            ->setDecorators(array (
                'ViewHelper', 
                'Errors', 
                array ( array ( 'dd' => 'HtmlTag' ), array ( 'tag' => 'dd', 'class' => 'botMenu' ) ) 
            ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //back button
        $back = new Zend_Form_Element_Button('back_button');
        $back->setLabel('Done')
            ->setOrder($elementCounter)
            ->setAttrib('onClick', 'javascript: document.location.href="../admin";')
            ->setDecorators(array (
                'ViewHelper', 
                'Errors', 
                array ( array ( 'dd' => 'HtmlTag' ), array ( 'tag' => 'dd', 'class' => 'botMenu' ) ) 
            ));
        array_push($elements, $back);
        $elementCounter ++;
        
        //add all defined elements to the form
        $this->addElements($elements);
    
    } //end function __construct

    public function set_validation ($data)
    {
        if (isset($data ['is_leased']) && $data ['is_leased'] == "1")
        {
            $this->leased_toner_yield->setRequired(true);    
        }
    
        if (isset($data ['is_replacement_device']) && $data ['is_replacement_device'] == "0")
        {
            $this->replacement_category->setRequired(false);
            $this->print_speed->setRequired(false);
            $this->resolution->setRequired(false);
            $this->paper_capacity->setRequired(false);
            $this->cpp_above->setRequired(false);
            $this->monthly_rate->setRequired(false);
    	}
    	return $data;
    }
    
}
?>

