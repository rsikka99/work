<?php

/**
 * Class Proposalgen_Model_Toner
 */
class Proposalgen_Model_Toner extends My_Model_Abstract
{
    const MANUFACTURER_ASSUMED_COVERAGE = 0.05;

    // Database fields
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $userId;

    /**
     * @var int
     */
    public $isSystemDevice;

    /**
     * @var string
     */
    public $sku;

    /**
     * @var float
     */
    public $cost;

    /**
     * @var int
     */
    public $yield;

    /**
     * @var int
     */
    public $manufacturerId;

    /**
     * @var int
     */
    public $tonerColorId;

    /**
     * @var float
     */
    public $calculatedCost;

    /**
     * @var bool
     */
    public $isUsingCustomerPricing = false;

    /**
     * @var bool
     */
    public $isUsingDealerPricing = false;

    /**
     * @var Proposalgen_Model_Manufacturer
     */
    protected $_manufacturer;

    /**
     * @var Proposalgen_Model_TonerColor
     */
    protected $_tonerColor;

    /**
     * @var Proposalgen_Model_Dealer_Toner_Attribute
     */
    protected $_dealerTonerAttribute;

    /**
     * @var bool
     */
    protected $_isCompatible;

    /**
     * @var Proposalgen_Model_Toner[]
     */
    protected $_compatibleToners;

    /**
     * @var Proposalgen_Model_Toner[]
     */
    protected $_oemToners;

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

        if (isset($params->userId) && !is_null($params->userId))
        {
            $this->userId = $params->userId;
        }

        if (isset($params->isSystemDevice) && !is_null($params->isSystemDevice))
        {
            $this->isSystemDevice = $params->isSystemDevice;
        }

        if (isset($params->sku) && !is_null($params->sku))
        {
            $this->sku = $params->sku;
        }

        if (isset($params->cost) && !is_null($params->cost))
        {
            $this->cost = $params->cost;
        }

        if (isset($params->yield) && !is_null($params->yield))
        {
            $this->yield = $params->yield;
        }

        if (isset($params->manufacturerId) && !is_null($params->manufacturerId))
        {
            $this->manufacturerId = $params->manufacturerId;
        }

        if (isset($params->tonerColorId) && !is_null($params->tonerColorId))
        {
            $this->tonerColorId = $params->tonerColorId;
        }

        if (isset($params->calculatedCost) && !is_null($params->calculatedCost))
        {
            $this->calculatedCost = $params->calculatedCost;
        }

        if (isset($params->isUsingCustomerPricing) && !is_null($params->isUsingCustomerPricing))
        {
            $this->isUsingCustomerPricing = $params->isUsingCustomerPricing;
        }

