<?php
/**
 * Class Proposalgen_Model_MasterDevice
 */
class Proposalgen_Model_MasterDevice extends My_Model_Abstract
{
    /*
     * The different device types
     */
    const DEVICE_TYPE_MONO      = 0;
    const DEVICE_TYPE_MONO_MFP  = 1;
    const DEVICE_TYPE_COLOR     = 2;
    const DEVICE_TYPE_COLOR_MFP = 3;

    static $TonerConfigNames = array(
        self::DEVICE_TYPE_MONO      => "Monochrome",
        self::DEVICE_TYPE_MONO_MFP  => "Monochrome Mfp",
        self::DEVICE_TYPE_COLOR     => "Color",
        self::DEVICE_TYPE_COLOR_MFP => "Color Mfp"
    );


    static $ReportLaborCostPerPage = 0;
    static $ReportPartsCostPerPage = 0;


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
    public $userId;

    /**
     * @var int
     */
    public $isSystemDevice;

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

    /**
     * @var float
     */
    public $partsCostPerPage;

    /**
     * @var float
     */
    public $laborCostPerPage;

    /**
     * @var int
     */
    public $calculatedLaborCostPerPage;

    /**
     * @var int
     */
    public $isUsingDealerLaborCostPerPage;

    /**
     * @var int
     */
    public $isUsingDeviceLaborCostPerPage;

    /**
     * @var int
     */
    public $isUsingReportLaborCostPerPage;

    /**
     * @var int
     */
    public $calculatedPartsCostPerPage;

    /**
     * @var int
     */
    public $isUsingDealerPartsCostPerPage;

    /**
     * @var int
     */
    public $isUsingDevicePartsCostPerPage;

    /**
     * @var int
     */
    public $isUsingReportPartsCostPerPag;
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
    protected $_cachedCostPerPage;
    protected $_cachedCheapestTonerVendorSet;
    protected $_usingIncompleteBlackTonerData;
    protected $_usingIncompleteColorTonerData;
    protected $_maximumMonthlyPageVolume;
    protected $_hasValidMonoGrossMarginToners;
    protected $_hasValidColorGrossMarginToners;
    protected $_tonersForAssessment;
    protected $_tonersForHealthcheck;
    protected $_tonersForGrossMargin;
    protected $_requiredTonerColors;
    protected $_age;

    protected $_dealerAttributes;

    /**
     * @return Proposalgen_Model_Dealer_Master_Device_Attribute
     */
    public function getDealerAttributes ()
    {
        if (!isset($this->_dealerAttributes))
        {

            $where                   = Proposalgen_Model_Mapper_Dealer_Master_Device_Attribute::getInstance()->getWhereId(array($this->id, Zend_Auth::getInstance()->getIdentity()->dealerId));
            $this->_dealerAttributes = Proposalgen_Model_Mapper_Dealer_Master_Device_Attribute::getInstance()->fetch($where);
        }

        return $this->_dealerAttributes;
    }

    /**
     * Whether or not overrides have been processed
     *
     * @var boolean
     */
    protected $_overridesProcessed;

    /**
     * @param $adminCostPerPage
     */
    public function processOverrides ($adminCostPerPage)
    {
        if (!$this->_overridesProcessed)
        {
            // Admin Charge
            $this->adminCostPerPage = $adminCostPerPage;

            $this->_overridesProcessed = true;
        }
    }


