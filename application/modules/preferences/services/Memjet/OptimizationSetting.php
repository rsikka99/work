<?php

/**
 * Class Preferences_Service_Memjet_OptimizationSetting
 */
class Preferences_Service_Memjet_OptimizationSetting
{
    /**
     * Default report settings and survey settings combined into an array
     *
     * @var Memjetoptimization_Model_Memjet_Optimization_Setting|null
     */
    protected $_defaultSettings;

    /**
     * Gets the report setting form.
     *
     * @var Preferences_Form_Memjet_OptimizationSetting
     */
    protected $_form;

    /**
     * @var Memjetoptimization_Model_Memjet_Optimization_Setting
     */
    protected $_systemSettings;

    /**
     *
     * @param $defaultSettings Memjetoptimization_Model_Memjet_Optimization_Setting
     */
    public function __construct ($defaultSettings = null)
    {
        $this->_systemSettings  = Memjetoptimization_Model_Mapper_Memjet_Optimization_Setting::getInstance()->find(1);
        $this->_defaultSettings = $defaultSettings;
    }

    /**
     * Gets the report setting form with default values populated
     *
     * @param $populateSettings array
     *
     * @return Preferences_Form_Memjet_OptimizationSetting
     */
    public function getFormWithDefaults ($populateSettings)
    {
        if (!isset($this->_form))
        {
            $this->_form = new Preferences_Form_Memjet_OptimizationSetting();

            // User form will populate the description with defaults
            if (is_array($this->_defaultSettings->toArray()))
            {
                $this->_form->getElement("pageCoverageMonochrome")->setDescription($populateSettings["pageCoverageMonochrome"]);
                $this->_form->getElement("pageCoverageColor")->setDescription($populateSettings["pageCoverageColor"]);
                $this->_form->getElement("lossThreshold")->setDescription($populateSettings["lossThreshold"]);
                $this->_form->getElement("blackToColorRatio")->setDescription($populateSettings["blackToColorRatio"]);
                $this->_form->getElement("costThreshold")->setDescription($populateSettings["costThreshold"]);
                $this->_form->getElement("adminCostPerPage")->setDescription(number_Format($populateSettings["adminCostPerPage"], 4));
                $this->_form->getElement("laborCostPerPage")->setDescription(number_Format($populateSettings["laborCostPerPage"], 4));
                $this->_form->getElement("partsCostPerPage")->setDescription(number_Format($populateSettings["partsCostPerPage"], 4));
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
     * @return Preferences_Form_Memjet_OptimizationSetting
     */
    public function getForm ()
    {
        if (!isset($this->_form))
        {
            $this->_form = new Preferences_Form_Memjet_OptimizationSetting();

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
                if (empty($value) && $value != 0)
                {
                    unset($validData [$key]);
                }
            }

            $memjetOptimizationSetting = new Memjetoptimization_Model_Memjet_Optimization_Setting();
            $rankingSetMapper          = Proposalgen_Model_Mapper_Toner_Vendor_Ranking_Set::getInstance();

            if (isset($validData['replacementColorRankSetArray']))
            {
                $memjetOptimizationSetting->replacementColorRankSetId = $rankingSetMapper->saveRankingSets($this->_defaultSettings->replacementColorRankSetId, $validData['replacementColorRankSetArray']);
            }
            else
            {
                Proposalgen_Model_Mapper_Toner_Vendor_Ranking::getInstance()->deleteByTonerVendorRankingId($this->_defaultSettings->replacementColorRankSetId);
            }

            if (isset($validData['replacementMonochromeRankSetArray']))
            {
                $memjetOptimizationSetting->replacementMonochromeRankSetId = $rankingSetMapper->saveRankingSets($this->_defaultSettings->replacementMonochromeRankSetId, $validData['replacementMonochromeRankSetArray']);
            }
            else
            {
                Proposalgen_Model_Mapper_Toner_Vendor_Ranking::getInstance()->deleteByTonerVendorRankingId($this->_defaultSettings->replacementMonochromeRankSetId);
            }

            if (isset($validData['dealerColorRankSetArray']))
            {
                $memjetOptimizationSetting->dealerColorRankSetId = $rankingSetMapper->saveRankingSets($this->_defaultSettings->dealerColorRankSetId, $validData['dealerColorRankSetArray']);
            }
            else
            {
                Proposalgen_Model_Mapper_Toner_Vendor_Ranking::getInstance()->deleteByTonerVendorRankingId($this->_defaultSettings->dealerColorRankSetId);
            }

            if (isset($validData['dealerMonochromeRankSetArray']))
            {
                $memjetOptimizationSetting->dealerMonochromeRankSetId = $rankingSetMapper->saveRankingSets($this->_defaultSettings->dealerMonochromeRankSetId, $validData['dealerMonochromeRankSetArray']);
            }
            else
            {
                Proposalgen_Model_Mapper_Toner_Vendor_Ranking::getInstance()->deleteByTonerVendorRankingId($this->_defaultSettings->dealerMonochromeRankSetId);
            }

            $memjetOptimizationSetting->populate($validData);

            if ($this->_defaultSettings)
            {
                $memjetOptimizationSetting->id = $this->_defaultSettings->id;
            }
            else
            {
                $memjetOptimizationSetting->id = $this->_systemSettings->id;
            }

            Memjetoptimization_Model_Mapper_Memjet_Optimization_Setting::getInstance()->save($memjetOptimizationSetting);

            return true;
        }

        return false;
    }
}