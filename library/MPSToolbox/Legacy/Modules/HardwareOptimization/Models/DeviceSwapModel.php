<?php

namespace MPSToolbox\Legacy\Modules\HardwareOptimization\Models;

use ArrayObject;
use Exception;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers\DeviceSwapMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DeviceInstanceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\MasterDeviceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\CostPerPageModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceMasterDeviceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel;
use My_Model_Abstract;

/**
 * Class DeviceSwapModel
 *
 * @package MPSToolbox\Legacy\Modules\HardwareOptimization\Models
 */
class DeviceSwapModel extends My_Model_Abstract
{
    const REPLACEMENT_BW        = 1;
    const REPLACEMENT_BW_MFP    = 2;
    const REPLACEMENT_COLOR     = 3;
    const REPLACEMENT_COLOR_MFP = 4;

    /**
     * An array of replacement type names with the id as the array key
     *
     * @var string[]
     */
    public static $replacementTypes = [
        self::REPLACEMENT_BW        => 'monochrome',
        self::REPLACEMENT_BW_MFP    => 'monochromeMfp',
        self::REPLACEMENT_COLOR     => 'color',
        self::REPLACEMENT_COLOR_MFP => 'colorMfp',
    ];


    /**
     * @var int
     */
    public $masterDeviceId;

    /**
     * @var int
     */
    public $dealerId;

    /**
     * @var int
     */
    public $minimumPageCount;

    /**
     * @var int
     */
    public $maximumPageCount;

    /**
     * @var CostPerPageModel
     */
    protected $_costPerPage;

    /**
     * @var string
     */
    protected $_replacementCategory;

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->masterDeviceId) && !is_null($params->masterDeviceId))
        {
            $this->masterDeviceId = $params->masterDeviceId;
        }

        if (isset($params->dealerId) && !is_null($params->dealerId))
        {
            $this->dealerId = $params->dealerId;
        }

        if (isset($params->minimumPageCount) && !is_null($params->minimumPageCount))
        {
            $this->minimumPageCount = $params->minimumPageCount;
        }

        if (isset($params->maximumPageCount) && !is_null($params->maximumPageCount))
        {
            $this->maximumPageCount = $params->maximumPageCount;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return [
            "masterDeviceId"   => $this->masterDeviceId,
            "dealerId"         => $this->dealerId,
            "minimumPageCount" => $this->minimumPageCount,
            "maximumPageCount" => $this->maximumPageCount,
        ];
    }

    /**
     * Gets the master device
     *
     * @return MasterDeviceModel
     */
    public function getMasterDevice ()
    {
        if (empty($this->_masterDevice))
        {
            $this->_masterDevice = MasterDeviceMapper::getInstance()->find($this->masterDeviceId);
        }

        return $this->_masterDevice;
    }

    /**
     *  Sets the master device
     *
     * @param MasterDeviceModel $masterDevice
     *
     * @return DeviceInstanceMasterDeviceModel
     */
    public function setMasterDevice ($masterDevice)
    {
        $this->_masterDevice = $masterDevice;

        return $this;
    }

    /**
     * Inserts or saves the object to the database.
     *
     * @param null|array $data
     *
     * @return bool
     * @throws Exception
     */
    public function saveObject ($data = null)
    {
        $successful = false;
        if (is_array($data))
        {
            $this->populate($data);
        }

        if (!isset($this->masterDeviceId) || !isset($this->dealerId))
        {
            throw new Exception("Device missing required data. Please try again.");
        }

        $deviceSwapMapper = DeviceSwapMapper::getInstance();

        $deviceSwap = $deviceSwapMapper->find([$this->masterDeviceId, $this->dealerId]);

        try
        {
            if ($deviceSwap instanceof DeviceSwapModel)
            {
                $deviceSwapMapper->save($this);
            }
            else
            {
                $deviceSwapMapper->insert($this);
            }
            $successful = true;
        }
        catch (Exception $e)
        {

        }

        return $successful;
    }

    /**
     * Setter for _costPerPage
     *
     * @param CostPerPageModel $costPerPage
     *
     * @return $this
     */
    public function setCostPerPage ($costPerPage)
    {
        $this->_costPerPage = $costPerPage;

        return $this;
    }

    /**
     * Getter for _costPerPage
     *
     * @return CostPerPageModel
     */
    public function getCostPerPage ()
    {
        return $this->_costPerPage;
    }

    /**
     * Setter for _replacementCategory
     *
     * @param string $replacementCategory
     */
    public function setReplacementCategory ($replacementCategory)
    {
        $this->_replacementCategory = $replacementCategory;
    }

    /**
     * Getter for _replacementCategory
     *
     * @return string
     */
    public function getReplacementCategory ()
    {
        if (!isset($this->_replacementCategory))
        {
            $this->_replacementCategory = null;
        }

        return $this->_replacementCategory;
    }
}