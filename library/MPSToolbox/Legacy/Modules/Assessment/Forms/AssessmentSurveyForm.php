<?php
namespace MPSToolbox\Legacy\Modules\Assessment\Forms;

use Tangent\Validate\FieldDependsOnValue;
use Zend_Currency;
use Zend_Form;
use Zend_Validate_Between;
use Zend_Validate_Digits;
use Zend_Validate_Float;
use Zend_Validate_NotEmpty;


/**
 * Class AssessmentSurveyForm
 *
 * @package MPSToolbox\Legacy\Modules\Assessment\Forms
 */
class AssessmentSurveyForm extends \My_Form_Form
{
    protected $currency;
    protected $currencyRegex;

    public static $repairTimeOptions = [
        '0.5' => 'Less than a day',
        '1'   => 'One day',
        '2'   => 'Two days',
        '3'   => 'Three to five days',
        '5'   => 'More than five days',
    ];

    public static $volumeOptions     = [
        '5'  => 'Less than 10%',
        '18' => '10% to 25%',
        '38' => '26% to 50%',
        '75' => 'More than 50%',
    ];

    /**
     * @param null|array $options
     */
    public function __construct ($options = null)
    {
        $this->currency      = new Zend_Currency();
        $this->currencyRegex = '/^\d+(?:\.\d{0,2})?$/';

        // This runs, among other things, the init functions. Therefore it must come before anything that affects the form.
        parent::__construct($options);


    }

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');

        $multiOptions = [
            'guess' => 'I don\'t know',
            'exact' => 'I know the exact amount',
        ];

        /*
         * Ink And Toner cost
         */
        $this->addElement('radio', 'toner_cost_radio', [
            'multiOptions' => $multiOptions,
            'value'        => $multiOptions['exact'],
        ]);

        $this->addElement('text_currency', 'toner_cost', [
            'label'       => 'Cost of ink and toner last year',
            'placeholder' => 'Enter amount...',
            'allowEmpty'  => false,
            'validators'  => [
                new FieldDependsOnValue('toner_cost_radio', 'exact', [
                    new Zend_Validate_NotEmpty(),
                    new Zend_Validate_Float(),
                ])
            ],
        ]);

        /*
         * Service/Labor Cost
         */
        $this->addElement('radio', 'labor_cost_radio', [
            'multiOptions' => $multiOptions,
            'value'        => $multiOptions['exact'],
        ]);

        $this->addElement('text_currency', 'labor_cost', [
            'label'       => 'Cost of parts and labor last year',
            'placeholder' => 'Enter amount...',
            'allowEmpty'  => false,
            'validators'  => [
                new FieldDependsOnValue('labor_cost_radio', 'exact', [
                    new Zend_Validate_NotEmpty(),
                    new Zend_Validate_Float(),
                ])
            ],
        ]);

        /*
         * Average Purchase
         */
        $this->addElement('text_currency', 'avg_purchase', [
            'label'       => 'Average supply purchase order amount',
            'placeholder' => 'Enter amount...',
            'value'       => number_format(50, 2),
            'required'    => true,
            'validators'  => [
                [
                    'validator' => 'Float',
                    'options'   => [
                        'messages' => [
                            'notFloat' => 'Please enter a valid number.'
                        ],
                    ],
                ],
                [
                    'validator' => 'GreaterThan',
                    'options'   => ['min' => 0],
                ],
            ],
        ]);

        /*
         * Hourly Rate
         */
        $this->addElement('text_currency', 'it_hourlyRate', [
            'label'       => 'Average IT hourly rate',
            'placeholder' => 'Enter amount...',
            'value'       => number_format(40, 2),
            'required'    => true,
            'validators'  => [
                [
                    'validator' => 'Float',
                    'options'   => [
                        'messages' => [
                            'notFloat' => 'Please enter a valid number.'
                        ],
                    ],
                ],
                [
                    'validator' => 'GreaterThan',
                    'options'   => ['min' => 0],
                ],
            ],
        ]);

        /**
         * Number of supply orders
         */
        $inkTonerOrderOptions = [
            'Daily'           => 'Daily',
            'Weekly'          => 'Weekly',
            'Times per month' => 'Custom',
        ];

