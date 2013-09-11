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
     * @param $defaultSettings Healthcheck_Model_Healthcheck_Setting
     */
    public function __construct ($defaultSettings = null)
    {
        $this->_systemHealthcheckSettings = Healthcheck_Model_Mapper_Healthcheck_Setting::getInstance()->find(1);
        $this->_defaultSettings           = $defaultSettings;
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
            $this->_form = new Preferences_Form_HealthcheckSetting();

            // User form will populate the description with defaults
            if (is_array($this->_defaultSettings->toArray()))
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
                $this->_form->getElement("kilowattsPerHour")->setDescription(number_Format($populateSettings["kilowattsPerHour"],4));
                $this->_form->getElement("adminCostPerPage")->setDescription(number_Format($populateSettings["adminCostPerPage"],4));
                $this->_form->getElement("laborCostPerPage")->setDescription(number_Format($populateSettings["laborCostPerPage"],4));
                $this->_form->getElement("partsCostPerPage")->setDescription(number_Format($populateSettings["partsCostPerPage"],4));
                $this->_form->getElement("averageItHourlyRate")->setDescription($populateSettings["averageItHourlyRate"]);
                $this->_form->getElement("hoursSpentOnIt")->setDescription(($populateSettings["hoursSpentOnIt"] ? $populateSettings["hoursSpentOnIt"] : "15 minutes per week per printer"));
                $this->_form->getElement("costOfLabor")->setDescription(($populateSettings["costOfLabor"] ? $populateSettings["costOfLabor"] : "$200 per printer"));
                $this->_form->getElement("costToExecuteSuppliesOrder")->setDescription($populateSettings["costToExecuteSuppliesOrder"]);
                $this->_form->getElement("numberOfSupplyOrdersPerMonth")->setDescription($populateSettings["numberOfSupplyOrdersPerMonth"]);
                // Re-load the settings into Healthcheck settings
                $populateSettings = $this->_defaultSettings->toArray();
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
            $this->_form = new Preferences_Form_HealthcheckSetting();

            if ($this->_defaultSettings)
            {
                // Override the system settings with anything that the dealer has saved ($this->_defaultSettings).
                $this->_systemHealthcheckSettings->populate($this->_defaultSettings);
                $this->_form->populate($this->_defaultSettings->getTonerRankSets());
            }
            else
            {
                $this->_form->populate($this->_systemHealthcheckSettings->getTonerRankSets());
            }

            // Get the current class of the element and adds default settings
            foreach ($this->_form->getElements() as $element)
            {
                $currentClass = $element->getAttrib('class');
                $element->setAttrib('class', "{$currentClass} defaultSettings ");
            }

            $this->_form->populate($this->_systemHealthcheckSettings->toArray());
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
                if (empty($value) && $value != 0)
                {
                    unset($validData [$key]);
                }
            }

            $healthcheckSetting = new Healthcheck_Model_Healthcheck_Setting();
            $rankingSetMapper   = Proposalgen_Model_Mapper_Toner_Vendor_Ranking_Set::getInstance();

            if (isset($validData['customerColorRankSetArray']))
            {
                $healthcheckSetting->customerColorRankSetId = $rankingSetMapper->saveRankingSets($this->_defaultSettings->customerColorRankSetId, $validData['customerColorRankSetArray']);
            }
            else
            {
                Proposalgen_Model_Mapper_Toner_Vendor_Ranking::getInstance()->deleteByTonerVendorRankingId($this->_defaultSettings->customerColorRankSetId);
            }

            if (isset($validData['customerMonochromeRankSetArray']))
            {
                $healthcheckSetting->customerMonochromeRankSetId = $rankingSetMapper->saveRankingSets($this->_defaultSettings->customerMonochromeRankSetId, $validData['customerMonochromeRankSetArray']);
            }
            else
            {
                Proposalgen_Model_Mapper_Toner_Vendor_Ranking::getInstance()->deleteByTonerVendorRankingId($this->_defaultSettings->customerMonochromeRankSetId);
            }

            $healthcheckSetting->populate($validData);

            if ($this->_defaultSettings)
            {
                $healthcheckSetting->id = $this->_defaultSettings->id;
            }
            else
            {
                $healthcheckSetting->id = $this->_systemHealthcheckSettings->id;
            }

            Healthcheck_Model_Mapper_Healthcheck_Setting::getInstance()->save($healthcheckSetting);

            return true;
        }

        return false;
    }
}