        if (isset($params->isUsingDealerPricing) && !is_null($params->isUsingDealerPricing))
        {
            $this->isUsingDealerPricing = $params->isUsingDealerPricing;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"             => $this->id,
            "userId"         => $this->userId,
            "isSystemDevice" => $this->isSystemDevice,
            "sku"            => $this->sku,
            "cost"           => $this->cost,
            "yield"          => $this->yield,
            "manufacturerId" => $this->manufacturerId,
            "tonerColorId"   => $this->tonerColorId,
        );
    }


    /**
     * @return Proposalgen_Model_Manufacturer
     */
    public function getManufacturer ()
    {
        if (!isset($this->_manufacturer))
        {
            $this->_manufacturer = Proposalgen_Model_Mapper_Manufacturer::getInstance()->find($this->manufacturerId);
        }

        return $this->_manufacturer;
    }

    /**
     * @param Proposalgen_Model_Manufacturer $Manufacturer
     *
     * @return Proposalgen_Model_Toner
     */
    public function setManufacturer ($Manufacturer)
    {
        $this->_manufacturer = $Manufacturer;

        return $this;
    }

    /**
     * @return Proposalgen_Model_TonerColor
     */
    public function getTonerColor ()
    {
        if (!isset($this->_tonerColor))
        {
            $this->_tonerColor = Proposalgen_Model_Mapper_TonerColor::getInstance()->find($this->tonerColorId);
        }

        return $this->_tonerColor;
    }

    /**
     * @param Proposalgen_Model_TonerColor $tonerColor
     *
     * @return \Proposalgen_Model_Toner
     */
    public function setTonerColor ($tonerColor)
    {
        $this->_tonerColor = $tonerColor;

        return $this;
    }

    /**
     * @param int $dealerId
     *
     * @return Proposalgen_Model_Dealer_Toner_Attribute
     */
    public function getDealerTonerAttribute ($dealerId)
    {
        if (!isset($this->_dealerTonerAttribute))
        {
            $this->_dealerTonerAttribute = array();
        }

        if (!isset($this->_dealerTonerAttribute[$dealerId]))
        {
            $this->_dealerTonerAttribute[$dealerId] = Proposalgen_Model_Mapper_Dealer_Toner_Attribute::getInstance()->find(array($this->id, $dealerId));
        }

        return $this->_dealerTonerAttribute[$dealerId];
    }

    /**
     * @param Proposalgen_Model_Dealer_Toner_Attribute $dealerTonerAttribute
     *
     * @return Proposalgen_Model_Dealer_Toner_Attribute
     */
    public function setDealerTonerAttribute ($dealerTonerAttribute)
    {
        $this->_dealerTonerAttribute[$dealerTonerAttribute->dealerId] = $dealerTonerAttribute;

        return $this;
    }

    /**
     * Calculates the coverage adjusted cost per page
     *
     * @param Proposalgen_Model_CostPerPageSetting $costPerPageSetting
     *            The settings to use when calculating cost per page
     *
     * @return Proposalgen_Model_CostPerPage
     */
    public function calculateCostPerPage (Proposalgen_Model_CostPerPageSetting $costPerPageSetting)
    {
        // Make sure our array is initialized
        if (!isset($this->_cachedCostPerPage))
        {
            $this->_cachedCostPerPage = array();
        }

        // Turn coverage into a decimal
        $monochromeCoverage = $costPerPageSetting->pageCoverageMonochrome / 100;
        $colorCoverage      = $costPerPageSetting->pageCoverageColor / 100;

        $cacheKey = "{$monochromeCoverage}_{$colorCoverage}";
        if (!array_key_exists($cacheKey, $this->_cachedCostPerPage))
        {
            $costPerPage = new Proposalgen_Model_CostPerPage();
            /*
             * Cost per page is calculated by dividing the price by the yield. When providing new coverage we need to
             * divide the manufacturers coverage by our coverage in order to arrive at the right number.
             */
            $monochromeCostPerPage = 0;
            $colorCostPerPage      = 0;

            switch ($this->tonerColorId)
            {
                case Proposalgen_Model_TonerColor::BLACK :
                    $monochromeCostPerPage = $this->calculatedCost / ($this->yield * (self::MANUFACTURER_ASSUMED_COVERAGE / $monochromeCoverage));
                    break;
                case Proposalgen_Model_TonerColor::CYAN :
                case Proposalgen_Model_TonerColor::MAGENTA :
                case Proposalgen_Model_TonerColor::YELLOW :
                    $colorCostPerPage = $this->calculatedCost / ($this->yield * (self::MANUFACTURER_ASSUMED_COVERAGE / ($colorCoverage / 4)));
                    break;
                case Proposalgen_Model_TonerColor::THREE_COLOR :
                    $colorCostPerPage = $this->calculatedCost / ($this->yield * (self::MANUFACTURER_ASSUMED_COVERAGE / ($colorCoverage / 4 * 3)));
                    break;
                case Proposalgen_Model_TonerColor::FOUR_COLOR :
                    $monochromeCostPerPage = $this->calculatedCost / ($this->yield * (self::MANUFACTURER_ASSUMED_COVERAGE / $monochromeCoverage));
                    $colorCostPerPage      = $this->calculatedCost / ($this->yield * (self::MANUFACTURER_ASSUMED_COVERAGE / $colorCoverage));
                    break;
            }

            $costPerPage->monochromeCostPerPage = $monochromeCostPerPage;
            $costPerPage->colorCostPerPage      = $colorCostPerPage;

            $this->_cachedCostPerPage [$cacheKey] = $costPerPage;
        }

        return $this->_cachedCostPerPage [$cacheKey];
    }

    /**
     * Whether or not this toner is a compatible toner
     *
     * @return bool
     */
    public function isCompatible ()
    {
        if (!isset($this->_isCompatible))
        {

            $this->_isCompatible = (Proposalgen_Model_Mapper_TonerVendorManufacturer::getInstance()->find($this->manufacturerId) instanceof Proposalgen_Model_TonerVendorManufacturer);
        }

        return $this->_isCompatible;
    }

    /**
     * Finds other compatible toners that can be used in place of this one.
     *
     * @param null $clientId
     * @param null $dealerId
     *
     * @return Proposalgen_Model_Toner[]
     */
    public function getCompatibleToners ($clientId = null, $dealerId = null)
    {
        if (!isset($this->_compatibleToners))
        {
            if ($this->isCompatible())
            {
                $this->_compatibleToners = array();
            }
            else
            {
                $this->_compatibleToners = Proposalgen_Model_Mapper_Toner::getInstance()->findCompatibleToners($this->id, $clientId, $dealerId);
            }
        }

        return $this->_compatibleToners;
    }

    /**
     * Finds other OEM toners that can be used in place of this one.
     *
     * @param null $clientId
     * @param null $dealerId
     *
     * @return Proposalgen_Model_Toner[]
     */
    public function getOemToners ($clientId = null, $dealerId = null)
    {
        if (!isset($this->_oemToners))
        {
            if ($this->isCompatible())
            {
                $this->_oemToners = Proposalgen_Model_Mapper_Toner::getInstance()->findOemToners($this->id, $clientId, $dealerId);
            }
            else
            {
                $this->_oemToners = array();
            }
        }

        return $this->_oemToners;
    }
}