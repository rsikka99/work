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
    public $partTypeId;

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
     * @var string
     */
    public $dealerSku;

    /**
     * @var Proposalgen_Model_PartType
     */
    protected $_partType;

    /**
     * @var Proposalgen_Model_Manufacturer
     */
    protected $_manufacturer;

    /**
     * @var Proposalgen_Model_TonerColor
     */
    protected $_tonerColor;

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

        if (isset($params->partTypeId) && !is_null($params->partTypeId))
        {
            $this->partTypeId = $params->partTypeId;
        }

        if (isset($params->manufacturerId) && !is_null($params->manufacturerId))
        {
            $this->manufacturerId = $params->manufacturerId;
        }

        if (isset($params->tonerColorId) && !is_null($params->tonerColorId))
        {
            $this->tonerColorId = $params->tonerColorId;
        }

        if (isset($params->dealerSku) && !is_null($params->dealerSku))
        {
            $this->dealerSku = $params->dealerSku;
        }

        if (isset($params->calculatedCost) && !is_null($params->calculatedCost))
        {
            $this->calculatedCost = $params->calculatedCost;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"             => $this->id,
            "sku"            => $this->sku,
            "cost"           => $this->cost,
            "yield"          => $this->yield,
            "partTypeId"     => $this->partTypeId,
            "manufacturerId" => $this->manufacturerId,
            "tonerColorId"   => $this->tonerColorId,
        );
    }

    /**
     * Gets the part type
     *
     * @return Proposalgen_Model_PartType
     */
    public function getPartType ()
    {
        if (!isset($this->_partType))
        {
            $partTypeMapper  = Proposalgen_Model_Mapper_PartType::getInstance();
            $this->_partType = $partTypeMapper->find($this->partTypeId);
        }

        return $this->_partType;
    }

    /**
     * Sets the part type
     *
     * @param Proposalgen_Model_PartType $PartType
     *
     * @return \Proposalgen_Model_Toner
     */
    public function setPartType ($PartType)
    {
        $this->_partType = $PartType;

        return $this;
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
            $tonerColorMapper  = Proposalgen_Model_Mapper_TonerColor::getInstance();
            $this->_tonerColor = $tonerColorMapper->find($this->tonerColorId);
        }

        return $this->_tonerColor;
    }

    /**
     * @param Proposalgen_Model_TonerColor $TonerColor
     *
     * @return \Proposalgen_Model_Toner
     */
    public function setTonerColor ($TonerColor)
    {
        $this->_tonerColor = $TonerColor;

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
             * Cost per page is calculated by dividing the price by the yield. When providing a new coverage we need to
             * divide the manufacturers coverage by our coverage in order to arrive to the right number.
             */
            $monochromeCostPerPage = 0;
            $colorCostPerPage      = 0;

            switch ($this->getTonerColor()->tonerColorId)
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
}