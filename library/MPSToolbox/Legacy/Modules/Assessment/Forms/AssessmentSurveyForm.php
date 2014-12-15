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
class AssessmentSurveyForm extends Zend_Form
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
    public static $volumeOptions     = array(
        '5'  => 'Less than 10%',
        '18' => '10% to 25%',
        '38' => '26% to 50%',
        '75' => 'More than 50%'
    );

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

        $multiOptions = array(
            'guess' => 'I don\'t know',
            'exact' => 'I know the exact amount',
        );

        /*
         * Ink And Toner cost
         */
        $this->addElement('radio', 'toner_cost_radio', array(
            'multiOptions' => $multiOptions,
            'value'        => $multiOptions['exact'],
        ));

        $this->addElement('text', 'toner_cost', array(
            'label'       => 'Cost of ink and toner last year',
            'placeholder' => 'Enter amount...',
            'allowEmpty'  => false,
            'validators'  => array(
                new FieldDependsOnValue('toner_cost_radio', 'exact', array(
                    new Zend_Validate_NotEmpty(),
                    new Zend_Validate_Float(),
                ))
            ),
        ));

        /*
         * Service/Labor Cost
         */
        $this->addElement('radio', 'labor_cost_radio', array(
            'multiOptions' => $multiOptions,
            'value'        => $multiOptions['exact'],
        ));

        $this->addElement('text', 'labor_cost', array(
            'label'       => 'Cost of parts and labor last year',
            'placeholder' => 'Enter amount...',
            'allowEmpty'  => false,
            'validators'  => array(
                new FieldDependsOnValue('labor_cost_radio', 'exact', array(
                    new Zend_Validate_NotEmpty(),
                    new Zend_Validate_Float(),
                ))
            ),
        ));

        /*
         * Average Purchase
         */
        $this->addElement('text', 'avg_purchase', array(
            'label'       => 'Average supply purchase order amount',
            'placeholder' => 'Enter amount...',
            'value'       => number_format(50, 2),
            'required'    => true,
            'validators'  => array(
                array(
                    'validator' => 'Float',
                    'options'   => array(
                        'messages' => array(
                            'notFloat' => 'Please enter a valid number.'
                        ),
                    ),
                ),
                array(
                    'validator' => 'GreaterThan',
                    'options'   => array('min' => 0),
                ),
            ),
        ));

        /*
         * Hourly Rate
         */
        $this->addElement('text', 'it_hourlyRate', array(
            'label'       => 'Average IT hourly rate',
            'placeholder' => 'Enter amount...',
            'value'       => number_format(40, 2),
            'required'    => true,
            'validators'  => array(
                array(
                    'validator' => 'Float',
                    'options'   => array(
                        'messages' => array(
                            'notFloat' => 'Please enter a valid number.'
                        ),
                    ),
                ),
                array(
                    'validator' => 'GreaterThan',
                    'options'   => array('min' => 0),
                ),
            ),
        ));

        /**
         * Number of supply orders
         */
        $inkTonerOrderOptions = array(
            'Daily'           => 'Daily',
            'Weekly'          => 'Weekly',
            'Times per month' => 'Custom',
        );

        $this->addElement('radio', 'inkTonerOrderRadio', array(
            'label'        => 'Supply orders per month',
            'multiOptions' => $inkTonerOrderOptions,
            'value'        => $inkTonerOrderOptions['Daily'],
        ));

        $this->addElement('text', 'numb_monthlyOrders', array(
            'label'       => 'Supply orders per month',
            'placeholder' => 'Enter amount...',
            'allowEmpty'  => false,
            'validators'  => array(
                new FieldDependsOnValue('inkTonerOrderRadio', 'Times per month', array(
                    new Zend_Validate_NotEmpty(),
                    new Zend_Validate_Digits(),
                )),
            ),
        ));

        /**
         * IT Hours Radio
         */
        $this->addElement('radio', 'itHoursRadio', array(
            'multiOptions' => $multiOptions,
            'value'        => $multiOptions['exact'],
        ));

        $this->addElement('text', 'itHours', array(
            'label'       => 'IT hours spent per month',
            'placeholder' => 'Enter amount...',
            'allowEmpty'  => false,
            'validators'  => array(
                new FieldDependsOnValue('itHoursRadio', 'exact', array(
                    new Zend_Validate_NotEmpty(),
                    new Zend_Validate_Digits(),
                )),
            ),
        ));

        /**
         * Monthly Breakdowns Text
         */
        $this->addElement('radio', 'monthlyBreakdownRadio', array(
            'multiOptions' => $multiOptions,
            'value'        => $multiOptions['exact'],
        ));

        $this->addElement('text', 'monthlyBreakdown', array(
            'label'      => 'Average breakdowns per month',
            'allowEmpty' => false,
            'validators' => array(
                new FieldDependsOnValue('monthlyBreakdownRadio', 'exact', array(
                    new Zend_Validate_NotEmpty(),
                    new Zend_Validate_Digits(),
                )),
            ),
        ));

        /**
         * Page Coverage Mono
         */
        $this->addElement('text', 'pageCoverage_BW', array(
            'label'      => 'Monochrome Page Coverage',
            'required'   => true,
            'validators' => array(
                array(
                    'validator' => 'Float',
                    'options'   => array(
                        'messages' => array(
                            'notFloat' => 'Please enter a valid number.'
                        ),
                    ),
                ),
                array(
                    'validator' => 'Between',
                    'options'   => array(
                        'min'      => 0,
                        'max'      => 100,
                        'messages' => array(
                            'notBetween'       => 'Must be between 1 and 100',
                            'notBetweenStrict' => 'Must be between 1 and 100',
                        ),
                    ),
                ),
            ),
        ));

        /**
         * Page Coverage Color
         */
        $this->addElement('text', 'pageCoverage_Color', array(
            'label'      => 'Color Page Coverage',
            'required'   => true,
            'validators' => array(
                array(
                    'validator' => 'Float',
                    'options'   => array(
                        'messages' => array(
                            'notFloat' => 'Please enter a valid number.'
                        ),
                    ),
                ),
                array(
                    'validator' => 'Between',
                    'options'   => array(
                        'min'      => 0,
                        'max'      => 100,
                        'messages' => array(
                            'notBetween'       => 'Must be between 1 and 100',
                            'notBetweenStrict' => 'Must be between 1 and 100',
                        ),
                    ),
                ),
            ),
        ));

        /**
         * Print Volume Question
         */
        $this->addElement('radio', 'printVolume', array(
            'label'        => 'Inkjet print volume',
            'multiOptions' => self::$volumeOptions,
            'required'     => true,
            'filters'      => array('StringTrim')
        ));


        /**
         * Repair Time Question
         */
        $repairTimeRadio = $this->addElement('radio', 'repairTime', array(
            'label'        => 'Average repair time?',
            'required'     => true,
            'filters'      => array('StringTrim'),
            'multiOptions' => self::$repairTimeOptions,
        ));

        /**
         * Add our form actions
         */
        AssessmentNavigationForm::addFormActionsToForm(AssessmentNavigationForm::BUTTONS_BACK_NEXT, $this);
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators(array(
            array(
                'ViewScript',
                array(
                    'viewScript' => 'forms/assessment/survey-form.phtml'
                )
            )
        ));
    }
}