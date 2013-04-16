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
     * The image id
     *
     * @var int
     */
    public $dealerLogoImageId;

    /**
     * @var Preferences_Model_Dealer_Setting
     */
    protected $_dealerSettings;


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
        if (isset($params->dealerLogoImageId) && !is_null($params->dealerLogoImageId))
        {
            $this->dealerLogoImageId = $params->dealerLogoImageId;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"                => $this->id,
            "userLicenses"      => $this->userLicenses,
            "dealerName"        => $this->dealerName,
            "dateCreated"       => $this->dateCreated,
            "dealerLogoImageId" => $this->dealerLogoImageId,
        );
    }


    /**
     * Gets a dealers settings object
     *
     * @return Preferences_Model_Dealer_Setting
     */
    public function getDealerSettings ()
    {
        if (!isset($this->_dealerSettings))
        {
            $this->_dealerSettings = Preferences_Model_Mapper_Dealer_Setting::getInstance()->find($this->id);
            if (!$this->_dealerSettings instanceof Preferences_Model_Dealer_Setting)
            {
                $this->_dealerSettings           = new Preferences_Model_Dealer_Setting();
                $this->_dealerSettings->dealerId = $this->id;

                Preferences_Model_Mapper_Dealer_Setting::getInstance()->insert($this->_dealerSettings);
            }
        }

        return $this->_dealerSettings;
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

    /**
     * Gets the path (relative to the public path) of the dealer report logo
     *
     * @return bool|string
     */
    public function getDealerLogoImageFile ()
    {
        $publicFilePath = false;
        if ($this->dealerLogoImageId > 0)
        {
            $publicFilePath = '/downloads/dealer-' . $this->id . '-ReportLogo.png';
            $filePath       = PUBLIC_PATH . $publicFilePath;
            if (file_exists($filePath))
            {
                @unlink($filePath);
            }
            $image = Admin_Model_Mapper_Image::getInstance()->find($this->dealerLogoImageId);
            file_put_contents($filePath, base64_decode($image->image));
        }

        return $publicFilePath;
    }
}