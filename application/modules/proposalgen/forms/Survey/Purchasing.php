<?php

class Proposalgen_Form_Survey_Purchasing extends Proposalgen_Form_Survey_BaseSurveyForm
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
        $this->setAttrib('class', 'proposalForm form-vertical');
        
        $numb_vendors = new Zend_Form_Element_Text('numb_vendors');
        $numb_vendors->setRequired(true)
            ->setAttrib('maxlength', 3)
            ->setAttrib('class', 'span1')
            ->setAutoInsertNotEmptyValidator(false)
            ->addValidator('greaterThan', true, array (
                'min' => 0 
        ));
        $vendorsQst = "How many vendors do you currently deal with for printer supplies, service and hardware?";
        $numb_vendors->setLabel($vendorsQst);
        $numb_vendors->getValidator('greaterThan')->setMessage('Must be greater than zero');
        $this->addElement($numb_vendors);
        
        // **********************************
        $inkTonerOrderRadio = new Zend_Form_Element_Radio('inkTonerOrderRadio');
        $inkTonerOrderRadio->addMultiOption('Daily', 'Daily')
            ->addMultiOption('Weekly', 'Weekly')
            ->addMultiOption('Times per month', 'Custom');
        $inkTonerOrderRadioQst = "How many times per month does your organization order printer supplies?";
        $inkTonerOrderRadio->setLabel($inkTonerOrderRadioQst);
        
        $this->addElement($inkTonerOrderRadio);
        
        $ink_toner_order = new Zend_Form_Element_Text('numb_monthlyOrders');
        $ink_toner_order->setAttrib('maxlength', 3)
            ->setAttrib('class', 'span1')
            ->setAllowEmpty(false)
            ->addValidator(new Custom_Validate_FieldDependsOnValue('inkTonerOrderRadio', 'Times per month', array (
                new Zend_Validate_NotEmpty(), 
                new Zend_Validate_Digits() 
        )), true)
            ->setDescription('times per month');
        
        $this->addElement($ink_toner_order);
        
        parent::init();
    }

    /**
     * Validate the form
     *
     * @param array $data            
     * @return boolean
     */
    public function isValid ($data)
    {
        if (! is_array($data))
        {
            require_once 'Zend/Form/Exception.php';
            throw new Zend_Form_Exception(__METHOD__ . ' expects an array');
        }
        
        // Validate our radio buttons
        if (true || $data ['name'] == 'xyz')
        {
            //$this->getElement('toner_cost_radio')->addError('Wrong name provided!');
        }
        return parent::isValid($data);
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators(array (
                array (
                        'ViewScript', 
                        array (
                                'viewScript' => 'survey/form/purchasing.phtml' 
                        ) 
                ) 
        ));
    }
}