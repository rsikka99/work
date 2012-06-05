<?php

class Proposalgen_Form_Survey_Finance extends Proposalgen_Form_Survey_BaseSurveyForm
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
        $this->setAttrib('class', 'surveyForm form-vertical');
        
        $multiOptions = array (
                'guess' => 'I don\'t know', 
                'I know the exact amount' => 'I know the exact amount' 
        );
        
        /*
         * Ink And Toner cost
         */
        $inkAndTonerCostRadio = new Zend_Form_Element_Radio('toner_cost_radio');
        $inkAndTonerCostRadio->addMultiOptions($multiOptions);
        $inkAndTonerCostRadio->setValue('I know the exact amount');
        $this->addElement($inkAndTonerCostRadio);
        
        $toner_cost = new Zend_Form_Element_Text('toner_cost');
        $toner_cost->setAttrib('maxlength', 7)
            ->setAttrib('class', 'span2')
            ->setAttrib('placeholder', 'Enter Amount...')
            ->setDescription($this->currency->getSymbol())
            ->setAllowEmpty(false)
            ->addValidator(new Custom_Validate_FieldDependsOnValue('toner_cost_radio', 'I know the exact amount', array (
                new Zend_Validate_NotEmpty(), 
                new Zend_Validate_Float() 
        )), true);
        
        $tonerQst = "How much did you spend last year on ink and toner for your printer fleet (excluding the cost of leased copiers)?";
        $toner_cost->setLabel($tonerQst);
        $this->addElement($toner_cost);
        
        /*
         * Service/Labor Cost
         */
        $laborCostRadio = new Zend_Form_Element_Radio('labor_cost_radio');
        $laborCostRadio->addMultiOptions($multiOptions);
        $laborCostRadio->setValue('I know the exact amount');
        $this->addElement($laborCostRadio);
        
        $labor_cost = new Zend_Form_Element_Text('labor_cost');
        $labor_cost->setAttrib('maxlength', 7)
            ->setAttrib('class', 'span2')
            ->setAttrib('placeholder', 'Enter Amount...')
            ->setDescription($this->currency->getSymbol())
            ->setAllowEmpty(false)
            ->addValidator(new Custom_Validate_FieldDependsOnValue('labor_cost_radio', 'I know the exact amount', array (
                new Zend_Validate_NotEmpty(), 
                new Zend_Validate_Float() 
        )), true);
        $laborQst = "How much did you spend last year on service from outside vendors for your printer fleet, including maintenance kits, parts and labor (excluding the cost of leased copiers)? If you select \"I don't know\", an average of $200 per printer per year will be used.";
        $labor_cost->setLabel($laborQst);
        $this->addElement($labor_cost);
        
        /*
         * Average Purchase
         */
        $avg_purchase = new Zend_Form_Element_Text('avg_purchase');
        $avg_purchase->setRequired(true)
            ->setAttrib('class', 'span2')
            ->setAttrib('placeholder', 'Enter Amount...')
            ->setAttrib('maxlength', 7)
            ->addValidator('float', false, array (
                'messages' => array (
                        'notFloat' => 'Please enter a valid number.' 
                ) 
        ))
            ->setValue(number_format(50, 2))
            ->setDescription($this->currency->getSymbol())
            ->addValidator('greaterThan', true, array (
                'min' => 0 
        ));
        
        $purchaseQst = "What is the average cost for you to execute a supplies purchase order, including labor for purchasing and administrative personnel? The average cost is $50 per transaction.";
        $avg_purchase->setLabel($purchaseQst);
        $this->addElement($avg_purchase);
        
        /*
         * Hourly Rate
         */
        $it_hourlyRate = new Zend_Form_Element_Text('it_hourlyRate');
        $it_hourlyRate->setRequired(true)
            ->setAttrib('class', 'span2')
            ->setAttrib('placeholder', 'Enter Amount...')
            ->setAttrib('maxlength', 6)
            ->setRequired(true)
            ->addValidator('float', false, array (
                'messages' => array (
                        'notFloat' => 'Please enter a valid number.' 
                ) 
        ))
            ->setValue(number_format(40, 2))
            ->setDescription($this->currency->getSymbol())
            ->addValidator('greaterThan', true, array (
                'min' => 0 
        ));
        $itQst = "What is the average hourly rate for IT personnel involved in managing printing devices? The average rate is $40/hour.";
        $it_hourlyRate->setLabel($itQst);
        $this->addElement($it_hourlyRate);
        
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
                                'viewScript' => 'survey/form/finance.phtml' 
                        ) 
                ) 
        ));
    }
}