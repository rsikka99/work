<?php
/**
 * Class Proposalgen_Model_Dealer_Master_Device_Attribute
 */
class Proposalgen_Model_Dealer_Master_Device_Attribute extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $masterDeviceId;

    /**
     * @var int
     */
    public $dealerId;

    /**
     * @var Float
     */
    public $partsCostPerPage;

    /**
     * @var Float
     */
    public $laborCostPerPage;

    /**
     * @var Float
     */
    public $leaseBuybackPrice;

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->masterDeviceId) && !is_null($params->masterDeviceId))
        {
            $this->masterDeviceId = $params->masterDeviceId;
        }

        if (isset($params->dealerId) && !is_null($params->dealerId))
        {
            $this->dealerId = $params->dealerId;
        }

        if (isset($params->partsCostPerPage) && !is_null($params->partsCostPerPage))
        {
            $this->partsCostPerPage = $params->partsCostPerPage;
        }

        if (isset($params->laborCostPerPage) && !is_null($params->laborCostPerPage))
        {
            $this->laborCostPerPage = $params->laborCostPerPage;
        }

        if (isset($params->leaseBuybackPrice) && !is_null($params->leaseBuybackPrice))
        {
            $this->leaseBuybackPrice = $params->leaseBuybackPrice;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "masterDeviceId"    => $this->masterDeviceId,
            "dealerId"          => $this->dealerId,
            "partsCostPerPage"  => $this->partsCostPerPage,
            "laborCostPerPage"  => $this->laborCostPerPage,
            "leaseBuybackPrice" => $this->leaseBuybackPrice,
        );
    }

    /**
     * Saves a dealer master device attribute
     *
     * @return $this
     */
    public function saveObject ()
    {
        // Do we have an instance of it in our database?
        $dealerMasterDeviceAttributeMapper = Proposalgen_Model_Mapper_Dealer_Master_Device_Attribute::getInstance();

        $this->_filterData();

        if ($this->laborCostPerPage instanceof Zend_Db_Expr && $this->partsCostPerPage instanceof Zend_Db_Expr && $this->leaseBuybackPrice instanceof Zend_Db_expr)
        {
            $dealerMasterDeviceAttributeMapper->delete($this);
        }
        else
        {
            if ($dealerMasterDeviceAttributeMapper->fetch($dealerMasterDeviceAttributeMapper->getWhereId(array($this->masterDeviceId, $this->dealerId))))
            {
                $dealerMasterDeviceAttributeMapper->save($this);
            }
            else
            {
                $dealerMasterDeviceAttributeMapper->insert($this);
            }
        }

        return $this;
    }

    protected function _filterData ()
    {
        if ($this->laborCostPerPage < 0)
        {
            $this->laborCostPerPage = new Zend_Db_Expr("null");
        }

        if ($this->partsCostPerPage < 0)
        {
            $this->partsCostPerPage = new Zend_Db_Expr("null");
        }

        if ($this->leaseBuybackPrice < 0)
        {
            $this->leaseBuybackPrice = new Zend_Db_Expr("null");
        }
    }
}