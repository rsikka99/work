<?php
class Proposalgen_Model_MasterDevice extends My_Model_Abstract
{
    /*
     * The different device types
     */
    const DEVICETYPE_MONO      = 0;
    const DEVICETYPE_MONO_MFP  = 1;
    const DEVICETYPE_COLOR     = 2;
    const DEVICETYPE_COLOR_MFP = 3;

    private static $ReportMargin;
    private static $PricingConfig;
    private static $GrossMarginPricingConfig;

    /*
     * Database fields
     */

    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $manufacturerId;

    /**
     * @var string
     */
    public $modelName;

    /**
     * @var int
     */
    public $tonerConfigId;

    /**
     * @var bool
     */
    public $isCopier;

    /**
     * @var bool
     */
    public $isFax;

    /**
     * @var bool
     */
    public $isScanner;

    /**
     * @var bool
     */
    public $isDuplex;

    /**
     * @var bool
     */
    public $isReplacementDevice;

    /**
     * @var int
     */
    public $wattsPowerNormal;

    /**
     * @var int
     */
    public $wattsPowerIdle;

    /**
     * @var float
     */
    public $cost;

    /**
     * @var float
     */
    public $serviceCostPerPage;

    /**
     * @var string
     */
    public $launchDate;

    /**
     * @var string
     */
    public $dateCreated;

    /**
     * @var int
     */
    public $dutyCycle;

    /**
     * @var int
     */
    public $ppmBlack;

    /**
     * @var int
     */
    public $ppmColor;

    /**
     * @var bool
     */
    public $isLeased;

    /**
     * @var int
     */
    public $leasedTonerYield;

    /**
     * @var bool
     */
    public $reportsTonerLevels;

    /*
     * Related Objects
     */
    protected $_toners;
    protected $_manufacturer;
    protected $_tonerConfig;

    /*
     * calculated values and other things
     */
    public $adminCostPerPage;
    protected $_costPerPage;
    protected $_usingIncompleteBlackTonerData;
    protected $_usingIncompleteColorTonerData;
    protected $_maximumMonthlyPageVolume;
    protected $_hasValidMonoGrossMarginToners;
    protected $_hasValidColorGrossMarginToners;
    protected $_tonersForAssessment;
    protected $_tonersForGrossMargin;
    protected $_requiredTonerColors;


    /**
     * Whether or not overrides have been processed
     *
     * @var boolean
     */
    protected $_overridesProcessed;

    /**
     * @param Proposalgen_Model_Report $report
     */
    public function processOverrides ($report)
    {
        if (!$this->_overridesProcessed)
        {
            /*
             * Only apply overrides if we have a master device id. If this is an unknown device it won't have one.
             */
            if ($this->id > 0)
            {
                $userDeviceOverrideMapper = Proposalgen_Model_Mapper_UserDeviceOverride::getInstance();
                /*
                 * Check for a user override
                 */
                $deviceOverride = $userDeviceOverrideMapper->findOverrideForMasterDevice($report->userId, $this->id);

                /*
                 * Check to see if we found an override.
                 */
                if ($deviceOverride)
                {
                    $this->cost = $deviceOverride->overrideDevicePrice;
                }
            }

            // Apply Report Margin to the device price
            $this->cost = $this->cost / $report->getReportSettings()->assessmentReportMargin;

            // Handle Toners
            // Toner Overrides + Margin
            foreach ($this->getToners() as $tonersByPartType)
            {
                foreach ($tonersByPartType as $tonersByColor)
                {
                    /* @var $toner Proposalgen_Model_Toner */
                    foreach ($tonersByColor as $toner)
                    {
                        if (!in_array($toner->sku, Proposalgen_Model_DeviceInstance::$uniqueTonerArray))
                        {
                            Proposalgen_Model_DeviceInstance::$uniqueTonerArray [] = $toner->sku;
                        }

                        // Process the overrides
                        $toner->processOverrides($report);
                    }
                }
            } // End of toners loop

            // Service Cost Per Page Cost
            if ($this->serviceCostPerPage <= 0)
            {
                $this->serviceCostPerPage = $report->getReportSettings()->serviceCostPerPage;
            }

            // Admin Charge
            $this->adminCostPerPage = $report->getReportSettings()->adminCostPerPage;

            $this->_overridesProcessed = true;
        }
    }

