<?php

class Admin_Form_Toner extends EasyBib_Form
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
         *  SKU
        */
        $this->addElement('text', 'sku', array (
                'label' => 'SKU:', 
                'required' => true, 
                'class' => 'span2',
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
         *  Cost
         */
        $this->addElement('text', 'cost', array (
                'label' => 'Cost:', 
                'required' => true, 
                'class' => 'span1',
                'filters' => array (
                        'StringTrim', 
                        'StripTags' 
                ), 
                'validators' => array (
                        'Float' 
                ) 
        ));
        
        /*
         *  Yield
         */
        $this->addElement('text', 'yield', array (
                'label' => 'Yield:', 
                'required' => true, 
                'class' => 'span1',
                'filters' => array (
                        'StringTrim', 
                        'StripTags' 
                ), 
                'validators' => array (
                        'Float' 
                ) 
        ));
        
        /*
         * Color
        */
        $colors = array ();
        /* @var $color Proposalgen_Model_Toner_Colors */
        foreach ( Proposalgen_Model_Mapper_TonerColor::getInstance()->fetchAll() as $color )
        {
            $colors [$color->getTonerColorId()] = $color->getTonerColorName();
        }
        
        $this->addElement('select', 'toner_color_id', array (
                'label' => 'Color:',
                'class' => 'span2',
                'multiOptions' => $colors
        ));
        
        /*
         * Part Type
        */
        $parttypes = array ();
        /* @var $parttypes Proposalgen_Model_PartType */
        foreach ( Proposalgen_Model_Mapper_PartType::getInstance()->fetchAll() as $parttype)
        {
            $parttypes [$parttype->getPartTypeId()] = $parttype->getTypeName();
        }
        
        $this->addElement('select', 'part_type_id', array (
                'label' => 'Part Type:',
                'class' => 'span2',
                'multiOptions' => $parttypes
        ));
        
        // Add the submit button
        $this->addElement('submit', 'submit', array (
                'label' => 'Save' 
        ));
        
        // Add the cancel button
        $this->addElement('submit', 'cancel', array (
                'label' => 'Cancel' 
        ));
        
        EasyBib_Form_Decorator::setFormDecorator($this, EasyBib_Form_Decorator::BOOTSTRAP, 'submit', 'cancel');
    }
}