<?php

/**
 * Class Proposalgen_Model_Device
 * 
 * @author "Lee Robert"
 */
class Proposalgen_Model_MasterDevice extends Tangent_Model_Abstract
{
    private static $ReportMargin;
    private static $PricingConfig;
    private static $GrossMarginPricingConfig;
    protected $MasterDeviceId;
    protected $ManufacturerId;
    protected $PrinterModel;
    protected $TonerConfigId;
    protected $IsCopier;
    protected $IsFax;
    protected $IsScanner;
    protected $IsDuplex;
    protected $IsReplacementDevice;
    protected $WattsPowerNormal;
    protected $WattsPowerIdle;
    protected $DevicePrice;
    protected $ServiceCostPerPage;
    protected $LaunchDate;
    protected $DateCreated;
    protected $DutyCycle;
    protected $PPMBlack;
    protected $PPMColor;
    protected $IsLeased;
    protected $LeasedTonerYield;
    protected $Toners;
    protected $Manufacturer;
    protected $TonerConfig;
    
    // Extra's
    protected $AdminCostPerPage;
    protected $CostPerPage;
    protected $UsingIncompleteBlackTonerData;
    protected $UsingIncompleteColorTonerData;
    protected $MaximumMonthlyPageVolume;
    protected $HasValidMonoGrossMarginToners;
    protected $HasValidColorGrossMarginToners;
    protected $TonersForAssessment;
    protected $TonersForGrossMargin;
    protected $RequiredTonerColors;

    /**
     * The maximum monthly page volume is calculated using the smallest toner yield
     * given the current pricing configuration
     * SPECIAL: Leased devices have a yield set, so we use that
     * 
     * @return the $MaximumMonthlyPageVolume
     */
    public function getMaximumMonthlyPageVolume ()
    {
        if (! isset($this->MaximumMonthlyPageVolume))
        {
            $smallestYield = null;
            if ($this->getIsLeased())
            {
                $smallestYield = $this->getLeasedTonerYield();
            }
            else
            {
                $requiredToners = Proposalgen_Model_TonerConfig::getRequiredTonersForTonerConfig($this->getTonerConfigId());
                foreach ( $requiredToners as $tonerColor )
                {
                    $toner = $this->getCheapestToner($tonerColor, self::$PricingConfig);
                    if ($toner->TonerYield < $smallestYield || is_null($smallestYield))
                    {
                        $smallestYield = $toner->TonerYield;
                    }
                }
            }
            $this->MaximumMonthlyPageVolume = $smallestYield;
        }
        return $this->MaximumMonthlyPageVolume;
    }

    /**
     *
     * @return the $PricingConfig
     */
    public static function getPricingConfig ()
    {
        if (! isset(Proposalgen_Model_MasterDevice::$PricingConfig))
        {
            
            Proposalgen_Model_MasterDevice::$PricingConfig = null;
        }
        return Proposalgen_Model_MasterDevice::$PricingConfig;
    }

    /**
     *
     * @param field_type $PricingConfig            
     */
    public static function setPricingConfig ($PricingConfig)
    {
        Proposalgen_Model_MasterDevice::$PricingConfig = $PricingConfig;
    }

    /**
     *
     * @return the $ReportMargin
     */
    public static function getReportMargin ()
    {
        if (! isset(Proposalgen_Model_MasterDevice::$ReportMargin))
        {
            Proposalgen_Model_MasterDevice::$ReportMargin = 1;
        }
        return Proposalgen_Model_MasterDevice::$ReportMargin;
    }

    /**
     *
     * @param field_type $ReportMargin            
     */
    public static function setReportMargin ($ReportMargin)
    {
        Proposalgen_Model_MasterDevice::$ReportMargin = $ReportMargin;
    }

    public function isUsingIncompleteTonerData ()
    {
        return ($this->UsingIncompleteTonerData);
    }

