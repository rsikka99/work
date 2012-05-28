<?php

/**
 * Unknown Device Form: Used for Adding device details for unknown devices found
 * during data import
 *
 * @author	John Sadler
 * @version v1.0
 */

class Proposalgen_Form_UnknownDevice extends Zend_Form
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
        parent::__construct($options);
        $elements = array ();
        $elementCounter = 0;
        
        $this->setName('unknown_device_form');
        $this->setAttrib('class', 'outlined');
        
        //*****************************************************************
        //DEVICE FIELDS
        //*****************************************************************
        
        //upload_data_collector_id
        $element = new Zend_Form_Element_Hidden('upload_data_collector_id');
        $element->setAttrib('id', 'upload_data_collector_id');
        $element->setDecorators(array (
                'ViewHelper' 
        ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //devices_pf_id
        $element = new Zend_Form_Element_Hidden('devices_pf_id');
        $element->setAttrib('id', 'devices_pf_id');
        $element->setDecorators(array (
                'ViewHelper' 
        ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //unknown_device_instance_id
        $element = new Zend_Form_Element_Hidden('unknown_device_instance_id');
        $element->setAttrib('id', 'unknown_device_instance_id');
        $element->setDecorators(array (
                'ViewHelper' 
        ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //grid display
        $element = new Zend_Form_Element_Hidden('grid');
        $element->setAttrib('id', 'grid');
        $element->setDecorators(array (
                'ViewHelper' 
        ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //device_manufacturer
        $element = new Zend_Form_Element_Text('device_manufacturer');
        $element->setLabel('* Manufacturer:')
            ->setRequired(true)
            ->setOrder($elementCounter)
            ->setAttrib('size', 30)
            ->setAttrib('maxlength', 50)
            ->addErrorMessage("You must enter a device manufacturer.")
            ->setAttrib('id', 'device_manufacturer')
            ->setDecorators(array (
                'ViewHelper', 
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'device_manufacturer-element' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) )
            ) );
        array_push($elements, $element);
        $elementCounter ++;
        
        //printer_model
        $element = new Zend_Form_Element_Text('printer_model');
        $element->setLabel('* Printer Model:')
            ->setRequired(true)
            ->setAttrib('size', 30)
            ->setAttrib('maxlength', 50)
            ->setAttrib('id', 'printer_model')
            ->addErrorMessage("You must enter a printer model.")
            ->setOrder($elementCounter)
            ->setDecorators(array (
                'ViewHelper', 
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'printer_model-element' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) )
            ) );
        array_push($elements, $element);
        $elementCounter ++;
        
        //launch_date
        $element = new Zend_Form_Element_Text('mps_launch_date');
        $element->setLabel('* Launch Date:')
            ->setRequired(true)
            ->setAttrib('size', 20)
            ->setAttrib('maxlength', 30)
            ->setAttrib('id', 'mps_launch_date')
            ->setDescription('mm/dd/yyyy (<i>Ex. 01/21/11)</i>')
            ->addErrorMessage("You must enter a launch date.")
            ->setDecorators(array (
                'ViewHelper', 
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'mps_launch_date-element' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) )
            ) )
            ->setOrder($elementCounter);
        array_push($elements, $element);
        $elementCounter ++;
        
        //ip address
        $element = new Zend_Form_Element_Text('ipaddress');
        $element->setLabel('IP Address:')
            ->setAttrib('size', 30)
            ->setAttrib('maxlength', 50)
            ->setAttrib('id', 'ipaddress')
            ->setOrder($elementCounter)
            ->setDecorators(array (
                'ViewHelper', 
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'ipaddress-element' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) )
            ) );
        array_push($elements, $element);
        $elementCounter ++;
        
        //serial number
        $element = new Zend_Form_Element_Text('serial_number');
        $element->setLabel('Serial Number:')
            ->setAttrib('size', 30)
            ->setAttrib('maxlength', 50)
            ->setAttrib('id', 'serial_number')
            ->setOrder($elementCounter)
            ->setDecorators(array (
                'ViewHelper', 
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'serial_number-element' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) )
            ) );
        array_push($elements, $element);
        $elementCounter ++;
        
        //is_copier
        $element = new Zend_Form_Element_Checkbox('is_copier');
        $element->setLabel('Copier:')->setOrder($elementCounter)
            ->setDecorators(array (
                'ViewHelper', 
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'is_copier-element' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) )
            ) );
        array_push($elements, $element);
        $elementCounter ++;
        
        //is_scanner
        $element = new Zend_Form_Element_Checkbox('is_scanner');
        $element->setLabel('Scanner:')->setOrder($elementCounter)
            ->setDecorators(array (
                'ViewHelper', 
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'is_scanner-element' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) )
            ) );
        array_push($elements, $element);
        $elementCounter ++;
        
        //is_fax
        $element = new Zend_Form_Element_Checkbox('is_fax');
        $element->setLabel('Fax:')->setOrder($elementCounter)
            ->setDecorators(array (
                'ViewHelper', 
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'is_fax-element' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) )
            ) );
        array_push($elements, $element);
        $elementCounter ++;
        
        //is_duplex
        $element = new Zend_Form_Element_Checkbox('is_duplex');
        $element->setLabel('Duplex:')->setOrder($elementCounter)
            ->setDecorators(array (
                'ViewHelper', 
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'is_duplex-element' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) )
            ) );
        array_push($elements, $element);
        $elementCounter ++;
        
        //ppm black
        $element = new Zend_Form_Element_Text('ppm_black');
        $element->setLabel('PPM Black:')
            ->setAttrib('maxlength', 4)
            ->setAttrib('size', 8)
            ->addValidator(new Zend_Validate_Float())
            ->setOrder($elementCounter)
            ->setDecorators(array (
                'ViewHelper', 
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'ppm_black-element' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) )
            ) );
        array_push($elements, $element);
        $element->getValidator('Float')->setMessage('Please enter a number.');
        $elementCounter ++;
        
        //ppm color
        $element = new Zend_Form_Element_Text('ppm_color');
        $element->setLabel('PPM Color:')
            ->setAttrib('maxlength', 4)
            ->setAttrib('size', 8)
            ->addValidator(new Zend_Validate_Float())
            ->setOrder($elementCounter)
            ->setDecorators(array (
                'ViewHelper', 
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'ppm_color-element' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) )
            ) );
        array_push($elements, $element);
        $element->getValidator('Float')->setMessage('Please enter a number.');
        $elementCounter ++;
        
        //duty cycle
        $element = new Zend_Form_Element_Text('duty_cycle');
        $element->setLabel('Duty Cycle:')
            ->setAttrib('maxlength', 8)
            ->setAttrib('size', 8)
            ->addValidator(new Zend_Validate_Float())
            ->setOrder($elementCounter)
            ->setDecorators(array (
                'ViewHelper', 
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'duty_cycle-element' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) )
            ) );
        array_push($elements, $element);
        $elementCounter ++;
        
        //watts_power_normal
        $element = new Zend_Form_Element_Text('watts_power_normal');
        $element->setLabel('Watts Power Normal:')
            ->setAttrib('maxlength', 6)
            ->setAttrib('size', 8)
            ->addValidator(new Zend_Validate_Float())
            ->setOrder($elementCounter)
            ->setDecorators(array (
                'ViewHelper', 
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'watts_power_normal-element' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) )
            ) );
        array_push($elements, $element);
        $elementCounter ++;
        
        //watts_power_idle
        $element = new Zend_Form_Element_Text('watts_power_idle');
        $element->setLabel('Watts Power Idle:')
            ->setAttrib('maxlength', 6)
            ->setAttrib('size', 8)
            ->addValidator(new Zend_Validate_Float())
            ->setOrder($elementCounter)
            ->setDecorators(array (
                'ViewHelper', 
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'watts_power_idle-element' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) )
            ) );
        array_push($elements, $element);
        $elementCounter ++;

        //device_price
        $element = new Zend_Form_Element_Text('device_price');
        $element->setLabel('Printer Price:')
            ->setAttrib('size', 6)
            ->setAttrib('maxlength', 10)
            ->setAttrib('id', 'device_price')
            ->addValidator(new Zend_Validate_Float())
            ->setOrder($elementCounter)
            ->setAttrib('style', 'text-align: right')
            ->setDescription('$')
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'device_price-element' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) ) 
            ));
        $element->getValidator('Float')->setMessage('Please enter a number.');
        array_push($elements, $element);
        $elementCounter ++;
        
        //toner_config
        $element = new Zend_Form_Element_Select('toner_config');
        $element->setLabel('* Toner Config:')
            ->setRequired(true)
            ->addErrorMessage("You must select a toner config.")
            ->setOrder($elementCounter)
            ->setAttrib('id', 'toner_config')
            ->setDecorators(array (
                'ViewHelper', 
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'toner_config-element' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) )
            ) );
        array_push($elements, $element);
        $elementCounter ++;
        
        //is leased flag
        $element = new Zend_Form_Element_Checkbox('is_leased');
        $element->setLabel('Leased:')
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
        
        //*****************************************************************
        //OEM TONERS
        //*****************************************************************
        
        $this->addElement('hidden', 'oem_title', array (
            'description' => 'OEM Toners:', 
            'ignore' => true, 
            'decorators' => array ( array ( 'Description', array ( 'escape' => false ) ) ) 
        ));
        $this->getElement('oem_title')
            ->addDecorator('ViewHelper')
            ->addDecorator('Errors')
            ->addDecorator('Description', array ( 'tag' => '<h5>', 'class' => 'forms_header' ))
            ->addDecorator('HtmlTag', array ( 'id' => 'oem_title-element' ))
            ->addDecorator('Label')
            ->setOrder($elementCounter);
        $descriptionDecorator = $this->getElement('oem_title')->getDecorator('Description');
        $descriptionDecorator->setEscape(false);
        array_push($elements, $descriptionDecorator);
        $elementCounter ++;
        
        //black_toner_SKU
        $element = new Zend_Form_Element_Text('black_toner_SKU');
        $element->setLabel('* Black SKU / Price / Yield:')
            ->setAttrib('size', 10)
            ->setAttrib('maxlength', 10)
            ->setAttrib('id', 'black_toner_SKU')
            ->setOrder($elementCounter)
            ->setDecorators(array (
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'black_toner_SKU-element', 'style' => 'display: inline' ) ), 
                array ( 'Label', array ( 'tag' => 'dt' ) ) 
            ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //black_toner_price
        $element = new Zend_Form_Element_Text('black_toner_price');
        $element->setAttrib('size', 6)
            ->setAttrib('maxlength', 10)
            ->setAttrib('id', 'black_toner_price')
            ->addValidator(new Zend_Validate_Float())
            ->setOrder($elementCounter)
            ->setAttrib('style', 'text-align: right')
            ->setDescription('$')
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'black_toner_price-element', 'style' => 'display: inline' ) ) 
            ));
        $element->getValidator('Float')->setMessage('Please enter a number.');
        array_push($elements, $element);
        $elementCounter ++;
        
        //black_toner_yield
        $element = new Zend_Form_Element_Text('black_toner_yield');
        $element->setAttrib('size', 6)
            ->setAttrib('maxlength', 5)
            ->setAttrib('id', 'black_toner_yield')
            ->addValidator(new Zend_Validate_Int())
            ->setOrder($elementCounter)
            ->setAttrib('style', 'text-align: right')
            ->setDecorators(array (
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'black_toner_yield-element', 'style' => 'display: inline' ) ) 
            ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //cyan_toner_SKU
        $element = new Zend_Form_Element_Text('cyan_toner_SKU');
        $element->setLabel('* Cyan SKU / Price / Yield:')
            ->setAttrib('size', 10)
            ->setAttrib('maxlength', 10)
            ->setAttrib('id', 'cyan_toner_SKU')
            ->setOrder($elementCounter)
            ->setDecorators(array (
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd',  'id' => 'cyan_toner_SKU-element',  'style' => 'display: inline' ) ), 
                array ( 'Label', array ( 'tag' => 'dt' ) ) 
            ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //cyan_toner_price
        $element = new Zend_Form_Element_Text('cyan_toner_price');
        $element->setAttrib('size', 6)
            ->setAttrib('maxlength', 10)
            ->setAttrib('id', 'cyan_toner_price')
            ->addValidator(new Zend_Validate_Float())
            ->setOrder($elementCounter)
            ->setAttrib('style', 'text-align: right')
            ->setDescription('$')
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'cyan_toner_price-element', 'style' => 'display: inline' ) ) 
            ));
        $element->getValidator('Float')->setMessage('Please enter a number.');
        array_push($elements, $element);
        $elementCounter ++;
        
        //cyan_toner_yield
        $element = new Zend_Form_Element_Text('cyan_toner_yield');
        $element->setAttrib('size', 6)
            ->setAttrib('maxlength', 5)
            ->setAttrib('id', 'cyan_toner_yield')
            ->addValidator(new Zend_Validate_Int())
            ->setOrder($elementCounter)
            ->setAttrib('style', 'text-align: right')
            ->setDecorators(array (
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'cyan_toner_yield-element', 'style' => 'display: inline' ) ) 
            ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //magenta_toner_SKU
        $element = new Zend_Form_Element_Text('magenta_toner_SKU');
        $element->setLabel('* Magenta SKU / Price / Yield:')
            ->setAttrib('size', 10)
            ->setAttrib('maxlength', 10)
            ->setAttrib('id', 'magenta_toner_SKU')
            ->setOrder($elementCounter)
            ->setDecorators(array (
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'magenta_toner_SKU-element', 'style' => 'display: inline' ) ), 
                array ( 'Label', array ( 'tag' => 'dt' ) ) 
            ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //magenta_toner_price
        $element = new Zend_Form_Element_Text('magenta_toner_price');
        $element->setAttrib('size', 6)
            ->setAttrib('maxlength', 10)
            ->setAttrib('id', 'magenta_toner_price')
            ->addValidator(new Zend_Validate_Float())
            ->setOrder($elementCounter)
            ->setAttrib('style', 'text-align: right')
            ->setDescription('$')
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'magenta_toner_price-element', 'style' => 'display: inline' ) ) 
            ));
        $element->getValidator('Float')->setMessage('Please enter a number.');
        array_push($elements, $element);
        $elementCounter ++;
        
        //magenta_toner_yield
        $element = new Zend_Form_Element_Text('magenta_toner_yield');
        $element->setAttrib('size', 6)
            ->setAttrib('maxlength', 5)
            ->setAttrib('id', 'magenta_toner_yield')
            ->addValidator(new Zend_Validate_Int())
            ->setOrder($elementCounter)
            ->setAttrib('style', 'text-align: right')
            ->setDecorators(array (
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'magenta_toner_yield-element', 'style' => 'display: inline' ) ) 
            ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //yellow_toner_SKU
        $element = new Zend_Form_Element_Text('yellow_toner_SKU');
        $element->setLabel('* Yellow SKU / Price / Yield:')
            ->setAttrib('size', 10)
            ->setAttrib('maxlength', 10)
            ->setAttrib('id', 'yellow_toner_SKU')
            ->setOrder($elementCounter)
            ->setDecorators(array (
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'yellow_toner_SKU-element', 'style' => 'display: inline' ) ), 
                array ( 'Label', array ( 'tag' => 'dt' ) ) 
            ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //yellow_toner_price
        $element = new Zend_Form_Element_Text('yellow_toner_price');
        $element->setAttrib('size', 6)
            ->setAttrib('maxlength', 10)
            ->setAttrib('id', 'yellow_toner_price')
            ->addValidator(new Zend_Validate_Float())
            ->setOrder($elementCounter)
            ->setAttrib('style', 'text-align: right')
            ->setDescription('$')
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'yellow_toner_price-element', 'style' => 'display: inline' ) ) 
            ));
        $element->getValidator('Float')->setMessage('Please enter a number.');
        array_push($elements, $element);
        $elementCounter ++;
        
        //yellow_toner_yield
        $element = new Zend_Form_Element_Text('yellow_toner_yield');
        $element->setAttrib('size', 6)
            ->setAttrib('maxlength', 5)
            ->setAttrib('id', 'yellow_toner_yield')
            ->addValidator(new Zend_Validate_Int())
            ->setOrder($elementCounter)
            ->setAttrib('style', 'text-align: right')
            ->setDecorators(array (
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'yellow_toner_yield-element', 'style' => 'display: inline' ) ) 
            ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //3color_toner_SKU
        $element = new Zend_Form_Element_Text('3color_toner_SKU');
        $element->setLabel('* 3 Color SKU / Price / Yield:')
            ->setAttrib('size', 10)
            ->setAttrib('maxlength', 10)
            ->setAttrib('id', '3color_toner_SKU')
            ->setOrder($elementCounter)
            ->setDecorators(array (
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => '3color_toner_SKU-element', 'style' => 'display: inline' ) ), 
                array ( 'Label', array ( 'tag' => 'dt' ) ) 
            ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //3color_toner_price
        $element = new Zend_Form_Element_Text('3color_toner_price');
        $element->setAttrib('size', 6)
            ->setAttrib('maxlength', 10)
            ->setAttrib('id', '3color_toner_price')
            ->addValidator(new Zend_Validate_Float())
            ->setOrder($elementCounter)
            ->setAttrib('style', 'text-align: right')
            ->setDescription('$')
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => '3color_toner_price-element', 'style' => 'display: inline' ) ) 
            ));
        $element->getValidator('Float')->setMessage('Please enter a number.');
        array_push($elements, $element);
        $elementCounter ++;
        
        //3color_toner_yield
        $element = new Zend_Form_Element_Text('3color_toner_yield');
        $element->setAttrib('size', 6)
            ->setAttrib('maxlength', 5)
            ->setAttrib('id', '3color_toner_yield')
            ->addValidator(new Zend_Validate_Int())
            ->setOrder($elementCounter)
            ->setAttrib('style', 'text-align: right')
            ->setDecorators(array (
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => '3color_toner_yield-element', 'style' => 'display: inline' ) ) 
            ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //4color_toner_SKU
        $element = new Zend_Form_Element_Text('4color_toner_SKU');
        $element->setLabel('* 4 Color SKU / Price / Yield:')
            ->setAttrib('size', 10)
            ->setAttrib('maxlength', 10)
            ->setAttrib('id', '4color_toner_SKU')
            ->setOrder($elementCounter)
            ->setDecorators(array (
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => '4color_toner_SKU-element', 'style' => 'display: inline' ) ), 
                array ( 'Label', array ( 'tag' => 'dt' ) ) 
            ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //4color_toner_price
        $element = new Zend_Form_Element_Text('4color_toner_price');
        $element->setAttrib('size', 6)
            ->setAttrib('maxlength', 10)
            ->setAttrib('id', '4color_toner_price')
            ->addValidator(new Zend_Validate_Float())
            ->setOrder($elementCounter)
            ->setAttrib('style', 'text-align: right')
            ->setDescription('$')
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => '4color_toner_price-element', 'style' => 'display: inline' ) ) 
            ));
        $element->getValidator('Float')->setMessage('Please enter a number.');
        array_push($elements, $element);
        $elementCounter ++;
        
        //4color_toner_yield
        $element = new Zend_Form_Element_Text('4color_toner_yield');
        $element->setAttrib('size', 6)
            ->setAttrib('maxlength', 5)
            ->setAttrib('id', '4color_toner_yield')
            ->addValidator(new Zend_Validate_Int())
            ->setOrder($elementCounter)
            ->setAttrib('style', 'text-align: right')
            ->setDecorators(array (
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => '4color_toner_yield-element', 'style' => 'display: inline' ) ) 
            ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //*****************************************************************
        //COMP TONERS
        //*****************************************************************
        
        $this->addElement('hidden', 'comp_title', array (
            'description' => 'Compatible Toners:', 
            'ignore' => true, 
            'decorators' => array ( array ( 'Description', array ( 'escape' => false ) ) ) 
        ));
        $this->getElement('comp_title')
            ->addDecorator('ViewHelper')
            ->addDecorator('Errors')
            ->addDecorator('Description', array ( 'tag' => '<h5>', 'class' => 'forms_header' ))
            ->addDecorator('HtmlTag', array ( 'id' => 'comp_title-element' ))
            ->addDecorator('Label')
            ->setOrder($elementCounter);
        $descriptionDecorator = $this->getElement('comp_title')->getDecorator('Description');
        $descriptionDecorator->setEscape(false);
        array_push($elements, $descriptionDecorator);
        $elementCounter ++;
        
        //black_toner_SKU
        $element = new Zend_Form_Element_Text('black_comp_SKU');
        $element->setLabel('Black SKU / Price / Yield:')
            ->setAttrib('size', 10)
            ->setAttrib('maxlength', 10)
            ->setAttrib('id', 'black_comp_SKU')
            ->setOrder($elementCounter)
            ->setDecorators(array (
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'black_comp_SKU-element', 'style' => 'display: inline' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) ) 
            ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //black_comp_price
        $element = new Zend_Form_Element_Text('black_comp_price');
        $element->setAttrib('size', 6)
            ->setAttrib('maxlength', 10)
            ->setAttrib('id', 'black_comp_price')
            ->addValidator(new Zend_Validate_Float())
            ->setOrder($elementCounter)
            ->setAttrib('style', 'text-align: right')
            ->setDescription('$')
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'black_comp_price-element', 'style' => 'display: inline' ) ) 
            ));
        $element->getValidator('Float')->setMessage('Please enter a number.');
        array_push($elements, $element);
        $elementCounter ++;
        
        //black_comp_yield
        $element = new Zend_Form_Element_Text('black_comp_yield');
        $element->setAttrib('size', 6)
            ->setAttrib('maxlength', 5)
            ->setAttrib('id', 'black_comp_yield')
            ->addValidator(new Zend_Validate_Int())
            ->setOrder($elementCounter)
            ->setAttrib('style', 'text-align: right')
            ->setDecorators(array (
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'black_comp_yield-element', 'style' => 'display: inline' ) ) 
            ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //cyan_comp_SKU
        $element = new Zend_Form_Element_Text('cyan_comp_SKU');
        $element->setLabel('Cyan SKU / Price / Yield:')
            ->setAttrib('size', 10)
            ->setAttrib('maxlength', 10)
            ->setAttrib('id', 'cyan_comp_SKU')
            ->setOrder($elementCounter)
            ->setDecorators(array (
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd',  'id' => 'cyan_comp_SKU-element',  'style' => 'display: inline' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) ) 
            ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //cyan_comp_price
        $element = new Zend_Form_Element_Text('cyan_comp_price');
        $element->setAttrib('size', 6)
            ->setAttrib('maxlength', 10)
            ->setAttrib('id', 'cyan_comp_price')
            ->addValidator(new Zend_Validate_Float())
            ->setOrder($elementCounter)
            ->setAttrib('style', 'text-align: right')
            ->setDescription('$')
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'cyan_comp_price-element', 'style' => 'display: inline' ) ) 
            ));
        $element->getValidator('Float')->setMessage('Please enter a number.');
        array_push($elements, $element);
        $elementCounter ++;
        
        //cyan_comp_yield
        $element = new Zend_Form_Element_Text('cyan_comp_yield');
        $element->setAttrib('size', 6)
            ->setAttrib('maxlength', 5)
            ->setAttrib('id', 'cyan_comp_yield')
            ->addValidator(new Zend_Validate_Int())
            ->setOrder($elementCounter)
            ->setAttrib('style', 'text-align: right')
            ->setDecorators(array (
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'cyan_comp_yield-element', 'style' => 'display: inline' ) ) 
            ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //magenta_comp_SKU
        $element = new Zend_Form_Element_Text('magenta_comp_SKU');
        $element->setLabel('Magenta SKU / Price / Yield:')
            ->setAttrib('size', 10)
            ->setAttrib('maxlength', 10)
            ->setAttrib('id', 'magenta_comp_SKU')
            ->setOrder($elementCounter)
            ->setDecorators(array (
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'magenta_comp_SKU-element', 'style' => 'display: inline' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) ) 
            ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //magenta_comp_price
        $element = new Zend_Form_Element_Text('magenta_comp_price');
        $element->setAttrib('size', 6)
            ->setAttrib('maxlength', 10)
            ->setAttrib('id', 'magenta_comp_price')
            ->addValidator(new Zend_Validate_Float())
            ->setOrder($elementCounter)
            ->setAttrib('style', 'text-align: right')
            ->setDescription('$')
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'magenta_comp_price-element', 'style' => 'display: inline' ) ) 
            ));
        $element->getValidator('Float')->setMessage('Please enter a number.');
        array_push($elements, $element);
        $elementCounter ++;
        
        //magenta_comp_yield
        $element = new Zend_Form_Element_Text('magenta_comp_yield');
        $element->setAttrib('size', 6)
            ->setAttrib('maxlength', 5)
            ->setAttrib('id', 'magenta_comp_yield')
            ->addValidator(new Zend_Validate_Int())
            ->setOrder($elementCounter)
            ->setAttrib('style', 'text-align: right')
            ->setDecorators(array (
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'magenta_comp_yield-element', 'style' => 'display: inline' ) ) 
            ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //yellow_comp_SKU
        $element = new Zend_Form_Element_Text('yellow_comp_SKU');
        $element->setLabel('Yellow SKU / Price / Yield:')
            ->setAttrib('size', 10)
            ->setAttrib('maxlength', 10)
            ->setAttrib('id', 'yellow_comp_SKU')
            ->setOrder($elementCounter)
            ->setDecorators(array (
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'yellow_comp_SKU-element', 'style' => 'display: inline' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) ) 
            ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //yellow_comp_price
        $element = new Zend_Form_Element_Text('yellow_comp_price');
        $element->setAttrib('size', 6)
            ->setAttrib('maxlength', 10)
            ->setAttrib('id', 'yellow_comp_price')
            ->addValidator(new Zend_Validate_Float())
            ->setOrder($elementCounter)
            ->setAttrib('style', 'text-align: right')
            ->setDescription('$')
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'yellow_comp_price-element', 'style' => 'display: inline' ) ) 
            ));
        $element->getValidator('Float')->setMessage('Please enter a number.');
        array_push($elements, $element);
        $elementCounter ++;
        
        //yellow_comp_yield
        $element = new Zend_Form_Element_Text('yellow_comp_yield');
        $element->setAttrib('size', 6)
            ->setAttrib('maxlength', 5)
            ->setAttrib('id', 'yellow_comp_yield')
            ->addValidator(new Zend_Validate_Int())
            ->setOrder($elementCounter)
            ->setAttrib('style', 'text-align: right')
            ->setDecorators(array (
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'yellow_comp_yield-element', 'style' => 'display: inline' ) ) 
            ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //3color_comp_SKU
        $element = new Zend_Form_Element_Text('3color_comp_SKU');
        $element->setLabel('3 Color SKU / Price / Yield:')
            ->setAttrib('size', 10)
            ->setAttrib('maxlength', 10)
            ->setAttrib('id', '3color_comp_SKU')
            ->setOrder($elementCounter)
            ->setDecorators(array (
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => '3color_comp_SKU-element', 'style' => 'display: inline' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) ) 
            ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //3color_comp_price
        $element = new Zend_Form_Element_Text('3color_comp_price');
        $element->setAttrib('size', 6)
            ->setAttrib('maxlength', 10)
            ->setAttrib('id', '3color_comp_price')
            ->addValidator(new Zend_Validate_Float())
            ->setOrder($elementCounter)
            ->setAttrib('style', 'text-align: right')
            ->setDescription('$')
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => '3color_comp_price-element', 'style' => 'display: inline' ) ) 
            ));
        $element->getValidator('Float')->setMessage('Please enter a number.');
        array_push($elements, $element);
        $elementCounter ++;
        
        //3color_comp_yield
        $element = new Zend_Form_Element_Text('3color_comp_yield');
        $element->setAttrib('size', 6)
            ->setAttrib('maxlength', 5)
            ->setAttrib('id', '3color_comp_yield')
            ->addValidator(new Zend_Validate_Int())
            ->setOrder($elementCounter)
            ->setAttrib('style', 'text-align: right')
            ->setDecorators(array (
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => '3color_comp_yield-element', 'style' => 'display: inline' ) ) 
            ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //4color_comp_SKU
        $element = new Zend_Form_Element_Text('4color_comp_SKU');
        $element->setLabel('4 Color SKU / Price / Yield:')
            ->setAttrib('size', 10)
            ->setAttrib('maxlength', 10)
            ->setAttrib('id', '4color_comp_SKU')
            ->setOrder($elementCounter)
            ->setDecorators(array (
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => '4color_comp_SKU-element', 'style' => 'display: inline' ) ), 
                array ( 'Label', array ( 'tag' => 'dt', 'class' => 'forms_label' ) ) 
            ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //4color_comp_price
        $element = new Zend_Form_Element_Text('4color_comp_price');
        $element->setAttrib('size', 6)
            ->setAttrib('maxlength', 10)
            ->setAttrib('id', '4color_comp_price')
            ->addValidator(new Zend_Validate_Float())
            ->setOrder($elementCounter)
            ->setAttrib('style', 'text-align: right')
            ->setDescription('$')
            ->setDecorators(array (
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => '4color_comp_price-element', 'style' => 'display: inline' ) ) 
            ));
        $element->getValidator('Float')->setMessage('Please enter a number.');
        array_push($elements, $element);
        $elementCounter ++;
        
        //4color_comp_yield
        $element = new Zend_Form_Element_Text('4color_comp_yield');
        $element->setAttrib('size', 6)
            ->setAttrib('maxlength', 5)
            ->setAttrib('id', '4color_comp_yield')
            ->addValidator(new Zend_Validate_Int())
            ->setOrder($elementCounter)
            ->setAttrib('style', 'text-align: right')
            ->setDecorators(array (
                'ViewHelper', 
                'Errors', 
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => '4color_comp_yield-element', 'style' => 'display: inline' ) ) 
            ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //request support link
        $element = new Zend_Form_Element_Text('request_support');
        $element->setLabel('Request support for this Printer')
            ->setAttrib('style','display: none')
            ->setDecorators(array(
                array ( 'Description', array ( 'escape' => false, 'tag' => false ) ),
                'ViewHelper',
                'Errors',
                array ( 'HtmlTag', array ( 'tag' => 'dd', 'id' => 'request_support-element', 'style' => 'display: inline' ) ),
                array ( 'Label', array ( 'tag' => 'dt', 'onclick' => 'javascript: toggle_request(true);', 'style' => 'text-decoration: underline; color: blue;' ) )
              ))
            ->setOrder($elementCounter);
        array_push($elements, $element);
        $elementCounter ++;
        
        //save button
        $element = new Zend_Form_Element_Submit('save_device', array ('disableLoadDefaultDecorators' => true));
        $element->setLabel('Save')
            ->setOrder($elementCounter)
            ->setDecorators(array (
                'ViewHelper', 
                'Errors', 
                array ( array ( 'dd' => 'HtmlTag' ), array ( 'tag' => 'dd', 'class' => 'botMenu', 'style' => 'clear: both;' ) ) 
            ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //cancel button
        $element = new Zend_Form_Element_Button('cancel_button');
        $element->setLabel('Cancel')
            ->setOrder($elementCounter)
            ->setAttrib('onClick', 'javascript: document.location.href = "../data/devicemapping?grid="+document.getElementById("grid").value;')
            ->setDecorators(array (
                'ViewHelper', 
                'Errors', 
                array ( array ( 'dd' => 'HtmlTag' ), array ( 'tag' => 'dd', 'class' => 'botMenu' ) ) 
            ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //add all defined elements to the form
        $this->addElements($elements);
    
    } //end function __construct

    
    public function set_validation ($data)
    {
        //CONDITIONAL VALIDATION CAN BE DONE HERE
        //this was conditionally making part type required or not depending on the leased flag
        //but part type has been removed. Left this function here for future use if needed.
        
    	return $data;
    }
    
}
?>

