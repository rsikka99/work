<?php

/**
 * Class Assessment_Service_Assessment_Settings
 */
class Assessment_Service_Assessment_Settings
{
    /**
     * The form for a client
     *
     * @var Assessment_Form_Assessment_Settings
     */
    protected $_form;

    /**
     * The system assessment settings
     *
     * @var Assessment_Model_Assessment_Setting
     */
    protected $_dealerSettings;

    /**
     * The user assessment settings
     *
     * @var Assessment_Model_Assessment_Setting
     */
    protected $_userSettings;

    /**
     * The assessment's assessment settings
     *
     * @var Assessment_Model_Assessment_Setting
     */
    protected $_assessmentSettings;

    /**
     * The default settings (uses overrides)
     *
     * @var Assessment_Model_Assessment_Setting
     */
    protected $_defaultSettings;

    /**
     * The settings used to populate the values in the form
     *
     * @var Assessment_Model_Assessment_Setting
     */
    protected $_populateSettings;

    /**
     * The assessment
     *
     * @var Assessment_Model_Assessment
     */
    protected $_assessment;

    /**
     * @param int $assessmentId
     * @param int $userId
     * @param int $dealerId
     */
    public function __construct ($assessmentId, $userId, $dealerId)
    {
        $user                      = Application_Model_Mapper_User::getInstance()->find($userId);
        $dealer                    = Application_Model_Mapper_Dealer::getInstance()->find($dealerId);
        $this->_assessment         = Assessment_Model_Mapper_Assessment::getInstance()->find($assessmentId);
        $this->_dealerSettings     = $dealer->getDealerSettings()->getAssessmentSettings();
        $this->_userSettings       = $user->getUserSettings()->getAssessmentSettings();
        $this->_assessmentSettings = $this->_assessment->getAssessmentSettings();

        $this->_populateSettings = clone $this->_dealerSettings;
        $this->_populateSettings->populate($this->_userSettings->toArray());
        $this->_populateSettings->populate($this->_assessmentSettings->toArray());

        // Calculate the default settings
        $this->_defaultSettings = new Assessment_Model_Assessment_Setting(array_merge($this->_userSettings->toArray(), $this->_dealerSettings->toArray()));
    }

    /**
     * Gets the client form
     *
     * @return Assessment_Form_Assessment_Settings
     */
    public function getForm ()
    {
        if (!isset($this->_form))
        {
            $this->_form = new Assessment_Form_Assessment_Settings($this->_defaultSettings, $this->_assessment->id);

            $this->_form->populate(array_merge($this->_populateSettings->toArray(), $this->_populateSettings->getTonerRankSets()));
            $reportDate = date('m/d/Y', strtotime($this->_assessment->reportDate));
            $reportName = $this->_assessment->name;

            $this->_form->populate(array(
                'reportDate' => $reportDate,
                'name'       => $reportName,
            ));

            $this->_form->setDecorators(array(
                array(
                    'ViewScript',
                    array(
                        'viewScript' => 'forms/assessment/settings.phtml'
                    )
                )
            ));
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

        return $validData;
    }

    /**
     * Updates the assessment's settings
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
            $reportDate                    = date('Y-m-d h:i:s', strtotime($validData ['reportDate']));
            $this->_assessment->reportDate = $reportDate;


            foreach ($validData as $key => $value)
            {
                if (empty($value) && $value != 0)
                {
                    unset($validData [$key]);
                }
            }

            $this->_assessment->name = (isset($validData['name'])) ? $validData['name'] : "Assessment " . date('Y/m/d');
            Assessment_Model_Mapper_Assessment::getInstance()->save($this->_assessment);

            // Save the id as it will get erased
            $assessmentSettingsId = $this->_assessmentSettings->id;

            $rankingSetMapper = Proposalgen_Model_Mapper_Toner_Vendor_Ranking_Set::getInstance();

            // If we have selected toners, we have to save the to the table
            if (isset($validData['customerColorRankSetArray']))
            {
                $this->_assessmentSettings->customerColorRankSetId = $rankingSetMapper->saveRankingSets($this->_assessmentSettings->customerColorRankSetId, $validData['customerColorRankSetArray']);
            }
            else
            {
                Proposalgen_Model_Mapper_Toner_Vendor_Ranking::getInstance()->deleteByTonerVendorRankingId($this->_assessmentSettings->customerColorRankSetId);
            }

            if (isset($validData['customerMonochromeRankSetArray']))
            {
                $this->_assessmentSettings->customerMonochromeRankSetId = $rankingSetMapper->saveRankingSets($this->_assessmentSettings->customerMonochromeRankSetId, $validData['customerMonochromeRankSetArray']);
            }
            else
            {
                Proposalgen_Model_Mapper_Toner_Vendor_Ranking::getInstance()->deleteByTonerVendorRankingId($this->_assessmentSettings->customerMonochromeRankSetId);
            }

            if (isset($validData['dealerColorRankSetArray']))
            {
                $this->_assessmentSettings->dealerColorRankSetId = $rankingSetMapper->saveRankingSets($this->_assessmentSettings->dealerColorRankSetId, $validData['dealerColorRankSetArray']);
            }
            else
            {
                Proposalgen_Model_Mapper_Toner_Vendor_Ranking::getInstance()->deleteByTonerVendorRankingId($this->_assessmentSettings->dealerColorRankSetId);
            }

            if (isset($validData['dealerMonochromeRankSetArray']))
            {
                $this->_assessmentSettings->dealerMonochromeRankSetId = $rankingSetMapper->saveRankingSets($this->_assessmentSettings->dealerMonochromeRankSetId, $validData['dealerMonochromeRankSetArray']);
            }
            else
            {
                Proposalgen_Model_Mapper_Toner_Vendor_Ranking::getInstance()->deleteByTonerVendorRankingId($this->_assessmentSettings->dealerMonochromeRankSetId);
            }

            // Override the setting so the id doesn't get overwritten when we populate
            $this->_defaultSettings->customerMonochromeRankSetId = $this->_assessmentSettings->customerMonochromeRankSetId;
            $this->_defaultSettings->customerColorRankSetId      = $this->_assessmentSettings->customerColorRankSetId;
            $this->_defaultSettings->dealerMonochromeRankSetId   = $this->_assessmentSettings->dealerMonochromeRankSetId;
            $this->_defaultSettings->dealerColorRankSetId        = $this->_assessmentSettings->dealerColorRankSetId;

            $this->_assessmentSettings->populate($this->_defaultSettings->toArray());
            $this->_assessmentSettings->populate($validData);
            // Restore the ID
            $this->_assessmentSettings->id = $assessmentSettingsId;

            Assessment_Model_Mapper_Assessment_Setting::getInstance()->save($this->_assessmentSettings);

            $this->getForm()->populate($this->_assessmentSettings->toArray());

            return true;
        }

        return false;
    }


}
