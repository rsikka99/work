<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Models;

use ArrayObject;
use MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables\TonerVendorRankingDbTable;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerVendorRankingMapper;
use My_Model_Abstract;

/**
 * Class TonerVendorRankingSetModel
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Models
 */
class TonerVendorRankingSetModel extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var TonerVendorRankingModel[]
     */
    protected $_rankings;

    /**
     * @var array|TonerVendorRankingModel[]
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
        return [
            "id" => $this->id,
        ];
    }

    /**
     * @return array|TonerVendorRankingModel[]
     */
    public function getRanksAsArray ()
    {
        if (!isset($this->_ranksAsArray))
        {
            if ($this->id)
            {
                $this->_ranksAsArray = TonerVendorRankingMapper::getInstance()->fetchAllByRankingSetIdAsArray($this->id);
            }
            else
            {
                if ($this->_rankings)
                {
                    $sortedRankings = [];
                    foreach ($this->_rankings as $ranking)
                    {
                        $sortedRankings[$ranking->rank] = $ranking->manufacturerId;
                    }

                    usort($sortedRankings, function ($rankingA, $rankingB)
                    {
                        return ($rankingA->rank > $rankingB->rank);
                    });

                    $this->_ranksAsArray = $sortedRankings;
                }
                else
                {
                    $this->_ranksAsArray = [];
                }
            }
        }

        return $this->_ranksAsArray;
    }

    /**
     * @return TonerVendorRankingModel[]
     */
    public function getRankings ()
    {
        if (!isset($this->_rankings))
        {
            $this->_rankings = TonerVendorRankingMapper::getInstance()->fetchAllByRankingSetId($this->id);
        }

        return $this->_rankings;
    }

    /**
     * @param $rankings TonerVendorRankingModel[]
     */
    public function setRankings ($rankings)
    {
        $this->_rankings = $rankings;
        unset($this->_ranksAsArray);
    }
}