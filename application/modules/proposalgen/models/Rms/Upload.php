<?php
class Proposalgen_Model_Rms_Upload extends My_Model_Abstract
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
    public $rmsProviderId;

    /**
     * @var string
     */
    public $fileName;

    /**
     * @var int
     */
    public $validRowCount;

    /**
     * @var int
     */
    public $invalidRowCount;

    /**
     * @var string
     */
    public $uploadDate;

    /**
     * @var Proposalgen_Model_DeviceInstance[]
     */
    protected $_deviceInstances;

    /**
     * @var Proposalgen_Model_Rms_Upload_Row[]
     */
    protected $_rmsUploadRows;

    /**
     * @var Proposalgen_Model_Rms_Provider
     */
    protected $_rmsProvider;


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

        if (isset($params->rmsProviderId) && !is_null($params->rmsProviderId))
        {
            $this->rmsProviderId = $params->rmsProviderId;
        }

        if (isset($params->fileName) && !is_null($params->fileName))
        {
            $this->fileName = $params->fileName;
        }

        if (isset($params->validRowCount) && !is_null($params->validRowCount))
        {
            $this->validRowCount = $params->validRowCount;
        }

        if (isset($params->invalidRowCount) && !is_null($params->invalidRowCount))
        {
            $this->invalidRowCount = $params->invalidRowCount;
        }

        if (isset($params->uploadDate) && !is_null($params->uploadDate))
        {
            $this->uploadDate = $params->uploadDate;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"              => $this->id,
            "clientId"        => $this->clientId,
            "rmsProviderId"   => $this->rmsProviderId,
            "fileName"        => $this->fileName,
            "validRowCount"   => $this->validRowCount,
            "invalidRowCount" => $this->invalidRowCount,
            "uploadDate"      => $this->uploadDate,
        );
    }

    /**
     * Gets the device instances
     *
     * @return Proposalgen_Model_DeviceInstance[]
     */
    public function getDeviceInstances ()
    {
        if (!isset($this->_rmsUploadRows))
        {
            $this->_rmsUploadRows = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->fetchAllForRmsUpload($this->id);
        }

        return $this->_rmsUploadRows;
    }

    /**
     * Sets the rms upload
     *
     * @param Proposalgen_Model_Rms_Upload_Row[] $rmsUploadRows
     *
     * @return Proposalgen_Model_Assessment
     */
    public function setRmsUploadRows ($rmsUploadRows)
    {
        $this->_rmsUploadRows = $rmsUploadRows;

        return $this;
    }


    /**
     * Gets the rms provider
     *
     * @return Proposalgen_Model_Rms_Provider
     */
    public function getRmsProvider ()
    {
        if (!isset($this->_rmsProvider))
        {
            $this->_rmsProvider = Proposalgen_Model_Mapper_Rms_Provider::getInstance()->find($this->rmsProviderId);
        }

        return $this->_rmsProvider;
    }

    /**
     * @param Proposalgen_Model_Rms_Provider $rmsProvider
     *
     * @return Proposalgen_Model_Rms_Upload
     */
    public function setRmsProvider ($rmsProvider)
    {
        $this->_rmsProvider = $rmsProvider;

        return $this;
    }
}