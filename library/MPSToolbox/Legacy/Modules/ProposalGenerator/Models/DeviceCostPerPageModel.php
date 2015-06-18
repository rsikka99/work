<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Models;

use ArrayObject;
use My_Model_Abstract;
use Tangent\Accounting;

/**
 * Class DeviceCostPerPageModel
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Models
 */
class DeviceCostPerPageModel extends My_Model_Abstract
{

    /**
     * @var CostPerPageSettingModel
     */
    public $costPerPageSetting;

    /**
     * @var TonerModel []
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
     * @var CostPerPageModel []
     */
    protected $_cachedCostOfInkAndTonerPerPage;

    /**
     * @var CostPerPageModel []
     */
    protected $_cachedCostPerPage;

    /**
     * @param TonerModel []           $toners
     * @param CostPerPageSettingModel $costPerPageSetting
     * @param float                   $laborCostPerPage
     * @param float                   $partsCostPerPage
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
        return [
            "costPerPageSetting" => $this->costPerPageSetting,
            "toners"             => $this->toners,
            "laborCostPerPage"   => $this->laborCostPerPage,
            "partsCostPerPage"   => $this->partsCostPerPage,
            "isManaged"          => $this->isManaged,
        ];
    }

    /**
     * Calculates the cost per page for the given toners and adds parts/labor/admin
     *
     * @return CostPerPageModel
     */
    public function getCostPerPage ()
    {
        // Make sure our array is initialized
        if (!isset($this->_cachedCostPerPage))
        {
            $this->_cachedCostPerPage = [];
        }

        $cacheKey = $this->createCacheKey();

        if (!array_key_exists($cacheKey, $this->_cachedCostPerPage))
        {
            $costPerPage                        = new CostPerPageModel();
            $costPerPage->monochromeCostPerPage = 0;
            $costPerPage->colorCostPerPage      = 0;
            $costPerPage->add($this->getCostOfInkAndTonerPerPage());

            /**
             * Customer cost per page is used when we showing the cost per page to a customer and it's
             * a device that is currently managed by the dealer. This helps reflect accurate costs of
             * a fleet from the customers point of view.
             *
             * In this case we don't need to apply service/admin cost per page when we're using the
             * customer cost per page.
             */
            if (!$this->costPerPageSetting->useCustomerCostPerPageForManagedDevices || !$this->isManaged)
            {
                if ($costPerPage->monochromeCostPerPage > 0)
                {
                    if ($costPerPage->colorCostPerPage > 0)
                    {
                        $partsCostPerPage = ($this->partsCostPerPage !== null) ? $this->partsCostPerPage : $this->costPerPageSetting->colorPartsCostPerPage;
                        $laborCostPerPage = ($this->laborCostPerPage !== null) ? $this->laborCostPerPage : $this->costPerPageSetting->colorLaborCostPerPage;
                    }
                    else
                    {
                        $partsCostPerPage = ($this->partsCostPerPage !== null) ? $this->partsCostPerPage : $this->costPerPageSetting->monochromePartsCostPerPage;
                        $laborCostPerPage = ($this->laborCostPerPage !== null) ? $this->laborCostPerPage : $this->costPerPageSetting->monochromeLaborCostPerPage;
                    }


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
     * @return CostPerPageModel MPSToolbox\Legacy\Modules\ProposalGenerator\Models\CostPerPageModel
     */
    public function getCostOfInkAndTonerPerPage ()
    {
        // Make sure our array is initialized
        if (!isset($this->_cachedCostOfInkAndTonerPerPage))
        {
            $this->_cachedCostOfInkAndTonerPerPage = [];
        }

        $cacheKey = $this->createCacheKey();

        if (!array_key_exists($cacheKey, $this->_cachedCostOfInkAndTonerPerPage))
        {
            $costPerPage                        = new CostPerPageModel();
            $costPerPage->monochromeCostPerPage = 0;
            $costPerPage->colorCostPerPage      = 0;
            foreach ($this->toners as $toner)
            {
                $tonerCostPerPage = $toner->calculateCostPerPage($this->costPerPageSetting);

                if (!$toner->isUsingCustomerPricing)
                {
                    $tonerCostPerPage->monochromeCostPerPage = Accounting::applyMargin($tonerCostPerPage->monochromeCostPerPage, $this->costPerPageSetting->pricingMargin);
                    $tonerCostPerPage->colorCostPerPage      = Accounting::applyMargin($tonerCostPerPage->colorCostPerPage, $this->costPerPageSetting->pricingMargin);
                }

                $costPerPage->add($tonerCostPerPage);
            }

            /**
             * Customer cost per page is used when we showing the cost per page to a customer and it's
             * a device that is currently managed by the dealer. This helps reflect accurate costs of
             * a fleet from the customers point of view.
             */
            if ($this->costPerPageSetting->useCustomerCostPerPageForManagedDevices && $this->isManaged)
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
        $laborCostPerPage = (isset($this->laborCostPerpage) ? $this->laborCostPerpage : "null");
        $partsCostPerPage = (isset($this->partsCostPerPage) ? $this->partsCostPerPage : "null");

        return "{$laborCostPerPage}_{$partsCostPerPage}_{$this->isManaged}_{$this->costPerPageSetting->pricingMargin}";
    }
}