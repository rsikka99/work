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
     * @var int
     */
    public $overrideManufacturer = null;

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
        if($this->overrideManufacturer == null)
        {
            return Proposalgen_Model_Mapper_Toner_Vendor_Ranking::getInstance()->fetchAllByRankingSetIdAsArray($this->id);
        }
        else
        {
            return array($this->overrideManufacturer);
        }
    }

    /**
     * @return Proposalgen_Model_Toner_Vendor_Ranking[]
     */
    public function getRankings ()
    {
        if($this->overrideManufacturer == null)
        {
            return Proposalgen_Model_Mapper_Toner_Vendor_Ranking::getInstance()->fetchAllByRankingSetId($this->id);
        }
        else
        {
            $vendorRankings = array();
            $rank = new Proposalgen_Model_Toner_Vendor_Ranking();
            $rank->manufacturerId = $this->overrideManufacturer;
            $vendorRankings[] = $rank;
            return $vendorRankings;
        }
    }
}