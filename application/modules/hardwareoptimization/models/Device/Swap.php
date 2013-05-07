<?php
/**
 * Class Hardwareoptimization_Model_Device_Swap
 */
class Hardwareoptimization_Model_Device_Swap extends My_Model_Abstract
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
    public static $replacementTypes = array(
        self::REPLACEMENT_BW        => 'monochrome',
        self::REPLACEMENT_BW_MFP    => 'monochromeMfp',
        self::REPLACEMENT_COLOR     => 'color',
        self::REPLACEMENT_COLOR_MFP => 'colorMfp'
    );


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
     * @var Proposalgen_Model_CostPerPage
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
        return array(
            "masterDeviceId"   => $this->masterDeviceId,
            "dealerId"         => $this->dealerId,
            "minimumPageCount" => $this->minimumPageCount,
            "maximumPageCount" => $this->maximumPageCount,
        );
    }

    /**
     * Gets the device instance
     *
     * @return Proposalgen_Model_DeviceInstance
     */
    public function getDeviceInstance ()
    {
        if (!isset($this->_deviceInstance))
        {
            $this->_deviceInstance = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->find($this->deviceInstanceId);
        }

        return $this->_deviceInstance;
    }

    /**
     *  Sets the device instance
     *
     * @param Proposalgen_Model_DeviceInstance $deviceInstance
     *
     * @return Proposalgen_Model_Device_Instance_Master_Device
     */
    public function setDeviceInstance ($deviceInstance)
    {
        $this->_deviceInstance = $deviceInstance;

        return $this;
    }

    /**
     * Gets the master device
     *
     * @return Proposalgen_Model_MasterDevice
     */
    public function getMasterDevice ()
    {
        if (!isset($this->_masterDevice))
        {
            $this->_masterDevice = Proposalgen_Model_Mapper_MasterDevice::getInstance()->find($this->masterDeviceId);
        }

        return $this->_masterDevice;
    }

    /**
     *  Sets the master device
     *
     * @param Proposalgen_Model_MasterDevice $masterDevice
     *
     * @return Proposalgen_Model_Device_Instance_Master_Device
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

        $deviceSwapMapper = Hardwareoptimization_Model_Mapper_Device_Swap::getInstance();

        $deviceSwap = $deviceSwapMapper->find(array($this->masterDeviceId, $this->dealerId));

        try
        {
            if ($deviceSwap instanceof Hardwareoptimization_Model_Device_Swap)
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
     * @param \Proposalgen_Model_CostPerPage $costPerPage
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
     * @return \Proposalgen_Model_CostPerPage
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