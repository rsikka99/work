<?php

namespace MPSToolbox\Legacy\Models;

use ArrayObject;
use MPSToolbox\Legacy\Mappers\UserMapper;
use MPSToolbox\Legacy\Modules\Admin\Mappers\ImageMapper;
use MPSToolbox\Legacy\Modules\Preferences\Mappers\DealerSettingMapper;
use MPSToolbox\Legacy\Modules\Preferences\Models\DealerSettingModel;
use My_Model_Abstract;

/**
 * Class DealerModel
 *
 * @package MPSToolbox\Legacy\Models
 */
class DealerModel extends My_Model_Abstract
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
     * @var string
     */
    public $dateCreated;

    /**
     * The image id
     *
     * @var int
     */
    public $dealerLogoImageId;

    /**
     * @var DealerSettingModel
     */
    protected $_dealerSettings;

    /**
     * @var UserModel[]
     */
    protected $_users;


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
     * @return DealerSettingModel
     */
    public function getDealerSettings ()
    {
        if (!isset($this->_dealerSettings))
        {
            $this->_dealerSettings = DealerSettingMapper::getInstance()->find($this->id);
            if (!$this->_dealerSettings instanceof DealerSettingModel)
            {
                $this->_dealerSettings           = new DealerSettingModel();
                $this->_dealerSettings->dealerId = $this->id;

                DealerSettingMapper::getInstance()->insert($this->_dealerSettings);
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
        return count(UserMapper::getInstance()->fetchUserListForDealer($this->id));
    }

    /**
     * Gets the path (relative to the public path) of the dealer report logo
     *
     * @param bool $recreate Whether or not the file should be recreated if it exists
     *
     * @return bool|string
     */
    public function getDealerLogoImageFile ($recreate = false)
    {
        $publicFilePath = false;
        if ($this->dealerLogoImageId > 0)
        {
            $publicFilePath = '/downloads/dealer-' . $this->id . '-ReportLogo.png';
            $filePath       = PUBLIC_PATH . $publicFilePath;
            if (!file_exists($filePath) || $recreate)
            {
                if (file_exists($filePath))
                {
                    @unlink($filePath);
                }
                $image = ImageMapper::getInstance()->find($this->dealerLogoImageId);
                file_put_contents($filePath, base64_decode($image->image));
            }
        }

        return $publicFilePath;
    }

    /**
     * Gets the users
     *
     * @return UserModel[]
     */
    public function getUsers ()
    {
        if (!isset($this->_users))
        {
            $this->_users = UserMapper::getInstance()->fetchUserListForDealer($this->id);
        }

        return $this->_users;
    }

    /**
     * Sets the users
     *
     * @param UserModel[] $users
     *
     * @return $this
     */
    public function setUsers ($users)
    {
        $this->_users = $users;

        return $this;
    }
}