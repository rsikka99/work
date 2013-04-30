<?php
/**
 * Class Quotegen_Model_Client
 */
class Quotegen_Model_Client extends My_Model_Abstract
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

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"            => $this->id,
            "dealerId"      => $this->dealerId,
            "accountNumber" => $this->accountNumber,
            "companyName"   => $this->companyName,
            "legalName"     => $this->legalName,
            "employeeCount" => $this->employeeCount,
        );
    }

    /**
     * Gets the address of this client
     *
     * @return \Quotegen_Model_Address
     */
    public function getAddress ()
    {
        return Quotegen_Model_Mapper_Address::getInstance()->getAddressByClientId($this->id);
    }

    /**
     * Gets the contact of this client
     *
     * @return \Quotegen_Model_Contact
     */
    public function getContact ()
    {
        return Quotegen_Model_Mapper_Contact::getInstance()->getContactByClientId($this->id);
    }
}