    /**
     * The maximum monthly page volume is calculated using the smallest toner yield
     * given the current pricing configuration
     * SPECIAL: Leased devices have a yield set, so we use that
     *
     * @return int $MaximumMonthlyPageVolume
     */
    public function getMaximumMonthlyPageVolume ()
    {
        if (!isset($this->_maximumMonthlyPageVolume))
        {
            $smallestYield = null;
            if ($this->isLeased)
            {
                $smallestYield = $this->leasedTonerYield;
            }
            else
            {
                $requiredToners = Proposalgen_Model_TonerConfig::getRequiredTonersForTonerConfig($this->tonerConfigId);
                foreach ($requiredToners as $tonerColor)
                {
                    $toner = $this->getCheapestToner($tonerColor, self::$PricingConfig);
                    if ($toner instanceof Proposalgen_Model_Toner)
                    {
                        if ($toner->yield < $smallestYield || is_null($smallestYield))
                        {
                            $smallestYield = $toner->yield;
                        }
                    }
                }
            }
            $this->_maximumMonthlyPageVolume = $smallestYield;
        }

        return $this->_maximumMonthlyPageVolume;
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

        if (isset($params->id) && !is_null($params->id))
        {
            $this->id = $params->id;
        }

        if (isset($params->manufacturerId) && !is_null($params->manufacturerId))
        {
            $this->manufacturerId = $params->manufacturerId;
        }

        if (isset($params->modelName) && !is_null($params->modelName))
        {
            $this->modelName = $params->modelName;
        }

        if (isset($params->tonerConfigId) && !is_null($params->tonerConfigId))
        {
            $this->tonerConfigId = $params->tonerConfigId;
        }

        if (isset($params->isCopier) && !is_null($params->isCopier))
        {
            $this->isCopier = $params->isCopier;
        }

        if (isset($params->isFax) && !is_null($params->isFax))
        {
            $this->isFax = $params->isFax;
        }

        if (isset($params->isScanner) && !is_null($params->isScanner))
        {
            $this->isScanner = $params->isScanner;
        }

        if (isset($params->isDuplex) && !is_null($params->isDuplex))
        {
            $this->isDuplex = $params->isDuplex;
        }

        if (isset($params->isReplacementDevice) && !is_null($params->isReplacementDevice))
        {
            $this->isReplacementDevice = $params->isReplacementDevice;
        }

        if (isset($params->wattsPowerNormal) && !is_null($params->wattsPowerNormal))
        {
            $this->wattsPowerNormal = $params->wattsPowerNormal;
        }

        if (isset($params->wattsPowerIdle) && !is_null($params->wattsPowerIdle))
        {
            $this->wattsPowerIdle = $params->wattsPowerIdle;
        }

        if (isset($params->cost) && !is_null($params->cost))
        {
            $this->cost = $params->cost;
        }

        if (isset($params->serviceCostPerPage) && !is_null($params->serviceCostPerPage))
        {
            $this->serviceCostPerPage = $params->serviceCostPerPage;
        }

        if (isset($params->launchDate) && !is_null($params->launchDate))
        {
            $this->launchDate = $params->launchDate;
        }

        if (isset($params->dateCreated) && !is_null($params->dateCreated))
        {
            $this->dateCreated = $params->dateCreated;
        }

        if (isset($params->dutyCycle) && !is_null($params->dutyCycle))
        {
            $this->dutyCycle = $params->dutyCycle;
        }

        if (isset($params->ppmBlack) && !is_null($params->ppmBlack))
        {
            $this->ppmBlack = $params->ppmBlack;
        }

        if (isset($params->ppmColor) && !is_null($params->ppmColor))
        {
            $this->ppmColor = $params->ppmColor;
        }

        if (isset($params->isLeased) && !is_null($params->isLeased))
        {
            $this->isLeased = $params->isLeased;
        }

        if (isset($params->leasedTonerYield) && !is_null($params->leasedTonerYield))
        {
            $this->leasedTonerYield = $params->leasedTonerYield;
        }
        if (isset($params->reportsTonerLevels) && !is_null($params->reportsTonerLevels))
        {
            $this->reportsTonerLevels = $params->reportsTonerLevels;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"                  => $this->id,
            "manufacturerId"      => $this->manufacturerId,
            "modelName"           => $this->modelName,
            "tonerConfigId"       => $this->tonerConfigId,
            "isCopier"            => $this->isCopier,
            "isFax"               => $this->isFax,
            "isScanner"           => $this->isScanner,
            "isDuplex"            => $this->isDuplex,
            "isReplacementDevice" => $this->isReplacementDevice,
            "wattsPowerNormal"    => $this->wattsPowerNormal,
            "wattsPowerIdle"      => $this->wattsPowerIdle,
            "cost"                => $this->cost,
            "serviceCostPerPage"  => $this->serviceCostPerPage,
            "launchDate"          => $this->launchDate,
            "dateCreated"         => $this->dateCreated,
            "dutyCycle"           => $this->dutyCycle,
            "ppmBlack"            => $this->ppmBlack,
            "ppmColor"            => $this->ppmColor,
            "isLeased"            => $this->isLeased,
            "leasedTonerYield"    => $this->leasedTonerYield,
            "reportsTonerLevels"  => $this->reportsTonerLevels,
        );
    }

