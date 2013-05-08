<?php

/**
 * Class Preferences_Service_ReportSetting
 */
class Preferences_Service_ReportSetting
{
    /**
     * Default report settings and survey settings combined into an array
     *
     * @var Proposalgen_Model_Assessment_Setting|array|null
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
     * @var Proposalgen_Model_Assessment_Setting
     */
    protected $_systemReportSettings;

    /**
     * @var Proposalgen_Model_Survey_Setting
     */
    protected $_systemSurveySettings;

    /**
     *
     * @param $defaultSettings array
     */
    public function __construct ($defaultSettings = null)
    {
        $this->_systemReportSettings = Assessment_Model_Mapper_Assessment_Setting::getInstance()->find(1);
        $this->_systemSurveySettings = Proposalgen_Model_Mapper_Survey_Setting::getInstance()->find(1);
        $this->_defaultSettings      = $defaultSettings;
    }

    /**
     * Gets the report setting form with default values populated
     *
     * @param $populateSettings array
     *
     * @return Preferences_Form_ReportSetting
     */
    public function getFormWithDefaults ($populateSettings)
    {
        if (!isset($this->_form))
        {
            $this->_form = new Preferences_Form_ReportSetting();

            // User form will populate the description with defaults
            if (is_array($this->_defaultSettings))
            {
                $this->_form->getElement("pageCoverageMono")->setDescription($populateSettings["pageCoverageMono"]);
                $this->_form->getElement("pageCoverageColor")->setDescription($populateSettings["pageCoverageColor"]);
                $this->_form->getElement("assessmentReportMargin")->setDescription($populateSettings["assessmentReportMargin"]);
                $this->_form->getElement("monthlyLeasePayment")->setDescription($populateSettings["monthlyLeasePayment"]);
                $this->_form->getElement("defaultPrinterCost")->setDescription($populateSettings["defaultPrinterCost"]);
                $this->_form->getElement("leasedBwCostPerPage")->setDescription($populateSettings["leasedBwCostPerPage"]);
                $this->_form->getElement("leasedColorCostPerPage")->setDescription($populateSettings["leasedColorCostPerPage"]);
                $this->_form->getElement("mpsBwCostPerPage")->setDescription($populateSettings["mpsBwCostPerPage"]);
                $this->_form->getElement("mpsColorCostPerPage")->setDescription($populateSettings["mpsColorCostPerPage"]);
                $this->_form->getElement("kilowattsPerHour")->setDescription($populateSettings["kilowattsPerHour"]);
                $this->_form->getElement("actualPageCoverageMono")->setDescription($populateSettings["actualPageCoverageMono"]);
                $this->_form->getElement("actualPageCoverageColor")->setDescription($populateSettings["actualPageCoverageColor"]);
                $this->_form->getElement("adminCostPerPage")->setDescription($populateSettings["adminCostPerPage"]);
                $this->_form->getElement("laborCostPerPage")->setDescription($populateSettings["laborCostPerPage"]);
                $this->_form->getElement("partsCostPerPage")->setDescription($populateSettings["partsCostPerPage"]);
                $this->_form->getElement("assessmentPricingConfigId")->setDescription(Proposalgen_Model_PricingConfig::$ConfigNames[$populateSettings['assessmentPricingConfigId']]);
                $this->_form->getElement("grossMarginPricingConfigId")->setDescription(Proposalgen_Model_PricingConfig::$ConfigNames[$populateSettings['grossMarginPricingConfigId']]);
                // Re-load the settings into report settings
                $populateSettings = $this->_defaultSettings;
            }
            // This function sets up the third row column header decorator
            $this->_form->allowNullValues();
            $this->_form->setUpFormWithDefaultDecorators();

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
        $form      = $this->getForm();

        if ($form->isValid($data))
        {
            if ($this->_form->allowsNull)
            {
                foreach ($data as $key => $value)
                {
                    if ($value === "")
                    {
                        $data [$key] = new Zend_Db_Expr("NULL");
                    }
                }

                $validData = $data;
            }
            else
            {
                $validData = $form->getValues();
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
            if ((int)$validData ['replacementPricingConfigId'] === Proposalgen_Model_PricingConfig::NONE)
            {
                unset($validData ['replacementPricingConfigId']);
            }

            $assessmentSetting = new Assessment_Model_Assessment_Setting();
            $surveySetting     = new Proposalgen_Model_Survey_Setting();

            $assessmentSetting->populate($validData);
            $surveySetting->populate($validData);

            if ($this->_defaultSettings)
            {
                $assessmentSetting->id = $this->_defaultSettings['reportSettingId'];
                $surveySetting->id     = $this->_defaultSettings['surveySettingId'];
            }
            else
            {
                $assessmentSetting->id = $this->_systemReportSettings->id;
                $surveySetting->id     = $this->_systemReportSettings->id;
            }

            Assessment_Model_Mapper_Assessment_Setting::getInstance()->save($assessmentSetting);
            Proposalgen_Model_Mapper_Survey_Setting::getInstance()->save($surveySetting);

            return true;
        }

        return false;
    }
}
