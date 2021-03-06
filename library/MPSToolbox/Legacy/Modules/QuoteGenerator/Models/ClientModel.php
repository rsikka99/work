<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Models;

use ArrayObject;
use MPSToolbox\Legacy\Entities\DealerEntity;
use MPSToolbox\Legacy\Entities\SurveyEntity;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\AddressMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ContactMapper;
use MPSToolbox\Legacy\Repositories\DealerRepository;
use MPSToolbox\Settings\Entities\ClientSettingsEntity;
use MPSToolbox\Settings\Service\ClientSettingsService;
use My_Model_Abstract;

/**
 * Class ClientModel
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Models
 */
class ClientModel extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $id = 0;

    /**
     * @var int
     */
    public $dealerId;
    /**
     * @var string
     */
    public $accountNumber = '';

    /**
     * @var string
     */
    public $companyName = '';

    /**
     * @var string
     */
    public $legalName = '';

    /**
     * @var int
     */
    public $employeeCount = 0;

    /**
     * @var ClientSettingsEntity
     */
    protected $clientSettings;

    /**
     * @var DealerEntity
     */
    protected $dealer;

    /**
     * @var SurveyEntity
     */
    protected $survey;

    /** @var  int */
    public $webId = null;

    /** @var  string */
    public $deviceGroup = null;

    /** @var  string */
    public $priceLevelId = null;

    /** @var  string */
    public $transactionType = null;

    /** @var  string */
    public $ecomMonochromeRank = null;

    /** @var  string */
    public $ecomColorRank = null;

    /** @var  int */
    public $templateNum = 1;

    /** @var  string */
    public $industry = null;

    /** @var boolean */
    public $monitoringEnabled = false;

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

        if (isset($params->dealerId) && !is_null($params->dealerId))
        {
            $this->dealerId = $params->dealerId;
        }

        if (isset($params->accountNumber) && !is_null($params->accountNumber))
        {
            $this->accountNumber = $params->accountNumber;
        }

        if (isset($params->companyName) && !is_null($params->companyName))
        {
            $this->companyName = $params->companyName;
        }

        if (isset($params->legalName) && !is_null($params->legalName))
        {
            $this->legalName = $params->legalName;
        }

        if (isset($params->employeeCount) && !is_null($params->employeeCount))
        {
            $this->employeeCount = $params->employeeCount;
        }

        if (isset($params->webId) && !is_null($params->webId))
        {
            $this->webId = $params->webId;
        }

        if (isset($params->deviceGroup) && !is_null($params->deviceGroup))
        {
            $this->deviceGroup = $params->deviceGroup;
        }

        if (isset($params->priceLevelId) && !is_null($params->priceLevelId))
        {
            $this->priceLevelId = $params->priceLevelId;
        }
        if (isset($params->transactionType) && !is_null($params->transactionType))
        {
            $this->transactionType = $params->transactionType;
        }
        if (isset($params->ecomMonochromeRank) && !is_null($params->ecomMonochromeRank))
        {
            $this->ecomMonochromeRank = $params->ecomMonochromeRank;
        }
        if (isset($params->ecomColorRank) && !is_null($params->ecomColorRank))
        {
            $this->ecomColorRank = $params->ecomColorRank;
        }
        if (isset($params->templateNum) && !is_null($params->templateNum))
        {
            $this->templateNum = $params->templateNum;
        }
        if (isset($params->industry) && !is_null($params->industry))
        {
            $this->industry = $params->industry;
        }
        if (isset($params->monitoringEnabled) && !is_null($params->monitoringEnabled))
        {
            $this->monitoringEnabled = $params->monitoringEnabled;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return [
            "id"            => $this->id,
            "dealerId"      => $this->dealerId,
            "accountNumber" => $this->accountNumber,
            "companyName"   => $this->companyName,
            "legalName"     => $this->legalName,
            "employeeCount" => $this->employeeCount,
            "webId" => $this->webId,
            "deviceGroup" => $this->deviceGroup,
            "priceLevelId" => $this->priceLevelId,
            "transactionType" => $this->transactionType,
            "ecomMonochromeRank" => $this->ecomMonochromeRank,
            "ecomColorRank" => $this->ecomColorRank,
            "templateNum" => $this->templateNum,
            "industry" => $this->industry,
            "monitoringEnabled" => $this->monitoringEnabled,
        ];
    }

    /**
     * Gets the address of this client
     *
     * @return AddressModel
     */
    public function getAddress ()
    {
        return AddressMapper::getInstance()->getAddressByClientId($this->id);
    }

    /**
     * Gets the contact of this client
     *
     * @return ContactModel
     */
    public function getContact ()
    {
        return ContactMapper::getInstance()->getContactByClientId($this->id);
    }

    /**
     * Gets the client settings
     *
     * @return ClientSettingsEntity
     */
    public function getClientSettings ()
    {
        if (!isset($this->clientSettings))
        {
            $clientSettingsService = new ClientSettingsService();
            $this->clientSettings  = $clientSettingsService->getClientSettings($this->id, $this->dealerId);
        }

        return $this->clientSettings;
    }

    /**
     * Gets the dealer
     *
     * @return DealerEntity
     */
    public function getDealer ()
    {
        if (!isset($this->dealer))
        {
            $this->dealer = DealerRepository::find($this->dealerId);
        }

        return $this->dealer;
    }

    /**
     * Gets the survey
     *
     * @return SurveyEntity
     */
    public function getSurvey ()
    {
        if (!isset($this->survey))
        {
            $this->survey = SurveyEntity::find($this->id);
        }

        return $this->survey;
    }
}