    /**
     * @return Proposalgen_Model_PricingConfig
     */
    public static function getPricingConfig ()
    {
        if (!isset(Proposalgen_Model_MasterDevice::$PricingConfig))
        {

            Proposalgen_Model_MasterDevice::$PricingConfig = null;
        }

        return Proposalgen_Model_MasterDevice::$PricingConfig;
    }

    /**
     * @param Proposalgen_Model_PricingConfig Proposalgen_Model_PricingConfig $PricingConfig
     */
    public static function setPricingConfig ($PricingConfig)
    {
        Proposalgen_Model_MasterDevice::$PricingConfig = $PricingConfig;
    }

    /**
     * @return float
     */
    public static function getReportMargin ()
    {
        if (!isset(Proposalgen_Model_MasterDevice::$ReportMargin))
        {
            Proposalgen_Model_MasterDevice::$ReportMargin = 1;
        }

        return Proposalgen_Model_MasterDevice::$ReportMargin;
    }

    /**
     * @param float $ReportMargin
     */
    public static function setReportMargin ($ReportMargin)
    {
        Proposalgen_Model_MasterDevice::$ReportMargin = $ReportMargin;
    }

    /**
     * Gets the cheapest toner from a group of the same color.
     * Can specify a preferred part type to get.
     * Will return a default toner value if it does not find an appropriate toner
     *
     * @param int                             $tonerColor (Constant value in Proposalgen_Model_TonerColor)
     * @param Proposalgen_Model_PricingConfig $pricingConfig
     *
     * @return Proposalgen_Model_Toner
     */
    public function getCheapestToner ($tonerColor, $pricingConfig)
    {
        $PricingConfigId   = $pricingConfig->pricingConfigId;
        $preferredPartType = null;
        if (isset($PricingConfigId))
        {
            if ($tonerColor == Proposalgen_Model_TonerColor::BLACK)
            {
                $preferredPartType = $pricingConfig->getMonoTonerPartType();
            }
            else
            {
                $preferredPartType = $pricingConfig->getColorTonerPartType();
            }
        }
        $cheapestToner    = null;
        $tonersByPartType = $this->getToners(); // Grab this devices toners


        // If we have a preferred part type and the device has toners of that type
        if (isset($preferredPartType) &&
            is_array($tonersByPartType) &&
            array_key_exists($preferredPartType->partTypeId, $tonersByPartType) &&
            is_array($tonersByPartType [$preferredPartType->partTypeId]) &&
            array_key_exists($tonerColor, $tonersByPartType [$preferredPartType->partTypeId])
        )
        {
            // Figure out which is the cheapest black toner
            /* @var $toner Proposalgen_Model_Toner */
            foreach ($tonersByPartType [$preferredPartType->partTypeId] [$tonerColor] as $toner)
            {
                if ($cheapestToner instanceof Proposalgen_Model_Toner)
                {

                    // Compare Toner Ranks to figure out which is the cheapest
                    if ($toner->getCostPerPage()->Rank < $cheapestToner->getCostPerPage()->Rank)
                    {
                        $cheapestToner = $toner;
                    }
                }
                else
                {
                    $cheapestToner = $toner;
                }
            }
        }
        else
        {
            // Since we don't have a preferred toner type, check all the toners of $tonerColor
            /* @var $tonersByColor Proposalgen_Model_Toner[] */
            foreach ($tonersByPartType as $tonersByColor)
            {
                if (array_key_exists($tonerColor, $tonersByColor))
                {
                    /* @var $toner Proposalgen_Model_Toner */
                    foreach ($tonersByColor [$tonerColor] as $toner)
                    {
                        // Compare Toner Ranks to figure out which is the cheapest
                        if ($cheapestToner instanceof Proposalgen_Model_Toner)
                        {
                            if ($toner->getCostPerPage()->Rank < $cheapestToner->getCostPerPage()->Rank)
                            {
                                $cheapestToner = $toner;
                            }
                        }
                        else
                        {
                            $cheapestToner = $toner;
                        }
                    }
                }
            }
        }

        // If we don't have a toner, return false.
        if (!isset($cheapestToner))
        {
            return false;
        }

        return $cheapestToner;
    }

