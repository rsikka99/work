<?php

/**
 * Class Memjetoptimization_Service_Setting
 */
class Memjetoptimization_Service_Setting
{
    /**
     *
     * @var Memjetoptimization_Model_Memjet_Optimization_Setting|null
     */
    protected $_MemjetoptimizationSettings;

    /**
     *
     * @var Memjetoptimization_Model_Memjet_Optimization_Setting|null
     */
    protected $_populateSettings;

    /**
     * Gets the report setting form.
     *
     * @var Memjetoptimization_Form_Setting
     */
    protected $_form;

    /**
     * @var Memjetoptimization_Model_Memjet_Optimization_Setting
     */
    protected $_systemSettings;

    /**
     * @var Memjetoptimization_Model_Memjet_Optimization
     */
    protected $_Memjetoptimization;

    /**
     *
     * @param $defaultSettings        Memjetoptimization_Model_Memjet_Optimization_Setting
     * @param $populateSettings       Memjetoptimization_Model_Memjet_Optimization_Setting
     * @param $MemjetoptimizationId   int
     */
    public function __construct ($defaultSettings, $populateSettings, $MemjetoptimizationId)
    {
        $this->_Memjetoptimization          = Memjetoptimization_Model_Mapper_Memjet_Optimization::getInstance()->find($MemjetoptimizationId);
        $this->_MemjetoptimizationSettingss = $defaultSettings;
        $this->_populateSettings            = $populateSettings;
    }

