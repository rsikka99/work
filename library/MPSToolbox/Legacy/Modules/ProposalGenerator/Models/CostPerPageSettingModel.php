<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Models;

use ArrayObject;
use My_Model_Abstract;
use Zend_Auth;
use Zend_Session_Namespace;

/**
 * Class CostPerPageSettingModel
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Models
 */
class CostPerPageSettingModel extends My_Model_Abstract
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
    public $monochromeLaborCostPerPage = 0;

    /**
     * The default service cost per page
     *
     * @var float
     */
    public $monochromePartsCostPerPage = 0;

    /**
     * The default service cost per page
     *
     * @var float
     */
    public $colorLaborCostPerPage = 0;

    /**
     * The default service cost per page
     *
     * @var float
     */
    public $colorPartsCostPerPage = 0;

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
     * @var TonerVendorRankingSetModel
     */
    public $monochromeTonerRankSet;

    /**
     * The toner preferences
     *
     * @var TonerVendorRankingSetModel
     */
    public $colorTonerRankSet;

    /**
     * Whether we are using device page Coverages or not
     *
     * @var bool
     */
    public $useDevicePageCoverages;

    /**
     * The monochrome page coverage
     *
     * @var float
     */
    public $customerMonochromeCostPerPage;

    /**
     * The color page coverage
     *
     * @var float
     */
    public $customerColorCostPerPage;

    /**
     * The client id
     *
     * @var int
     */
    public $clientId;

    /**
     * The dealer id
     *
     * @var int
     */
    public $dealerId;

    /**
     * The pricing margin
     *
     * @var float
     */
    public $pricingMargin = 0;

    /**
     * Whether or not to apply service to managed devices
     *
     * @var bool
     */
    public $useCustomerCostPerPageForManagedDevices = false;

    /**
     * @param null|array $options
     */
    public function __construct ($options = null)
    {
        parent::__construct($options);

        // If we haven't passed in a pricing configuration through the constructor, lets set it to OEM by default
        if (!isset($this->monochromeTonerRankSet))
        {
            $this->monochromeTonerRankSet = new TonerVendorRankingSetModel();
        }

        if (!isset($this->colorTonerRankSet))
        {
            $this->colorTonerRankSet = new TonerVendorRankingSetModel();
        }

        if (!isset($this->clientId))
        {
            $this->clientId = (new Zend_Session_Namespace('mps-tools'))->selectedClientId;
        }

        if (!isset($this->dealerId))
        {
            $this->dealerId = Zend_Auth::getInstance()->getIdentity()->dealerId;
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

        if (isset($params->monochromeLaborCostPerPage) && !is_null($params->monochromeLaborCostPerPage))
        {
            $this->monochromeLaborCostPerPage = $params->monochromeLaborCostPerPage;
        }

        if (isset($params->monochromePartsCostPerPage) && !is_null($params->monochromePartsCostPerPage))
        {
            $this->monochromePartsCostPerPage = $params->monochromePartsCostPerPage;
        }

        if (isset($params->colorLaborCostPerPage) && !is_null($params->colorLaborCostPerPage))
        {
            $this->colorLaborCostPerPage = $params->colorLaborCostPerPage;
        }

        if (isset($params->colorPartsCostPerPage) && !is_null($params->colorPartsCostPerPage))
        {
            $this->colorPartsCostPerPage = $params->colorPartsCostPerPage;
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

        if (isset($params->customerMonochromeCostPerPage) && !is_null($params->customerMonochromeCostPerPage))
        {
            $this->customerMonochromeCostPerPage = $params->customerMonochromeCostPerPage;
        }

        if (isset($params->customerColorCostPerPage) && !is_null($params->customerColorCostPerPage))
        {
            $this->customerColorCostPerPage = $params->customerColorCostPerPage;
        }

        if (isset($params->clientId) && !is_null($params->clientId))
        {
            $this->clientId = $params->clientId;
        }

        if (isset($params->dealerId) && !is_null($params->dealerId))
        {
            $this->dealerId = $params->dealerId;
        }

        if (isset($params->pricingMargin) && !is_null($params->pricingMargin))
        {
            $this->pricingMargin = $params->pricingMargin;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "adminCostPerPage"              => $this->adminCostPerPage,
            "monochromePartsCostPerPage"    => $this->monochromePartsCostPerPage,
            "monochromeLaborCostPerPage"    => $this->monochromeLaborCostPerPage,
            "colorPartsCostPerPage"         => $this->colorPartsCostPerPage,
            "colorLaborCostPerPage"         => $this->colorLaborCostPerPage,
            "pageCoverageMonochrome"        => $this->pageCoverageMonochrome,
            "pageCoverageColor"             => $this->pageCoverageColor,
            "monochromeTonerRankSet"        => $this->monochromeTonerRankSet,
            "colorTonerRankSet"             => $this->colorTonerRankSet,
            "useDevicePageCoverages"        => $this->useDevicePageCoverages,
            "customerMonochromeCostPerPage" => $this->customerMonochromeCostPerPage,
            "customerColorCostPerPage"      => $this->customerColorCostPerPage,
            "clientId"                      => $this->clientId,
            "dealerId"                      => $this->dealerId,
            "pricingMargin"                 => $this->pricingMargin,
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

        return "CPPSetting"
               . "_{$this->clientId}"
               . "_{$this->dealerId}"
               . "_{$this->adminCostPerPage}"
               . "_{$this->monochromePartsCostPerPage}"
               . "_{$this->monochromeLaborCostPerPage}"
               . "_{$this->colorPartsCostPerPage}"
               . "_{$this->colorLaborCostPerPage}"
               . "_{$this->pageCoverageMonochrome}"
               . "_{$this->pageCoverageColor}"
               . "_{$this->useDevicePageCoverages}"
               . "_monoranks_{$monochromeRanks}"
               . "_colorranks_{$colorRanks}"
               . "_{$this->customerMonochromeCostPerPage}"
               . "_{$this->customerColorCostPerPage}"
               . "_{$this->pricingMargin}";
    }
}