    /**
     * Calculates the cost per page for a device based on a pricing config.
     * Once calculated if you pass a new pricing config, it will recalculate the value
     *
     * @return stdClass
     */
    public function getCostPerPage ()
    {
        if (!isset($this->_costPerPage))
        {
            $costPerPage = new stdClass();

            $ReportMargin = self::getReportMargin();

            $costPerPage->Estimated = new stdClass();
            $costPerPage->Actual    = new stdClass();

            $costPerPage->Actual->Raw                = new stdClass();
            $costPerPage->Actual->Raw->BlackAndWhite = 0;
            $costPerPage->Actual->Raw->Color         = 0;

            // Base CPP
            $costPerPage->Actual->Base                   = new stdClass();
            $costPerPage->Estimated->Base                = new stdClass();
            $costPerPage->Actual->Base->BlackAndWhite    = 0;
            $costPerPage->Actual->Base->Color            = 0;
            $costPerPage->Estimated->Base->BlackAndWhite = 0;
            $costPerPage->Estimated->Base->Color         = 0;

            // Base + Margin
            $costPerPage->Actual->BasePlusMargin                   = new stdClass();
            $costPerPage->Estimated->BasePlusMargin                = new stdClass();
            $costPerPage->Actual->BasePlusMargin->BlackAndWhite    = 0;
            $costPerPage->Actual->BasePlusMargin->Color            = 0;
            $costPerPage->Estimated->BasePlusMargin->BlackAndWhite = 0;
            $costPerPage->Estimated->BasePlusMargin->Color         = 0;

            // Base Plus Service and Admin CPP
            $costPerPage->Actual->BasePlusService                   = new stdClass();
            $costPerPage->Estimated->BasePlusService                = new stdClass();
            $costPerPage->Actual->BasePlusService->BlackAndWhite    = 0;
            $costPerPage->Actual->BasePlusService->Color            = 0;
            $costPerPage->Estimated->BasePlusService->BlackAndWhite = 0;
            $costPerPage->Estimated->BasePlusService->Color         = 0;

            // Base Plus Service and Admin CPP and Margin
            $costPerPage->Actual->BasePlusServiceAndMargin                   = new stdClass();
            $costPerPage->Estimated->BasePlusServiceAndMargin                = new stdClass();
            $costPerPage->Actual->BasePlusServiceAndMargin->BlackAndWhite    = 0;
            $costPerPage->Actual->BasePlusServiceAndMargin->Color            = 0;
            $costPerPage->Estimated->BasePlusServiceAndMargin->BlackAndWhite = 0;
            $costPerPage->Estimated->BasePlusServiceAndMargin->Color         = 0;

            $ServicePlusAdminCPP                    = $this->serviceCostPerPage + $this->adminCostPerPage;
            $ServiceCPP                             = $this->serviceCostPerPage;
            $costPerPage->Actual->Raw->ServiceCPP   = $this->serviceCostPerPage;
            $costPerPage->Actual->Raw->AdminCPP     = $this->adminCostPerPage;
            $costPerPage->Actual->Raw->ReportMargin = $ReportMargin;

            /* Estimated Cost Per Page */
            $tempTonerList = array();
            $tonerColors   = Proposalgen_Model_TonerConfig::getRequiredTonersForTonerConfig($this->tonerConfigId);
            foreach ($tonerColors as $tonerColor)
            {
                $tempTonerList [$tonerColor] = $this->getCheapestToner($tonerColor, self::getPricingConfig());
            }

            // Black Base CPP = Toner Cost / Toner Yield
            // Color CPP = Toner Cost / Toner Yield
            /* @var $toner Proposalgen_Model_Toner */
            foreach ($tempTonerList as $toner)
            {
                // If we didn't get a toner, skip it.
                if ($toner)
                {
                    $tonerCPP = $toner->getCostPerPage();
                    $costPerPage->Estimated->Base->BlackAndWhite += $tonerCPP->Estimated->BlackAndWhite;
                    $costPerPage->Estimated->Base->Color += $tonerCPP->Estimated->BlackAndWhite;
                    $costPerPage->Estimated->Base->Color += $tonerCPP->Estimated->Color;
                }
            }

            /* Actual Cost Per Page */
            $tempTonerList = array();
            foreach ($tonerColors as $tonerColor)
            {
                $tempTonerList [$tonerColor] = $this->getCheapestToner($tonerColor, self::getGrossMarginPricingConfig());
            }

            // Black Base CPP = Toner Cost / Toner Yield
            // Color CPP = Toner Cost / Toner Yield
            /* @var $toner Proposalgen_Model_Toner */
            foreach ($tempTonerList as $toner)
            {
                // If we didn't get a toner, skip it.
                if ($toner)
                {
                    $tonerCPP = $toner->getCostPerPage();
                    $costPerPage->Actual->Base->BlackAndWhite += $tonerCPP->Actual->BlackAndWhite;
                    $costPerPage->Actual->Base->Color += $tonerCPP->Actual->BlackAndWhite;
                    $costPerPage->Actual->Base->Color += $tonerCPP->Actual->Color;
                    $costPerPage->Actual->Raw->BlackAndWhite += $tonerCPP->Raw->BlackAndWhite;
                    $costPerPage->Actual->Raw->Color += $tonerCPP->Raw->BlackAndWhite;
                    $costPerPage->Actual->Raw->Color += $tonerCPP->Raw->Color;
                }
            }

            // Apply a margin to the base cost per page
            $costPerPage->Actual->BasePlusMargin->BlackAndWhite    = $costPerPage->Actual->Base->BlackAndWhite / $ReportMargin;
            $costPerPage->Actual->BasePlusMargin->Color            = $costPerPage->Actual->Base->Color / $ReportMargin;
            $costPerPage->Estimated->BasePlusMargin->BlackAndWhite = $costPerPage->Estimated->Base->BlackAndWhite / $ReportMargin;
            $costPerPage->Estimated->BasePlusMargin->Color         = $costPerPage->Estimated->Base->Color / $ReportMargin;

            // Add the device's costs to the base cost per page
            $costPerPage->Actual->BasePlusService->BlackAndWhite += $costPerPage->Actual->Base->BlackAndWhite + $ServicePlusAdminCPP;
            $costPerPage->Actual->BasePlusService->Color += $costPerPage->Actual->Base->Color + $ServicePlusAdminCPP;
            $costPerPage->Estimated->BasePlusService->BlackAndWhite += $costPerPage->Estimated->Base->BlackAndWhite + $ServiceCPP;
            $costPerPage->Estimated->BasePlusService->Color += $costPerPage->Estimated->Base->Color + $ServiceCPP;

            // Apply the report margin to the adjusted cost per page
            $costPerPage->Actual->BasePlusServiceAndMargin->BlackAndWhite    = $costPerPage->Actual->BasePlusService->BlackAndWhite / $ReportMargin;
            $costPerPage->Actual->BasePlusServiceAndMargin->Color            = $costPerPage->Actual->BasePlusService->Color / $ReportMargin;
            $costPerPage->Estimated->BasePlusServiceAndMargin->BlackAndWhite = ($costPerPage->Estimated->Base->BlackAndWhite / $ReportMargin) + $ServiceCPP;
            $costPerPage->Estimated->BasePlusServiceAndMargin->Color         = ($costPerPage->Estimated->Base->Color / $ReportMargin) + $ServiceCPP;

            $this->_costPerPage = $costPerPage;
        }

        return $this->_costPerPage;
    }

