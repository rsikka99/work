<?php

class Preferences_Service_HardwareoptimizationSetting
{
    /**
     * Default report settings and survey settings combined into an array
     *
     * @var Hardwareoptimization_Model_Setting|array|null
     */
    protected $_defaultSettings;

    /**
     * Gets the report setting form.
     *
     * @var Preferences_Form_HardwareoptimizationSetting
     */
    protected $_form;

    /**
     * @var Hardwareoptimization_Model_Setting
     */
    protected $_systemSettings;

    /**
     *
     * @param $defaultSettings array
     */
    public function __construct ($defaultSettings = null)
    {
        $this->_systemSettings  = Hardwareoptimization_Model_Mapper_Setting::getInstance()->find(1);
        $this->_defaultSettings = $defaultSettings;
    }

    /**
     * Gets the report setting form with default values populated
     *
     * @param $populateSettings array
     *
     * @return Preferences_Form_HardwareoptimizationSetting
     */
    public function getFormWithDefaults ($populateSettings)
    {
        if (!isset($this->_form))
        {
            $this->_form = new Preferences_Form_HardwareoptimizationSetting();

            // User form will populate the description with defaults
            if (is_array($this->_defaultSettings))
            {
                $this->_form->getElement("dealerMargin")->setDescription($populateSettings["dealerMargin"]);
                $this->_form->getElement("costThreshold")->setDescription($populateSettings["costThreshold"]);
                $this->_form->getElement("targetMonochromeCostPerPage")->setDescription($populateSettings["targetMonochromeCostPerPage"]);
                $this->_form->getElement("targetColorCostPerPage")->setDescription($populateSettings["targetColorCostPerPage"]);
                $this->_form->getElement("replacementPricingConfigId")->setDescription(Proposalgen_Model_PricingConfig::$ConfigNames[$populateSettings['replacementPricingConfigId']]);
                $this->_form->getElement("dealerPricingConfigId")->setDescription(Proposalgen_Model_PricingConfig::$ConfigNames[$populateSettings['dealerPricingConfigId']]);
                $this->_form->getElement("customerPricingConfigId")->setDescription(Proposalgen_Model_PricingConfig::$ConfigNames[$populateSettings['customerPricingConfigId']]);

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
     * @return Preferences_Form_HardwareoptimizationSetting
     */
    public function getForm ()
    {
        if (!isset($this->_form))
        {
            $this->_form = new Preferences_Form_HardwareoptimizationSetting();

            if ($this->_defaultSettings)
            {
                // Get the user settings for population
                $this->_systemSettings->populate($this->_defaultSettings);
            }

            // Get the current class of the element and adds default settings
            foreach ($this->_form->getElements() as $element)
            {
                $currentClass = $element->getAttrib('class');
                $element->setAttrib('class', "{$currentClass} defaultSettings ");
            }

            $this->_form->populate($this->_systemSettings->toArray());
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
            if ((int)$validData ['replacementPricingConfigId'] === Proposalgen_Model_PricingConfig::NONE)
            {
                unset($validData ['replacementPricingConfigId']);
            }
            if ((int)$validData ['dealerPricingConfigId'] === Proposalgen_Model_PricingConfig::NONE)
            {
                unset($validData ['dealerPricingConfigId']);
            }
            if ((int)$validData ['customerPricingConfigId'] === Proposalgen_Model_PricingConfig::NONE)
            {
                unset($validData ['customerPricingConfigId']);
            }

            $hardwareOptimizationSetting = new Hardwareoptimization_Model_Setting();
            $hardwareOptimizationSetting->populate($validData);

            if ($this->_defaultSettings)
            {
                $hardwareOptimizationSetting->id = $this->_defaultSettings['id'];
            }
            else
            {
                $hardwareOptimizationSetting = $this->_systemSettings->id;
            }

            Hardwareoptimization_Model_Mapper_Setting::getInstance()->save($hardwareOptimizationSetting);

            return true;
        }

        return false;
    }
}
