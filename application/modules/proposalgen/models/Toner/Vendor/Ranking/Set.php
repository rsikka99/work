<?php
/**
 * Class Proposalgen_Model_Toner_Vendor_Ranking_Set
 */
class Proposalgen_Model_Toner_Vendor_Ranking_Set extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var Proposalgen_Model_DbTable_Toner_Vendor_Ranking[]
     */
    protected $_rankings;

    /**
     * @var array|Proposalgen_Model_Toner_Vendor_Ranking[]
     */
    protected $_ranksAsArray;

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
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id" => $this->id,
        );
    }

    /**
     * @return array|Proposalgen_Model_Toner_Vendor_Ranking[]
     */
    public function getRanksAsArray ()
    {
        if (!isset($this->_ranksAsArray))
        {
            $this->_ranksAsArray = Proposalgen_Model_Mapper_Toner_Vendor_Ranking::getInstance()->fetchAllByRankingSetIdAsArray($this->id);
        }

        return $this->_ranksAsArray;
    }

    /**
     * @return Proposalgen_Model_Toner_Vendor_Ranking[]
     */
    public function getRankings ()
    {
        if (!isset($this->_rankings))
        {
            $this->_rankings = Proposalgen_Model_Mapper_Toner_Vendor_Ranking::getInstance()->fetchAllByRankingSetId($this->id);
        }

        return $this->_rankings;
    }

    /**
     * @param $rankings Proposalgen_Model_Toner_Vendor_Ranking
     */
    public function setRankings ($rankings)
    {
        $this->_ranksAsArray = array($rankings->manufacturerId);
        $this->_rankings     = array($rankings);
    }
}