    /**
     * @param stdClass $CostPerPage
     *
     * @return Proposalgen_Model_MasterDevice
     */
    public function setCostPerPage ($CostPerPage)
    {
        $this->_costPerPage = $CostPerPage;

        return $this;
    }

    /**
     * @return Proposalgen_Model_Manufacturer
     */
    public function getManufacturer ()
    {
        if (!isset($this->_manufacturer))
        {
            $manufacturerMapper  = Proposalgen_Model_Mapper_Manufacturer::getInstance();
            $this->_manufacturer = $manufacturerMapper->find($this->manufacturerId);
        }

        return $this->_manufacturer;
    }

    /**
     * @param Proposalgen_Model_Manufacturer $Manufacturer
     *
     * @return Proposalgen_Model_MasterDevice
     */
    public function setManufacturer ($Manufacturer)
    {
        $this->_manufacturer = $Manufacturer;

        return $this;
    }

    /**
     * Gets the toner config object for this device
     *
     * @return Proposalgen_Model_TonerConfig
     */
    public function getTonerConfig ()
    {
        if (!isset($this->_tonerConfig))
        {
            $tonerConfigMapper  = Proposalgen_Model_Mapper_TonerConfig::getInstance();
            $this->_tonerConfig = $tonerConfigMapper->find($this->tonerConfigId);
        }

        return $this->_tonerConfig;
    }

