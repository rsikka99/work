<?php

/**
 * Class Dealermanagement_Form_Dealer_Branding
 */
class Dealermanagement_Form_Dealer_Branding extends Twitter_Bootstrap_Form_Horizontal
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
        $this->addElement('text', 'dealerName', array(
            'label'      => 'Dealer Name:',
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(2, 255),),
            ),
        ));

        $this->addElement('text', 'shortDealerName', array(
            'label'      => 'Shortened Dealer Name:',
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(2, 255),),
            ),
        ));

        $this->addElement('text', 'mpsProgramName', array(
            'label'      => 'MPS Program Name:',
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(2, 255),),
            ),
        ));

        $this->addElement('text', 'shortMpsProgramName', array(
            'label'      => 'Shortened MPS Program Name:',
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(2, 255),),
            ),
        ));

        $this->addElement('text', 'jitProgramName', array(
            'label'      => 'JIT Program Name:',
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(2, 255),),
            ),
        ));

        $this->addElement('text', 'shortJitProgramName', array(
            'label'      => 'Shortened JIT Program Name:',
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(2, 255),),
            ),
        ));

        /**
         * ==============================================
         * Font Colors
         * ==============================================
         */
        $this->addElement('text', 'titlePageTitleFontColor', array(
            'label'      => 'Title Page Title Font Color:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                $hexColorValidator,
            ),
        ));

        $this->addElement('text', 'titlePageTitleBackgroundColor', array(
            'label'      => 'Title Page Title Background Color:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                $hexColorValidator,
            ),
        ));

        $this->addElement('text', 'titlePageInformationFontColor', array(
            'label'      => 'Title Page Text Color:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                $hexColorValidator,
            ),
        ));

        $this->addElement('text', 'titlePageInformationBackgroundColor', array(
            'label'      => 'Title Page Text Background Color:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                $hexColorValidator,
            ),
        ));

        $this->addElement('text', 'h1FontColor', array(
            'label'      => 'H1 Font Color:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                $hexColorValidator,
            ),
        ));

        $this->addElement('text', 'h1BackgroundColor', array(
            'label'      => 'H1 Background Color:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                $hexColorValidator,
            ),
        ));

        $this->addElement('text', 'h2FontColor', array(
            'label'      => 'H2 Font Color:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                $hexColorValidator,
            ),
        ));

        $this->addElement('text', 'h2BackgroundColor', array(
            'label'      => 'H2 Background Color:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                $hexColorValidator,
            ),
        ));


        /**
         * ==============================================
         * Graph Colors
         * ==============================================
         */
        $this->addElement('text', 'graphCustomerColor', array(
            'label'      => 'Graph Color - Customer:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                $hexColorValidator,
            ),
        ));

        $this->addElement('text', 'graphDealerColor', array(
            'label'      => 'Graph Color - Your Company:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                $hexColorValidator,
            ),
        ));

        $this->addElement('text', 'graphPositiveColor', array(
            'label'      => 'Graph Color - Positive Meaning:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                $hexColorValidator,
            ),
        ));

        $this->addElement('text', 'graphNegativeColor', array(
            'label'      => 'Graph Color - Negative Meaning:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                $hexColorValidator,
            ),
        ));

        $this->addElement('text', 'graphMonoDeviceColor', array(
            'label'      => 'Graph Color - Monochrome Devices:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                $hexColorValidator,
            ),
        ));

        $this->addElement('text', 'graphColorDeviceColor', array(
            'label'      => 'Graph Color - Color Devices:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                $hexColorValidator,
            ),
        ));

        $this->addElement('text', 'graphCopyCapableDeviceColor', array(
            'label'      => 'Graph Color - Copy/Scan Capable Devices:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                $hexColorValidator,
            ),
        ));

        $this->addElement('text', 'graphDuplexCapableDeviceColor', array(
            'label'      => 'Graph Color - Duplex Capable Devices:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                $hexColorValidator,
            ),
        ));

        $this->addElement('text', 'graphFaxCapableDeviceColor', array(
            'label'      => 'Graph Color - Fax Capable Devices:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                $hexColorValidator,
            ),
        ));

        $this->addElement('text', 'graphPurchasedDeviceColor', array(
            'label'      => 'Graph Color - Purchased Devices:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                $hexColorValidator,
            ),
        ));

        $this->addElement('text', 'graphLeasedDeviceColor', array(
            'label'      => 'Graph Color - Leased Devices:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                $hexColorValidator,
            ),
        ));

        $this->addElement('text', 'graphExcludedDeviceColor', array(
            'label'      => 'Graph Color - Excluded Devices:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                $hexColorValidator,
            ),
        ));

        $this->addElement('text', 'graphIndustryAverageColor', array(
            'label'      => 'Graph Color - Industry Average:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                $hexColorValidator,
            ),
        ));

        $this->addElement('text', 'graphKeepDeviceColor', array(
            'label'      => 'Graph Color - Optimization - Keep Devices:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                $hexColorValidator,
            ),
        ));

        $this->addElement('text', 'graphReplacedDeviceColor', array(
            'label'      => 'Graph Color - Optimization - Replaced Devices:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                $hexColorValidator,
            ),
        ));

        $this->addElement('text', 'graphDoNotRepairDeviceColor', array(
            'label'      => 'Graph Color - Optimization - Do Not Repair Devices:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                $hexColorValidator,
            ),
        ));

        $this->addElement('text', 'graphRetireDeviceColor', array(
            'label'      => 'Graph Color - Optimization - Retire Devices:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                $hexColorValidator,
            ),
        ));

        $this->addElement('text', 'graphManagedDeviceColor', array(
            'label'      => 'Graph Color - Health Check - Managed Devices:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                $hexColorValidator,
            ),
        ));

        $this->addElement('text', 'graphManageableDeviceColor', array(
            'label'      => 'Graph Color - Health Check - Manageable Devices:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                $hexColorValidator,
            ),
        ));

        $this->addElement('text', 'graphFutureReviewDeviceColor', array(
            'label'      => 'Graph Color - Health Check - Future Review Devices:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                $hexColorValidator,
            ),
        ));

        $this->addElement('text', 'graphJitCompatibleDeviceColor', array(
            'label'      => 'Graph Color - JIT Compatible Devices:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                $hexColorValidator,
            ),
        ));

        $this->addElement('text', 'graphCompatibleDeviceColor', array(
            'label'      => 'Graph Color - Compatible Devices (for any reason):',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                $hexColorValidator,
            ),
        ));

        $this->addElement('text', 'graphNotCompatibleDeviceColor', array(
            'label'      => 'Graph Color - Not Compatible Devices (for any reason):',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                $hexColorValidator,
            ),
        ));

        $this->addElement('text', 'graphCurrentSituationColor', array(
            'label'      => 'Graph Color - Current Situation:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                $hexColorValidator,
            ),
        ));

        $this->addElement('text', 'graphNewSituationColor', array(
            'label'      => 'Graph Color - New Situation:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                $hexColorValidator,
            ),
        ));

        $this->addElement('text', 'graphAgeOfDevices1', array(
            'label'      => 'Graph Color - Age Of Devices 0-2 Years:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                $hexColorValidator,
            ),
        ));

        $this->addElement('text', 'graphAgeOfDevices2', array(
            'label'      => 'Graph Color - Age Of Devices 2-4 Years:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                $hexColorValidator,
            ),
        ));

        $this->addElement('text', 'graphAgeOfDevices3', array(
            'label'      => 'Graph Color - Age Of Devices 4-8 Years:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                $hexColorValidator,
            ),
        ));

        $this->addElement('text', 'graphAgeOfDevices4', array(
            'label'      => 'Graph Color - Age Of Devices 8+ Years:',
            'required'   => true,
            'class'      => 'hex-color-input',
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                $hexColorValidator,
            ),
        ));

        $this->addElement('text', 'assessmentTitle', array(
            'label'      => 'Assessment Title:',
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                array(
                    'validator' => 'StringLength',
                    'options'   => array('min' => 0, 'max' => 200),
                ),
            ),
        ));

        $this->addElement('text', 'customerCostAnalysisTitle', array(
            'label'      => 'Customer Cost Analysis Title:',
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                array(
                    'validator' => 'StringLength',
                    'options'   => array('min' => 0, 'max' => 200),
                ),
            ),
        ));

        $this->addElement('text', 'customerOptimizationTitle', array(
            'label'      => 'Customer Optimization Title:',
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                array(
                    'validator' => 'StringLength',
                    'options'   => array('min' => 0, 'max' => 200),
                ),
            ),
        ));

        $this->addElement('text', 'healthCheckTitle', array(
            'label'      => 'Business Review Title:',
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                array(
                    'validator' => 'StringLength',
                    'options'   => array('min' => 0, 'max' => 200),
                ),
            ),
        ));

        $this->addElement('text', 'leaseQuoteTitle', array(
            'label'      => 'Lease Quote Title:',
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                array(
                    'validator' => 'StringLength',
                    'options'   => array('min' => 0, 'max' => 200),
                ),
            ),
        ));

        $this->addElement('text', 'purchaseQuoteTitle', array(
            'label'      => 'Purchase Quote Title:',
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                array(
                    'validator' => 'StringLength',
                    'options'   => array('min' => 0, 'max' => 200),
                ),
            ),
        ));

        $this->addElement('text', 'solutionTitle', array(
            'label'      => 'Solution Title:',
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags',),
            'validators' => array(
                array(
                    'validator' => 'StringLength',
                    'options'   => array('min' => 0, 'max' => 200),
                ),
            ),
        ));


        /**
         * ==============================================
         * FORM ACTIONS
         * ==============================================
         */

        /**
         * Cancel Button
         */
        $cancel = $this->createElement('submit', 'cancel', array(
            'ignore' => true,
            'label'  => 'Cancel'
        ));

        /**
         * Save Button
         */
        $submit = $this->createElement('submit', 'submit', array(
            'ignore'     => true,
            'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
            'label'      => 'Save'
        ));

        $this->addDisplayGroup(array(
            $submit,
            $cancel,
        ), 'actions', array(
            'disableLoadDefaultDecorators' => true,
            'decorators'                   => array(
                'Actions'
            ),
            'class'                        => 'form-actions-center'
        ));
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators(array(
            array(
                'ViewScript',
                array(
                    'viewScript' => 'forms/dealer-branding.phtml'
                )
            )
        ));
    }
}