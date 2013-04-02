<?php
class Proposalgen_Model_Toner extends My_Model_Abstract
{
    const MANUFACTURER_ASSUMED_COVERAGE = 0.05;
    static $ACTUAL_PAGE_COVERAGE_BLACK_AND_WHITE;
    static $ACTUAL_PAGE_COVERAGE_COLOR;
    static $ESTIMATED_PAGE_COVERAGE_BLACK_AND_WHITE;
    static $ESTIMATED_PAGE_COVERAGE_COLOR;
    static $DefaultToners;

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

    // Calculated Fields
    protected $_costPerPage;

    /**
     * Whether or not overrides have been processed
     *
     * @var bool
     */
    protected $_overridesProcessed;

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
     * Handles processing overrides for a toner
     *
     * @param Proposalgen_Model_Assessment $report
     */
    public function processOverrides ($report)
    {
        if (!$this->_overridesProcessed)
        {
            $userTonerOverrideMapper = Proposalgen_Model_Mapper_UserTonerOverride::getInstance();

            $tonerOverride = null;
            /*
             * We can never have overrides on an unknown device
             */
            if ($this->id > 0)
            {
                $tonerOverride = $userTonerOverrideMapper->findOverrideForToner(Zend_Auth::getInstance()->getIdentity()->id, $this->id);
            }
            // If we found a toner override, apply it
            if ($tonerOverride)
            {
                $this->cost = $tonerOverride->overrideTonerPrice;
            }

            $this->_overridesProcessed = true;
        }
    }


    /**
     * Deprecated Method.
     *
     * @deprecated
     * @return Proposalgen_Model_Toner[]
     * @throws BadMethodCallException
     */
    public static function getDefaultToners ()
    {
        throw new BadMethodCallException('Default toners are now deprecated since master devices require at least 1 set of toners to be defined.');
    }

