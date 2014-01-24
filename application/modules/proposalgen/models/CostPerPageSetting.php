<?php

/**
 * Class Proposalgen_Model_CostPerPageSetting
 */
class Proposalgen_Model_CostPerPageSetting extends My_Model_Abstract
{
    /**
     * The admin cost per page
     *
     * @var float
     */
    public $adminCostPerPage = 0;

    /**
     * The default service cost per page
     *
     * @var float
     */
    public $laborCostPerPage = 0;

    /**
     * The default service cost per page
     *
     * @var float
     */
    public $partsCostPerPage = 0;
    /**
     * The monochrome page coverage
     *
     * @var float
     */
    public $pageCoverageMonochrome = 5;

    /**
     * The color page coverage
     *
     * @var float
     */
    public $pageCoverageColor = 20;

    /**
     * The toner preferences
     *
     * @var Proposalgen_Model_Toner_Vendor_Ranking_Set
     */
    public $monochromeTonerRankSet;

    /**
     * The toner preferences
     *
     * @var Proposalgen_Model_Toner_Vendor_Ranking_Set
     */
    public $colorTonerRankSet;

    /**
     * Whether we are using device page Coverages or not
     *
     * @var bool
     */
    public $useDevicePageCoverages;


    /**
     * @param null|array $options
     */
    public function __construct ($options = null)
    {
        parent::__construct($options);

        // If we haven't passed in a pricing configuration through the constructor, lets set it to OEM by default
        if (!isset($this->monochromeTonerRankSet))
        {
            $this->monochromeTonerRankSet = new Proposalgen_Model_Toner_Vendor_Ranking_Set();
        }

        if (!isset($this->colorTonerRankSet))
        {
            $this->colorTonerRankSet = new Proposalgen_Model_Toner_Vendor_Ranking_Set();
        }
    }

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->adminCostPerPage) && !is_null($params->adminCostPerPage))
        {
            $this->adminCostPerPage = $params->adminCostPerPage;
        }

        if (isset($params->pageCoverageMonochrome) && !is_null($params->pageCoverageMonochrome))
        {
            $this->pageCoverageMonochrome = $params->pageCoverageMonochrome;
        }

        if (isset($params->pageCoverageColor) && !is_null($params->pageCoverageColor))
        {
            $this->pageCoverageColor = $params->pageCoverageColor;
        }

        if (isset($params->monochromeTonerRankSet) && !is_null($params->monochromeTonerRankSet))
        {
            $this->monochromeTonerRankSet = $params->monochromeTonerRankSet;
        }

        if (isset($params->colorTonerRankSet) && !is_null($params->colorTonerRankSet))
        {
            $this->colorTonerRankSet = $params->colorTonerRankSet;
        }

        if (isset($params->useDevicePageCoverages) && !is_null($params->useDevicePageCoverages))
        {
            $this->useDevicePageCoverages = $params->useDevicePageCoverages;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "adminCostPerPage"       => $this->adminCostPerPage,
            "partsCostPerPage"       => $this->partsCostPerPage,
            "laborCostPerPage"       => $this->laborCostPerPage,
            "pageCoverageMonochrome" => $this->pageCoverageMonochrome,
            "pageCoverageColor"      => $this->pageCoverageColor,
            "monochromeTonerRankSet" => $this->monochromeTonerRankSet,
            "colorTonerRankSet"      => $this->colorTonerRankSet,
            "useDevicePageCoverages" => $this->useDevicePageCoverages,
        );
    }

    /**
     * Used to cache this object
     *
     * @return string
     */
    public function createCacheKey ()
    {
        $monochromeRanks = implode("_", $this->monochromeTonerRankSet->getRanksAsArray());
        $colorRanks      = implode("_", $this->colorTonerRankSet->getRanksAsArray());

        return "{$this->adminCostPerPage}_{$this->partsCostPerPage}_{$this->laborCostPerPage}_{$this->pageCoverageMonochrome}_{$this->pageCoverageColor}_{$this->useDevicePageCoverages}_monoranks_{$monochromeRanks}_colorranks_{$colorRanks}";
    }
}