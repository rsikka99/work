<?php

namespace MPSToolbox\Legacy\Modules\HealthCheck\Models;

use ArrayObject;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\RmsUploadMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\RmsUploadModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ClientMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\ClientModel;
use My_Model_Abstract;

/**
 * Class HealthCheckModel
 *
 * @package MPSToolbox\Legacy\Modules\HealthCheck\Models
 */
class HealthCheckModel extends My_Model_Abstract
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
    public $rmsUploadId;

    /**
     * @var string
     */
    public $stepName;

    /**
     * @var string
     */
    public $dateCreated;

    /**
     * @var string
     */
    public $lastModified;

    /**
     * @var string
     */
    public $reportDate;

    /**
     * @var bool
     */
    public $devicesModified;

    /**
     * @var  string
     */
    public $name;

    // Non database fields
    /**
     * @var ClientModel
     */
    protected $_client;

    /**
     * @var RmsUploadModel
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

        if (isset($params->rmsUploadId) && !is_null($params->rmsUploadId))
        {
            $this->rmsUploadId = $params->rmsUploadId;
        }

        if (isset($params->stepName) && !is_null($params->stepName))
        {
            $this->stepName = $params->stepName;
        }

        if (isset($params->dateCreated) && !is_null($params->dateCreated))
        {
            $this->dateCreated = $params->dateCreated;
        }

        if (isset($params->lastModified) && !is_null($params->lastModified))
        {
            $this->lastModified = $params->lastModified;
        }

        if (isset($params->reportDate) && !is_null($params->reportDate))
        {
            $this->reportDate = $params->reportDate;
        }

        if (isset($params->devicesModified) && !is_null($params->devicesModified))
        {
            $this->devicesModified = $params->devicesModified;
        }

        if (isset($params->name) && !is_null($params->name))
        {
            $this->name = $params->name;
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
            "dealerId"        => $this->dealerId,
            "rmsUploadId"     => $this->rmsUploadId,
            "stepName"        => $this->stepName,
            "dateCreated"     => $this->dateCreated,
            "lastModified"    => $this->lastModified,
            "reportDate"      => $this->reportDate,
            "devicesModified" => $this->devicesModified,
            "name"            => $this->name,
        );
    }

    /**
     * Gets the client
     *
     * @return ClientModel
     */
    public function getClient ()
    {
        if (!isset($this->_client))
        {
            $this->_client = ClientMapper::getInstance()->find($this->clientId);
        }

        return $this->_client;
    }

    /**
     * Sets the client
     *
     * @param ClientModel $client
     *
     * @return HealthCheckModel
     */
    public function setClient ($client)
    {
        $this->_client = $client;

        return $this;
    }


    /**
     * Gets the RMS upload
     *
     * @return RmsUploadModel
     */
    public function getRmsUpload ()
    {
        if (!isset($this->_rmsUpload))
        {
            $this->_rmsUpload = RmsUploadMapper::getInstance()->find($this->rmsUploadId);
        }

        return $this->_rmsUpload;
    }

    /**
     * Sets the RMS upload
     *
     * @param RmsUploadModel $rmsUpload
     *
     * @return HealthCheckModel
     */
    public function setRmsUpload ($rmsUpload)
    {
        $this->_rmsUpload = $rmsUpload;

        return $this;
    }
}