    /**
     * Gets the report setting form with default values populated
     *
     *
     * @return Memjetoptimization_Form_Setting
     */
    // populate setting should be dealer user Memjet optimization settings overwritten
    public function getFormWithDefaults ()
    {
        if (!isset($this->_form))
        {
            $this->_form = new Memjetoptimization_Form_Setting();

            // User form will populate the description with defaults
            $this->_form->getElement("name")->setDescription("Memjetoptimization" . date('Ymd'));
            $this->_form->getElement("pageCoverageMonochrome")->setDescription($this->_populateSettings->pageCoverageMonochrome . "%");
            $this->_form->getElement("pageCoverageColor")->setDescription($this->_populateSettings->pageCoverageColor . "%");
            $this->_form->getElement("partsCostPerPage")->setDescription("$" . $this->_populateSettings->partsCostPerPage . " / page");
            $this->_form->getElement("laborCostPerPage")->setDescription("$" . $this->_populateSettings->laborCostPerPage . " / page");
            $this->_form->getElement("adminCostPerPage")->setDescription("$" . $this->_populateSettings->adminCostPerPage . " / page");
            $this->_form->getElement("lossThreshold")->setDescription("$" . $this->_populateSettings->lossThreshold);
            $this->_form->getElement("blackToColorRatio")->setDescription($this->_populateSettings->blackToColorRatio . "%");

            $this->_form->getElement("targetMonochromeCostPerPage")->setDescription("$" . $this->_populateSettings->targetMonochromeCostPerPage . " / page");
            $this->_form->getElement("targetColorCostPerPage")->setDescription("$" . $this->_populateSettings->targetColorCostPerPage . " / page");

            // This function sets up the third row column header decorator
            $this->_form->allowNullValues();
            $this->_form->setUpFormWithDefaultDecorators();
            $MemjetSettingsArray = $this->_MemjetoptimizationSettingss->toArray();

            foreach ($MemjetSettingsArray as $key => $value)
            {
                if ($value === null)
                {
                    unset($MemjetSettingsArray[$key]);
                }
            }

            $this->_form->populate(array_merge($this->_populateSettings->toArray(), $MemjetSettingsArray, $this->_populateSettings->getTonerRankSets(), $this->_MemjetoptimizationSettingss->getTonerRankSets()));
            $this->_form->populate(array('name' => $this->_Memjetoptimization->name));
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
                if (empty($value) && $value != 0 || $value === '')
                {
                    unset($validData [$key]);
                }
            }

            // Save the report name
            $this->_Memjetoptimization->name = (isset($validData['name'])) ? $validData['name'] : "Memjetoptimization" . date('Ymd');
            Memjetoptimization_Model_Mapper_Memjet_Optimization::getInstance()->save($this->_Memjetoptimization);

            $this->_MemjetoptimizationSettings = new Memjetoptimization_Model_Memjet_Optimization_Setting();
            $rankingSetMapper                  = Proposalgen_Model_Mapper_Toner_Vendor_Ranking_Set::getInstance();

            if (isset($validData['replacementColorRankSetArray']))
            {
                $this->_MemjetoptimizationSettings->replacementColorRankSetId = $rankingSetMapper->saveRankingSets($this->_MemjetoptimizationSettingss->replacementColorRankSetId, $validData['replacementColorRankSetArray']);
            }
            else
            {
                Proposalgen_Model_Mapper_Toner_Vendor_Ranking::getInstance()->deleteByTonerVendorRankingId($this->_MemjetoptimizationSettingss->replacementColorRankSetId);
            }

            if (isset($validData['replacementMonochromeRankSetArray']))
            {
                $this->_MemjetoptimizationSettings->replacementMonochromeRankSetId = $rankingSetMapper->saveRankingSets($this->_MemjetoptimizationSettingss->replacementMonochromeRankSetId, $validData['replacementMonochromeRankSetArray']);
            }
            else
            {
                Proposalgen_Model_Mapper_Toner_Vendor_Ranking::getInstance()->deleteByTonerVendorRankingId($this->_MemjetoptimizationSettingss->replacementMonochromeRankSetId);
            }

            if (isset($validData['dealerColorRankSetArray']))
            {
                $this->_MemjetoptimizationSettings->dealerColorRankSetId = $rankingSetMapper->saveRankingSets($this->_MemjetoptimizationSettingss->dealerColorRankSetId, $validData['dealerColorRankSetArray']);
            }
            else
            {
                Proposalgen_Model_Mapper_Toner_Vendor_Ranking::getInstance()->deleteByTonerVendorRankingId($this->_MemjetoptimizationSettingss->dealerColorRankSetId);
            }

            if (isset($validData['dealerMonochromeRankSetArray']))
            {
                $this->_MemjetoptimizationSettings->dealerMonochromeRankSetId = $rankingSetMapper->saveRankingSets($this->_MemjetoptimizationSettingss->dealerMonochromeRankSetId, $validData['dealerMonochromeRankSetArray']);
            }
            else
            {
                Proposalgen_Model_Mapper_Toner_Vendor_Ranking::getInstance()->deleteByTonerVendorRankingId($this->_MemjetoptimizationSettingss->dealerMonochromeRankSetId);
            }

            $this->_populateSettings->replacementMonochromeRankSetId = $this->_MemjetoptimizationSettings->replacementMonochromeRankSetId;
            $this->_populateSettings->replacementColorRankSetId      = $this->_MemjetoptimizationSettings->replacementColorRankSetId;
            $this->_populateSettings->dealerMonochromeRankSetId      = $this->_MemjetoptimizationSettings->dealerMonochromeRankSetId;
            $this->_populateSettings->dealerColorRankSetId           = $this->_MemjetoptimizationSettings->dealerColorRankSetId;

            $this->_MemjetoptimizationSettings->populate($this->_populateSettings->toArray());
            $this->_MemjetoptimizationSettings->populate($validData);
            $this->_MemjetoptimizationSettings->id = $this->_MemjetoptimizationSettingss->id;

            Memjetoptimization_Model_Mapper_Memjet_Optimization_Setting::getInstance()->save($this->_MemjetoptimizationSettings);

            $this->_form->populate($this->_MemjetoptimizationSettings->toArray());
            $this->_form->populate(array('name' => $this->_Memjetoptimization->name));

            return true;
        }

        return false;
    }
}
