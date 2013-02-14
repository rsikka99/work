<?php

class Proposalgen_Form_Survey extends Proposalgen_Form_Survey_BaseSurveyForm
{

    public static $repairTimeOptions = array(
        '0.5' => 'Less than a day',
        '1'   => 'One day',
        '2'   => 'Two days',
        '3'   => 'Three to five days',
        '5'   => 'More than five days'
    );
    public static $volumeOptions = array(
        '5'  => 'Less than 10%',
        '18' => '10% to 25%',
        '38' => '26% to 50%',
        '75' => 'More than 50%'
    );

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');
        /**
         * Add class to form for label alignment
         *
         * - Vertical .form-vertical (not required)    Stacked, left-aligned labels
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

        $multiOptions = array(
            'guess'                   => 'I don\'t know',
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
            ->addValidator(new Custom_Validate_FieldDependsOnValue('toner_cost_radio', 'I know the exact amount', array(
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
            ->addValidator(new Custom_Validate_FieldDependsOnValue('labor_cost_radio', 'I know the exact amount', array(
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
            ->addValidator('float', false, array(
                                                'messages' => array(
                                                    'notFloat' => 'Please enter a valid number.'
                                                )
                                           ))
            ->setValue(number_format(50, 2))
            ->setDescription($this->currency->getSymbol())
            ->addValidator('greaterThan', true, array(
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
            ->addValidator('float', false, array(
                                                'messages' => array(
                                                    'notFloat' => 'Please enter a valid number.'
                                                )
                                           ))
            ->setValue(number_format(40, 2))
            ->setDescription($this->currency->getSymbol())
            ->addValidator('greaterThan', true, array(
                                                     'min' => 0
                                                ));
        $itQst = "What is the average hourly rate for IT personnel involved in managing printing devices? The average rate is $40/hour.";
        $it_hourlyRate->setLabel($itQst);
        $this->addElement($it_hourlyRate);

        $numb_vendors = new Zend_Form_Element_Text('numb_vendors');
        $numb_vendors->setRequired(true)
            ->setAttrib('maxlength', 3)
            ->setAttrib('class', 'span1')
            ->setAutoInsertNotEmptyValidator(false)
            ->addValidator('greaterThan', true, array(
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
            ->addValidator(new Custom_Validate_FieldDependsOnValue('inkTonerOrderRadio', 'Times per month', array(
                                                                                                                 new Zend_Validate_NotEmpty(),
                                                                                                                 new Zend_Validate_Digits()
                                                                                                            )), true)
            ->setDescription('times per month');

        $this->addElement($ink_toner_order);


        $multiOptions = array(
            'guess'                   => 'I don\'t know',
            'I know the exact amount' => 'I know the exact amount'
        );

        $itHoursRadio = new Zend_Form_Element_Radio('itHoursRadio');
        $itHoursRadio->addMultiOptions($multiOptions);

        $this->addElement($itHoursRadio);
        $this->getElement('itHoursRadio')->setValue('I know the exact amount');

        $itHours = new Zend_Form_Element_Text('itHours');
        $itHours->setAttrib('maxlength', 3)
            ->setAttrib('class', 'span1')
            ->setDescription('hours')
            ->setAllowEmpty(false)
            ->addValidator(new Custom_Validate_FieldDependsOnValue('itHoursRadio', 'I know the exact amount', array(
                                                                                                                   new Zend_Validate_NotEmpty(),
                                                                                                                   new Zend_Validate_Float()
                                                                                                              )), true);
        $itHoursQst = "How many hours per week do IT personnel spend servicing and supporting printers? If you select \"I don't know\", an average of 15 minutes per week per printer will be used.";
        $itHours->setLabel($itHoursQst);
        $this->addElement($itHours);

        $monthlyBreakdown = new Zend_Form_Element_Text('monthlyBreakdown');
        $monthlyBreakdown->setAttrib('maxlength', 3)
            ->setAttrib('class', 'span1')
            ->setDescription('breakdowns per month')
            ->setAllowEmpty(false)
            ->addValidator(new Custom_Validate_FieldDependsOnValue('monthlyBreakdownRadio', 'I know the exact amount', array(
                                                                                                                            new Zend_Validate_NotEmpty(),
                                                                                                                            new Zend_Validate_Float()
                                                                                                                       )), true);
        $monthlyBreakdownQst = "How many times per month, on average, does your internal IT staff or an external service company need to be called to repair a broken printer in your fleet? If you select \"I don't know\", an average of 1 repair per month for every 20 printers will be used.";
        $monthlyBreakdown->setLabel($monthlyBreakdownQst);
        $this->addElement($monthlyBreakdown);

        $monthlyBreakdownRadio = new Zend_Form_Element_Radio('monthlyBreakdownRadio');
        $monthlyBreakdownRadio->addMultiOptions($multiOptions);
        $monthlyBreakdownRadio->setLabel($monthlyBreakdownQst);

        $this->addElement($monthlyBreakdownRadio);
        $this->getElement('monthlyBreakdownRadio')->setValue('I know the exact amount');

        $pageCoverage_BW = new Zend_Form_Element_Text('pageCoverage_BW');
        $pageCoverage_BW->setRequired(true)
            ->setLabel('Monochrome Coverage:')
            ->setAttrib('maxlength', 5)
            ->setAttrib('class', 'span1')
            ->setDescription('%')
            ->addValidator('float')
            ->addValidator(new Zend_Validate_Between(1, 100));

        $pageCoverage_BW->getValidator('Between')->setMessage('Must be between 1 and 100');
        $this->addElement($pageCoverage_BW);

        $pageCoverage_Colour = new Zend_Form_Element_Text('pageCoverage_Color');
        $pageCoverage_Colour->setRequired(true)
            ->setLabel('Color Coverage:')
            ->setAttrib('maxlength', 5)
            ->setAttrib('class', 'span1')
            ->setDescription('%')
            ->addValidator('float')
            ->addValidator(new Zend_Validate_Between(1, 100));
        $pageCoverage_Colour->getValidator('Between')->setMessage('Must be between 1 and 100');
        $this->addElement($pageCoverage_Colour);

        $volumeQuestion = "What percent of your print volume is done on inkjet and other desktop printers?";

        $printVolumeRadio = new Zend_Form_Element_Radio('printVolume');
        $printVolumeRadio->setLabel($volumeQuestion)
            ->setMultiOptions(self::$volumeOptions)
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addValidator('InArray', false, array(
                                                  array_keys(self::$volumeOptions)
                                             ));
        $this->addElement($printVolumeRadio);

        $repairTimeQuestion = "How long does it take, on average, for a printer to be fixed after it has broken down?";

        $repairTimeRadio = new Zend_Form_Element_Radio('repairTime');
        $repairTimeRadio->setLabel($repairTimeQuestion)
            ->setMultiOptions(self::$repairTimeOptions)
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addValidator('InArray', false, array(
                                                  array_keys(self::$repairTimeOptions)
                                             ));
        $this->addElement($repairTimeRadio);

        parent::init();
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators(array(
                                  array(
                                      'ViewScript',
                                      array(
                                          'viewScript' => 'survey/form/survey.phtml'
                                      )
                                  )
                             ));
    }
}