<?php

/**
 * Class Proposalgen_Model_DeviceCostPerPage
 */
class Proposalgen_Model_DeviceCostPerPage extends My_Model_Abstract
{

    /**
     * @var Proposalgen_Model_CostPerPageSetting
     */
    public $costPerPageSetting;

    /**
     * @var Proposalgen_Model_Toner []
     */
    public $toners;

    /**
     * @var float
     */
    public $laborCostPerPage;

    /**
     * @var float
     */
    public $partsCostPerPage;

    /**
     * @var bool
     */
    public $isManaged = false;

    /**
     * @var Proposalgen_Model_CostPerPage []
     */
    protected $_cachedCostOfInkAndTonerPerPage;

    /**
     * @var Proposalgen_Model_CostPerPage []
     */
    protected $_cachedCostPerPage;

    /**
     * @param Proposalgen_Model_Toner []           $toners
     * @param Proposalgen_Model_CostPerPageSetting $costPerPageSetting
     * @param float                                $laborCostPerPage
     * @param float                                $partsCostPerPage
     */
    public function __construct ($toners, $costPerPageSetting, $laborCostPerPage = null, $partsCostPerPage = null)
    {
        $this->toners             = $toners;
        $this->costPerPageSetting = clone $costPerPageSetting;
        $this->laborCostPerPage   = $laborCostPerPage;
        $this->partsCostPerPage   = $partsCostPerPage;
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

        if (isset($params->toners) && !is_null($params->toners))
        {
            $this->toners = $params->toners;
        }

        if (isset($params->costPerPageSetting) && !is_null($params->costPerPageSetting))
        {
            $this->costPerPageSetting = $params->costPerPageSetting;
        }

        if (isset($params->laborCostPerPage) && !is_null($params->laborCostPerPage))
        {
            $this->laborCostPerPage = $params->laborCostPerPage;
        }

        if (isset($params->partsCostPerPage) && !is_null($params->partsCostPerPage))
        {
            $this->partsCostPerPage = $params->partsCostPerPage;
        }

        if (isset($params->isManaged) && !is_null($params->isManaged))
        {
            $this->isManaged = $params->isManaged;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "costPerPageSetting" => $this->costPerPageSetting,
            "toners"             => $this->costPerPageSetting,
            "laborCostPerPage"   => $this->laborCostPerPage,
            "partsCostPerPage"   => $this->partsCostPerPage,
            "isManaged"          => $this->isManaged,
        );
    }

    /**
     * Calculates the cost per page for the given toners and adds parts/labor/admin
     *
     * @return Proposalgen_Model_CostPerPage
     */
    public function getCostPerPage ()
    {
        // Make sure our array is initialized
        if (!isset($this->_cachedCostPerPage))
        {
            $this->_cachedCostPerPage = array();
        }

        $cacheKey = $this->createCacheKey();

        if (!array_key_exists($cacheKey, $this->_cachedCostPerPage))
        {
            $costPerPage                        = new Proposalgen_Model_CostPerPage();
            $costPerPage->monochromeCostPerPage = 0;
            $costPerPage->colorCostPerPage      = 0;
            $costPerPage->add($this->getCostOfInkAndTonerPerPage());
            if (!$this->isManaged)
            {
                if ($costPerPage->monochromeCostPerPage > 0)
                {
                    $partsCostPerPage = ($this->partsCostPerPage !== null) ? $this->partsCostPerPage : $this->costPerPageSetting->partsCostPerPage;
                    $laborCostPerPage = ($this->laborCostPerPage !== null) ? $this->laborCostPerPage : $this->costPerPageSetting->laborCostPerPage;

                    $costPerPage->monochromeCostPerPage += $laborCostPerPage + $partsCostPerPage + $this->costPerPageSetting->adminCostPerPage;

                    if ($costPerPage->colorCostPerPage > 0)
                    {
                        $costPerPage->colorCostPerPage += $costPerPage->monochromeCostPerPage;
                    }
                }
            }

            $this->_cachedCostPerPage[$cacheKey] = $costPerPage;
        }

        return $this->_cachedCostPerPage[$cacheKey];
    }

    /**
     * Calculates the cost per page for a given device
     *
     * @return Proposalgen_Model_CostPerPage Proposalgen_Model_CostPerPage
     */
    public function getCostOfInkAndTonerPerPage ()
    {
        // Make sure our array is initialized
        if (!isset($this->_cachedCostOfInkAndTonerPerPage))
        {
            $this->_cachedCostOfInkAndTonerPerPage = array();
        }

        $cacheKey = $this->createCacheKey();

        if (!array_key_exists($cacheKey, $this->_cachedCostOfInkAndTonerPerPage))
        {
            $costPerPage                        = new Proposalgen_Model_CostPerPage();
            $costPerPage->monochromeCostPerPage = 0;
            $costPerPage->colorCostPerPage      = 0;
            foreach ($this->toners as $toner)
            {
                $tonerCostPerPage = $toner->calculateCostPerPage($this->costPerPageSetting);

                if (!$toner->isUsingCustomerPricing)
                {
                    $tonerCostPerPage->monochromeCostPerPage = Tangent_Accounting::applyMargin($tonerCostPerPage->monochromeCostPerPage, $this->costPerPageSetting->pricingMargin);
                    $tonerCostPerPage->colorCostPerPage      = Tangent_Accounting::applyMargin($tonerCostPerPage->colorCostPerPage, $this->costPerPageSetting->pricingMargin);
                }

                $costPerPage->add($tonerCostPerPage);
            }

            if ($this->isManaged)
            {
                if ($this->costPerPageSetting->customerMonochromeCostPerPage != null)
                {
                    $costPerPage->monochromeCostPerPage = $this->costPerPageSetting->customerMonochromeCostPerPage;
                }

                if ($this->costPerPageSetting->customerColorCostPerPage != null)
                {
                    $costPerPage->colorCostPerPage = $this->costPerPageSetting->customerColorCostPerPage;
                }
            }

            $this->_cachedCostOfInkAndTonerPerPage[$cacheKey] = $costPerPage;
        }

        return $this->_cachedCostOfInkAndTonerPerPage[$cacheKey];
    }

    public function createCacheKey ()
    {
        return "{$this->laborCostPerpage}_{$this->partsCostPerPage}_{$this->isManaged}_{$this->costPerPageSetting->pricingMargin}";
    }
}