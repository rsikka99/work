<?php
class Admin_Model_Dealer extends My_Model_Abstract
{
    /**
     * The id of the dealer
     *
     * @var int
     */
    public $id;
    /**
     * The row in the database where the report settings are store for the dealer.
     *
     * @var int
     */
    public $reportSettingId;
    /**
     * The amount of licences that a user is allowed to have at one time
     *
     * @var int
     */
    public $userLicenses;
    /**
     * The name assigned to the dealer
     *
     * @var string
     */
    public $dealerName;
    /**
     * Date that the dealer was created in our databases
     *
     * @var DateTime
     */
    public $dateCreated;

    /**
     * The id that relates to the quote setting object table.
     *
     * @var int
     */
    public $quoteSettingId;

    /**
     * DONT WORRY, SAID SHAWN!
     *
     * @var int
     */
    public $surveySettingId;

    /**
     * Gets a report setting object for the dealer.
     *
     * @var Proposalgen_Model_Report_Setting
     */
    protected $_reportSettings;

    /**
     * Gets a quote setting object for the dealer object.
     *
     * @var Quotegen_Model_QuoteSetting
     */
    protected $_quoteSetting;


    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }
        if (isset($params->id) && !is_null($params->id))
        {
            $this->id = $params->id;
        }
        if (isset($params->reportSettingId) && !is_null($params->reportSettingId))
        {
            $this->reportSettingId = $params->reportSettingId;
        }
        if (isset($params->userLicenses) && !is_null($params->userLicenses))
        {
            $this->userLicenses = $params->userLicenses;
        }
        if (isset($params->dealerName) && !is_null($params->dealerName))
        {
            $this->dealerName = $params->dealerName;
        }
        if (isset($params->dateCreated) && !is_null($params->dateCreated))
        {
            $this->dateCreated = $params->dateCreated;
        }
        if (isset($params->quoteSettingId) && !is_null($params->quoteSettingId))
        {
            $this->quoteSettingId = $params->quoteSettingId;
        }
        if (isset($params->surveySettingId) && !is_null($params->surveySettingId))
        {
            $this->surveySettingId = $params->surveySettingId;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"              => $this->id,
            "reportSettingId" => $this->reportSettingId,
            "userLicenses"    => $this->userLicenses,
            "dealerName"      => $this->dealerName,
            "dateCreated"     => $this->dateCreated,
            "quoteSettingId"  => $this->quoteSettingId,
            "surveySettingId"  => $this->surveySettingId,
        );
    }

    /**
     * Gets the report and survey settings for the user
     *
     * @return array
     */
    public function getReportSettings ()
    {
        if (!isset($this->_reportSettings))
        {
            $dealerReportSetting                      = Proposalgen_Model_Mapper_Report_Setting::getInstance()->fetchDealerReportSetting($this->id);
            $dealerSurveySetting                      = Proposalgen_Model_Mapper_Survey_Setting::getInstance()->fetchDealerSurveySetting($this->id);
            $this->_reportSettings                    = array_merge($dealerReportSetting->toArray(), $dealerSurveySetting->toArray());
            $this->_reportSettings['reportSettingId'] = $dealerReportSetting->id;
            $this->_reportSettings['surveySettingId'] = $dealerSurveySetting->id;
            unset($this->_reportSettings['id']);
        }

        return $this->_reportSettings;
    }

    /**
     * Getter for _quoteSetting
     *
     * @return \Quotegen_Model_QuoteSetting
     */
    public function getQuoteSetting ()
    {
        if (!isset($this->_quoteSetting))
        {
            // FIXME: Actually code this function
        }

        return $this->_quoteSetting;
    }

    /**
     * Gets how many users a dealer has
     *
     * @return int
     */
    public function getNumberOfLicensesUsed ()
    {
        return count(Application_Model_Mapper_User::getInstance()->fetchUserListForDealer($this->id));
    }
}