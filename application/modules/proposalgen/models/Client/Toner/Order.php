<?php

/**
 * Class Proposalgen_Model_Client_Toner_Attribute
 */
class Proposalgen_Model_Client_Toner_Order extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $tonerId;

    /**
     * @var int
     */
    public $clientId;

    /**
     * @var string
     */
    public $orderNumber;

    /**
     * @var string
     */
    public $oemSku;

    /**
     * @var string
     */
    public $dealerSku;

    /**
     * @var string
     */
    public $clientSku;

    /**
     * @var float
     */
    public $cost;

    /**
     * @var int
     */
    public $quantity;

    /**
     * @var string
     */
    public $dateOrdered;

    /**
     * @var string
     */
    public $dateShipped;

    /**
     * @var string
     */
    public $dateReconciled;

    /**
     * @var int
     */
    public $replacementTonerId;

    /**
     * @var Quotegen_Model_Client
     */
    protected $_client;

    /**
     * @var Proposalgen_Model_Toner
     */
    protected $_toner;

    /**
     * @var Proposalgen_Model_Toner
     */
    protected $_replacementToner;

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

        if (isset($params->tonerId) && !is_null($params->tonerId))
        {
            $this->tonerId = $params->tonerId;
        }

        if (isset($params->clientId) && !is_null($params->clientId))
        {
            $this->clientId = $params->clientId;
        }

        if (isset($params->orderNumber) && !is_null($params->orderNumber))
        {
            $this->orderNumber = $params->orderNumber;
        }

        if (isset($params->oemSku) && !is_null($params->oemSku))
        {
            $this->oemSku = $params->oemSku;
        }

        if (isset($params->dealerSku) && !is_null($params->dealerSku))
        {
            $this->dealerSku = $params->dealerSku;
        }

        if (isset($params->clientSku) && !is_null($params->clientSku))
        {
            $this->clientSku = $params->clientSku;
        }

        if (isset($params->cost) && !is_null($params->cost))
        {
            $this->cost = $params->cost;
        }

        if (isset($params->quantity) && !is_null($params->quantity))
        {
            $this->quantity = $params->quantity;
        }

        if (isset($params->dateOrdered) && !is_null($params->dateOrdered))
        {
            $this->dateOrdered = $params->dateOrdered;
        }

        if (isset($params->dateShipped) && !is_null($params->dateShipped))
        {
            $this->dateShipped = $params->dateShipped;
        }

        if (isset($params->dateReconciled) && !is_null($params->dateReconciled))
        {
            $this->dateReconciled = $params->dateReconciled;
        }
        if (isset($params->replacementTonerId) && !is_null($params->replacementTonerId))
        {
            $this->replacementTonerId = $params->replacementTonerId;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"                 => $this->id,
            "tonerId"            => $this->tonerId,
            "clientId"           => $this->clientId,
            "orderNumber"        => $this->orderNumber,
            "oemSku"             => $this->oemSku,
            "dealerSku"          => $this->dealerSku,
            "clientSku"          => $this->clientSku,
            "cost"               => $this->cost,
            "quantity"           => $this->quantity,
            "dateOrdered"        => $this->dateOrdered,
            "dateShipped"        => $this->dateShipped,
            "dateReconciled"     => $this->dateReconciled,
            "replacementTonerId" => $this->replacementTonerId,
        );
    }

    /**
     * Gets a client
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
     * Sets a client
     *
     * @param Quotegen_Model_Client $client
     *
     * @return $this
     */
    public function setClient ($client)
    {
        $this->_client = $client;

        return $this;
    }

    /**
     * Gets a toner
     *
     * @return Proposalgen_Model_Toner
     */
    public function getToner ()
    {
        if (!isset($this->_toner))
        {
            $this->_toner = Proposalgen_Model_Mapper_Toner::getInstance()->find($this->tonerId);
        }

        return $this->_toner;
    }


    /**
     * Sets a toner
     *
     * @param Proposalgen_Model_Toner $toner
     *
     * @return $this
     */
    public function setToner ($toner)
    {
        $this->_toner = $toner;

        return $this;
    }

    /**
     * Gets the replacement toner
     *
     * @return Proposalgen_Model_Toner
     */
    public function getReplacementToner ()
    {
        if (!isset($this->_replacementToner))
        {
            $this->_replacementToner = Proposalgen_Model_Mapper_Toner::getInstance()->find($this->replacementTonerId);
        }

        return $this->_replacementToner;
    }


    /**
     * Sets a toner
     *
     * @param Proposalgen_Model_Toner $toner
     *
     * @return $this
     */
    public function setReplacementTonerToner ($toner)
    {
        $this->_replacementToner = $toner;

        return $this;
    }
}