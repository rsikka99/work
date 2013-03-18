<?php

class Preferences_Service_ReportSettings
{
    /**
     * Default report settings and survey settings combined into an array
     *
     * @var array|null
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


    protected $_systemSurveySettings;

    protected $_userReportSettings;

    protected $_userSurveySettings;

    /**
     *
     * @param $defaultSettings array
     */
    public function __construct ($defaultSettings = null)
    {
        $this->_systemReportSettings = Proposalgen_Model_Mapper_Report_Setting::getInstance()->find(1);
        $this->_systemSurveySettings = Proposalgen_Model_Mapper_Survey_Setting::getInstance()->find(1);
        $this->_defaultSettings = $defaultSettings;
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
            $this->_form = new Preferences_Form_ReportSetting();
            $populateSettings = array_merge($this->_systemReportSettings->toArray(), $this->_systemSurveySettings->toArray());

            // User form will populate the description with defaults
            if($this->_defaultSettings)
            {
                foreach($this->_form->getElements() as $element)
                {
                    if(array_key_exists("Zend_Form_Decorator_Description", $element->getDecorators()))
                    {
                        $element->setDescription($populateSettings[$element->getName()]);
                    }
                }
                // Get the user settings for population
                $this->_systemReportSettings->populate($this->_defaultSettings);
                $this->_systemSurveySettings->populate($this->_defaultSettings);

                // Re-load the settings into report settings
                $populateSettings = array_merge($this->_systemReportSettings->toArray(), $this->_systemSurveySettings->toArray());
            }
            else
            {
                // Get the current class of the element and adds default settings
                foreach($this->_form->getElements() as $element)
                {
                    $currentClass = $element->getAttrib('class');
                    $element->setAttrib('class',"{$currentClass} defaultSettings ");
                }
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
        $form      = $this->getForm();

        if ($form->isValid($data))
        {
            $validData = $form->getValues();
        }
        else
        {
            if ($this->getForm() instanceof EasyBib_Form)
            {
                $this->getForm()->buildBootstrapErrorDecorators();
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

            // Save the id as it will get erased
            $reportSettingsId = $this->_reportSettings->id;

            $this->_reportSettings->populate($this->_defaultSettings->toArray());
            $this->_reportSettings->populate($validData);

            // Restore the ID
            $this->_reportSettings->id = $reportSettingsId;

            Proposalgen_Model_Mapper_Report_Setting::getInstance()->save($this->_reportSettings);

            $this->getForm()->populate($this->_reportSettings->toArray());

            return true;
        }
        return false;
    }
}