    /**
     * The maximum monthly page volume is calculated using the smallest toner yield
     * given the current pricing configuration
     * SPECIAL: Leased devices have a yield set, so we use that
     *
     * @param Proposalgen_Model_CostPerPageSetting $costPerPageSetting
     *
     * @return int $MaximumMonthlyPageVolume
     */
    public function getMaximumMonthlyPageVolume ($costPerPageSetting)
    {
        if (!isset($this->_maximumMonthlyPageVolume))
        {
            $this->_maximumMonthlyPageVolume = array();
        }

        $cacheKey = $costPerPageSetting->createCacheKey();

        if (!array_key_exists($cacheKey, $this->_maximumMonthlyPageVolume))
        {
            $smallestYield = null;
            if ($this->isLeased)
            {
                $smallestYield = $this->leasedTonerYield;
            }
            else
            {
                $toners = $this->getCheapestTonerSetByVendor($costPerPageSetting);
                foreach ($toners as $toner)
                {
                    if ($toner instanceof Proposalgen_Model_Toner)
                    {
                        if ($toner->yield < $smallestYield || is_null($smallestYield))
                        {
                            $smallestYield = $toner->yield;
                        }
                    }
                }
            }
            $this->_maximumMonthlyPageVolume[$cacheKey] = $smallestYield;
        }

        return $this->_maximumMonthlyPageVolume[$cacheKey];
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

        if (isset($params->userId) && !is_null($params->userId))
        {
            $this->userId = $params->userId;
        }

        if (isset($params->isSystemDevice) && !is_null($params->isSystemDevice))
        {
            $this->isSystemDevice = $params->isSystemDevice;
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

        if (isset($params->partsCostPerPage) && !is_null($params->partsCostPerPage))
        {
            $this->partsCostPerPage = $params->partsCostPerPage;
        }

        if (isset($params->laborCostPerPage) && !is_null($params->laborCostPerPage))
        {
            $this->laborCostPerPage = $params->laborCostPerPage;
        }
        if (isset($params->calculatedLaborCostPerPage) && !is_null($params->calculatedLaborCostPerPage))
        {
            $this->calculatedLaborCostPerPage = $params->calculatedLaborCostPerPage;
        }

        if (isset($params->isUsingDealerLaborCostPerPage) && !is_null($params->isUsingDealerLaborCostPerPage))
        {
            $this->isUsingDealerLaborCostPerPage = $params->isUsingDealerLaborCostPerPage;
        }

        if (isset($params->isUsingDeviceLaborCostPerPage) && !is_null($params->isUsingDeviceLaborCostPerPage))
        {
            $this->isUsingDeviceLaborCostPerPage = $params->isUsingDeviceLaborCostPerPage;
        }

        if (isset($params->isUsingReportLaborCostPerPage) && !is_null($params->isUsingReportLaborCostPerPage))
        {
            $this->isUsingReportLaborCostPerPage = $params->isUsingReportLaborCostPerPage;
        }

        if (isset($params->calculatedPartsCostPerPage) && !is_null($params->calculatedPartsCostPerPage))
        {
            $this->calculatedPartsCostPerPage = $params->calculatedPartsCostPerPage;
        }

        if (isset($params->isUsingDealerPartsCostPerPage) && !is_null($params->isUsingDealerPartsCostPerPage))
        {
            $this->isUsingDealerPartsCostPerPage = $params->isUsingDealerPartsCostPerPage;
        }

        if (isset($params->isUsingDevicePartsCostPerPage) && !is_null($params->isUsingDevicePartsCostPerPage))
        {
            $this->isUsingDevicePartsCostPerPage = $params->isUsingDevicePartsCostPerPage;
        }

        if (isset($params->isUsingReportPartsCostPerPag) && !is_null($params->isUsingReportPartsCostPerPag))
        {
            $this->isUsingReportPartsCostPerPag = $params->isUsingReportPartsCostPerPag;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"                  => $this->id,
            "userId"              => $this->userId,
            "manufacturerId"      => $this->manufacturerId,
            "modelName"           => $this->modelName,
            "tonerConfigId"       => $this->tonerConfigId,
            "isCopier"            => $this->isCopier,
            "isFax"               => $this->isFax,
            "isScanner"           => $this->isScanner,
            "isDuplex"            => $this->isDuplex,
            "isReplacementDevice" => $this->isReplacementDevice,
            "isSystemDevice"      => $this->isSystemDevice,
            "wattsPowerNormal"    => $this->wattsPowerNormal,
            "wattsPowerIdle"      => $this->wattsPowerIdle,
            "launchDate"          => $this->launchDate,
            "dateCreated"         => $this->dateCreated,
            "dutyCycle"           => $this->dutyCycle,
            "ppmBlack"            => $this->ppmBlack,
            "ppmColor"            => $this->ppmColor,
            "isLeased"            => $this->isLeased,
            "leasedTonerYield"    => $this->leasedTonerYield,
            "reportsTonerLevels"  => $this->reportsTonerLevels,
            "partsCostPerPage"    => $this->partsCostPerPage,
            "laborCostPerPage"    => $this->laborCostPerPage,
        );
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
            $this->_toners = Proposalgen_Model_Mapper_Toner::getInstance()->getReportToners($this->id, Zend_Auth::getInstance()->getIdentity()->dealerId);
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
     * @param Proposalgen_Model_CostPerPageSetting $costPerPageSetting
     *
     * @return bool
     */
    public function getHasValidMonoGrossMarginToners ($costPerPageSetting)
    {
        if (!isset($this->_hasValidMonoGrossMarginToners))
        {
            $usesAllValidToners      = true;
            $toners                  = $this->getCheapestTonerSetByVendor($costPerPageSetting);
            $oemRank                 = new Proposalgen_Model_Toner_Vendor_Ranking();
            $oemRank->manufacturerId = $this->manufacturerId;

            $preferredMonochromeVendors   = $costPerPageSetting->monochromeTonerRankSet->getRankings();
            $preferredMonochromeVendors[] = $oemRank;
            foreach ($toners as $toner)
            {
                switch ($toner->tonerColorId)
                {
                    case Proposalgen_Model_TonerColor::BLACK:
                        if ($toner->manufacturerId != $preferredMonochromeVendors[0]->manufacturerId)
                        {
                            $usesAllValidToners = false;
                        }
                        break;
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
     * @param Proposalgen_Model_CostPerPageSetting $costPerPageSetting
     *
     * @return bool
     */
    public function getHasValidColorGrossMarginToners ($costPerPageSetting)
    {
        if (!isset($this->_hasValidColorGrossMarginToners))
        {
            $usesAllValidToners           = true;
            $toners                       = $this->getCheapestTonerSetByVendor($costPerPageSetting);
            $oemRank                      = new Proposalgen_Model_Toner_Vendor_Ranking();
            $oemRank->manufacturerId      = $this->manufacturerId;
            $preferredMonochromeVendors   = $costPerPageSetting->colorTonerRankSet->getRankings();
            $preferredMonochromeVendors[] = $oemRank;
            $preferredColorVendors        = $costPerPageSetting->colorTonerRankSet->getRankings();
            $preferredColorVendors[]      = $oemRank;
            foreach ($toners as $toner)
            {
                switch ($toner->tonerColorId)
                {
                    case Proposalgen_Model_TonerColor::CYAN:
                    case Proposalgen_Model_TonerColor::MAGENTA:
                    case Proposalgen_Model_TonerColor::YELLOW:
                    case Proposalgen_Model_TonerColor::THREE_COLOR:
                        if ($toner->manufacturerId != $preferredColorVendors[0]->manufacturerId)
                        {
                            $usesAllValidToners = false;
                        }
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
     * @param Proposalgen_Model_CostPerPageSetting $costPerPageSetting
     *
     * @return Proposalgen_Model_Toner[]
     */
    public function getTonersForAssessment ($costPerPageSetting)
    {
        if (!isset($this->_tonersForAssessment))
        {
            $toners                     = $this->getCheapestTonerSetByVendor($costPerPageSetting);
            $this->_tonersForAssessment = $toners;
        }

        return $this->_tonersForAssessment;
    }

    /**
     * @param Proposalgen_Model_CostPerPageSetting $costPerPageSetting
     *
     * @return Proposalgen_Model_Toner[]
     */
    public function getTonersForGrossMargin ($costPerPageSetting)
    {
        if (!isset($this->_tonersForGrossMargin))
        {
            $toners                      = $this->getCheapestTonerSetByVendor($costPerPageSetting);
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
            foreach ($this->getCheapestTonerSetByVendor($costPerPageSetting) as $toner)
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
     * Gets a list of toners for the toner vendor id passed
     *
     * @param Proposalgen_Model_CostPerPageSetting $costPerPageSetting
     *
     * @return Proposalgen_Model_Toner[]
     */
    public function getCheapestTonerSetByVendor ($costPerPageSetting)
    {
        if (!isset($this->_cachedCheapestTonerVendorSet))
        {
            $this->_cachedCheapestTonerVendorSet = array();
        }

        $cacheKey = $costPerPageSetting->createCacheKey();

        if (!array_key_exists($cacheKey, $this->_cachedCheapestTonerVendorSet))
        {
            $monochromeManufacturerPreference               = implode(',', $costPerPageSetting->monochromeTonerRankSet->getRanksAsArray());
            $colorManufacturerPreference                    = implode(',', $costPerPageSetting->colorTonerRankSet->getRanksAsArray());
            $this->_cachedCheapestTonerVendorSet[$cacheKey] = Proposalgen_Model_Mapper_Toner::getInstance()->getCheapestTonersForDevice($this->id, Zend_Auth::getInstance()->getIdentity()->dealerId, $monochromeManufacturerPreference, $colorManufacturerPreference);
        }

        return $this->_cachedCheapestTonerVendorSet[$cacheKey];
    }

    /**
     * Gets a list of toners for the toner vendor id passed
     *
     * @param $vendorId
     *
     * @return Proposalgen_Model_Toner[]
     */
    public function getCheapestTonerSetByVendorId ($vendorId)
    {
        $monochromeManufacturerPreference = $vendorId;
        $colorManufacturerPreference      = $vendorId;

        return Proposalgen_Model_Mapper_Toner::getInstance()->getCheapestTonersForDevice($this->id, Zend_Auth::getInstance()->getIdentity()->dealerId, $monochromeManufacturerPreference, $colorManufacturerPreference);
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
                    $this->_deviceType = self::DEVICE_TYPE_MONO_MFP;
                }
                else
                {

                    $this->_deviceType = self::DEVICE_TYPE_MONO;
                }
            }
            else
            {
                if ($this->isCopier)
                {
                    $this->_deviceType = self::DEVICE_TYPE_COLOR_MFP;
                }
                else
                {
                    $this->_deviceType = self::DEVICE_TYPE_COLOR;
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
        if (!isset($this->_age))
        {
            // Get the time difference in seconds
            $launchDate          = time() - strtotime($this->launchDate);
            $correctedLaunchDate = ($launchDate > 31556926) ? ($launchDate - 31556926) : $launchDate;
            $this->_age          = floor($correctedLaunchDate / 31556926);
            if ($this->_age == 0)
            {
                $this->_age = 1;
            }
        }

        return $this->_age;
    }
}