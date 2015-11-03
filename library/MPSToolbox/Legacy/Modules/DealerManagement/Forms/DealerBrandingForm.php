<?php

namespace MPSToolbox\Legacy\Modules\DealerManagement\Forms;

use MPSToolbox\Legacy\Modules\Assessment\Forms\AssessmentNavigationForm;
use Zend_Validate_Regex;
use Zend_Form;

/**
 * Class DealerBrandingForm
 *
 * @package MPSToolbox\Legacy\Modules\DealerManagement\Forms
 */
class DealerBrandingForm extends Zend_Form
{
    /**
     * @param null|array $options
     */
    public function __construct ($options = null)
    {
        parent::__construct($options);
    }

    public function init ()
    {
        $this->setMethod('POST');

        /**
         * ==============================================
         * Validators
         * ==============================================
         */
        $hexColorValidator = new Zend_Validate_Regex('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/');

        /**
         * ==============================================
         * Names
         * ==============================================
         */
        $this->addElement('text', 'dealerName', [
            'label'      => 'Dealer Name:',
            'required'   => true,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                ['validator' => 'StringLength', 'options' => [2, 255]],
            ],
        ]);

        $this->addElement('text', 'dealerEmail', [
            'label'      => 'Dealer E-mail:',
            'required'   => true,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                ['validator' => 'StringLength', 'options' => [2, 255]],
            ],
        ]);

        $this->addElement('text', 'shortDealerName', [
            'label'      => 'Shortened Dealer Name:',
            'required'   => true,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                ['validator' => 'StringLength', 'options' => [2, 255]],
            ],
        ]);

        $this->addElement('text', 'mpsProgramName', [
            'label'      => 'MPS Program Name:',
            'required'   => true,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                ['validator' => 'StringLength', 'options' => [2, 255]],
            ],
        ]);

        $this->addElement('text', 'shortMpsProgramName', [
            'label'      => 'Shortened MPS Program Name:',
            'required'   => true,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                ['validator' => 'StringLength', 'options' => [2, 255]],
            ],
        ]);

        $this->addElement('text', 'jitProgramName', [
            'label'      => 'JIT Program Name:',
            'required'   => true,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                ['validator' => 'StringLength', 'options' => [2, 255]],
            ],
        ]);

        $this->addElement('text', 'shortJitProgramName', [
            'label'      => 'Shortened JIT Program Name:',
            'required'   => true,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                ['validator' => 'StringLength', 'options' => [2, 255]],
            ],
        ]);

        /**
         * ==============================================
         * Font Colors
         * ==============================================
         */
        $this->addElement('text', 'titlePageTitleFontColor', [
            'label'      => 'Title Page Title Font Color:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                $hexColorValidator,
            ],
        ]);

        $this->addElement('text', 'titlePageTitleBackgroundColor', [
            'label'      => 'Title Page Title Background Color:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                $hexColorValidator,
            ],
        ]);

        $this->addElement('text', 'titlePageInformationFontColor', [
            'label'      => 'Title Page Text Color:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                $hexColorValidator,
            ],
        ]);

        $this->addElement('text', 'titlePageInformationBackgroundColor', [
            'label'      => 'Title Page Text Background Color:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                $hexColorValidator,
            ],
        ]);

        $this->addElement('text', 'h1FontColor', [
            'label'      => 'H1 Font Color:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                $hexColorValidator,
            ],
        ]);

        $this->addElement('text', 'h1BackgroundColor', [
            'label'      => 'H1 Background Color:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                $hexColorValidator,
            ],
        ]);

        $this->addElement('text', 'h2FontColor', [
            'label'      => 'H2 Font Color:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                $hexColorValidator,
            ],
        ]);

        $this->addElement('text', 'h2BackgroundColor', [
            'label'      => 'H2 Background Color:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                $hexColorValidator,
            ],
        ]);


        /**
         * ==============================================
         * Graph Colors
         * ==============================================
         */
        $this->addElement('text', 'graphCustomerColor', [
            'label'      => 'Graph Color - Customer:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                $hexColorValidator,
            ],
        ]);

        $this->addElement('text', 'graphDealerColor', [
            'label'      => 'Graph Color - Your Company:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                $hexColorValidator,
            ],
        ]);

        $this->addElement('text', 'graphPositiveColor', [
            'label'      => 'Graph Color - Positive Meaning:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                $hexColorValidator,
            ],
        ]);

        $this->addElement('text', 'graphNegativeColor', [
            'label'      => 'Graph Color - Negative Meaning:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                $hexColorValidator,
            ],
        ]);

        $this->addElement('text', 'graphMonoDeviceColor', [
            'label'      => 'Graph Color - Monochrome Devices:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                $hexColorValidator,
            ],
        ]);

        $this->addElement('text', 'graphColorDeviceColor', [
            'label'      => 'Graph Color - Color Devices:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                $hexColorValidator,
            ],
        ]);

        $this->addElement('text', 'graphCopyCapableDeviceColor', [
            'label'      => 'Graph Color - Copy/Scan Capable Devices:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                $hexColorValidator,
            ],
        ]);

        $this->addElement('text', 'graphDuplexCapableDeviceColor', [
            'label'      => 'Graph Color - Duplex Capable Devices:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                $hexColorValidator,
            ],
        ]);

        $this->addElement('text', 'graphFaxCapableDeviceColor', [
            'label'      => 'Graph Color - Fax Capable Devices:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                $hexColorValidator,
            ],
        ]);

        $this->addElement('text', 'graphPurchasedDeviceColor', [
            'label'      => 'Graph Color - Purchased Devices:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                $hexColorValidator,
            ],
        ]);

        $this->addElement('text', 'graphLeasedDeviceColor', [
            'label'      => 'Graph Color - Leased Devices:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                $hexColorValidator,
            ],
        ]);

        $this->addElement('text', 'graphExcludedDeviceColor', [
            'label'      => 'Graph Color - Excluded Devices:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                $hexColorValidator,
            ],
        ]);

        $this->addElement('text', 'graphIndustryAverageColor', [
            'label'      => 'Graph Color - Industry Average:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                $hexColorValidator,
            ],
        ]);

        $this->addElement('text', 'graphKeepDeviceColor', [
            'label'      => 'Graph Color - Optimization - Keep Devices:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                $hexColorValidator,
            ],
        ]);

        $this->addElement('text', 'graphReplacedDeviceColor', [
            'label'      => 'Graph Color - Optimization - Replaced Devices:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                $hexColorValidator,
            ],
        ]);

        $this->addElement('text', 'graphDoNotRepairDeviceColor', [
            'label'      => 'Graph Color - Optimization - Do Not Repair Devices:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                $hexColorValidator,
            ],
        ]);

        $this->addElement('text', 'graphRetireDeviceColor', [
            'label'      => 'Graph Color - Optimization - Retire Devices:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                $hexColorValidator,
            ],
        ]);

        $this->addElement('text', 'graphManagedDeviceColor', [
            'label'      => 'Graph Color - Health Check - Managed Devices:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                $hexColorValidator,
            ],
        ]);

        $this->addElement('text', 'graphManageableDeviceColor', [
            'label'      => 'Graph Color - Health Check - Manageable Devices:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                $hexColorValidator,
            ],
        ]);

        $this->addElement('text', 'graphFutureReviewDeviceColor', [
            'label'      => 'Graph Color - Health Check - Future Review Devices:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                $hexColorValidator,
            ],
        ]);

        $this->addElement('text', 'graphJitCompatibleDeviceColor', [
            'label'      => 'Graph Color - JIT Compatible Devices:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                $hexColorValidator,
            ],
        ]);

        $this->addElement('text', 'graphCompatibleDeviceColor', [
            'label'      => 'Graph Color - Compatible Devices (for any reason):',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                $hexColorValidator,
            ],
        ]);

        $this->addElement('text', 'graphNotCompatibleDeviceColor', [
            'label'      => 'Graph Color - Not Compatible Devices (for any reason):',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                $hexColorValidator,
            ],
        ]);

        $this->addElement('text', 'graphCurrentSituationColor', [
            'label'      => 'Graph Color - Current Situation:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                $hexColorValidator,
            ],
        ]);

        $this->addElement('text', 'graphNewSituationColor', [
            'label'      => 'Graph Color - New Situation:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                $hexColorValidator,
            ],
        ]);

        $this->addElement('text', 'graphAgeOfDevices1', [
            'label'      => 'Graph Color - Age Of Devices 0-2 Years:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                $hexColorValidator,
            ],
        ]);

        $this->addElement('text', 'graphAgeOfDevices2', [
            'label'      => 'Graph Color - Age Of Devices 2-4 Years:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                $hexColorValidator,
            ],
        ]);

        $this->addElement('text', 'graphAgeOfDevices3', [
            'label'      => 'Graph Color - Age Of Devices 4-8 Years:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                $hexColorValidator,
            ],
        ]);

        $this->addElement('text', 'graphAgeOfDevices4', [
            'label'      => 'Graph Color - Age Of Devices 8+ Years:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                $hexColorValidator,
            ],
        ]);

        $this->addElement('text', 'assessmentTitle', [
            'label'      => 'Assessment Title:',
            'required'   => true,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                [
                    'validator' => 'StringLength',
                    'options'   => ['min' => 0, 'max' => 200],
                ],
            ],
        ]);

        $this->addElement('text', 'customerCostAnalysisTitle', [
            'label'      => 'Customer Cost Analysis Title:',
            'required'   => true,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                [
                    'validator' => 'StringLength',
                    'options'   => ['min' => 0, 'max' => 200],
                ],
            ],
        ]);

        $this->addElement('text', 'customerOptimizationTitle', [
            'label'      => 'Customer Optimization Title:',
            'required'   => true,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                [
                    'validator' => 'StringLength',
                    'options'   => ['min' => 0, 'max' => 200],
                ],
            ],
        ]);

        $this->addElement('text', 'healthCheckTitle', [
            'label'      => 'Business Review Title:',
            'required'   => true,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                [
                    'validator' => 'StringLength',
                    'options'   => ['min' => 0, 'max' => 200],
                ],
            ],
        ]);

        $this->addElement('text', 'leaseQuoteTitle', [
            'label'      => 'Lease Quote Title:',
            'required'   => true,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                [
                    'validator' => 'StringLength',
                    'options'   => ['min' => 0, 'max' => 200],
                ],
            ],
        ]);

        $this->addElement('text', 'purchaseQuoteTitle', [
            'label'      => 'Purchase Quote Title:',
            'required'   => true,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                [
                    'validator' => 'StringLength',
                    'options'   => ['min' => 0, 'max' => 200],
                ],
            ],
        ]);

        $this->addElement('text', 'solutionTitle', [
            'label'      => 'Solution Title:',
            'required'   => true,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                [
                    'validator' => 'StringLength',
                    'options'   => ['min' => 0, 'max' => 200],
                ],
            ],
        ]);


        /**
         * ==============================================
         * FORM ACTIONS
         * ==============================================
         */

        /**
         * Cancel Button
         */
        $this->addElement('submit', 'cancel', [
            'ignore' => true,
            'label'  => 'Cancel',
        ]);

        /**
         * Save Button
         */
        $this->addElement('submit', 'submit', [
            'ignore' => true,
            'label'  => 'Save',
        ]);
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators([
            [
                'ViewScript',
                [
                    'viewScript' => 'forms/dealermanagement/dealer-branding-form.phtml'
                ]
            ]
        ]);
    }
}