    /**
     * @return stdClass
     * @throws Exception
     */
    public function getCostPerPage ()
    {
        if (!isset($this->_costPerPage))
        {
            $actualBlackCoverage    = 0;
            $actualColorCoverage    = 0;
            $estimatedBlackCoverage = 0;
            $estimatedColorCoverage = 0;
            switch ($this->getTonerColor()->tonerColorId)
            {
                case Proposalgen_Model_TonerColor::BLACK :
                    $actualBlackCoverage    = self::getACTUAL_PAGE_COVERAGE_BLACK_AND_WHITE();
                    $estimatedBlackCoverage = self::getESTIMATED_PAGE_COVERAGE_BLACK_AND_WHITE();
                    break;
                case Proposalgen_Model_TonerColor::CYAN :
                case Proposalgen_Model_TonerColor::MAGENTA :
                case Proposalgen_Model_TonerColor::YELLOW :
                    $actualColorCoverage    = self::getACTUAL_PAGE_COVERAGE_COLOR() / 4;
                    $estimatedColorCoverage = self::getESTIMATED_PAGE_COVERAGE_COLOR() / 4;
                    break;
                case Proposalgen_Model_TonerColor::THREE_COLOR :
                    $actualColorCoverage    = (self::getACTUAL_PAGE_COVERAGE_COLOR() / 4) * 3;
                    $estimatedColorCoverage = (self::getESTIMATED_PAGE_COVERAGE_COLOR() / 4) * 3;
                    break;
                case Proposalgen_Model_TonerColor::FOUR_COLOR :
                    $actualBlackCoverage    = self::getACTUAL_PAGE_COVERAGE_BLACK_AND_WHITE();
                    $estimatedBlackCoverage = self::getESTIMATED_PAGE_COVERAGE_BLACK_AND_WHITE();

                    $actualColorCoverage    = self::getACTUAL_PAGE_COVERAGE_COLOR();
                    $estimatedColorCoverage = self::getESTIMATED_PAGE_COVERAGE_COLOR();
                    break;
            }

            // tonerCost / (tonerYield * (0.05 / coverage)
            // 0.05 is EMILY_RANDOM_NUMBER
            $costPerPage                           = new stdClass();
            $costPerPage->Actual                   = new stdClass();
            $costPerPage->Actual->BlackAndWhite    = 0;
            $costPerPage->Actual->Color            = 0;
            $costPerPage->Raw                      = new stdClass();
            $costPerPage->Raw->BlackAndWhite       = 0;
            $costPerPage->Raw->Color               = 0;
            $costPerPage->Estimated                = new stdClass();
            $costPerPage->Estimated->BlackAndWhite = 0;
            $costPerPage->Estimated->Color         = 0;
            // We must have a toner yield
            if ($this->yield)
            {
                // Check to make sure we have a black coverage
                if ($actualBlackCoverage && $estimatedBlackCoverage)
                {
                    $costPerPage->Actual->BlackAndWhite    = $this->cost / ($this->yield * (self::MANUFACTURER_ASSUMED_COVERAGE / $actualBlackCoverage));
                    $costPerPage->Raw->BlackAndWhite       = $this->cost / $this->yield;
                    $costPerPage->Estimated->BlackAndWhite = $this->cost / ($this->yield * (self::MANUFACTURER_ASSUMED_COVERAGE / $estimatedBlackCoverage));
                }
                // Check to make sure we have a color coverage
                if ($actualColorCoverage && $estimatedColorCoverage)
                {
                    $costPerPage->Actual->Color    = $this->cost / ($this->yield * (self::MANUFACTURER_ASSUMED_COVERAGE / $actualColorCoverage));
                    $costPerPage->Raw->Color       = $this->cost / $this->yield;
                    $costPerPage->Estimated->Color = $this->cost / ($this->yield * (self::MANUFACTURER_ASSUMED_COVERAGE / $estimatedColorCoverage));
                }
            }
            else
            {
                throw new Exception("Toner has a yield of zero! ID:" . $this->yield);
            }

            // This is a ranking to be able to compare a toner with other toners of the same kind
            $costPerPage->Rank = $costPerPage->Estimated->BlackAndWhite + $costPerPage->Estimated->Color;

            $this->_costPerPage = $costPerPage;
        }

        return $this->_costPerPage;
    }

