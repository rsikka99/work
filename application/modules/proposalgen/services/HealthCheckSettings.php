<?php

class Proposalgen_Service_HealthcheckSettings
{
    /**
     * The form for a client
     *
     * @var Proposalgen_Form_Settings_Healthcheck
     */
    protected $_form;

    /**
     * The system Healthcheck settings
     *
     * @var Proposalgen_Model_Healthcheck_Setting
     */
    protected $_systemSettings;

    /**
     * The system Healthcheck settings
     *
     * @var Proposalgen_Model_Healthcheck_Setting
     */
    protected $_dealerSettings;

    /**
     * The user Healthcheck settings
     *
     * @var Proposalgen_Model_Healthcheck_Setting
     */
    protected $_userSettings;

    /**
     * The Healthcheck's Healthcheck settings
     *
     * @var Proposalgen_Model_Healthcheck_Setting
     */
    protected $_HealthcheckSettings;

    /**
     * The default settings (uses overrides)
     *
     * @var Proposalgen_Model_Healthcheck_Setting
     */
    protected $_defaultSettings;

    /**
     * The Healthcheck
     *
     * @var Proposalgen_Model_Healthcheck
     */
    protected $_Healthcheck;

    public function __construct ($HealthcheckId, $userId, $dealerId)
    {
        $this->_Healthcheck         = Proposalgen_Model_Mapper_Healthcheck::getInstance()->find($HealthcheckId);
        $this->_systemSettings = Proposalgen_Model_Mapper_Healthcheck_Setting::getInstance()->fetchSystemHealthcheckSetting();
        $this->_dealerSettings = Proposalgen_Model_Mapper_Healthcheck_Setting::getInstance()->fetchDealerSetting($dealerId);
//
//        $this->_userSettings   = Proposalgen_Model_Mapper_Healthcheck_Setting::getInstance()->fetchUserSetting($userId); //TODO FIX THIS ONCE USER IS DONE
        $this->_HealthcheckSettings = Proposalgen_Model_Mapper_Healthcheck_Setting::getInstance()->fetchSetting($HealthcheckId);
//
//        // Calculate the default settings
//        $this->_defaultSettings = new Proposalgen_Model_Healthcheck_Setting(array_merge($this->_userSettings->toArray(), $this->_dealerSettings->toArray())); TODO USE THIS
        $this->_defaultSettings = new Proposalgen_Model_Healthcheck_Setting(array_merge($this->_dealerSettings->toArray(), $this->_dealerSettings->toArray()));
//        $this->_defaultSettings->populate($this->_userSettings->toArray());
        $this->_defaultSettings->populate($this->_dealerSettings->toArray());
    }

    /**
     * Gets the client form
     *
     * @return Proposalgen_Form_Settings_Healthcheck
     */
    public function getForm ()
    {
        if (!isset($this->_form))
        {
            $this->_form = new Proposalgen_Form_Settings_Healthcheck($this->_defaultSettings);

            // Populate with initial data?
//            $this->_form->populate(array_merge($this->_userSettings->toArray(), $this->_HealthcheckSettings->toArray()));
            $this->_form->populate(array_merge($this->_dealerSettings->toArray(), $this->_HealthcheckSettings->toArray()));
            $reportDate = date('m/d/Y', strtotime($this->_Healthcheck->reportDate));
            $this->_form->populate(array(
                                        'reportDate' => $reportDate
                                   ));

            $this->_form->setDecorators(array(
                                             array(
                                                 'ViewScript',
                                                 array(
                                                     'viewScript' => 'forms/settings/healthcheck.phtml'
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
            $reportDate                = date('Y-m-d h:i:s', strtotime($validData ['reportDate']));
            $this->_Healthcheck->reportDate = $reportDate;
            Proposalgen_Model_Mapper_Healthcheck::getInstance()->save($this->_Healthcheck);

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
            $HealthcheckSettingsId = $this->_HealthcheckSettings->id;

            $this->_HealthcheckSettings->populate($this->_defaultSettings->toArray());
            $this->_HealthcheckSettings->populate($validData);

            // Restore the ID
            $this->_HealthcheckSettings->id = $HealthcheckSettingsId;

            Proposalgen_Model_Mapper_Healthcheck_Setting::getInstance()->save($this->_HealthcheckSettings);

            $this->getForm()->populate($this->_HealthcheckSettings->toArray());

            return true;
        }

        return false;
    }
}
