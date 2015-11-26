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
    public $accountNumber;

    /**
     * @var string
     */
    public $companyName;

    /**
     * @var string
     */
    public $legalName;

    /**
     * @var int
     */
    public $employeeCount;

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
    protected $webId;

    /** @var  string */
    public $notSupportedMasterDevices;

    /** @var  string */
    public $deviceGroup;

    /** @var  string */
    public $priceLevel;

    /** @var  string */
    public $transactionType;

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

        if (isset($params->notSupportedMasterDevices) && !is_null($params->notSupportedMasterDevices))
        {
            if (is_array($params->notSupportedMasterDevices)) $params->notSupportedMasterDevices = trim(str_replace(',,',',',implode(',', $params->notSupportedMasterDevices)),',');
            $this->notSupportedMasterDevices = $params->notSupportedMasterDevices;
        }
        if (isset($params->deviceGroup) && !is_null($params->deviceGroup))
        {
            $this->deviceGroup = $params->deviceGroup;
        }

        if (isset($params->priceLevel) && !is_null($params->priceLevel))
        {
            $this->priceLevel = $params->priceLevel;
        }
        if (isset($params->transactionType) && !is_null($params->transactionType))
        {
            $this->transactionType = $params->transactionType;
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
            "notSupportedMasterDevices" => $this->notSupportedMasterDevices,
            "deviceGroup" => $this->deviceGroup,
            "priceLevel" => $this->priceLevel,
            "transactionType" => $this->transactionType,
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