    /**
     * Gets the cheapest toner from a group of the same color.
     * Can specify a preferred part type to get.
     * Will return a default toner value if it does not find an appropriate toner
     * 
     * @param integer $tonerColor
     *            (Constant value in Proposalgen_Model_TonerColor)
     * @param integer $preferredPartType
     *            (Constant value in Proposalgen_Model_PartType)
     * @return Proposalgen_Model_Toner
     */
    public function getCheapestToner ($tonerColor, $pricingConfig)
    {
        $PricingConfigId = $pricingConfig->getPricingConfigId();
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
        $cheapestToner = null;
        $tonersByPartType = $this->getToners(); // Grab this devices toners
        

        // If we have a preferred part type and the device has toners of that type
        if (isset($preferredPartType) && is_array($tonersByPartType) && array_key_exists($preferredPartType->getPartTypeId(), $tonersByPartType) && is_array($tonersByPartType [$preferredPartType->getPartTypeId()]) && array_key_exists($tonerColor, $tonersByPartType [$preferredPartType->getPartTypeId()]))
        {
            // Figure out which is the cheapest black toner
            foreach ( $tonersByPartType [$preferredPartType->getPartTypeId()] [$tonerColor] as $toner )
            {
                if (isset($cheapestToner))
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
            foreach ( $tonersByPartType as $tonersByColor )
            {
                if (array_key_exists($tonerColor, $tonersByColor))
                {
                    foreach ( $tonersByColor [$tonerColor] as $toner )
                    {
                        // Compare Toner Ranks to figure out which is the cheapest
                        if ($cheapestToner)
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
        
        // If we didn't get any toners, then use the default one
        if (! isset($cheapestToner))
        {
            $defaultToners = Proposalgen_Model_Toner::getDefaultToners();
            $cheapestToner = $defaultToners [$tonerColor];
            
            // Flag this device as using incomplete toner data
            $this->setUsingIncompleteTonerData($tonerColor, true);
        }
        
        return $cheapestToner;
    }

    /**
     * Calculates the cost per page for a device based on a pricing config.
     * Once calculated if you pass a new pricing config, it will recalculate the value
     * 
     * @param stdClass $pricingConfig            
     */
    public function getCostPerPage ()
    {
        if (! isset($this->CostPerPage))
        {
            
            $costPerPage = new stdClass();
            
            $ReportMargin = self::getReportMargin();
            
            $costPerPage->Estimated = new stdClass();
            $costPerPage->Actual = new stdClass();
            
            $costPerPage->Actual->Raw = new stdClass();
            $costPerPage->Actual->Raw->BlackAndWhite = 0;
            $costPerPage->Actual->Raw->Color = 0;
            
            // Base CPP
            $costPerPage->Actual->Base = new stdClass();
            $costPerPage->Estimated->Base = new stdClass();
            $costPerPage->Actual->Base->BlackAndWhite = 0;
            $costPerPage->Actual->Base->Color = 0;
            $costPerPage->Estimated->Base->BlackAndWhite = 0;
            $costPerPage->Estimated->Base->Color = 0;
            
            // Base + Margin
            $costPerPage->Actual->BasePlusMargin = new stdClass();
            $costPerPage->Estimated->BasePlusMargin = new stdClass();
            $costPerPage->Actual->BasePlusMargin->BlackAndWhite = 0;
            $costPerPage->Actual->BasePlusMargin->Color = 0;
            $costPerPage->Estimated->BasePlusMargin->BlackAndWhite = 0;
            $costPerPage->Estimated->BasePlusMargin->Color = 0;
            
            // Base Plus Service and Admin CPP
            $costPerPage->Actual->BasePlusService = new stdClass();
            $costPerPage->Estimated->BasePlusService = new stdClass();
            $costPerPage->Actual->BasePlusService->BlackAndWhite = 0;
            $costPerPage->Actual->BasePlusService->Color = 0;
            $costPerPage->Estimated->BasePlusService->BlackAndWhite = 0;
            $costPerPage->Estimated->BasePlusService->Color = 0;
            
            // Base Plus Service and Admin CPP and Margin
            $costPerPage->Actual->BasePlusServiceAndMargin = new stdClass();
            $costPerPage->Estimated->BasePlusServiceAndMargin = new stdClass();
            $costPerPage->Actual->BasePlusServiceAndMargin->BlackAndWhite = 0;
            $costPerPage->Actual->BasePlusServiceAndMargin->Color = 0;
            $costPerPage->Estimated->BasePlusServiceAndMargin->BlackAndWhite = 0;
            $costPerPage->Estimated->BasePlusServiceAndMargin->Color = 0;
            
            $ServicePlusAdminCPP = $this->getServiceCostPerPage() + $this->getAdminCostPerPage();
            $ServiceCPP = $this->getServiceCostPerPage();
            $costPerPage->Actual->Raw->ServiceCPP = $this->getServiceCostPerPage();
            $costPerPage->Actual->Raw->AdminCPP = $this->getAdminCostPerPage();
            $costPerPage->Actual->Raw->ReportMargin = $ReportMargin;
            
            /* Estimated Cost Per Page */
            $tempTonerList = array ();
            $tonerColors = Proposalgen_Model_TonerConfig::getRequiredTonersForTonerConfig($this->getTonerConfigId());
            foreach ( $tonerColors as $tonerColor )
            {
                $tempTonerList [$tonerColor] = $this->getCheapestToner($tonerColor, self::getPricingConfig());
            }
            
            // Black Base CPP = Toner Cost / Toner Yield
            // Color CPP = Toner Cost / Toner Yield
            foreach ( $tempTonerList as $toner )
            {
                $tonerCPP = $toner->getCostPerPage();
                $costPerPage->Estimated->Base->BlackAndWhite += $tonerCPP->Estimated->BlackAndWhite;
                $costPerPage->Estimated->Base->Color += $tonerCPP->Estimated->BlackAndWhite;
                $costPerPage->Estimated->Base->Color += $tonerCPP->Estimated->Color;
            }
            
            /* Actual Cost Per Page */
            $tempTonerList = array ();
            foreach ( $tonerColors as $tonerColor )
            {
                $tempTonerList [$tonerColor] = $this->getCheapestToner($tonerColor, self::getGrossMarginPricingConfig());
            }
            
            // Black Base CPP = Toner Cost / Toner Yield
            // Color CPP = Toner Cost / Toner Yield
            foreach ( $tempTonerList as $toner )
            {
                $tonerCPP = $toner->getCostPerPage();
                $costPerPage->Actual->Base->BlackAndWhite += $tonerCPP->Actual->BlackAndWhite;
                $costPerPage->Actual->Base->Color += $tonerCPP->Actual->BlackAndWhite;
                $costPerPage->Actual->Base->Color += $tonerCPP->Actual->Color;
                $costPerPage->Actual->Raw->BlackAndWhite += $tonerCPP->Raw->BlackAndWhite;
                $costPerPage->Actual->Raw->Color += $tonerCPP->Raw->BlackAndWhite;
                $costPerPage->Actual->Raw->Color += $tonerCPP->Raw->Color;
            }
            
            // Apply a margin to the base cost per page
            $costPerPage->Actual->BasePlusMargin->BlackAndWhite = $costPerPage->Actual->Base->BlackAndWhite / $ReportMargin;
            $costPerPage->Actual->BasePlusMargin->Color = $costPerPage->Actual->Base->Color / $ReportMargin;
            $costPerPage->Estimated->BasePlusMargin->BlackAndWhite = $costPerPage->Estimated->Base->BlackAndWhite / $ReportMargin;
            $costPerPage->Estimated->BasePlusMargin->Color = $costPerPage->Estimated->Base->Color / $ReportMargin;
            
            // Add the device's costs to the base cost per page
            $costPerPage->Actual->BasePlusService->BlackAndWhite += $costPerPage->Actual->Base->BlackAndWhite + $ServicePlusAdminCPP;
            $costPerPage->Actual->BasePlusService->Color += $costPerPage->Actual->Base->Color + $ServicePlusAdminCPP;
            $costPerPage->Estimated->BasePlusService->BlackAndWhite += $costPerPage->Estimated->Base->BlackAndWhite + $ServiceCPP;
            $costPerPage->Estimated->BasePlusService->Color += $costPerPage->Estimated->Base->Color + $ServiceCPP;
            
            // Apply the report margin to the adjusted cost per page
            $costPerPage->Actual->BasePlusServiceAndMargin->BlackAndWhite = $costPerPage->Actual->BasePlusService->BlackAndWhite / $ReportMargin;
            $costPerPage->Actual->BasePlusServiceAndMargin->Color = $costPerPage->Actual->BasePlusService->Color / $ReportMargin;
            $costPerPage->Estimated->BasePlusServiceAndMargin->BlackAndWhite = ($costPerPage->Estimated->Base->BlackAndWhite / $ReportMargin) + $ServiceCPP;
            $costPerPage->Estimated->BasePlusServiceAndMargin->Color = ($costPerPage->Estimated->Base->Color / $ReportMargin) + $ServiceCPP;
            
            $this->CostPerPage = $costPerPage;
        }
        return $this->CostPerPage;
    }

    /**
     *
     * @param field_type $CostPerPage            
     */
    public function setCostPerPage ($CostPerPage)
    {
        $this->CostPerPage = $CostPerPage;
        return $this;
    }

    /**
     *
     * @return the $MasterDeviceId
     */
    public function getMasterDeviceId ()
    {
        if (! isset($this->MasterDeviceId))
        {
            
            $this->MasterDeviceId = null;
        }
        return $this->MasterDeviceId;
    }

    /**
     *
     * @param field_type $MasterDeviceId            
     */
    public function setMasterDeviceId ($MasterDeviceId)
    {
        $this->MasterDeviceId = $MasterDeviceId;
        return $this;
    }

    /**
     *
     * @return Proposalgen_Model_Manufacturer
     */
    public function getManufacturer ()
    {
        if (! isset($this->Manufacturer))
        {
            $manufacturerMapper = Proposalgen_Model_Mapper_Manufacturer::getInstance();
            $this->Manufacturer = $manufacturerMapper->find($this->ManufacturerId);
        }
        return $this->Manufacturer;
    }

    /**
     *
     * @param field_type $Manufacturer            
     */
    public function setManufacturer ($Manufacturer)
    {
        $this->Manufacturer = $Manufacturer;
        return $this;
    }

    /**
     *
     * @return the $PrinterModel
     */
    public function getPrinterModel ()
    {
        if (! isset($this->PrinterModel))
        {
            
            $this->PrinterModel = null;
        }
        return $this->PrinterModel;
    }

    /**
     *
     * @param field_type $PrinterModel            
     */
    public function setPrinterModel ($PrinterModel)
    {
        $this->PrinterModel = $PrinterModel;
        return $this;
    }

    /**
     *
     * @return the $TonerConfig
     */
    public function getTonerConfig ()
    {
        if (! isset($this->TonerConfig))
        {
            $tonerConfigMapper = Proposalgen_Model_Mapper_TonerConfig::getInstance();
            $this->TonerConfig = $tonerConfigMapper->find($this->TonerConfigId);
        }
        return $this->TonerConfig;
    }

    /**
     *
     * @param field_type $TonerConfig            
     */
    public function setTonerConfig ($TonerConfig)
    {
        $this->TonerConfig = $TonerConfig;
        return $this;
    }

    /**
     *
     * @return the $IsCopier
     */
    public function getIsCopier ()
    {
        if (! isset($this->IsCopier))
        {
            
            $this->IsCopier = null;
        }
        return $this->IsCopier;
    }

    /**
     *
     * @param field_type $IsCopier            
     */
    public function setIsCopier ($IsCopier)
    {
        $this->IsCopier = $IsCopier;
        return $this;
    }

    /**
     *
     * @return the $IsFax
     */
    public function getIsFax ()
    {
        if (! isset($this->IsFax))
        {
            
            $this->IsFax = null;
        }
        return $this->IsFax;
    }

    /**
     *
     * @param field_type $IsFax            
     */
    public function setIsFax ($IsFax)
    {
        $this->IsFax = $IsFax;
        return $this;
    }

    /**
     *
     * @return the $IsScanner
     */
    public function getIsScanner ()
    {
        if (! isset($this->IsScanner))
        {
            
            $this->IsScanner = null;
        }
        return $this->IsScanner;
    }

    /**
     *
     * @param field_type $IsScanner            
     */
    public function setIsScanner ($IsScanner)
    {
        $this->IsScanner = $IsScanner;
        return $this;
    }

    /**
     *
     * @return the $IsDuplex
     */
    public function getIsDuplex ()
    {
        if (! isset($this->IsDuplex))
        {
            
            $this->IsDuplex = null;
        }
        return $this->IsDuplex;
    }

    /**
     *
     * @param field_type $IsDuplex            
     */
    public function setIsDuplex ($IsDuplex)
    {
        $this->IsDuplex = $IsDuplex;
        return $this;
    }

    /**
     *
     * @return the $IsReplacementDevice
     */
    public function getIsReplacementDevice ()
    {
        if (! isset($this->IsReplacementDevice))
        {
            
            $this->IsReplacementDevice = null;
        }
        return $this->IsReplacementDevice;
    }

    /**
     *
     * @param field_type $IsReplacementDevice            
     */
    public function setIsReplacementDevice ($IsReplacementDevice)
    {
        $this->IsReplacementDevice = $IsReplacementDevice;
        return $this;
    }

    /**
     *
     * @return the $WattsPowerNormal
     */
    public function getWattsPowerNormal ()
    {
        if (! isset($this->WattsPowerNormal))
        {
            $this->WattsPowerNormal = null;
        }
        return $this->WattsPowerNormal;
    }

    /**
     *
     * @param field_type $WattsPowerNormal            
     */
    public function setWattsPowerNormal ($WattsPowerNormal)
    {
        $this->WattsPowerNormal = $WattsPowerNormal;
        return $this;
    }

    /**
     *
     * @return the $WattsPowerIdle
     */
    public function getWattsPowerIdle ()
    {
        if (! isset($this->WattsPowerIdle))
        {
            $this->WattsPowerIdle = 10;
        }
        return $this->WattsPowerIdle;
    }

    /**
     *
     * @param field_type $WattsPowerIdle            
     */
    public function setWattsPowerIdle ($WattsPowerIdle)
    {
        $this->WattsPowerIdle = $WattsPowerIdle;
        return $this;
    }

    /**
     *
     * @return the $DevicePrice
     */
    public function getDevicePrice ()
    {
        if (! isset($this->DevicePrice))
        {
            
            $this->DevicePrice = null;
        }
        return $this->DevicePrice;
    }

    /**
     *
     * @param field_type $DevicePrice            
     */
    public function setDevicePrice ($DevicePrice)
    {
        $this->DevicePrice = $DevicePrice;
        return $this;
    }

    /**
     *
     * @return the $LaunchDate
     */
    public function getLaunchDate ()
    {
        if (! isset($this->LaunchDate))
        {
            
            $this->LaunchDate = null;
        }
        return $this->LaunchDate;
    }

    /**
     *
     * @param field_type $LaunchDate            
     */
    public function setLaunchDate ($LaunchDate)
    {
        $this->LaunchDate = $LaunchDate;
        return $this;
    }

    /**
     *
     * @return the $DateCreated
     */
    public function getDateCreated ()
    {
        if (! isset($this->DateCreated))
        {
            
            $this->DateCreated = null;
        }
        return $this->DateCreated;
    }

    /**
     *
     * @param field_type $DateCreated            
     */
    public function setDateCreated ($DateCreated)
    {
        $this->DateCreated = $DateCreated;
        return $this;
    }

    /**
     *
     * @return the $Toners
     */
    public function getToners ()
    {
        if (! isset($this->Toners))
        {
            // Get the toners for the device
            /*
             * $toners = array (); $deviceTonerMapper = Proposalgen_Model_Mapper_DeviceToner::getInstance();
             * $tonerMapper = Proposalgen_Model_Mapper_Toner::getInstance(); $tonerRows =
             * $deviceTonerMapper->fetchAll(array ( "master_device_id = ?" => $this->MasterDeviceId )); if ($tonerRows)
             * { foreach ( $tonerRows as $tonerRow ) { $toner = $tonerMapper->find($tonerRow->getTonerId()); $toners
             * [$toner->getPartType()->getPartTypeId()] [$toner->getTonerColor()->getTonerColorId()] [] = $toner; } }
             */
            $this->Toners = Proposalgen_Model_Mapper_Toner::getInstance()->getTonersForDevice($this->getMasterDeviceId());
        }
        return $this->Toners;
    }

    /**
     *
     * @param field_type $Toners            
     */
    public function setToners ($Toners)
    {
        $this->Toners = $Toners;
        return $this;
    }

    /**
     *
     * @return the $ManufacturerId
     */
    public function getManufacturerId ()
    {
        if (! isset($this->ManufacturerId))
        {
            
            $this->ManufacturerId = null;
        }
        return $this->ManufacturerId;
    }

    /**
     *
     * @param field_type $ManufacturerId            
     */
    public function setManufacturerId ($ManufacturerId)
    {
        $this->ManufacturerId = $ManufacturerId;
        return $this;
    }

    /**
     *
     * @return the $TonerConfigId
     */
    public function getTonerConfigId ()
    {
        if (! isset($this->TonerConfigId))
        {
            
            $this->TonerConfigId = null;
        }
        return $this->TonerConfigId;
    }

    /**
     *
     * @param field_type $TonerConfigId            
     */
    public function setTonerConfigId ($TonerConfigId)
    {
        $this->TonerConfigId = $TonerConfigId;
        return $this;
    }

    /**
     *
     * @return the $AdminCostPerPage
     */
    public function getAdminCostPerPage ()
    {
        if (! isset($this->AdminCostPerPage))
        {
            $this->AdminCostPerPage = null;
        }
        return $this->AdminCostPerPage;
    }

    /**
     *
     * @param field_type $AdminCostPerPage            
     */
    public function setAdminCostPerPage ($AdminCostPerPage)
    {
        $this->AdminCostPerPage = $AdminCostPerPage;
        return $this;
    }

    /**
     *
     * @param field_type $UsingIncompleteTonerData            
     */
    public function setUsingIncompleteTonerData ($tonerColor, $incomplete)
    {
        switch ($tonerColor)
        {
            case Proposalgen_Model_TonerColor::FOUR_COLOR :
                $this->UsingIncompleteBlackTonerData = $incomplete;
                $this->UsingIncompleteColorTonerData = $incomplete;
                break;
            case Proposalgen_Model_TonerColor::THREE_COLOR :
            case Proposalgen_Model_TonerColor::CYAN :
            case Proposalgen_Model_TonerColor::MAGENTA :
            case Proposalgen_Model_TonerColor::YELLOW :
                $this->UsingIncompleteColorTonerData = $incomplete;
                break;
            case Proposalgen_Model_TonerColor::BLACK :
                $this->UsingIncompleteBlackTonerData = $incomplete;
                break;
        }
        return $this;
    }

    /**
     *
     * @return the $UsingIncompleteBlackTonerData
     */
    public function isUsingIncompleteBlackTonerData ()
    {
        return ($this->UsingIncompleteBlackTonerData);
    }

    public function isUsingIncompleteColorTonerData ()
    {
        return ($this->UsingIncompleteColorTonerData);
    }

    /**
     *
     * @return the $DutyCycle
     */
    public function getDutyCycle ()
    {
        if (! isset($this->DutyCycle))
        {
            
            $this->DutyCycle = null;
        }
        return $this->DutyCycle;
    }

    /**
     *
     * @param field_type $DutyCycle            
     */
    public function setDutyCycle ($DutyCycle)
    {
        $this->DutyCycle = $DutyCycle;
        return $this;
    }

    /**
     *
     * @return the $PPMBlack
     */
    public function getPPMBlack ()
    {
        if (! isset($this->PPMBlack))
        {
            
            $this->PPMBlack = null;
        }
        return $this->PPMBlack;
    }

    /**
     *
     * @param field_type $PPMBlack            
     */
    public function setPPMBlack ($PPMBlack)
    {
        $this->PPMBlack = $PPMBlack;
        return $this;
    }

    /**
     *
     * @return the $PPMColor
     */
    public function getPPMColor ()
    {
        if (! isset($this->PPMColor))
        {
            
            $this->PPMColor = null;
        }
        return $this->PPMColor;
    }

    /**
     *
     * @param field_type $PPMColor            
     */
    public function setPPMColor ($PPMColor)
    {
        $this->PPMColor = $PPMColor;
        return $this;
    }

    /**
     *
     * @return the $ServiceCostPerPage
     */
    public function getServiceCostPerPage ()
    {
        if (! isset($this->ServiceCostPerPage))
        {
            
            $this->ServiceCostPerPage = null;
        }
        return $this->ServiceCostPerPage;
    }

    /**
     *
     * @param field_type $ServiceCostPerPage            
     */
    public function setServiceCostPerPage ($ServiceCostPerPage)
    {
        $this->ServiceCostPerPage = $ServiceCostPerPage;
        return $this;
    }

    /**
     *
     * @return the $GrossMarginPricingConfig
     */
    public static function getGrossMarginPricingConfig ()
    {
        if (! isset(Proposalgen_Model_MasterDevice::$GrossMarginPricingConfig))
        {
            
            Proposalgen_Model_MasterDevice::$GrossMarginPricingConfig = null;
        }
        return Proposalgen_Model_MasterDevice::$GrossMarginPricingConfig;
    }

    /**
     *
     * @param field_type $GrossMarginPricingConfig            
     */
    public static function setGrossMarginPricingConfig ($GrossMarginPricingConfig)
    {
        Proposalgen_Model_MasterDevice::$GrossMarginPricingConfig = $GrossMarginPricingConfig;
    }

    /**
     *
     * @return the $IsLeased
     */
    public function getIsLeased ()
    {
        if (! isset($this->IsLeased))
        {
            
            $this->IsLeased = null;
        }
        return $this->IsLeased;
    }

    /**
     *
     * @param field_type $IsLeased            
     */
    public function setIsLeased ($IsLeased)
    {
        $this->IsLeased = $IsLeased;
        return $this;
    }

    /**
     *
     * @return the $LeasedTonerYield
     */
    public function getLeasedTonerYield ()
    {
        if (! isset($this->LeasedTonerYield))
        {
            $this->LeasedTonerYield = null;
        }
        return $this->LeasedTonerYield;
    }

    /**
     *
     * @param field_type $LeasedTonerYield            
     */
    public function setLeasedTonerYield ($LeasedTonerYield)
    {
        $this->LeasedTonerYield = $LeasedTonerYield;
        return $this;
    }

    /**
     *
     * @return the $HasValidMonoGrossMarginToners
     */
    public function getHasValidMonoGrossMarginToners ()
    {
        if (! isset($this->HasValidMonoGrossMarginToners))
        {
            $usesAllValidToners = true;
            $requiredToners = Proposalgen_Model_TonerConfig::getRequiredTonersForTonerConfig($this->getTonerConfigId());
            foreach ( $requiredToners as $tonerColor )
            {
                $toner = $this->getCheapestToner($tonerColor, self::getGrossMarginPricingConfig());
                
                if ($tonerColor == Proposalgen_Model_TonerColor::BLACK && $toner->getPartTypeId() != self::getGrossMarginPricingConfig()->getMonoTonerPartTypeId())
                {
                    $usesAllValidToners = false;
                    break;
                }
                else if ($toner->getPartTypeId() != self::getGrossMarginPricingConfig()->getColorTonerPartTypeId())
                {
                    //$usesAllValidToners = false;
                    //break;
                }
            }
            
            $this->HasValidMonoGrossMarginToners = $usesAllValidToners;
        }
        return $this->HasValidMonoGrossMarginToners;
    }

    /**
     *
     * @param field_type $HasValidMonoGrossMarginToners            
     */
    public function setHasValidMonoGrossMarginToners ($HasValidMonoGrossMarginToners)
    {
        $this->HasValidMonoGrossMarginToners = $HasValidMonoGrossMarginToners;
        return $this;
    }

    /**
     *
     * @return the $HasValidColorGrossMarginToners
     */
    public function getHasValidColorGrossMarginToners ()
    {
        if (! isset($this->HasValidColorGrossMarginToners))
        {
            $usesAllValidToners = true;
            $requiredToners = Proposalgen_Model_TonerConfig::getRequiredTonersForTonerConfig($this->getTonerConfigId());
            foreach ( $requiredToners as $tonerColor )
            {
                $toner = $this->getCheapestToner($tonerColor, self::getGrossMarginPricingConfig());
                
                if ($tonerColor == Proposalgen_Model_TonerColor::BLACK && $toner->getPartTypeId() != self::getGrossMarginPricingConfig()->getMonoTonerPartTypeId())
                {
                    //$usesAllValidToners = false;
                    //break;
                }
                else if ($toner->getPartTypeId() != self::getGrossMarginPricingConfig()->getColorTonerPartTypeId())
                {
                    $usesAllValidToners = false;
                    break;
                }
            }
            
            $this->HasValidColorGrossMarginToners = $usesAllValidToners;
        }
        return $this->HasValidColorGrossMarginToners;
    }

    /**
     *
     * @param field_type $HasValidColorGrossMarginToners            
     */
    public function setHasValidColorGrossMarginToners ($HasValidColorGrossMarginToners)
    {
        $this->HasValidColorGrossMarginToners = $HasValidColorGrossMarginToners;
        return $this;
    }

    /**
     *
     * @return the $TonersForAssessment
     */
    public function getTonersForAssessment ()
    {
        if (! isset($this->TonersForAssessment))
        {
            $toners = array ();
            foreach ( $this->getRequiredTonerColors() as $tonerColor )
            {
                $toners [$tonerColor] = $this->getCheapestToner($tonerColor, self::getPricingConfig());
            }
            $this->TonersForAssessment = $toners;
        }
        return $this->TonersForAssessment;
    }

    /**
     *
     * @return the $TonersForGrossMargin
     */
    public function getTonersForGrossMargin ()
    {
        if (! isset($this->TonersForGrossMargin))
        {
            $toners = array ();
            foreach ( $this->getRequiredTonerColors() as $tonerColor )
            {
                $toners [$tonerColor] = $this->getCheapestToner($tonerColor, self::getGrossMarginPricingConfig());
            }
            $this->TonersForGrossMargin = $toners;
        }
        return $this->TonersForGrossMargin;
    }

    /**
     *
     * @param field_type $TonersForAssessment            
     */
    public function setTonersForAssessment ($TonersForAssessment)
    {
        $this->TonersForAssessment = $TonersForAssessment;
        return $this;
    }

    /**
     *
     * @param field_type $TonersForGrossMargin            
     */
    public function setTonersForGrossMargin ($TonersForGrossMargin)
    {
        $this->TonersForGrossMargin = $TonersForGrossMargin;
        return $this;
    }

    /**
     *
     * @return the $RequiredTonerColors
     */
    public function getRequiredTonerColors ()
    {
        if (! isset($this->RequiredTonerColors))
        {
            $this->RequiredTonerColors = Proposalgen_Model_TonerConfig::getRequiredTonersForTonerConfig($this->getTonerConfigId());
        }
        return $this->RequiredTonerColors;
    }

    /**
     *
     * @param field_type $RequiredTonerColors            
     */
    public function setRequiredTonerColors ($RequiredTonerColors)
    {
        $this->RequiredTonerColors = $RequiredTonerColors;
        return $this;
    }
}