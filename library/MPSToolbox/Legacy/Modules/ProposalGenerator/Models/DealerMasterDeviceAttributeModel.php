<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Models;

use ArrayObject;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DealerMasterDeviceAttributeMapper;
use My_Model_Abstract;
use Zend_Db_Expr;

/**
 * Class DealerMasterDeviceAttributeModel
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Models
 */
class DealerMasterDeviceAttributeModel extends My_Model_Abstract
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

    /** @var  boolean */
    public $isLeased;

    /** @var  int */
    public $leasedTonerYield;

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

        if (isset($params->isLeased) && !is_null($params->isLeased)) $this->isLeased = $params->isLeased;
        if (isset($params->leasedTonerYield) && !is_null($params->leasedTonerYield)) $this->leasedTonerYield = $params->leasedTonerYield;
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return [
            "masterDeviceId"    => $this->masterDeviceId,
            "dealerId"          => $this->dealerId,
            "partsCostPerPage"  => $this->partsCostPerPage,
            "laborCostPerPage"  => $this->laborCostPerPage,
            "leaseBuybackPrice" => $this->leaseBuybackPrice,
            "isLeased" => $this->isLeased,
            "leasedTonerYield" => $this->leasedTonerYield,
        ];
    }

    /**
     * Saves a dealer master device attribute
     *
     * @return $this
     */
    public function saveObject ()
    {
        // Do we have an instance of it in our database?
        $dealerMasterDeviceAttributeMapper = DealerMasterDeviceAttributeMapper::getInstance();

        $this->_filterData();

        #if ($this->laborCostPerPage instanceof Zend_Db_Expr && $this->partsCostPerPage instanceof Zend_Db_Expr && $this->leaseBuybackPrice instanceof Zend_Db_Expr)
        #{
        #    $dealerMasterDeviceAttributeMapper->delete($this);
        #}
        #else
        #{
            if ($dealerMasterDeviceAttributeMapper->fetch($dealerMasterDeviceAttributeMapper->getWhereId([$this->masterDeviceId, $this->dealerId])))
            {
                $dealerMasterDeviceAttributeMapper->save($this);
            }
            else
            {
                $dealerMasterDeviceAttributeMapper->insert($this);
            }
        #}

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

        if ($this->leasedTonerYield < 0)
        {
            $this->leasedTonerYield = new Zend_Db_Expr("null");
        }
    }
}