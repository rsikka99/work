<?php
/**
 * Class Quotegen_Model_QuoteSetting
 */
class Quotegen_Model_QuoteSetting extends My_Model_Abstract
{
    const SYSTEM_ROW_ID = 1;

    /**
     * @var int
     */
    public $id = 0;

    /**
     * @var float
     */
    public $pageCoverageMonochrome;

    /**
     * @var float
     */
    public $pageCoverageColor;

    /**
     * @var float
     */
    public $deviceMargin;

    /**
     * @var float
     */
    public $pageMargin;

    /**
     * @var float
     */
    public $adminCostPerPage;

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

        if (isset($params->pageCoverageMonochrome) && !is_null($params->pageCoverageMonochrome))
        {
            $this->pageCoverageMonochrome = $params->pageCoverageMonochrome;
        }

        if (isset($params->pageCoverageColor) && !is_null($params->pageCoverageColor))
        {
            $this->pageCoverageColor = $params->pageCoverageColor;
        }

        if (isset($params->deviceMargin) && !is_null($params->deviceMargin))
        {
            $this->deviceMargin = $params->deviceMargin;
        }

        if (isset($params->pageMargin) && !is_null($params->pageMargin))
        {
            $this->pageMargin = $params->pageMargin;
        }

        if (isset($params->adminCostPerPage) && !is_null($params->adminCostPerPage))
        {
            $this->adminCostPerPage = $params->adminCostPerPage;
        }

        if (isset($params->dealerMonochromeRankSetId) && !is_null($params->dealerMonochromeRankSetId))
        {
            $this->dealerMonochromeRankSetId = $params->dealerMonochromeRankSetId;
        }

        if (isset($params->dealerColorRankSetId) && !is_null($params->dealerColorRankSetId))
        {
            $this->dealerColorRankSetId = $params->dealerColorRankSetId;
        }
    }

    /**
     * Takes an array or a quote setting object and applies overrides
     *
     * @param
     *            <array, Quotegen_Model_QuoteSetting> $settings
     */
    public function applyOverride ($settings)
    {
        // Turn an object into an array
        if ($settings instanceof Quotegen_Model_QuoteSetting)
        {
            $settings = $settings->toArray();
        }

        // Turn an array into an ArrayObject
        if (is_array($settings))
        {
            $settings = new ArrayObject($settings, ArrayObject::ARRAY_AS_PROPS);
        }

        // Do the same logic as populate, except never set the id.
        if (isset($settings->pageCoverageMonochrome) && !is_null($settings->pageCoverageMonochrome))
        {
            $this->pageCoverageMonochrome = $settings->pageCoverageMonochrome;
        }
        if (isset($settings->pageCoverageColor) && !is_null($settings->pageCoverageColor))
        {
            $this->pageCoverageColor = $settings->pageCoverageColor;
        }
        if (isset($settings->deviceMargin) && !is_null($settings->deviceMargin))
        {
            $this->deviceMargin = $settings->deviceMargin;
        }
        if (isset($settings->pageMargin) && !is_null($settings->pageMargin))
        {
            $this->pageMargin = $settings->pageMargin;
        }
        if (isset($settings->adminCostPerPage) && !is_null($settings->adminCostPerPage))
        {
            $this->adminCostPerPage = $settings->adminCostPerPage;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"                        => $this->id,
            "pageCoverageMonochrome"    => $this->pageCoverageMonochrome,
            "pageCoverageColor"         => $this->pageCoverageColor,
            "deviceMargin"              => $this->deviceMargin,
            "pageMargin"                => $this->pageMargin,
            "adminCostPerPage"          => $this->adminCostPerPage,
            "dealerMonochromeRankSetId" => $this->dealerMonochromeRankSetId,
            "dealerColorRankSetId"      => $this->dealerColorRankSetId,
        );
    }

    /**
     * @return array
     */
    public function getTonerRankSets ()
    {
        return array(
            "dealerColorRankSetArray"      => $this->getdealerColorRankSet()->getRanksAsArray(),
            "dealerMonochromeRankSetArray" => $this->getdealerMonochromeRankSet()->getRanksAsArray(),
        );
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
                Quotegen_Model_Mapper_QuoteSetting::getInstance()->save($this);
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
                Quotegen_Model_Mapper_QuoteSetting::getInstance()->save($this);
            }
        }

        return $this->_dealerColorRankSet;
    }
}