    /**
     * Sets the toner config object for this device
     *
     * @param Proposalgen_Model_TonerConfig $TonerConfig
     *
     * @return \Proposalgen_Model_MasterDevice
     */
    public function setTonerConfig ($TonerConfig)
    {
        $this->_tonerConfig = $TonerConfig;

        return $this;
    }

    /**
     * @return Proposalgen_Model_Toner[]
     */
    public function getToners ()
    {
        if (!isset($this->_toners))
        {
            // Get the toners for the device
            $this->_toners = Proposalgen_Model_Mapper_Toner::getInstance()->getTonersForDevice($this->id);
        }

        return $this->_toners;
    }

    /**
     * @param Proposalgen_Model_Toner[][][] $Toners
     *
     * @return Proposalgen_Model_MasterDevice
     */
    public function setToners ($Toners)
    {
        $this->_toners = $Toners;

        return $this;
    }

    /**
     * @param $tonerColor
     * @param $incomplete
     *
     * @return Proposalgen_Model_MasterDevice
     */
    public function setUsingIncompleteTonerData ($tonerColor, $incomplete)
    {
        switch ($tonerColor)
        {
            case Proposalgen_Model_TonerColor::FOUR_COLOR :
                $this->_usingIncompleteBlackTonerData = $incomplete;
                $this->_usingIncompleteColorTonerData = $incomplete;
                break;
            case Proposalgen_Model_TonerColor::THREE_COLOR :
            case Proposalgen_Model_TonerColor::CYAN :
            case Proposalgen_Model_TonerColor::MAGENTA :
            case Proposalgen_Model_TonerColor::YELLOW :
                $this->_usingIncompleteColorTonerData = $incomplete;
                break;
            case Proposalgen_Model_TonerColor::BLACK :
                $this->_usingIncompleteBlackTonerData = $incomplete;
                break;
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function isUsingIncompleteBlackTonerData ()
    {
        return ($this->_usingIncompleteBlackTonerData);
    }

    /**
     * @return bool
     */
    public function isUsingIncompleteColorTonerData ()
    {
        return ($this->_usingIncompleteColorTonerData);
    }


    /**
     * @return Proposalgen_Model_PricingConfig
     */
    public static function getGrossMarginPricingConfig ()
    {
        if (!isset(Proposalgen_Model_MasterDevice::$GrossMarginPricingConfig))
        {

            Proposalgen_Model_MasterDevice::$GrossMarginPricingConfig = null;
        }

        return Proposalgen_Model_MasterDevice::$GrossMarginPricingConfig;
    }

    /**
     * @param Proposalgen_Model_PricingConfig $GrossMarginPricingConfig
     */
    public static function setGrossMarginPricingConfig ($GrossMarginPricingConfig)
    {
        Proposalgen_Model_MasterDevice::$GrossMarginPricingConfig = $GrossMarginPricingConfig;
    }

    /**
     * @return bool
     */
    public function getHasValidMonoGrossMarginToners ()
    {
        if (!isset($this->_hasValidMonoGrossMarginToners))
        {
            $usesAllValidToners = true;
            $requiredToners     = Proposalgen_Model_TonerConfig::getRequiredTonersForTonerConfig($this->tonerConfigId);
            foreach ($requiredToners as $tonerColor)
            {
                $toner = $this->getCheapestToner($tonerColor, self::getGrossMarginPricingConfig());

                if ($tonerColor == Proposalgen_Model_TonerColor::BLACK && $toner->partTypeId != self::getGrossMarginPricingConfig()->monoTonerPartTypeId)
                {
                    $usesAllValidToners = false;
                    break;
                }
                else if ($toner->partTypeId != self::getGrossMarginPricingConfig()->colorTonerPartTypeId)
                {
                    //$usesAllValidToners = false;
                    //break;
                }
            }

            $this->_hasValidMonoGrossMarginToners = $usesAllValidToners;
        }

        return $this->_hasValidMonoGrossMarginToners;
    }

    /**
     * @param bool $HasValidMonoGrossMarginToners
     *
     * @return Proposalgen_Model_MasterDevice
     */
    public function setHasValidMonoGrossMarginToners ($HasValidMonoGrossMarginToners)
    {
        $this->_hasValidMonoGrossMarginToners = $HasValidMonoGrossMarginToners;

        return $this;
    }

    /**
     * @return bool
     */
    public function getHasValidColorGrossMarginToners ()
    {
        if (!isset($this->_hasValidColorGrossMarginToners))
        {
            $usesAllValidToners = true;
            $requiredToners     = Proposalgen_Model_TonerConfig::getRequiredTonersForTonerConfig($this->tonerConfigId);
            foreach ($requiredToners as $tonerColor)
            {
                $toner = $this->getCheapestToner($tonerColor, self::getGrossMarginPricingConfig());

                if ($tonerColor == Proposalgen_Model_TonerColor::BLACK && $toner->partTypeId != self::getGrossMarginPricingConfig()->monoTonerPartTypeId)
                {
                    //$usesAllValidToners = false;
                    //break;
                }
                else if ($toner->partTypeId != self::getGrossMarginPricingConfig()->colorTonerPartTypeId)
                {
                    $usesAllValidToners = false;
                    break;
                }
            }

            $this->_hasValidColorGrossMarginToners = $usesAllValidToners;
        }

        return $this->_hasValidColorGrossMarginToners;
    }

    /**
     * @param bool $HasValidColorGrossMarginToners
     *
     * @return Proposalgen_Model_MasterDevice
     */
    public function setHasValidColorGrossMarginToners ($HasValidColorGrossMarginToners)
    {
        $this->_hasValidColorGrossMarginToners = $HasValidColorGrossMarginToners;

        return $this;
    }

    /**
     * @return Proposalgen_Model_Toner[]
     */
    public function getTonersForAssessment ()
    {
        if (!isset($this->_tonersForAssessment))
        {
            $toners = array();
            foreach ($this->getRequiredTonerColors() as $tonerColor)
            {
                $toner = $this->getCheapestToner($tonerColor, self::getPricingConfig());
                if ($toner instanceof Proposalgen_Model_Toner)
                {
                    $toners [$tonerColor] = $toner;
                }
            }
            $this->_tonersForAssessment = $toners;
        }

        return $this->_tonersForAssessment;
    }

    /**
     * @return Proposalgen_Model_Toner[]
     */
    public function getTonersForGrossMargin ()
    {
        if (!isset($this->_tonersForGrossMargin))
        {
            $toners = array();
            foreach ($this->getRequiredTonerColors() as $tonerColor)
            {
                $toners [$tonerColor] = $this->getCheapestToner($tonerColor, self::getGrossMarginPricingConfig());
            }
            $this->_tonersForGrossMargin = $toners;
        }

        return $this->_tonersForGrossMargin;
    }

    /**
     * @param Proposalgen_Model_Toner[] $TonersForAssessment
     *
     * @return Proposalgen_Model_MasterDevice
     */
    public function setTonersForAssessment ($TonersForAssessment)
    {
        $this->_tonersForAssessment = $TonersForAssessment;

        return $this;
    }

    /**
     * @param Proposalgen_Model_Toner[] $TonersForGrossMargin
     *
     * @return Proposalgen_Model_MasterDevice
     */
    public function setTonersForGrossMargin ($TonersForGrossMargin)
    {
        $this->_tonersForGrossMargin = $TonersForGrossMargin;

        return $this;
    }

    /**
     * @return array
     */
    public function getRequiredTonerColors ()
    {
        if (!isset($this->_requiredTonerColors))
        {
            $this->_requiredTonerColors = Proposalgen_Model_TonerConfig::getRequiredTonersForTonerConfig($this->tonerConfigId);
        }

        return $this->_requiredTonerColors;
    }

    /**
     * @param $RequiredTonerColors
     *
     * @return Proposalgen_Model_MasterDevice
     */
    public function setRequiredTonerColors ($RequiredTonerColors)
    {
        $this->_requiredTonerColors = $RequiredTonerColors;

        return $this;
    }

    /**
     * Gets the manufacturer display name + printer model
     *
     * @return string
     */
    public function getFullDeviceName ()
    {
        return "{$this->getManufacturer()->displayname} {$this->modelName}";
    }

    /**
     * Returns whether or not a device is color.
     *
     * @return boolean
     */
    public function isColor ()
    {
        return ((int)$this->tonerConfigId !== Proposalgen_Model_TonerConfig::BLACK_ONLY);
    }

    /**
     * Calculates the cost per page for a master device.
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

        $cacheKey = $costPerPageSetting->createCacheKey();
        if (!array_key_exists($cacheKey, $this->_cachedCostPerPage))
        {
            // Initialize the cpp object
            $costPerPage                        = new Proposalgen_Model_CostPerPage();
            $costPerPage->monochromeCostPerPage = 0;
            $costPerPage->colorCostPerPage      = 0;

            /* @var $toner Proposalgen_Model_Toner */
            foreach ($this->getCheapestTonerSet($costPerPageSetting->pricingConfiguration) as $toner)
            {
                if ($toner)
                {
                    $tonerCostPerPage = $toner->calculateCostPerPage($costPerPageSetting);

                    $costPerPage->add($tonerCostPerPage);
                }
            }

            $this->_cachedCostPerPage [$cacheKey] = $costPerPage;
        }

        return $this->_cachedCostPerPage [$cacheKey];
    }

