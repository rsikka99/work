<?php

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
    protected $_systemSettings;

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
     * The assessment
     *
     * @var Assessment_Model_Assessment
     */
    protected $_assessment;

    public function __construct ($assessmentId, $userId, $dealerId)
    {
        $user                      = Application_Model_Mapper_User::getInstance()->find($userId);
        $dealer                    = Admin_Model_Mapper_Dealer::getInstance()->find($dealerId);
        $this->_assessment         = Assessment_Model_Mapper_Assessment::getInstance()->find($assessmentId);
        $this->_systemSettings     = Assessment_Model_Mapper_Assessment_Setting::getInstance()->fetchSystemAssessmentSetting();
        $this->_dealerSettings     = $dealer->getDealerSettings()->getAssessmentSettings();
        $this->_userSettings       = $user->getUserSettings()->getAssessmentSettings();
        $this->_assessmentSettings = Assessment_Model_Mapper_Assessment_Setting::getInstance()->fetchAssessmentAssessmentSetting($assessmentId);

        // Calculate the default settings
        $this->_defaultSettings = new Assessment_Model_Assessment_Setting(array_merge($this->_userSettings->toArray(), $this->_dealerSettings->toArray()));
        $this->_defaultSettings->populate($this->_userSettings->toArray());
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
            $this->_form = new Assessment_Form_Assessment_Settings($this->_defaultSettings);

            // Populate with initial data?
            $this->_form->populate(array_merge($this->_userSettings->toArray(), $this->_assessmentSettings->toArray()));
            $assessmentDate = date('m/d/Y', strtotime($this->_assessment->assessmentDate));
            $this->_form->populate(array(
                                        'assessmentDate' => $assessmentDate
                                   ));

            $this->_form->setDecorators(array(
                                             array(
                                                 'ViewScript',
                                                 array(
                                                     'viewScript' => 'forms/settings/assessment.phtml'
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
            $assessmentDate                    = date('Y-m-d h:i:s', strtotime($validData ['assessmentDate']));
            $this->_assessment->assessmentDate = $assessmentDate;
            Assessment_Model_Mapper_Assessment::getInstance()->save($this->_assessment);

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

            // Save the id as it will get erased
            $assessmentSettingsId = $this->_assessmentSettings->id;

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
