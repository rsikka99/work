<?php

/**
 * Class Hardwareoptimization_Service_Setting
 */
class Hardwareoptimization_Service_Setting
{
    /**
     *
     * @var Hardwareoptimization_Model_Hardware_Optimization_Setting|null
     */
    protected $_hardwareOptimizationSettings;

    /**
     *
     * @var Hardwareoptimization_Model_Hardware_Optimization_Setting|null
     */
    protected $_populateSettings;

    /**
     * Gets the report setting form.
     *
     * @var Hardwareoptimization_Form_Setting
     */
    protected $_form;

    /**
     * @var Hardwareoptimization_Model_Hardware_Optimization_Setting
     */
    protected $_systemSettings;

    /**
     *
     * @param $defaultSettings  Hardwareoptimization_Model_Hardware_Optimization_Setting
     * @param $populateSettings Hardwareoptimization_Model_Hardware_Optimization_Setting
     */
    public function __construct ($defaultSettings, $populateSettings)
    {
        $this->_hardwareOptimizationSettingss = $defaultSettings;
        $this->_populateSettings              = $populateSettings;
    }

    /**
     * Gets the report setting form with default values populated
     *
     *
     * @return Hardwareoptimization_Form_Setting
     */
    // populate setting should be dealer user hardware optimization settings overwritten
    public function getFormWithDefaults ()
    {
        if (!isset($this->_form))
        {
            $this->_form = new Hardwareoptimization_Form_Setting();

            // User form will populate the description with defaults
            $this->_form->getElement("pageCoverageMonochrome")->setDescription($this->_populateSettings->pageCoverageMonochrome . "%");
            $this->_form->getElement("pageCoverageColor")->setDescription($this->_populateSettings->pageCoverageColor . "%");
            $this->_form->getElement("partsCostPerPage")->setDescription("$" . $this->_populateSettings->partsCostPerPage . " / page");
            $this->_form->getElement("laborCostPerPage")->setDescription("$" . $this->_populateSettings->laborCostPerPage . " / page");
            $this->_form->getElement("adminCostPerPage")->setDescription("$" . $this->_populateSettings->adminCostPerPage . " / page");
            $this->_form->getElement("costThreshold")->setDescription("$" . $this->_populateSettings->costThreshold);
            $this->_form->getElement("targetMonochromeCostPerPage")->setDescription("$" . $this->_populateSettings->targetMonochromeCostPerPage . " / page");
            $this->_form->getElement("targetColorCostPerPage")->setDescription("$" . $this->_populateSettings->targetColorCostPerPage . " / page");

            // This function sets up the third row column header decorator
            $this->_form->allowNullValues();
            $this->_form->setUpFormWithDefaultDecorators();
            $this->_form->populate(array_merge($this->_hardwareOptimizationSettingss->toArray(), $this->_populateSettings->getTonerRankSets(), $this->_hardwareOptimizationSettingss->getTonerRankSets()));
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

        return $validData;
    }

    /**
     * Updates the report's settings
     *
     * @param array $data
     *
     * @param null  $defaultValues
     *
     * @return boolean
     */
    public function update ($data, $defaultValues = null)
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
            $this->_hardwareOptimizationSettings = new Hardwareoptimization_Model_Hardware_Optimization_Setting();
            $rankingSetMapper                    = Proposalgen_Model_Mapper_Toner_Vendor_Ranking_Set::getInstance();

            if (isset($validData['replacementColorRankSetArray']))
            {
                $this->_hardwareOptimizationSettings->replacementColorRankSetId = $rankingSetMapper->saveRankingSets($this->_hardwareOptimizationSettingss->replacementColorRankSetId, $validData['replacementColorRankSetArray']);
            }
            else
            {
                Proposalgen_Model_Mapper_Toner_Vendor_Ranking::getInstance()->deleteByTonerVendorRankingId($this->_hardwareOptimizationSettingss->replacementColorRankSetId);
            }

            if (isset($validData['replacementMonochromeRankSetArray']))
            {
                $this->_hardwareOptimizationSettings->replacementMonochromeRankSetId = $rankingSetMapper->saveRankingSets($this->_hardwareOptimizationSettingss->replacementMonochromeRankSetId, $validData['replacementMonochromeRankSetArray']);
            }
            else
            {
                Proposalgen_Model_Mapper_Toner_Vendor_Ranking::getInstance()->deleteByTonerVendorRankingId($this->_hardwareOptimizationSettingss->replacementMonochromeRankSetId);
            }

            if (isset($validData['dealerColorRankSetArray']))
            {
                $this->_hardwareOptimizationSettings->dealerColorRankSetId = $rankingSetMapper->saveRankingSets($this->_hardwareOptimizationSettingss->dealerColorRankSetId, $validData['dealerColorRankSetArray']);
            }
            else
            {
                Proposalgen_Model_Mapper_Toner_Vendor_Ranking::getInstance()->deleteByTonerVendorRankingId($this->_hardwareOptimizationSettingss->dealerColorRankSetId);
            }

            if (isset($validData['dealerMonochromeRankSetArray']))
            {
                $this->_hardwareOptimizationSettings->dealerMonochromeRankSetId = $rankingSetMapper->saveRankingSets($this->_hardwareOptimizationSettingss->dealerMonochromeRankSetId, $validData['dealerMonochromeRankSetArray']);
            }
            else
            {
                Proposalgen_Model_Mapper_Toner_Vendor_Ranking::getInstance()->deleteByTonerVendorRankingId($this->_hardwareOptimizationSettingss->dealerMonochromeRankSetId);
            }

            $this->_populateSettings->replacementMonochromeRankSetId = $this->_hardwareOptimizationSettings->replacementMonochromeRankSetId;
            $this->_populateSettings->replacementColorRankSetId      = $this->_hardwareOptimizationSettings->replacementColorRankSetId;
            $this->_populateSettings->dealerMonochromeRankSetId      = $this->_hardwareOptimizationSettings->dealerMonochromeRankSetId;
            $this->_populateSettings->dealerColorRankSetId           = $this->_hardwareOptimizationSettings->dealerColorRankSetId;

            $this->_hardwareOptimizationSettings->populate($this->_populateSettings->toArray());
            $this->_hardwareOptimizationSettings->populate($validData);
            $this->_hardwareOptimizationSettings->id = $this->_hardwareOptimizationSettingss->id;

            Hardwareoptimization_Model_Mapper_Hardware_Optimization_Setting::getInstance()->save($this->_hardwareOptimizationSettings);

            $this->_form->populate($this->_hardwareOptimizationSettings->toArray());

            return true;
        }

        return false;
    }
}
