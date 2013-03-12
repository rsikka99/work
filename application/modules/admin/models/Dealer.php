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
        if (isset($params->id) && !is_null($params->id))
        {
            $this->id = $params->dateCreated;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id" => $this->id,
            "reportSettingId" => $this->reportSettingId,
            "userLicenses" => $this->userLicenses,
            "dealerName" => $this->dealerName,
            "dateCreated" => $this->dateCreated,
        );
    }
}