<?php

class Proposalgen_Form_Assessment_Survey extends Twitter_Bootstrap_Form_Horizontal
{

    protected $currency;
    protected $currencyRegex;

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

    public function __construct ($options = null)
    {
        $this->currency      = new Zend_Currency();
        $this->currencyRegex = '/^\d+(?:\.\d{0,2})?$/';

        // This runs, among other things, the init functions. Therefore it must come before anything that affects the form.
        parent::__construct($options);

        $this->addPrefixPath('Tangent_Form_Element', 'Tangent/Form/Element/', 'element');
    }

    public function init ()
    {
        $this->setAttrib('class', 'form-vertical');
        $this->_addClassNames("surveyForm form-center-actions");

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


        $multiOptions = array(
            'guess' => 'I don\'t know',
            'exact' => 'I know the exact amount'
        );

        /*
         * Ink And Toner cost
         */
        $inkAndTonerCostRadio = $this->createElement('radio', 'toner_cost_radio');
        $inkAndTonerCostRadio->setMultiOptions($multiOptions)
            ->setValue('I know the exact amount');
        $this->addElement($inkAndTonerCostRadio);

        $toner_cost = $this->createElement('text', 'toner_cost');
        $toner_cost->setAttrib('maxlength', 7)
            ->setAttrib('class', 'span2')
            ->setAttrib('placeholder', 'Enter Amount...')
            ->setDescription($this->currency->getSymbol())
            ->setAllowEmpty(false)
            ->addValidator(new Custom_Validate_FieldDependsOnValue('toner_cost_radio', 'exact', array(
                                                                                                                       new Zend_Validate_NotEmpty(),
                                                                                                                       new Zend_Validate_Float()
                                                                                                                  )), true);

        $tonerQst = "How much did you spend last year on ink and toner for your printer fleet (excluding the cost of leased copiers)?";
        $toner_cost->setLabel($tonerQst);
        $this->addElement($toner_cost);

        /*
         * Service/Labor Cost
         */
        $laborCostRadio = $this->createElement('radio', 'labor_cost_radio');
        $laborCostRadio->setMultiOptions($multiOptions);
        $laborCostRadio->setValue('I know the exact amount');
        $this->addElement($laborCostRadio);

        $labor_cost = $this->createElement('text', 'labor_cost');
        $labor_cost->setAttrib('maxlength', 7)
            ->setAttrib('class', 'span2')
            ->setAttrib('placeholder', 'Enter Amount...')
            ->setDescription($this->currency->getSymbol())
            ->setAllowEmpty(false)
            ->addValidator(new Custom_Validate_FieldDependsOnValue('labor_cost_radio', 'exact', array(
                                                                                                                       new Zend_Validate_NotEmpty(),
                                                                                                                       new Zend_Validate_Float()
                                                                                                                  )), true);
        $laborQst = "How much did you spend last year on service from outside vendors for your printer fleet, including maintenance kits, parts and labor (excluding the cost of leased copiers)? If you select \"I don't know\", an average of $200 per printer per year will be used.";
        $labor_cost->setLabel($laborQst);
        $this->addElement($labor_cost);

        /*
         * Average Purchase
         */
        $avg_purchase = $this->createElement('text', 'avg_purchase');
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
        $it_hourlyRate = $this->createElement('text', 'it_hourlyRate');
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

        /**
         * Number of supply vendors
         */
        $numb_vendors = $this->createElement('text', 'numb_vendors');
        $numb_vendors->setRequired(true)
            ->setAttrib('maxlength', 3)
            ->setAttrib('class', 'span1')
            ->setAutoInsertNotEmptyValidator(false)
            ->addValidator('greaterThan', true, array(
                                                     'min' => 0
                                                ))
            ->setLabel("How many vendors do you currently deal with for printer supplies, service and hardware?")
            ->getValidator('greaterThan')->setMessage('Must be greater than zero');
        $this->addElement($numb_vendors);

        /**
         * Ink and toner
         */
        $inkTonerOrderRadio = $this->createElement('radio', 'inkTonerOrderRadio');
        $inkTonerOrderRadio->addMultiOption('Daily', 'Daily')
            ->addMultiOption('Weekly', 'Weekly')
            ->addMultiOption('Times per month', 'Custom')
            ->setLabel("How many times per month does your organization order printer supplies?");

        $this->addElement($inkTonerOrderRadio);

        $ink_toner_order = $this->createElement('text', 'numb_monthlyOrders');
        $ink_toner_order->setAttrib('maxlength', 3)
            ->setAttrib('class', 'span1')
            ->setAllowEmpty(false)
            ->addValidator(new Custom_Validate_FieldDependsOnValue('inkTonerOrderRadio', 'Times per month', array(
                                                                                                                 new Zend_Validate_NotEmpty(),
                                                                                                                 new Zend_Validate_Digits()
                                                                                                            )), true)
            ->setDescription('times per month');

        $this->addElement($ink_toner_order);

        /**
         * IT Hours Radio
         */
        $itHoursRadio = $this->createElement('radio', 'itHoursRadio');
        $itHoursRadio->addMultiOptions(array(
                                            'guess'                   => 'I don\'t know',
                                            'I know the exact amount' => 'I know the exact amount'
                                       ))
            ->setValue('I know the exact amount');

        $this->addElement($itHoursRadio);

        /**
         * IT hours Text
         */
        $itHours = $this->createElement('text', 'itHours');
        $itHours->setAttrib('maxlength', 3)
            ->setAttrib('class', 'span1')
            ->setDescription('hours')
            ->setAllowEmpty(false)
            ->addValidator(new Custom_Validate_FieldDependsOnValue('itHoursRadio', 'I know the exact amount', array(
                                                                                                                   new Zend_Validate_NotEmpty(),
                                                                                                                   new Zend_Validate_Float()
                                                                                                              )), true)
            ->setLabel("How many hours per week do IT personnel spend servicing and supporting printers? If you select \"I don't know\", an average of 15 minutes per week per printer will be used.");
        $this->addElement($itHours);

        /**
         * Monthly Breakdowns Text
         */
        $monthlyBreakdown = $this->createElement('text', 'monthlyBreakdown');
        $monthlyBreakdown->setAttrib('maxlength', 3)
            ->setAttrib('class', 'span1')
            ->setDescription('breakdowns per month')
            ->setAllowEmpty(false)
            ->addValidator(new Custom_Validate_FieldDependsOnValue('monthlyBreakdownRadio', 'exact', array(
                                                                                                                            new Zend_Validate_NotEmpty(),
                                                                                                                            new Zend_Validate_Float()
                                                                                                                       )), true)
            ->setLabel("How many times per month, on average, does your internal IT staff or an external service company need to be called to repair a broken printer in your fleet? If you select \"I don't know\", an average of 1 repair per month for every 20 printers will be used.");
        $this->addElement($monthlyBreakdown);

        /**
         * Monthly Breakdowns Radio
         */
        $monthlyBreakdownRadio = $this->createElement('radio', 'monthlyBreakdownRadio');
        $monthlyBreakdownRadio->addMultiOptions($multiOptions)
            ->setLabel("")
            ->setValue('I know the exact amount');

        $this->addElement($monthlyBreakdownRadio);

        /**
         * Page Coverage Mono
         */
        $pageCoverage_BW = $this->createElement('text', 'pageCoverage_BW');
        $pageCoverage_BW->setRequired(true)
            ->setLabel('Monochrome Coverage:')
            ->setAttrib('maxlength', 5)
            ->setAttrib('class', 'span1')
            ->setDescription('%')
            ->addValidator('float')
            ->addValidator(new Zend_Validate_Between(1, 100));

        $pageCoverage_BW->getValidator('Between')->setMessage('Must be between 1 and 100');
        $this->addElement($pageCoverage_BW);

        /**
         * Page Coverage Color
         */
        $pageCoverage_Colour = $this->createElement('text', 'pageCoverage_Color');
        $pageCoverage_Colour->setRequired(true)
            ->setLabel('Color Coverage:')
            ->setAttrib('maxlength', 5)
            ->setAttrib('class', 'span1')
            ->setDescription('%')
            ->addValidator('float')
            ->addValidator(new Zend_Validate_Between(1, 100));
        $pageCoverage_Colour->getValidator('Between')->setMessage('Must be between 1 and 100');
        $this->addElement($pageCoverage_Colour);

        /**
         * Print Volume Question
         */
        $this->addElement('radio', 'printVolume', array(
                                                       'label'        => "What percent of your print volume is done on inkjet and other desktop printers ?",
                                                       'multiOptions' => self::$volumeOptions,
                                                       'required'     => true,
                                                       'filters'      => array('StringTrim')
                                                  ));


        /**
         * Repair Time Question
         */
        $repairTimeRadio = $this->createElement('radio', 'repairTime');
        $repairTimeRadio->setLabel("How long does it take, on average, for a printer to
    {be
} fixed after it has broken down
        ?")
            ->setMultiOptions(self::$repairTimeOptions)
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addValidator('InArray', false, array(
                                                  array_keys(self::$repairTimeOptions)
                                             ));
        $this->addElement($repairTimeRadio);

        /**
         * Add our form actions
         */

        Proposalgen_Form_Assessment_Navigation::addFormActionsToForm(Proposalgen_Form_Assessment_Navigation::BUTTONS_NEXT, $this);
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