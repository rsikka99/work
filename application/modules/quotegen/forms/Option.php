<?php

class Quotegen_Form_Option extends EasyBib_Form
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
        $this->setAttrib('class', 'form-horizontal form-center-actions');
        
        $this->addElement('text', 'name', array (
                'label' => 'Name:', 
                'required' => true, 
                'maxlength' => 255, 
                'filters' => array (
                        'StringTrim', 
                        'StripTags' 
                ), 
                'validator' => 'StringLength', 
                'options' => array (
                        1, 
                        255 
                ) 
        ));
        
        $this->addElement('textarea', 'description', array (
                'label' => 'Description:', 
                'required' => true, 
                'style' => 'height: 100px', 
                'maxlength' => 255, 
                'filters' => array (
                        'StringTrim', 
                        'StripTags' 
                ), 
                'validator' => 'StringLength', 
                'options' => array (
                        1, 
                        255 
                ) 
        ));
        
        $this->addElement('text', 'cost', array (
                'label' => 'Price:', 
                'required' => true, 
                'maxlength' => 8, 
                'filters' => array (
                        'StringTrim', 
                        'StripTags' 
                ), 
                'validators' => array (
                        'Float' 
                ) 
        ));
        
        $this->addElement('text', 'sku', array (
                'label' => 'Sku:', 
                'required' => true, 
                'maxlength' => 255, 
                'filters' => array (
                        'StringTrim', 
                        'StripTags' 
                ), 
                'validator' => 'StringLength', 
                'options' => array (
                        1, 
                        255 
                ) 
        ));
        
        $optionCategoryCheckBox = new Zend_Form_Element_MultiCheckbox('categories', array (
                'label' => 'Categories:' 
        ));
        
        $categories = Quotegen_Model_Mapper_Category::getInstance()->fetchAll();
        /* @var $category Quotegen_Model_Category */
        foreach ( $categories as $category )
        {
            $optionCategoryCheckBox->addMultiOption($category->getId(), $category->getName());
        }
        
        if ($categories)
            $this->addElement($optionCategoryCheckBox);
            
            // Add the submit button
        $this->addElement('submit', 'submit', array (
                'ignore' => true, 
                'label' => 'Save' 
        ));
        
        // Add the cancel button
        $this->addElement('submit', 'cancel', array (
                'ignore' => true, 
                'label' => 'Cancel' 
        ));

        EasyBib_Form_Decorator::setFormDecorator($this, EasyBib_Form_Decorator::BOOTSTRAP, 'submit', 'cancel');
    }
}

?>