    /**
     * @param $CostPerPage
     *
     * @return Proposalgen_Model_Toner
     */
    public function setCostPerPage ($CostPerPage)
    {
        $this->_costPerPage = $CostPerPage;

        return $this;
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
     * @throws Exception
     * @return float
     */
    public static function getACTUAL_PAGE_COVERAGE_BLACK_AND_WHITE ()
    {
        if (!isset(Proposalgen_Model_Toner::$ACTUAL_PAGE_COVERAGE_BLACK_AND_WHITE))
        {
            throw new Exception("Actual Black And White Page Coverage not set!!");
        }

        return Proposalgen_Model_Toner::$ACTUAL_PAGE_COVERAGE_BLACK_AND_WHITE;
    }

    /**
     * @param float $ACTUAL_PAGE_COVERAGE_BLACK_AND_WHITE
     */
    public static function setACTUAL_PAGE_COVERAGE_BLACK_AND_WHITE ($ACTUAL_PAGE_COVERAGE_BLACK_AND_WHITE)
    {
        Proposalgen_Model_Toner::$ACTUAL_PAGE_COVERAGE_BLACK_AND_WHITE = $ACTUAL_PAGE_COVERAGE_BLACK_AND_WHITE;
    }

    /**
     * @throws Exception
     * @return float
     */
    public static function getACTUAL_PAGE_COVERAGE_COLOR ()
    {
        if (!isset(Proposalgen_Model_Toner::$ACTUAL_PAGE_COVERAGE_COLOR))
        {
            throw new Exception("Actual Color Page Coverage not set!!");
        }

        return Proposalgen_Model_Toner::$ACTUAL_PAGE_COVERAGE_COLOR;
    }

    /**
     * @param float $ACTUAL_PAGE_COVERAGE_COLOR
     */
    public static function setACTUAL_PAGE_COVERAGE_COLOR ($ACTUAL_PAGE_COVERAGE_COLOR)
    {
        Proposalgen_Model_Toner::$ACTUAL_PAGE_COVERAGE_COLOR = $ACTUAL_PAGE_COVERAGE_COLOR;
    }

    /**
     * @throws Exception
     * @return float
     */
    public static function getESTIMATED_PAGE_COVERAGE_BLACK_AND_WHITE ()
    {
        if (!isset(Proposalgen_Model_Toner::$ESTIMATED_PAGE_COVERAGE_BLACK_AND_WHITE))
        {
            throw new Exception("Estimated Black And White Page Coverage not set!!");
        }

        return Proposalgen_Model_Toner::$ESTIMATED_PAGE_COVERAGE_BLACK_AND_WHITE;
    }

    /**
     * @param float $ESTIMATED_PAGE_COVERAGE_BLACK_AND_WHITE
     */
    public static function setESTIMATED_PAGE_COVERAGE_BLACK_AND_WHITE ($ESTIMATED_PAGE_COVERAGE_BLACK_AND_WHITE)
    {
        Proposalgen_Model_Toner::$ESTIMATED_PAGE_COVERAGE_BLACK_AND_WHITE = $ESTIMATED_PAGE_COVERAGE_BLACK_AND_WHITE;
    }

    /**
     * @throws Exception
     * @return float
     */
    public static function getESTIMATED_PAGE_COVERAGE_COLOR ()
    {
        if (!isset(Proposalgen_Model_Toner::$ESTIMATED_PAGE_COVERAGE_COLOR))
        {
            throw new Exception("Estimated Color Page Coverage not set!!");
        }

        return Proposalgen_Model_Toner::$ESTIMATED_PAGE_COVERAGE_COLOR;
    }

    /**
     * @param float $ESTIMATED_PAGE_COVERAGE_COLOR
     */
    public static function setESTIMATED_PAGE_COVERAGE_COLOR ($ESTIMATED_PAGE_COVERAGE_COLOR)
    {
        Proposalgen_Model_Toner::$ESTIMATED_PAGE_COVERAGE_COLOR = $ESTIMATED_PAGE_COVERAGE_COLOR;
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
                    $monochromeCostPerPage = $this->cost / ($this->yield * (self::MANUFACTURER_ASSUMED_COVERAGE / $monochromeCoverage));
                    break;
                case Proposalgen_Model_TonerColor::CYAN :
                case Proposalgen_Model_TonerColor::MAGENTA :
                case Proposalgen_Model_TonerColor::YELLOW :
                    $colorCostPerPage = $this->cost / ($this->yield * (self::MANUFACTURER_ASSUMED_COVERAGE / ($colorCoverage / 4)));
                    break;
                case Proposalgen_Model_TonerColor::THREE_COLOR :
                    $colorCostPerPage = $this->cost / ($this->yield * (self::MANUFACTURER_ASSUMED_COVERAGE / ($colorCoverage / 4 * 3)));
                    break;
                case Proposalgen_Model_TonerColor::FOUR_COLOR :
                    $monochromeCostPerPage = $this->cost / ($this->yield * (self::MANUFACTURER_ASSUMED_COVERAGE / $monochromeCoverage));
                    $colorCostPerPage      = $this->cost / ($this->yield * (self::MANUFACTURER_ASSUMED_COVERAGE / $colorCoverage));
                    break;
            }

            $costPerPage->monochromeCostPerPage = $monochromeCostPerPage;
            $costPerPage->colorCostPerPage = $colorCostPerPage;

            $this->_cachedCostPerPage [$cacheKey] = $costPerPage;
        }

        return $this->_cachedCostPerPage [$cacheKey];
    }
}