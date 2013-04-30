<?php

/**
 * Class Preferences_Service_HealthcheckSetting
 */
class Preferences_Service_HealthcheckSetting
{
    /**
     * Default Healthcheck settings
     *
     * @var Healthcheck_Model_Healthcheck_Setting|array|null
     */
    protected $_defaultSettings;

    /**
     * Gets the Healthcheck setting form.
     *
     * @var Preferences_Form_HealthcheckSetting
     */
    protected $_form;

    /**
     * Gets the Healthcheck settings from the system
     *
     * @var Healthcheck_Model_Healthcheck_Setting
     */
    protected $_systemHealthcheckSettings;


    /**
     *
     * @param $defaultSettings array
     */
    public function __construct ($defaultSettings = null)
    {
        $this->_systemHealthcheckSettings = Healthcheck_Model_Mapper_Healthcheck_Setting::getInstance()->find(1);
        $this->_defaultSettings      = $defaultSettings;
    }

    /**
     * Gets the Healthcheck setting form with default values populated
     *
     * @param $populateSettings array
     *
     * @return Preferences_Form_HealthcheckSetting
     */
    public function getFormWithDefaults ($populateSettings)
    {
        if (!isset($this->_form))
        {
            $this->_form      = new Preferences_Form_HealthcheckSetting();

            // User form will populate the description with defaults
            if (is_array($this->_defaultSettings))
            {
                $this->_form->getElement("pageCoverageMonochrome")->setDescription($populateSettings["pageCoverageMonochrome"]);
                $this->_form->getElement("pageCoverageColor")->setDescription($populateSettings["pageCoverageColor"]);
                $this->_form->getElement("healthcheckMargin")->setDescription($populateSettings["healthcheckMargin"]);
                $this->_form->getElement("monthlyLeasePayment")->setDescription($populateSettings["monthlyLeasePayment"]);
                $this->_form->getElement("defaultPrinterCost")->setDescription($populateSettings["defaultPrinterCost"]);
                $this->_form->getElement("leasedBwCostPerPage")->setDescription($populateSettings["leasedBwCostPerPage"]);
                $this->_form->getElement("leasedColorCostPerPage")->setDescription($populateSettings["leasedColorCostPerPage"]);
                $this->_form->getElement("mpsBwCostPerPage")->setDescription($populateSettings["mpsBwCostPerPage"]);
                $this->_form->getElement("mpsColorCostPerPage")->setDescription($populateSettings["mpsColorCostPerPage"]);
                $this->_form->getElement("kilowattsPerHour")->setDescription($populateSettings["kilowattsPerHour"]);
                $this->_form->getElement("adminCostPerPage")->setDescription($populateSettings["adminCostPerPage"]);
                $this->_form->getElement("laborCostPerPage")->setDescription($populateSettings["laborCostPerPage"]);
                $this->_form->getElement("partsCostPerPage")->setDescription($populateSettings["partsCostPerPage"]);
                $this->_form->getElement("averageItHourlyRate")->setDescription($populateSettings["averageItHourlyRate"]);
                $this->_form->getElement("hoursSpentOnIt")->setDescription(($populateSettings["hoursSpentOnIt"] ? $populateSettings["hoursSpentOnIt"] : "15 minutes per week per printer"));
                $this->_form->getElement("costOfLabor")->setDescription(($populateSettings["costOfLabor"] ? $populateSettings["costOfLabor"] : "$200 per printer"));
                $this->_form->getElement("costToExecuteSuppliesOrder")->setDescription($populateSettings["costToExecuteSuppliesOrder"]);
                $this->_form->getElement("numberOfSupplyOrdersPerMonth")->setDescription($populateSettings["numberOfSupplyOrdersPerMonth"]);
                $this->_form->getElement("healthcheckPricingConfigId")->setDescription(Proposalgen_Model_PricingConfig::$ConfigNames[$populateSettings['healthcheckPricingConfigId']]);
                // Re-load the settings into Healthcheck settings
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
     * Gets the Healthcheck setting form
     *
     * @return Preferences_Form_HealthcheckSetting
     */
    public function getForm ()
    {
        if (!isset($this->_form))
        {
            $this->_form      = new Preferences_Form_HealthcheckSetting();
            $populateSettings = $this->_systemHealthcheckSettings->toArray();
            if ($this->_defaultSettings)
            {
                // Get the user settings for population
                $this->_systemHealthcheckSettings->populate($this->_defaultSettings);
                // Re-load the settings into Healthcheck settings
                $populateSettings = $this->_systemHealthcheckSettings->toArray();
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
            if($this->_form->allowsNull)
            {
                foreach($data as $key => $value)
                {
                    if($value === "")
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
     * Updates the Healthcheck's settings
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
            if ((int)$validData ['healthcheckPricingConfigId'] === Proposalgen_Model_PricingConfig::NONE)
            {
                unset($validData ['healthcheckPricingConfigId']);
            }

            $HealthcheckSetting = new Healthcheck_Model_Healthcheck_Setting();

            $HealthcheckSetting->populate($validData);

            if ($this->_defaultSettings)
            {
                $HealthcheckSetting->id = $this->_defaultSettings['id'];
            }
            else
            {
                $HealthcheckSetting->id = $this->_systemHealthcheckSettings->id;
            }

            Healthcheck_Model_Mapper_Healthcheck_Setting::getInstance()->save($HealthcheckSetting);

            return true;
        }

        return false;
    }
}
