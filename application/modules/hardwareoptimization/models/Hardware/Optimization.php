<?php

/**
 * Class Hardwareoptimization_Model_Hardware_Optimization
 */
class Hardwareoptimization_Model_Hardware_Optimization extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $clientId;

    /**
     * @var int
     */
    public $dealerId;

    /**
     * @var int
     */
    public $hardwareOptimizationSettingId;

    /**
     * @var int
     */
    public $dateCreated;

    /**
     * @var int
     */
    public $lastModified;

    /**
     * @var int
     */
    public $name;

    /**
     * @var int
     */
    public $rmsUploadId;

    /**
     * @var int
     */
    public $stepName;

    /**
     * @var Quotegen_Model_Client
     */
    protected $_client;

    /**
     * @var Application_Model_Dealer
     */
    protected $_dealer;

    /**
     * @var Hardwareoptimization_Model_Hardware_Optimization_Setting
     */
    protected $_hardwareOptimizationSetting;

    /**
     * @var Proposalgen_Model_Rms_Upload
     */
    protected $_rmsUpload;

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

        if (isset($params->clientId) && !is_null($params->clientId))
        {
            $this->clientId = $params->clientId;
        }

        if (isset($params->dealerId) && !is_null($params->dealerId))
        {
            $this->dealerId = $params->dealerId;
        }

        if (isset($params->hardwareOptimizationSettingId) && !is_null($params->hardwareOptimizationSettingId))
        {
            $this->hardwareOptimizationSettingId = $params->hardwareOptimizationSettingId;
        }

        if (isset($params->dateCreated) && !is_null($params->dateCreated))
        {
            $this->dateCreated = $params->dateCreated;
        }

        if (isset($params->lastModified) && !is_null($params->lastModified))
        {
            $this->lastModified = $params->lastModified;
        }

        if (isset($params->name) && !is_null($params->name))
        {
            $this->name = $params->name;
        }

        if (isset($params->rmsUploadId) && !is_null($params->rmsUploadId))
        {
            $this->rmsUploadId = $params->rmsUploadId;
        }

        if (isset($params->stepName) && !is_null($params->stepName))
        {
            $this->stepName = $params->stepName;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"                            => $this->id,
            "clientId"                      => $this->clientId,
            "dealerId"                      => $this->dealerId,
            "hardwareOptimizationSettingId" => $this->hardwareOptimizationSettingId,
            "dateCreated"                   => $this->dateCreated,
            "lastModified"                  => $this->lastModified,
            "name"                          => $this->name,
            "rmsUploadId"                   => $this->rmsUploadId,
            "stepName"                      => $this->stepName,
        );
    }

    /**
     * Gets the client
     *
     * @return Quotegen_Model_Client
     */
    public function getClient ()
    {
        if (!isset($this->_client))
        {
            $this->_client = Quotegen_Model_Mapper_Client::getInstance()->find($this->clientId);
        }

        return $this->_client;
    }

    /**
     * Gets the dealer
     *
     * @return Application_Model_Dealer
     */
    public function getDealer ()
    {
        if (!isset($this->_dealer))
        {
            $this->_dealer = Application_Model_Mapper_Dealer::getInstance()->find($this->dealerId);
        }

        return $this->_dealer;
    }

    /**
     * Gets the hardware optimization settings
     *
     * @return Hardwareoptimization_Model_Hardware_Optimization_Setting
     */
    public function getHardwareOptimizationSetting ()
    {
        if (!isset($this->_hardwareOptimizationSetting))
        {
            $this->_hardwareOptimizationSetting = Hardwareoptimization_Model_Mapper_Hardware_Optimization_Setting::getInstance()->find($this->hardwareOptimizationSettingId);
        }

        return $this->_hardwareOptimizationSetting;
    }

    /**
     * Gets the dealer
     *
     * @return Proposalgen_Model_Rms_Upload
     */
    public function getRmsUpload ()
    {
        if (!isset($this->_rmsUpload))
        {
            $this->_rmsUpload = Proposalgen_Model_Mapper_Rms_Upload::getInstance()->find($this->rmsUploadId);
        }

        return $this->_rmsUpload;
    }
}