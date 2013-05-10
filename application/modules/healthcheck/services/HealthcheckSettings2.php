<?php

/**
 * Class Healthcheck_Service_HealthcheckSettings
 */
class Healthcheck_Service_HealthcheckSettings
{
    /**
     * The form for a client
     *
     * @var Healthcheck_Form_Healthcheck_Settings
     */
    protected $_form;

    /**
     * The system Healthcheck settings
     *
     * @var Healthcheck_Model_Healthcheck_Setting
     */
    protected $_systemSettings;

    /**
     * The system Healthcheck settings
     *
     * @var Healthcheck_Model_Healthcheck_Setting
     */
    protected $_dealerSettings;

    /**
     * The user Healthcheck settings
     *
     * @var Healthcheck_Model_Healthcheck_Setting
     */
    protected $_userSettings;

    /**
     * The Healthcheck's Healthcheck settings
     *
     * @var Healthcheck_Model_Healthcheck_Setting
     */
    protected $_healthcheckSettings;

    /**
     * The default settings (uses overrides)
     *
     * @var Healthcheck_Model_Healthcheck_Setting
     */
    protected $_defaultSettings;

    /**
     * The Healthcheck
     *
     * @var Healthcheck_Model_Healthcheck
     */
    protected $_healthcheck;

    /**
     * @param int $healthcheckId
     * @param int $userId
     * @param int $dealerId
     */
    public function __construct ($healthcheckId, $userId, $dealerId)
    {
        $user                       = Application_Model_Mapper_User::getInstance()->find($userId);
        $dealer                     = Admin_Model_Mapper_Dealer::getInstance()->find($dealerId);
        $this->_healthcheck         = Healthcheck_Model_Mapper_Healthcheck::getInstance()->find($healthcheckId);
        $this->_systemSettings      = Healthcheck_Model_Mapper_Healthcheck_Setting::getInstance()->fetchSystemHealthcheckSetting();
        $this->_dealerSettings      = $dealer->getDealerSettings()->getHealthcheckSettings();
        $this->_userSettings        = $user->getUserSettings()->getHealthcheckSettings();
        $this->_healthcheckSettings = $this->_healthcheck->getHealthcheckSettings();

        // Calculate the default settings
        $this->_defaultSettings = new Healthcheck_Model_Healthcheck_Setting(array_merge($this->_userSettings->toArray(), $this->_dealerSettings->toArray()));
        $this->_defaultSettings->populate($this->_userSettings->toArray());
    }

    /**
     * Gets the client form
     *
     * @return Healthcheck_Form_Healthcheck_Settings
     */
    public function getForm ()
    {
        if (!isset($this->_form))
        {
            $this->_form = new Healthcheck_Form_Healthcheck_Settings($this->_defaultSettings);
            $this->_form->populate($this->_healthcheckSettings->toArray());
            $reportDate = date('m/d/Y', strtotime($this->_healthcheck->reportDate));
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
            $reportDate                     = date('Y-m-d h:i:s', strtotime($validData ['reportDate']));
            $this->_healthcheck->reportDate = $reportDate;
            Healthcheck_Model_Mapper_Healthcheck::getInstance()->save($this->_healthcheck);

            foreach ($validData as $key => $value)
            {
                if (empty($value))
                {
                    unset($validData [$key]);
                }
            }
            // Check the valid data to see if toner preferences drop downs have been set.
            if ((int)$validData ['healthcheckPricingConfigId'] === Proposalgen_Model_PricingConfig::NONE)
            {
                unset($validData ['healthcheckPricingConfigId']);
            }

            // Save the id as it will get erased
            $HealthcheckSettingsId = $this->_healthcheckSettings->id;
            $healthcheckSettings = new Healthcheck_Model_Healthcheck_Setting();
            $healthcheckSettings->populate(array_merge($this->_defaultSettings->toArray(), $validData));
            $healthcheckSettings->id    = $HealthcheckSettingsId;
            $this->_healthcheckSettings = $healthcheckSettings;
//            $this->_HealthcheckSettings->populate($validData);

            $this->getForm()->populate($this->_healthcheckSettings->toArray());
            $this->_healthcheckSettings->costOfLabor    = ($this->_healthcheckSettings->costOfLabor != null) ? $this->_healthcheckSettings->costOfLabor : new Zend_Db_Expr('NULL');
            $this->_healthcheckSettings->hoursSpentOnIt = ($this->_healthcheckSettings->hoursSpentOnIt != null) ? $this->_healthcheckSettings->hoursSpentOnIt : new Zend_Db_Expr('NULL');

            Healthcheck_Model_Mapper_Healthcheck_Setting::getInstance()->save($this->_healthcheckSettings);


            return true;
        }

        return false;
    }
}
