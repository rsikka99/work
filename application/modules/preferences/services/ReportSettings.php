<?php

class Preferences_Service_ReportSettings
{
    /**
     * Default report settings and survey settings combined into an array
     *
     * @var Proposalgen_Model_Report_Setting|array|null
     */
    protected $_defaultSettings;

    /**
     * Gets the report setting form.
     *
     * @var Preferences_Form_ReportSetting
     */
    protected $_form;

    /**
     * Gets the report settings from the system
     *
     * @var Proposalgen_Model_Report_Setting
     */
    protected $_systemReportSettings;

    /**
     * @var Proposalgen_Model_Survey_Setting
     */
    protected $_systemSurveySettings;

//    /**
//     * @var Proposalgen_Model_Report_Setting
//     */
//    protected $_userReportSettings;
//
//    /**
//     * @var Proposalgen_Model_Survey_Setting
//     */
//    protected $_userSurveySettings;

    /**
     *
     * @param $defaultSettings array
     */
    public function __construct ($defaultSettings = null)
    {
        $this->_systemReportSettings = Proposalgen_Model_Mapper_Report_Setting::getInstance()->find(1);
        $this->_systemSurveySettings = Proposalgen_Model_Mapper_Survey_Setting::getInstance()->find(1);
        $this->_defaultSettings      = $defaultSettings;
    }

    /**
     * Gets the report setting form with default values populated
     *
     * @return Preferences_Form_ReportSetting
     */
    public function getFormWithDefaults ()
    {
        if (!isset($this->_form))
        {
            $this->_form      = new Preferences_Form_ReportSetting();
            $populateSettings = array_merge($this->_systemReportSettings->toArray(), $this->_systemSurveySettings->toArray());

            // User form will populate the description with defaults
            if (is_array($this->_defaultSettings))
            {
                foreach ($this->_form->getElements() as $element)
                {
                    if (array_key_exists("Zend_Form_Decorator_Description", $element->getDecorators()))
                    {
                        $element->setDescription($populateSettings[$element->getName()]);
                    }
                }
                // Re-load the settings into report settings
                $populateSettings = $this->_defaultSettings;
            }

            $this->_form->populate($populateSettings);
        }

        return $this->_form;
    }

    /**
     * Gets the report setting form
     *
     * @return Preferences_Form_ReportSetting
     */
    public function getForm ()
    {
        if (!isset($this->_form))
        {
            $this->_form      = new Preferences_Form_ReportSetting();
            $populateSettings = array_merge($this->_systemReportSettings->toArray(), $this->_systemSurveySettings->toArray());
            if ($this->_defaultSettings)
            {
                // Get the user settings for population
                $this->_systemReportSettings->populate($this->_defaultSettings);
                $this->_systemSurveySettings->populate($this->_defaultSettings);
                // Re-load the settings into report settings
                $populateSettings = array_merge($this->_systemReportSettings->toArray(), $this->_systemSurveySettings->toArray());
            }

            // Get the current class of the element and adds default settings
            foreach ($this->_form->getElements() as $element)
            {
                $currentClass = $element->getAttrib('class');
                $element->setAttrib('class', "{$currentClass} defaultSettings ");
            }

            $this->_form->populate($populateSettings);
        }

        return $this->_form;
    }

    /**
     * Validates the data with the form
     *
     * @param array $data
     *            The array of data to validate
     *
     * @return array The array of valid and filtered data, or false on error.
     */
    protected function validateAndFilterData ($data)
    {
        $validData = false;
        $form      = $this->getFormWithDefaults();

        if ($form->isValid($data))
        {
            $validData = $form->getValues();
        }
        else
        {
            if ($this->getFormWithDefaults() instanceof EasyBib_Form)
            {
                $this->getFormWithDefaults()->buildBootstrapErrorDecorators();
            }
        }

        return $validData;
    }

    /**
     * Updates the report's settings
     *
     * @param array $data
     *
     * @return boolean
     */
    public function update ($data)
    {
        $validData = $this->validateAndFilterData($data);
        if ($validData)
        {
            foreach ($validData as $key => $value)
            {
                if (empty($value))
                {
                    unset($validData [$key]);
                }
            }
            // Check the valid data to see if toner preferences drop downs have been set.
            if ((int)$validData ['assessmentPricingConfigId'] === Proposalgen_Model_PricingConfig::NONE)
            {
                unset($validData ['assessmentPricingConfigId']);
            }
            if ((int)$validData ['grossMarginPricingConfigId'] === Proposalgen_Model_PricingConfig::NONE)
            {
                unset($validData ['grossMarginPricingConfigId']);
            }

            $reportSetting = new Proposalgen_Model_Report_Setting();
            $surveySetting = new Proposalgen_Model_Survey_Setting();

            $reportSetting->populate($validData);
            $surveySetting->populate($validData);

            if (isset($this->_defaultSettings->id))
            {
                $id = $this->_defaultSettings->id;
            }
            else
            {
                $id = 1;
            }

            $reportSetting->id = $id;
            $surveySetting->id = $id;


            Proposalgen_Model_Mapper_Report_Setting::getInstance()->save($this->_userReportSettings);
            Proposalgen_Model_Mapper_Survey_Setting::getInstance()->save($this->_userSurveySettings);

            $this->getFormWithDefaults()->populate($this->_reportSettings->toArray());

            return true;
        }

        return false;
    }
}
