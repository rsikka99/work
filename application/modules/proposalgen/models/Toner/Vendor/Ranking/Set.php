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

    public function getRanksAsArray ()
    {
        return Proposalgen_Model_Mapper_Toner_Vendor_Ranking::getInstance()->fetchAllByRankingSetIdAsArray($this->id);
    }

    /**
     * @return Proposalgen_Model_Toner_Vendor_Ranking[]
     */
    public function getRankings ()
    {
        return Proposalgen_Model_Mapper_Toner_Vendor_Ranking::getInstance()->fetchAllByRankingSetId($this->id);
    }
}