<?php

namespace MPSToolbox\Legacy\Modules\HardwareOptimization\Models;

use ArrayObject;
use DateTime;
use MPSToolbox\Legacy\Mappers\DealerMapper;
use MPSToolbox\Legacy\Models\DealerModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\RmsUploadMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\RmsUploadModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ClientMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\ClientModel;
use My_Model_Abstract;

/**
 * Class HardwareOptimizationModel
 *
 * @package MPSToolbox\Legacy\Modules\HardwareOptimization\Models
 */
class HardwareOptimizationModel extends My_Model_Abstract
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
    public $dateCreated;

    /**
     * @var string
     */
    protected $_dateReportPrepared;

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
     * @var ClientModel
     */
    protected $_client;

    /**
     * @var DealerModel
     */
    protected $_dealer;

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
        return [
            "id"           => $this->id,
            "clientId"     => $this->clientId,
            "dealerId"     => $this->dealerId,
            "dateCreated"  => $this->dateCreated,
            "lastModified" => $this->lastModified,
            "name"         => $this->name,
            "rmsUploadId"  => $this->rmsUploadId,
            "stepName"     => $this->stepName,
        ];
    }

    public function setClient(ClientModel $client) {
        $this->_client = $client;
        $this->clientId = $client->id;
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

    public function setDealer(DealerModel $dealer) {
        $this->_dealer = $dealer;
        $this->dealerId = $dealer->id;
    }
    /**
     * Gets the dealer
     *
     * @return DealerModel
     */
    public function getDealer ()
    {
        if (!isset($this->_dealer))
        {
            $this->_dealer = DealerMapper::getInstance()->find($this->dealerId);
        }

        return $this->_dealer;
    }


    public function setRmsUpload(RmsUploadModel $rmsUpload) {
        $this->_rmsUpload = $rmsUpload;
        $this->rmsUploadId = $rmsUpload->id;
    }

    /**
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
     * For getting formatted date of dateCreated for reports
     *
     * @return string
     */
    public function getFormattedDatePrepared ()
    {
        if (!isset($this->_dateReportPrepared))
        {
            $report_date              = new DateTime($this->dateCreated);
            $this->_dateReportPrepared = date_format($report_date, 'F jS, Y');
        }

        return $this->_dateReportPrepared;
    }
}