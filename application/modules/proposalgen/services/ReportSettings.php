<?php

class Proposalgen_Service_ReportSettings
{
    /**
     * The form for a client
     *
     * @var Proposalgen_Form_Settings_Report
     */
    protected $_form;

    /**
     * The system report settings
     *
     * @var Proposalgen_Model_Assessment_Setting
     */
    protected $_systemSettings;

    /**
     * The system report settings
     *
     * @var Proposalgen_Model_Assessment_Setting
     */
    protected $_dealerSettings;

    /**
     * The user report settings
     *
     * @var Proposalgen_Model_Assessment_Setting
     */
    protected $_userSettings;

    /**
     * The report's report settings
     *
     * @var Proposalgen_Model_Assessment_Setting
     */
    protected $_reportSettings;

    /**
     * The default settings (uses overrides)
     *
     * @var Proposalgen_Model_Assessment_Setting
     */
    protected $_defaultSettings;

    /**
     * The report
     *
     * @var Assessment_Model_Assessment
     */
    protected $_report;

    public function __construct ($reportId, $userId, $dealerId)
    {
        $user                  = Application_Model_Mapper_User::getInstance()->find($userId);
        $dealer                = Admin_Model_Mapper_Dealer::getInstance()->find($dealerId);
        $this->_report         = Assessment_Model_Mapper_Assessment::getInstance()->find($reportId);
        $this->_systemSettings = Assessment_Model_Mapper_Assessment_Setting::getInstance()->fetchSystemAssessmentSetting();
        $this->_dealerSettings = $dealer->getDealerSettings()->getAssessmentSettings();
        $this->_userSettings   = $user->getUserSettings()->getAssessmentSettings();
        $this->_reportSettings = Assessment_Model_Mapper_Assessment_Setting::getInstance()->fetch($reportId);

        // Calculate the default settings
        $this->_defaultSettings = new Proposalgen_Model_Assessment_Setting(array_merge($this->_userSettings->toArray(), $this->_dealerSettings->toArray()));
        $this->_defaultSettings->populate($this->_userSettings->toArray());
    }

    /**
     * Gets the client form
     *
     * @return Proposalgen_Form_Settings_Report
     */
    public function getForm ()
    {
        if (!isset($this->_form))
        {
            $this->_form = new Proposalgen_Form_Settings_Report($this->_defaultSettings);

            // Populate with initial data?
            $this->_form->populate(array_merge($this->_userSettings->toArray(), $this->_reportSettings->toArray()));
            $reportDate = date('m/d/Y', strtotime($this->_report->reportDate));
            $this->_form->populate(array(
                                        'reportDate' => $reportDate
                                   ));

            $this->_form->setDecorators(array(
                                             array(
                                                 'ViewScript',
                                                 array(
                                                     'viewScript' => 'forms/settings/report.phtml'
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
            $reportDate                = date('Y-m-d h:i:s', strtotime($validData ['reportDate']));
            $this->_report->reportDate = $reportDate;
            Proposalgen_Model_Mapper_Assessment::getInstance()->save($this->_report);

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
            $reportSettingsId = $this->_reportSettings->id;

            $this->_reportSettings->populate($this->_defaultSettings->toArray());
            $this->_reportSettings->populate($validData);

            // Restore the ID
            $this->_reportSettings->id = $reportSettingsId;

            Proposalgen_Model_Mapper_Assessment_Setting::getInstance()->save($this->_reportSettings);

            $this->getForm()->populate($this->_reportSettings->toArray());

            return true;
        }

        return false;
    }
}
