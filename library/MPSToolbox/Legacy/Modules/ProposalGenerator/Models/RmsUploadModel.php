<?php
namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Models;

use ArrayObject;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DeviceInstanceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\RmsProviderMapper;
use My_Model_Abstract;

/**
 * Class RmsUploadModel
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Models
 */
class RmsUploadModel extends My_Model_Abstract
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
     * @var DeviceInstanceModel[]
     */
    protected $_deviceInstances;

    /**
     * @var RmsUploadRowModel[]
     */
    protected $_rmsUploadRows;

    /**
     * @var RmsProviderModel
     */
    public $rmsProvider;


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
     * @return DeviceInstanceModel[]
     */
    public function getDeviceInstances ()
    {
        if (!isset($this->_rmsUploadRows))
        {
            $this->_rmsUploadRows = DeviceInstanceMapper::getInstance()->fetchAllForRmsUpload($this->id);
        }

        return $this->_rmsUploadRows;
    }

    /**
     * Sets the RMS upload
     *
     * @param RmsUploadRowModel[] $rmsUploadRows
     *
     * @return RmsUploadModel
     */
    public function setRmsUploadRows ($rmsUploadRows)
    {
        $this->_rmsUploadRows = $rmsUploadRows;

        return $this;
    }


    /**
     * Gets the RMS provider
     *
     * @return RmsProviderModel
     */
    public function getRmsProvider ()
    {
        if (!isset($this->rmsProvider))
        {
            $this->rmsProvider = RmsProviderMapper::getInstance()->find($this->rmsProviderId);
        }

        return $this->rmsProvider;
    }

    /**
     * @param RmsProviderModel $rmsProvider
     *
     * @return RmsUploadModel
     */
    public function setRmsProvider ($rmsProvider)
    {
        $this->rmsProvider = $rmsProvider;

        return $this;
    }
}