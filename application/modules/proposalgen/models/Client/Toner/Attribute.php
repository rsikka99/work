<?php

/**
 * Class Proposalgen_Model_Client_Toner_Attribute
 */
class Proposalgen_Model_Client_Toner_Attribute extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $tonerId;

    /**
     * @var int
     */
    public $clientId;

    /**
     * @var Float
     */
    public $cost;

    /**
     * @var string
     */
    public $clientSku;

    /**
     * @var Quotegen_Model_Client
     */
    protected $_client;

    /**
     * @var Proposalgen_Model_Toner
     */
    protected $_toner;

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->tonerId) && !is_null($params->tonerId))
        {
            $this->tonerId = $params->tonerId;
        }

        if (isset($params->clientId) && !is_null($params->clientId))
        {
            $this->clientId = $params->clientId;
        }

        if (isset($params->cost) && !is_null($params->cost))
        {
            $this->cost = $params->cost;
        }

        if (isset($params->clientSku) && !is_null($params->clientSku))
        {
            $this->clientSku = $params->clientSku;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "tonerId"   => $this->tonerId,
            "clientId"  => $this->clientId,
            "cost"      => $this->cost,
            "clientSku" => $this->clientSku,
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
}