        $this->addElement('radio', 'inkTonerOrderRadio', [
            'label'        => 'Supply orders per month',
            'multiOptions' => $inkTonerOrderOptions,
            'value'        => $inkTonerOrderOptions['Daily'],
        ]);

        $this->addElement('text_int', 'numb_monthlyOrders', [
            'label'       => 'Supply orders per month',
            'placeholder' => 'Enter amount...',
            'allowEmpty'  => false,
            'validators'  => [
                new FieldDependsOnValue('inkTonerOrderRadio', 'Times per month', [
                    new Zend_Validate_NotEmpty(),
                    new Zend_Validate_Digits(),
                ]),
            ],
        ]);

        /**
         * IT Hours Radio
         */
        $this->addElement('radio', 'itHoursRadio', [
            'multiOptions' => $multiOptions,
            'value'        => $multiOptions['exact'],
        ]);

        $this->addElement('text_int', 'itHours', [
            'label'       => 'IT hours spent per month',
            'placeholder' => 'Enter amount...',
            'allowEmpty'  => false,
            'validators'  => [
                new FieldDependsOnValue('itHoursRadio', 'exact', [
                    new Zend_Validate_NotEmpty(),
                    new Zend_Validate_Digits(),
                ]),
            ],
        ]);

        /**
         * Monthly Breakdowns Text
         */
        $this->addElement('radio', 'monthlyBreakdownRadio', [
            'multiOptions' => $multiOptions,
            'value'        => $multiOptions['exact'],
        ]);

        $this->addElement('text_int', 'monthlyBreakdown', [
            'label'      => 'Average breakdowns per month',
            'allowEmpty' => false,
            'validators' => [
                new FieldDependsOnValue('monthlyBreakdownRadio', 'exact', [
                    new Zend_Validate_NotEmpty(),
                    new Zend_Validate_Digits(),
                ]),
            ],
        ]);

        /**
         * Page Coverage Mono
         */
        $this->addElement('text_float', 'pageCoverage_BW', [
            'label'      => 'Monochrome Page Coverage',
            'required'   => true,
            'validators' => [
                [
                    'validator' => 'Float',
                    'options'   => [
                        'messages' => [
                            'notFloat' => 'Please enter a valid number.'
                        ],
                    ],
                ],
                [
                    'validator' => 'Between',
                    'options'   => [
                        'min'      => 0,
                        'max'      => 100,
                        'messages' => [
                            'notBetween'       => 'Must be between 1 and 100',
                            'notBetweenStrict' => 'Must be between 1 and 100',
                        ],
                    ],
                ],
            ],
        ]);

        /**
         * Page Coverage Color
         */
        $this->addElement('text_float', 'pageCoverage_Color', [
            'label'      => 'Color Page Coverage',
            'required'   => true,
            'validators' => [
                [
                    'validator' => 'Float',
                    'options'   => [
                        'messages' => [
                            'notFloat' => 'Please enter a valid number.'
                        ],
                    ],
                ],
                [
                    'validator' => 'Between',
                    'options'   => [
                        'min'      => 0,
                        'max'      => 100,
                        'messages' => [
                            'notBetween'       => 'Must be between 1 and 100',
                            'notBetweenStrict' => 'Must be between 1 and 100',
                        ],
                    ],
                ],
            ],
        ]);

        /**
         * Print Volume Question
         */
        $this->addElement('radio', 'printVolume', [
            'label'        => 'Inkjet print volume',
            'multiOptions' => self::$volumeOptions,
            'required'     => true,
            'filters'      => ['StringTrim']
        ]);


        /**
         * Repair Time Question
         */
        $repairTimeRadio = $this->addElement('radio', 'repairTime', [
            'label'        => 'Average repair time?',
            'required'     => true,
            'filters'      => ['StringTrim'],
            'multiOptions' => self::$repairTimeOptions,
        ]);

        /**
         * Add our form actions
         */
        AssessmentNavigationForm::addFormActionsToForm(AssessmentNavigationForm::BUTTONS_BACK_NEXT, $this);
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/assessment/survey-form.phtml']]]);
    }
}