    /**
     * Gets the cheapest tonerset for a pricing configuration
     *
     * @param Proposalgen_Model_PricingConfig $pricingConfiguration
     *            The pricing configuration to use
     *
     * @return Proposalgen_Model_Toner[]
     */
    public function getCheapestTonerSet (Proposalgen_Model_PricingConfig $pricingConfiguration)
    {
        // Make sure our array is initialized
        if (!isset($this->_cachedCheapestTonerSets))
        {
            $this->_cachedCheapestTonerSets = array();
        }

        $cacheKey = "pricingConfiguration_{$pricingConfiguration->pricingConfigId}";
        if (!array_key_exists($cacheKey, $this->_cachedCostPerPage))
        {
            $tonerColors = $this->getRequiredTonerColors();
            $toners      = array();
            foreach ($tonerColors as $tonerColor)
            {
                $toners [] = $this->getCheapestToner($tonerColor, $pricingConfiguration);
            }
            $this->_cachedCheapestTonerSets [$cacheKey] = $toners;
        }

        return $this->_cachedCheapestTonerSets [$cacheKey];
    }

    /**
     * Gets the device type
     *
     * @return number @see Application_Model_MasterDevice for constants
     */
    public function getDeviceType ()
    {
        if (!isset($this->_deviceType))
        {
            if ($this->tonerConfigId === Proposalgen_Model_TonerConfig::BLACK_ONLY)
            {
                if ($this->isCopier)
                {
                    $this->_deviceType = self::DEVICETYPE_MONO_MFP;
                }
                else
                {

                    $this->_deviceType = self::DEVICETYPE_MONO;
                }
            }
            else
            {
                if ($this->isCopier)
                {
                    $this->_deviceType = self::DEVICETYPE_COLOR_MFP;
                }
                else
                {
                    $this->_deviceType = self::DEVICETYPE_COLOR;
                }
            }
        }

        return $this->_deviceType;
    }

    /**
     * The age is the difference between the launch date and today in years.
     * The only special thing here is that the launch date is changed -1 year if
     * it
     * is at least 1 year old. This is to compensate for the fact that most
     * printers
     * aren't deployed in a fleet as soon as they were launched by their
     * manufacturers
     *
     * @return int Calculated device age in years
     */
    public function getAge ()
    {
        if (!isset($this->Age))
        {
            // Get the time difference in seconds
            $launchDate          = time() - strtotime($this->launchDate);
            $correctedLaunchDate = ($launchDate > 31556926) ? ($launchDate - 31556926) : $launchDate;
            $this->Age           = floor($correctedLaunchDate / 31556926);
            if ($this->Age == 0)
            {
                $this->Age = 1;
            }
        }

        return $this->Age;
    }
}