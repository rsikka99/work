<?php

class Quotegen_Form_SelectOptions extends EasyBib_Form
{
    protected $_availableOptions;

    public function __construct ($availableOptions, $options = null)
    {
        $this->_availableOptions = $availableOptions;
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
        
        $optionList = array ();
        /* @var $option Quotegen_Model_Option */
        if (count($this->_availableOptions) > 0)
        {
            foreach ( $this->_availableOptions as $option )
            {
                $optionList [$option->id] = $option->name;
            }
            
            $this->addElement('multiCheckbox', 'options', array (
                    'label' => 'Options', 
                    'multiOptions' => $optionList 
            ));
            
            // Add the submit button
            $this->addElement('submit', 'submit', array (
                    'ignore' => true, 
                    'label' => 'Save' 
            ));
        }
        else
        {
            $paragraph = new My_Form_Element_Paragraph('test');
            $paragraph->setLabel('All available options have been added to the device already.');
            $this->addElement($paragraph);
        }
        // Add the cancel button
        $this->addElement('submit', 'cancel', array (
                'ignore' => true, 
                'label' => 'Cancel' 
        ));
        
        EasyBib_Form_Decorator::setFormDecorator($this, EasyBib_Form_Decorator::BOOTSTRAP, 'submit', 'cancel');
    }
}
