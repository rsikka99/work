<?php

/**
 * Class Preferences_Service_HardwareoptimizationSetting
 */
class Preferences_Service_HardwareoptimizationSetting
{
    /**
     * Default report settings and survey settings combined into an array
     *
     * @var Hardwareoptimization_Model_Hardware_Optimization_Setting|null
     */
    protected $_defaultSettings;

    /**
     * Gets the report setting form.
     *
     * @var Preferences_Form_HardwareoptimizationSetting
     */
    protected $_form;

    /**
     * @var Hardwareoptimization_Model_Hardware_Optimization_Setting
     */
    protected $_systemSettings;

    /**
     *
     * @param $defaultSettings Hardwareoptimization_Model_Hardware_Optimization_Setting
     */
    public function __construct ($defaultSettings = null)
    {
        $this->_systemSettings  = Hardwareoptimization_Model_Mapper_Hardware_Optimization_Setting::getInstance()->find(1);
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
            if (is_array($this->_defaultSettings->toArray()))
            {
                $this->_form->getElement("pageCoverageMonochrome")->setDescription($populateSettings["pageCoverageMonochrome"]);
                $this->_form->getElement("pageCoverageColor")->setDescription($populateSettings["pageCoverageColor"]);
                $this->_form->getElement("costThreshold")->setDescription($populateSettings["costThreshold"]);
                $this->_form->getElement("adminCostPerPage")->setDescription($populateSettings["adminCostPerPage"]);
                $this->_form->getElement("laborCostPerPage")->setDescription($populateSettings["laborCostPerPage"]);
                $this->_form->getElement("partsCostPerPage")->setDescription($populateSettings["partsCostPerPage"]);
                $this->_form->getElement("targetMonochromeCostPerPage")->setDescription($populateSettings["targetMonochromeCostPerPage"]);
                $this->_form->getElement("targetColorCostPerPage")->setDescription($populateSettings["targetColorCostPerPage"]);
                // Re-load the settings into report settings
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
                $this->_systemSettings->populate($this->_defaultSettings->toArray());
                $this->_form->populate($this->_defaultSettings->getTonerRankSets());
            }
            else
            {
                $this->_form->populate($this->_systemSettings->getTonerRankSets());
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

            $hardwareOptimizationSetting = new Hardwareoptimization_Model_Hardware_Optimization_Setting();
            $rankingSetMapper            = Proposalgen_Model_Mapper_Toner_Vendor_Ranking_Set::getInstance();

            if (isset($validData['replacementColorRankSetArray']))
            {
                $hardwareOptimizationSetting->replacementColorRankSetId = $rankingSetMapper->saveRankingSets($this->_defaultSettings->replacementColorRankSetId, $validData['replacementColorRankSetArray']);
            }
            else
            {
                Proposalgen_Model_Mapper_Toner_Vendor_Ranking::getInstance()->deleteByTonerVendorRankingId($this->_defaultSettings->replacementColorRankSetId);
            }

            if (isset($validData['replacementMonochromeRankSetArray']))
            {
                $hardwareOptimizationSetting->replacementMonochromeRankSetId = $rankingSetMapper->saveRankingSets($this->_defaultSettings->replacementMonochromeRankSetId, $validData['replacementMonochromeRankSetArray']);
            }
            else
            {
                Proposalgen_Model_Mapper_Toner_Vendor_Ranking::getInstance()->deleteByTonerVendorRankingId($this->_defaultSettings->replacementMonochromeRankSetId);
            }

            if (isset($validData['dealerColorRankSetArray']))
            {
                $hardwareOptimizationSetting->dealerColorRankSetId = $rankingSetMapper->saveRankingSets($this->_defaultSettings->dealerColorRankSetId, $validData['dealerColorRankSetArray']);
            }
            else
            {
                Proposalgen_Model_Mapper_Toner_Vendor_Ranking::getInstance()->deleteByTonerVendorRankingId($this->_defaultSettings->dealerColorRankSetId);
            }

            if (isset($validData['dealerMonochromeRankSetArray']))
            {
                $hardwareOptimizationSetting->dealerMonochromeRankSetId = $rankingSetMapper->saveRankingSets($this->_defaultSettings->dealerMonochromeRankSetId, $validData['dealerMonochromeRankSetArray']);
            }
            else
            {
                Proposalgen_Model_Mapper_Toner_Vendor_Ranking::getInstance()->deleteByTonerVendorRankingId($this->_defaultSettings->dealerMonochromeRankSetId);
            }

            $hardwareOptimizationSetting->populate($validData);

            if ($this->_defaultSettings)
            {
                $hardwareOptimizationSetting->id = $this->_defaultSettings->id;
            }
            else
            {
                $hardwareOptimizationSetting->id = $this->_systemSettings->id;
            }

            Hardwareoptimization_Model_Mapper_Hardware_Optimization_Setting::getInstance()->save($hardwareOptimizationSetting);

            return true;
        }

        return false;
    }
}
