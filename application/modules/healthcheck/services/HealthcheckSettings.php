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
     * Settings that are used to populate the form.
     *
     * @var Healthcheck_Model_Healthcheck_Setting
     */
    protected $_populateSettings;

    /**
     * The settings that are used to display the default values (good engrish)
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
        $this->_dealerSettings      = $dealer->getDealerSettings()->getHealthcheckSettings();
        $this->_userSettings        = $user->getUserSettings()->getHealthcheckSettings();
        $this->_healthcheckSettings = $this->_healthcheck->getHealthcheckSettings();

        $this->_populateSettings = clone $this->_dealerSettings;
        $this->_populateSettings->populate($this->_userSettings->toArray());
        $this->_populateSettings->populate($this->_healthcheckSettings->toArray());

        // Override the default settings
        $this->_defaultSettings = new Healthcheck_Model_Healthcheck_Setting(array_merge($this->_userSettings->toArray(), $this->_dealerSettings->toArray()));
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
            $this->_form->populate(array_merge($this->_populateSettings->toArray(), $this->_populateSettings->getTonerRankSets()));
            $reportDate = date('m/d/Y', strtotime($this->_healthcheck->reportDate));
            $this->_form->populate(array(
                                        'reportDate' => $reportDate,
                                        'name'       => $this->_healthcheck->name,
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

            foreach ($validData as $key => $value)
            {
                if (empty($value))
                {
                    unset($validData [$key]);
                }
            }

            $this->_healthcheck->name = (isset($validData['name'])) ? $validData['name'] : "Health Check " . date('Y/m/d');
            Healthcheck_Model_Mapper_Healthcheck::getInstance()->save($this->_healthcheck);

            // Save the id as it will get erased
            $healthcheckSettingsId = $this->_healthcheckSettings->id;

            $rankingSetMapper = Proposalgen_Model_Mapper_Toner_Vendor_Ranking_Set::getInstance();

            // If we have selected toners, we have to save the to the table
            if (isset($validData['customerColorRankSetArray']))
            {
                $this->_healthcheckSettings->customerColorRankSetId = $rankingSetMapper->saveRankingSets($this->_healthcheckSettings->customerColorRankSetId, $validData['customerColorRankSetArray']);
            }
            else
            {
                Proposalgen_Model_Mapper_Toner_Vendor_Ranking::getInstance()->deleteByTonerVendorRankingId($this->_healthcheckSettings->customerColorRankSetId);
            }

            if (isset($validData['customerMonochromeRankSetArray']))
            {
                $this->_healthcheckSettings->customerMonochromeRankSetId = $rankingSetMapper->saveRankingSets($this->_healthcheckSettings->customerMonochromeRankSetId, $validData['customerMonochromeRankSetArray']);
            }
            else
            {
                Proposalgen_Model_Mapper_Toner_Vendor_Ranking::getInstance()->deleteByTonerVendorRankingId($this->_healthcheckSettings->customerMonochromeRankSetId);
            }


            // Override the setting so the id doesn't get overwritten when we populate
            $this->_defaultSettings->customerMonochromeRankSetId = $this->_healthcheckSettings->customerMonochromeRankSetId;
            $this->_defaultSettings->customerColorRankSetId      = $this->_healthcheckSettings->customerColorRankSetId;

            $this->_healthcheckSettings->populate($this->_defaultSettings->toArray());
            $this->_healthcheckSettings->populate($validData);
            // Restore the Id
            $this->_healthcheckSettings->id = $healthcheckSettingsId;

            // Populate before the other values are set since these fields are a calculated field.
            $this->getForm()->populate($this->_healthcheckSettings->toArray());
            $this->getForm()->populate(array(
                                            'name' => $this->_healthcheck->name
                                       ));
            $this->_healthcheckSettings->costOfLabor    = ($this->_healthcheckSettings->costOfLabor != null) ? $this->_healthcheckSettings->costOfLabor : new Zend_Db_Expr('NULL');
            $this->_healthcheckSettings->hoursSpentOnIt = ($this->_healthcheckSettings->hoursSpentOnIt != null) ? $this->_healthcheckSettings->hoursSpentOnIt : new Zend_Db_Expr('NULL');

            Healthcheck_Model_Mapper_Healthcheck_Setting::getInstance()->save($this->_healthcheckSettings);

            return true;
        }

        return false;
    }
}
