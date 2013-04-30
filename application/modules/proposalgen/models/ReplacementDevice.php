<?php
/**
 * Class Proposalgen_Model_ReplacementDevice
 */
class Proposalgen_Model_ReplacementDevice extends My_Model_Abstract
{
    const REPLACEMENT_BW       = 1;
    const REPLACEMENT_BWMFP    = 2;
    const REPLACEMENT_COLOR    = 3;
    const REPLACEMENT_COLORMFP = 4;

    /**
     * An array of replacement type names with the id as the array key
     *
     * @var string[]
     */
    public static $replacementTypes = array(
        self::REPLACEMENT_BW       => 'BLACK & WHITE',
        self::REPLACEMENT_BWMFP    => 'BLACK & WHITE MFP',
        self::REPLACEMENT_COLOR    => 'COLOR',
        self::REPLACEMENT_COLORMFP => 'COLOR MFP'
    );

    /**
     * @var int
     */
    public $dealerId;

    /**
     * @var int
     */
    public $masterDeviceId;

    /**
     * @var int
     */
    public $replacementCategory;

    /**
     * @var int
     */
    public $printSpeed;

    /**
     * @var int
     */
    public $resolution;

    /**
     * @var float
     */
    public $monthlyRate;

    /**
     * @var Proposalgen_Model_MasterDevice
     */
    protected $_masterDevice;


    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->dealerId) && !is_null($params->dealerId))
        {
            $this->dealerId = $params->dealerId;
        }

        if (isset($params->masterDeviceId) && !is_null($params->masterDeviceId))
        {
            $this->masterDeviceId = $params->masterDeviceId;
        }

        if (isset($params->replacementCategory) && !is_null($params->replacementCategory))
        {
            $this->replacementCategory = $params->replacementCategory;
        }

        if (isset($params->printSpeed) && !is_null($params->printSpeed))
        {
            $this->printSpeed = $params->printSpeed;
        }

        if (isset($params->resolution) && !is_null($params->resolution))
        {
            $this->resolution = $params->resolution;
        }

        if (isset($params->monthlyRate) && !is_null($params->monthlyRate))
        {
            $this->monthlyRate = $params->monthlyRate;
        }

        if (isset($params->masterDevice) && !is_null($params->masterDevice))
        {
            $this->masterDevice = $params->masterDevice;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "dealerId"            => $this->dealerId,
            "masterDeviceId"      => $this->masterDeviceId,
            "replacementCategory" => $this->replacementCategory,
            "printSpeed"          => $this->printSpeed,
            "resolution"          => $this->resolution,
            "monthlyRate"         => $this->monthlyRate,
            "masterDevice"        => $this->masterDevice,
        );
    }

    /**
     * @return Proposalgen_Model_MasterDevice|void
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
     * @param $MasterDevice
     *
     * @return Proposalgen_Model_ReplacementDevice
     */
    public function setMasterDevice ($MasterDevice)
    {
        $this->_masterDevice = $MasterDevice;

        return $this;
    }
}