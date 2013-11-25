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
    protected $_memjetOptimizationSettings;

    /**
     *
     * @var Memjetoptimization_Model_Memjet_Optimization_Setting|null
     */
    protected $_memjetOptimizationDefaultSettings;

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
    protected $_memjetOptimization;

    /**
     *
     * @param $defaultSettings        Memjetoptimization_Model_Memjet_Optimization_Setting
     * @param $populateSettings       Memjetoptimization_Model_Memjet_Optimization_Setting
     * @param $memjetOptimizationId   int
     */
    public function __construct ($defaultSettings, $populateSettings, $memjetOptimizationId)
    {
        $this->_memjetOptimization                = Memjetoptimization_Model_Mapper_Memjet_Optimization::getInstance()->find($memjetOptimizationId);
        $this->_memjetOptimizationDefaultSettings = $defaultSettings;
        $this->_populateSettings                  = $populateSettings;
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
            $this->_form->getElement("costThreshold")->setDescription("$" . $this->_populateSettings->costThreshold);

            $this->_form->getElement("targetMonochromeCostPerPage")->setDescription("$" . $this->_populateSettings->targetMonochromeCostPerPage . " / page");
            $this->_form->getElement("targetColorCostPerPage")->setDescription("$" . $this->_populateSettings->targetColorCostPerPage . " / page");

            // This function sets up the third row column header decorator
            $this->_form->allowNullValues();
            $this->_form->setUpFormWithDefaultDecorators();
            $memjetSettingsArray = $this->_memjetOptimizationDefaultSettings->toArray();

            foreach ($memjetSettingsArray as $key => $value)
            {
                if ($value === null)
                {
                    unset($memjetSettingsArray[$key]);
                }
            }

            $this->_form->populate(array_merge($this->_populateSettings->toArray(), $memjetSettingsArray, $this->_populateSettings->getTonerRankSets(), $this->_memjetOptimizationDefaultSettings->getTonerRankSets()));
            $this->_form->populate(array('name' => $this->_memjetOptimization->name));
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
     * @return boolean
     */
    public function update ($data)
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
            $this->_memjetOptimization->name = (isset($validData['name'])) ? $validData['name'] : "Memjetoptimization" . date('Ymd');
            Memjetoptimization_Model_Mapper_Memjet_Optimization::getInstance()->save($this->_memjetOptimization);

            $this->_memjetOptimizationSettings = new Memjetoptimization_Model_Memjet_Optimization_Setting();
            $rankingSetMapper                  = Proposalgen_Model_Mapper_Toner_Vendor_Ranking_Set::getInstance();

            if (isset($validData['replacementColorRankSetArray']))
            {
                $this->_memjetOptimizationSettings->replacementColorRankSetId = $rankingSetMapper->saveRankingSets($this->_memjetOptimizationDefaultSettings->replacementColorRankSetId, $validData['replacementColorRankSetArray']);
            }
            else
            {
                Proposalgen_Model_Mapper_Toner_Vendor_Ranking::getInstance()->deleteByTonerVendorRankingId($this->_memjetOptimizationDefaultSettings->replacementColorRankSetId);
            }

            if (isset($validData['replacementMonochromeRankSetArray']))
            {
                $this->_memjetOptimizationSettings->replacementMonochromeRankSetId = $rankingSetMapper->saveRankingSets($this->_memjetOptimizationDefaultSettings->replacementMonochromeRankSetId, $validData['replacementMonochromeRankSetArray']);
            }
            else
            {
                Proposalgen_Model_Mapper_Toner_Vendor_Ranking::getInstance()->deleteByTonerVendorRankingId($this->_memjetOptimizationDefaultSettings->replacementMonochromeRankSetId);
            }

            if (isset($validData['dealerColorRankSetArray']))
            {
                $this->_memjetOptimizationSettings->dealerColorRankSetId = $rankingSetMapper->saveRankingSets($this->_memjetOptimizationDefaultSettings->dealerColorRankSetId, $validData['dealerColorRankSetArray']);
            }
            else
            {
                Proposalgen_Model_Mapper_Toner_Vendor_Ranking::getInstance()->deleteByTonerVendorRankingId($this->_memjetOptimizationDefaultSettings->dealerColorRankSetId);
            }

            if (isset($validData['dealerMonochromeRankSetArray']))
            {
                $this->_memjetOptimizationSettings->dealerMonochromeRankSetId = $rankingSetMapper->saveRankingSets($this->_memjetOptimizationDefaultSettings->dealerMonochromeRankSetId, $validData['dealerMonochromeRankSetArray']);
            }
            else
            {
                Proposalgen_Model_Mapper_Toner_Vendor_Ranking::getInstance()->deleteByTonerVendorRankingId($this->_memjetOptimizationDefaultSettings->dealerMonochromeRankSetId);
            }

            $this->_populateSettings->replacementMonochromeRankSetId = $this->_memjetOptimizationSettings->replacementMonochromeRankSetId;
            $this->_populateSettings->replacementColorRankSetId      = $this->_memjetOptimizationSettings->replacementColorRankSetId;
            $this->_populateSettings->dealerMonochromeRankSetId      = $this->_memjetOptimizationSettings->dealerMonochromeRankSetId;
            $this->_populateSettings->dealerColorRankSetId           = $this->_memjetOptimizationSettings->dealerColorRankSetId;

            $this->_memjetOptimizationSettings->populate($this->_populateSettings->toArray());
            $this->_memjetOptimizationSettings->populate($validData);
            $this->_memjetOptimizationSettings->id = $this->_memjetOptimizationDefaultSettings->id;

            Memjetoptimization_Model_Mapper_Memjet_Optimization_Setting::getInstance()->save($this->_memjetOptimizationSettings);

            $this->_form->populate($this->_memjetOptimizationSettings->toArray());
            $this->_form->populate(array('name' => $this->_memjetOptimization->name));

            return true;
        }

        return false;
    }
}
