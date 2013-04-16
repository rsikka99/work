<?php

class Proposalgen_Service_HealthCheckSettings
{
    /**
     * The form for a client
     *
     * @var Proposalgen_Form_Settings_HealthCheck
     */
    protected $_form;

    /**
     * The system HealthCheck settings
     *
     * @var Proposalgen_Model_HealthCheck_Setting
     */
    protected $_systemSettings;

    /**
     * The system HealthCheck settings
     *
     * @var Proposalgen_Model_HealthCheck_Setting
     */
    protected $_dealerSettings;

    /**
     * The user HealthCheck settings
     *
     * @var Proposalgen_Model_HealthCheck_Setting
     */
    protected $_userSettings;

    /**
     * The HealthCheck's HealthCheck settings
     *
     * @var Proposalgen_Model_HealthCheck_Setting
     */
    protected $_HealthCheckSettings;

    /**
     * The default settings (uses overrides)
     *
     * @var Proposalgen_Model_HealthCheck_Setting
     */
    protected $_defaultSettings;

    /**
     * The HealthCheck
     *
     * @var Proposalgen_Model_HealthCheck
     */
    protected $_HealthCheck;

    public function __construct ($HealthCheckId, $userId, $dealerId)
    {
        $this->_HealthCheck         = Proposalgen_Model_Mapper_HealthCheck::getInstance()->find($HealthCheckId);
        $this->_systemSettings = Proposalgen_Model_Mapper_HealthCheck_Setting::getInstance()->fetchSystemSetting();
        $this->_dealerSettings = Proposalgen_Model_Mapper_HealthCheck_Setting::getInstance()->fetchDealerSetting($dealerId);

        $this->_userSettings   = Proposalgen_Model_Mapper_HealthCheck_Setting::getInstance()->fetchUserSetting($userId); TODO FIX THIS ONCE USER IS DONE
        $this->_HealthCheckSettings = Proposalgen_Model_Mapper_HealthCheck_Setting::getInstance()->fetchSetting($HealthCheckId);

        // Calculate the default settings
        $this->_defaultSettings = new Proposalgen_Model_HealthCheck_Setting(array_merge($this->_userSettings->toArray(), $this->_dealerSettings->toArray()));
        $this->_defaultSettings->populate($this->_userSettings->toArray());
    }

    /**
     * Gets the client form
     *
     * @return Proposalgen_Form_Settings_HealthCheck
     */
    public function getForm ()
    {
        if (!isset($this->_form))
        {
            $this->_form = new Proposalgen_Form_Settings_HealthCheck($this->_defaultSettings);

            // Populate with initial data?
            $this->_form->populate(array_merge($this->_userSettings->toArray(), $this->_HealthCheckSettings->toArray()));
            $reportDate = date('m/d/Y', strtotime($this->_HealthCheck->reportDate));
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
     * Updates the HealthCheck's settings
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
            $this->_HealthCheck->reportDate = $reportDate;
            Proposalgen_Model_Mapper_HealthCheck::getInstance()->save($this->_HealthCheck);

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
            $HealthCheckSettingsId = $this->_HealthCheckSettings->id;

            $this->_HealthCheckSettings->populate($this->_defaultSettings->toArray());
            $this->_HealthCheckSettings->populate($validData);

            // Restore the ID
            $this->_HealthCheckSettings->id = $HealthCheckSettingsId;

            Proposalgen_Model_Mapper_HealthCheck_Setting::getInstance()->save($this->_HealthCheckSettings);

            $this->getForm()->populate($this->_HealthCheckSettings->toArray());

            return true;
        }

        return false;
    }
}
