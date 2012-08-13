<?php

class Quotegen_Form_DeviceSetup extends EasyBib_Form
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
        $this->setAttrib('class', 'form-horizontal');

        /*
         * Manufacturer
         */
        $manufacturers = array ();
        /* @var $manufacturer Proposalgen_Model_Manufacturer */
        foreach ( Proposalgen_Model_Mapper_Manufacturer::getInstance()->fetchAllAvailableManufacturers() as $manufacturer )
        {
            $manufacturers [$manufacturer->getId()] = $manufacturer->getFullname();
        }
        
        $this->addElement('select', 'manufacturer_id', array (
                'label' => 'Manufacturer:', 
                'multiOptions' => $manufacturers 
        ));
        
        /*
         * Printer Model Name
         */
        $this->addElement('text', 'printer_model', array (
                'label' => 'Model Name:', 
                'required' => true, 
                'filters' => array (
                        'StringTrim', 
                        'StripTags' 
                ), 
                'validators' => array (
                        array (
                                'validator' => 'StringLength', 
                                'options' => array (
                                        1, 
                                        255 
                                ) 
                        ) 
                ) 
        ));
        
        /*
         * Is Quote Gen Device
         */
        $this->addElement('checkbox', 'can_sell', array (
                'label' => 'Can Sell Device:', 
                'filters' => array (
                        'Boolean' 
                ) 
        ));

        /*
         * SKU
        */
        $this->addElement('text', 'sku', array (
                'label' => 'SKU:',
                'required' => false,
                'filters' => array (
                        'StringTrim',
                        'StripTags'
                ),
                'allowEmpty' => false, 
                'validators' => array (
                        new Custom_Validate_FieldDependsOnValue('can_sell', '1', array (
                                new Zend_Validate_NotEmpty()
                        ),
                        array (
                                'validator' => 'StringLength',
                                'options' => array (
                                        1,
                                        255
                                )
                        )) 
                )
        ));
        
        /*
         * Toner Configuration
         */
        $tonerConfigs = array ();
        /* @var $tonerConfig Proposalgen_Model_TonerConfig */
        foreach ( Proposalgen_Model_Mapper_TonerConfig::getInstance()->fetchAll() as $tonerConfig )
        {
            $tonerConfigs [$tonerConfig->getTonerConfigId()] = $tonerConfig->getTonerConfigName();
        }
        
        $this->addElement('select', 'toner_config_id', array (
                'label' => 'Toner Configuration:', 
                'required' => true, 
                'multiOptions' => $tonerConfigs 
        ));
        
        /*
         * Is copier
         */
        $this->addElement('checkbox', 'is_copier', array (
                'label' => 'Is Copier:', 
                'filters' => array (
                        'Boolean' 
                ) 
        ));
        
        /*
         * Is fax
         */
        $this->addElement('checkbox', 'is_fax', array (
                'label' => 'Is Fax:', 
                'filters' => array (
                        'Boolean' 
                ) 
        ));
        
        /*
         * Is scanner
         */
        $this->addElement('checkbox', 'is_scanner', array (
                'label' => 'Is Scanner:', 
                'filters' => array (
                        'Boolean' 
                ) 
        ));
        
        /*
         * Is duplex
         */
        $this->addElement('checkbox', 'is_duplex', array (
                'label' => 'Is Duplex:', 
                'filters' => array (
                        'Boolean' 
                ) 
        ));
        
        /*
         * Printer Wattage (Running)
         */
        $this->addElement('text', 'watts_power_normal', array (
                'label' => 'Watts Power Normal:', 
                'filters' => array (
                        'StringTrim', 
                        'StripTags' 
                ), 
                'validators' => array (
                        'Int', 
                        array (
                                'validator' => 'Between', 
                                'options' => array (
                                        'min' => 1, 
                                        'max' => 5000 
                                ) 
                        ) 
                ) 
        ));
        
        /*
         * Printer Wattage (Idle)
         */
        $this->addElement('text', 'watts_power_idle', array (
                'label' => 'Watts Power Idle:', 
                'filters' => array (
                        'StringTrim', 
                        'StripTags' 
                ), 
                'append' => 'watts', 
                'dimension' => 1, 
                'validators' => array (
                        'Int', 
                        array (
                                'validator' => 'Between', 
                                'options' => array (
                                        'min' => 1, 
                                        'max' => 5000 
                                ) 
                        ) 
                ) 
        ));
        
        /*
         * Device Price
         */
        $this->addElement('text', 'cost', array (
                'label' => 'Device Cost:', 
                'prepend' => '$', 
                'dimension' => 1, 
                'filters' => array (
                        'StringTrim', 
                        'StripTags' 
                ), 
                'validators' => array (
                        'Float', 
                        array (
                                'validator' => 'Between', 
                                'options' => array (
                                        'min' => 1, 
                                        'max' => 5000 
                                ) 
                        ) 
                ) 
        ));
        
        /*
         * Service Cost Per Page
         */
        $this->addElement('text', 'service_cost_per_page', array (
                'label' => 'Service Cost Per Page:', 
                'filters' => array (
                        'StringTrim', 
                        'StripTags' 
                ), 
                'validators' => array (
                        'Float', 
                        array (
                                'validator' => 'Between', 
                                'options' => array (
                                        'min' => 0.0001, 
                                        'max' => 5 
                                ) 
                        ) 
                ) 
        ));
        
        /*
         * Launch Date
         * 
         * DOES NOT WORK IN VIEW SCRIPT?!?
         * 
        $minYear = 1950;
        $maxYear = date('Y') + 2;
        $launchDate = new ZendX_JQuery_Form_Element_DatePicker('launch_date');
        
        $launchDate->setLabel('Launch Date:')
            ->setJQueryParam('dateFormat', 'yy-mm-dd')
            ->setJqueryParam('timeFormat', 'hh:mm')
            ->setJQueryParam('changeYear', 'true')
            ->setJqueryParam('changeMonth', 'true')
            ->setJqueryParam('yearRange', "{$minYear}:{$maxYear}")
            ->setDescription('yyyy-mm-dd')
            ->addValidator(new My_Validate_DateTime('/\d{4}-\d{2}-\d{2}/'))
            ->setRequired(true);
	        $launchDate->addFilters(array (
	                'StringTrim', 
	                'StripTags' 
	        ));
        
        $this->addElement($launchDate);
         */
        $this->addElement('text', 'launch_date', array (
                'label' => 'Launch Date:',
                'required' => true, 
                'filters' => array (
                        'StringTrim',
                        'StripTags'
                ),
                'description' => 'yyyy-mm-dd',
                'validators' => array (
                        'Date',
                        )
        ));
        
        /*
         * Duty Cycle
         */
        $this->addElement('text', 'duty_cycle', array (
                'label' => 'Duty Cycle:', 
                'filters' => array (
                        'StringTrim', 
                        'StripTags' 
                ), 
                'validators' => array (
                        'Int', 
                        array (
                                'validator' => 'Between', 
                                'options' => array (
                                        'min' => 0, 
                                        'max' => 50000 
                                ) 
                        ) 
                ) 
        ));
        
        /*
         * Print Speed (Monochrome)
         */
        $this->addElement('text', 'ppm_black', array (
                'label' => 'Print Speed (Monochrome):', 
                'filters' => array (
                        'StringTrim', 
                        'StripTags' 
                ), 
                'validators' => array (
                        'Int', 
                        array (
                                'validator' => 'Between', 
                                'options' => array (
                                        'min' => 0, 
                                        'max' => 1000 
                                ) 
                        ) 
                ) 
        ));
        
        /*
         * Print Speed (Color)
         */
        $this->addElement('text', 'ppm_color', array (
                'label' => 'Print Speed (Color):', 
                'filters' => array (
                        'StringTrim', 
                        'StripTags' 
                ), 
                'validators' => array (
                        'Int', 
                        array (
                                'validator' => 'Between', 
                                'options' => array (
                                        'min' => 0, 
                                        'max' => 1000 
                                ) 
                        ) 
                ) 
        ));
        
        /*
         * Is leased
         */
        $this->addElement('checkbox', 'is_leased', array (
                'label' => 'Is Leased:', 
                'filters' => array (
                        'Boolean' 
                ) 
        ));
        
        /*
         * Leased Toner Yield
         */
        $this->addElement('text', 'leased_toner_yield', array (
                'label' => 'Leased Toner Yield:', 
                'filters' => array (
                        'StringTrim', 
                        'StripTags' 
                ), 
                'allowEmpty' => false, 
                'validators' => array (
                        new Custom_Validate_FieldDependsOnValue('is_leased', '1', array (
                                new Zend_Validate_NotEmpty(), 
                                new Zend_Validate_Int(), 
                                new Zend_Validate_Between(array (
                                        'min' => 0, 
                                        'max' => 1000 
                                )) 
                        )) 
                ) 
        ));
        
        // Add the submit button
        $this->addElement('submit', 'submit', array (
                'label' => 'Save' 
        ));
        // Add the cancel button
        $this->addElement('submit', 'cancel', array (
                'label' => 'Done' 
        ));
        
        EasyBib_Form_Decorator::setFormDecorator($this, EasyBib_Form_Decorator::BOOTSTRAP, 'submit', 'cancel');
    }
}
