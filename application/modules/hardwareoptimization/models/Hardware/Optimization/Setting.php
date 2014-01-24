<?php

/**
 * Class Hardwareoptimization_Model_Hardware_Optimization_Setting
 */
class Hardwareoptimization_Model_Hardware_Optimization_Setting extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $costThreshold;

    /**
     * @var int
     */
    public $targetColorCostPerPage;

    /**
     * @var int
     */
    public $targetMonochromeCostPerPage;

    /**
     * @var float
     */
    public $adminCostPerPage;

    /**
     * @var float
     */
    public $laborCostPerPage;

    /**
     * @var float
     */
    public $partsCostPerPage;

    /**
     * @var float
     */
    public $pageCoverageMonochrome;

    /**
     * @var float
     */
    public $pageCoverageColor;

    /**
     * @var bool
     */
    public $useDevicePageCoverages = 0;

    /**
     * @var int
     */
    public $replacementMonochromeRankSetId;

    /**
     * @var int
     */
    public $replacementColorRankSetId;

    /**
     * @var int
     */
    public $dealerMonochromeRankSetId;

    /**
     * @var int
     */
    public $dealerColorRankSetId;

    /**
     * @var Proposalgen_Model_Toner_Vendor_Ranking_Set
     */
    protected $_replacementMonochromeRankSet;

    /**
     * @var Proposalgen_Model_Toner_Vendor_Ranking_Set
     */
    protected $_replacementColorRankSet;

    /**
     * @var Proposalgen_Model_Toner_Vendor_Ranking_Set
     */
    protected $_dealerMonochromeRankSet;

    /**
     * @var Proposalgen_Model_Toner_Vendor_Ranking_Set
     */
    protected $_dealerColorRankSet;


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

        if (isset($params->costThreshold) && !is_null($params->costThreshold))
        {
            $this->costThreshold = $params->costThreshold;
        }

        if (isset($params->targetColorCostPerPage) && !is_null($params->targetColorCostPerPage))
        {
            $this->targetColorCostPerPage = $params->targetColorCostPerPage;
        }

        if (isset($params->targetMonochromeCostPerPage) && !is_null($params->targetMonochromeCostPerPage))
        {
            $this->targetMonochromeCostPerPage = $params->targetMonochromeCostPerPage;
        }

        if (isset($params->adminCostPerPage) && !is_null($params->adminCostPerPage))
        {
            $this->adminCostPerPage = $params->adminCostPerPage;
        }

        if (isset($params->laborCostPerPage) && !is_null($params->laborCostPerPage))
        {
            $this->laborCostPerPage = $params->laborCostPerPage;
        }

        if (isset($params->partsCostPerPage) && !is_null($params->partsCostPerPage))
        {
            $this->partsCostPerPage = $params->partsCostPerPage;
        }

        if (isset($params->pageCoverageMonochrome) && !is_null($params->pageCoverageMonochrome))
        {
            $this->pageCoverageMonochrome = $params->pageCoverageMonochrome;
        }

        if (isset($params->pageCoverageColor) && !is_null($params->pageCoverageColor))
        {
            $this->pageCoverageColor = $params->pageCoverageColor;
        }

        if (isset($params->useDevicePageCoverages) && !is_null($params->useDevicePageCoverages))
        {
            $this->useDevicePageCoverages = $params->useDevicePageCoverages;
        }

        if (isset($params->replacementColorRankSetId) && !is_null($params->replacementColorRankSetId))
        {
            $this->replacementColorRankSetId = $params->replacementColorRankSetId;
        }

        if (isset($params->replacementMonochromeRankSetId) && !is_null($params->replacementMonochromeRankSetId))
        {
            $this->replacementMonochromeRankSetId = $params->replacementMonochromeRankSetId;
        }

        if (isset($params->dealerColorRankSetId) && !is_null($params->dealerColorRankSetId))
        {
            $this->dealerColorRankSetId = $params->dealerColorRankSetId;
        }

        if (isset($params->dealerMonochromeRankSetId) && !is_null($params->dealerMonochromeRankSetId))
        {
            $this->dealerMonochromeRankSetId = $params->dealerMonochromeRankSetId;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"                             => $this->id,
            "costThreshold"                  => $this->costThreshold,
            "targetColorCostPerPage"         => $this->targetColorCostPerPage,
            "targetMonochromeCostPerPage"    => $this->targetMonochromeCostPerPage,
            "adminCostPerPage"               => $this->adminCostPerPage,
            "laborCostPerPage"               => $this->laborCostPerPage,
            "partsCostPerPage"               => $this->partsCostPerPage,
            "pageCoverageMonochrome"         => $this->pageCoverageMonochrome,
            "pageCoverageColor"              => $this->pageCoverageColor,
            "useDevicePageCoverages"         => $this->useDevicePageCoverages,
            "replacementColorRankSetId"      => $this->replacementColorRankSetId,
            "replacementMonochromeRankSetId" => $this->replacementMonochromeRankSetId,
            "dealerColorRankSetId"           => $this->dealerColorRankSetId,
            "dealerMonochromeRankSetId"      => $this->dealerMonochromeRankSetId,
        );
    }

    /**
     * @return array
     */
    public function getTonerRankSets ()
    {
        return array(
            "replacementColorRankSetArray"      => $this->getReplacementColorRankSet()->getRanksAsArray(),
            "replacementMonochromeRankSetArray" => $this->getReplacementMonochromeRankSet()->getRanksAsArray(),
            "dealerMonochromeRankSetArray"      => $this->getDealerMonochromeRankSet()->getRanksAsArray(),
            "dealerColorRankSetArray"           => $this->getDealerColorRankSet()->getRanksAsArray(),
        );
    }


    /**
     * @return Proposalgen_Model_Toner_Vendor_Ranking_Set
     */
    public function getReplacementColorRankSet ()
    {
        if (!isset($this->_replacementColorRankSet))
        {
            if ($this->replacementColorRankSetId > 0)
            {
                $this->_replacementColorRankSet = Proposalgen_Model_Mapper_Toner_Vendor_Ranking_Set::getInstance()->find($this->replacementColorRankSetId);
            }
            else
            {
                $this->_replacementColorRankSet  = new Proposalgen_Model_Toner_Vendor_Ranking_Set();
                $this->replacementColorRankSetId = Proposalgen_Model_Mapper_Toner_Vendor_Ranking_Set::getInstance()->insert($this->_replacementColorRankSet);
                // Update ourselves
                Hardwareoptimization_Model_Mapper_Hardware_Optimization_Setting::getInstance()->save($this);
            }
        }

        return $this->_replacementColorRankSet;
    }


    /**
     * @return Proposalgen_Model_Toner_Vendor_Ranking_Set
     */
    public function getReplacementMonochromeRankSet ()
    {
        if (!isset($this->_replacementMonochromeRankSet))
        {
            if ($this->replacementMonochromeRankSetId > 0)
            {
                $this->_replacementMonochromeRankSet = Proposalgen_Model_Mapper_Toner_Vendor_Ranking_Set::getInstance()->find($this->replacementMonochromeRankSetId);
            }
            else
            {
                $this->_replacementMonochromeRankSet  = new Proposalgen_Model_Toner_Vendor_Ranking_Set();
                $this->replacementMonochromeRankSetId = Proposalgen_Model_Mapper_Toner_Vendor_Ranking_Set::getInstance()->insert($this->_replacementMonochromeRankSet);
                // Update ourselves
                Hardwareoptimization_Model_Mapper_Hardware_Optimization_Setting::getInstance()->save($this);
            }
        }

        return $this->_replacementMonochromeRankSet;
    }


    /**
     * @return Proposalgen_Model_Toner_Vendor_Ranking_Set
     */
    public function getDealerMonochromeRankSet ()
    {
        if (!isset($this->_dealerMonochromeRankSet))
        {
            if ($this->dealerMonochromeRankSetId > 0)
            {
                $this->_dealerMonochromeRankSet = Proposalgen_Model_Mapper_Toner_Vendor_Ranking_Set::getInstance()->find($this->dealerMonochromeRankSetId);
            }
            else
            {
                $this->_dealerMonochromeRankSet  = new Proposalgen_Model_Toner_Vendor_Ranking_Set();
                $this->dealerMonochromeRankSetId = Proposalgen_Model_Mapper_Toner_Vendor_Ranking_Set::getInstance()->insert($this->_dealerMonochromeRankSet);
                // Update ourselves
                Hardwareoptimization_Model_Mapper_Hardware_Optimization_Setting::getInstance()->save($this);
            }
        }

        return $this->_dealerMonochromeRankSet;
    }

    /**
     * @return Proposalgen_Model_Toner_Vendor_Ranking_Set
     */
    public function getDealerColorRankSet ()
    {
        if (!isset($this->_dealerColorRankSet))
        {
            if ($this->dealerColorRankSetId > 0)
            {
                $this->_dealerColorRankSet = Proposalgen_Model_Mapper_Toner_Vendor_Ranking_Set::getInstance()->find($this->dealerColorRankSetId);
            }
            else
            {
                $this->_dealerColorRankSet  = new Proposalgen_Model_Toner_Vendor_Ranking_Set();
                $this->dealerColorRankSetId = Proposalgen_Model_Mapper_Toner_Vendor_Ranking_Set::getInstance()->insert($this->_dealerColorRankSet);
                // Update ourselves
                Hardwareoptimization_Model_Mapper_Hardware_Optimization_Setting::getInstance()->save($this);
            }
        }

        return $this->_dealerColorRankSet;
    }
}