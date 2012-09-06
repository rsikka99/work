<?php

/**
 * Class Proposalgen_Model_Device
 *
 * @author "Lee Robert"
 */
class Proposalgen_Model_MasterDevice extends My_Model_Abstract
{
    private static $ReportMargin;
    private static $PricingConfig;
    private static $GrossMarginPricingConfig;
    protected $_id;
    protected $_manufacturerId;
    protected $_printerModel;
    protected $_tonerConfigId;
    protected $_isCopier;
    protected $_isFax;
    protected $_isScanner;
    protected $_isDuplex;
    protected $_isReplacementDevice;
    protected $_wattsPowerNormal;
    protected $_wattsPowerIdle;
    protected $_cost;
    protected $_serviceCostPerPage;
    protected $_launchDate;
    protected $_dateCreated;
    protected $_dutyCycle;
    protected $_ppmBlack;
    protected $_ppmColor;
    protected $_isLeased;
    protected $_leasedTonerYield;
    protected $_toners;
    protected $_manufacturer;
    protected $_tonerConfig;
    
    // Extra's
    protected $_adminCostPerPage;
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
     * The maximum monthly page volume is calculated using the smallest toner yield
     * given the current pricing configuration
     * SPECIAL: Leased devices have a yield set, so we use that
     *
     * @return the $MaximumMonthlyPageVolume
     */
    public function getMaximumMonthlyPageVolume ()
    {
        if (! isset($this->_maximumMonthlyPageVolume))
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
            $this->_maximumMonthlyPageVolume = $smallestYield;
        }
        return $this->_maximumMonthlyPageVolume;
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::populate()
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }
        if (isset($params->id) && ! is_null($params->id))
            $this->setId($params->id);
        
        if (isset($params->manufacturer_id) && ! is_null($params->manufacturer_id))
            $this->setManufacturerId($params->manufacturer_id);
        
        if (isset($params->printer_model) && ! is_null($params->printer_model))
            $this->setPrinterModel($params->printer_model);
        
        if (isset($params->toner_config_id) && ! is_null($params->toner_config_id))
            $this->setTonerConfigId($params->toner_config_id);
        
        if (isset($params->is_copier) && ! is_null($params->is_copier))
            $this->setIsCopier($params->is_copier);
        
        if (isset($params->is_fax) && ! is_null($params->is_fax))
            $this->setIsFax($params->is_fax);
        
        if (isset($params->is_scanner) && ! is_null($params->is_scanner))
            $this->setIsScanner($params->is_scanner);
        
        if (isset($params->is_duplex) && ! is_null($params->is_duplex))
            $this->setIsDuplex($params->is_duplex);
        
        if (isset($params->is_replacement_device) && ! is_null($params->is_replacement_device))
            $this->setIsReplacementDevice($params->is_replacement_device);
        
        if (isset($params->watts_power_normal) && ! is_null($params->watts_power_normal))
            $this->setWattsPowerNormal($params->watts_power_normal);
        
        if (isset($params->watts_power_idle) && ! is_null($params->watts_power_idle))
            $this->setWattsPowerIdle($params->watts_power_idle);
        
        if (isset($params->cost) && ! is_null($params->cost))
            $this->setCost($params->cost);
        
        if (isset($params->service_cost_per_page) && ! is_null($params->service_cost_per_page))
            $this->setServiceCostPerPage($params->service_cost_per_page);
        
        if (isset($params->launch_date) && ! is_null($params->launch_date))
            $this->setLaunchDate($params->launch_date);
        
        if (isset($params->date_created) && ! is_null($params->date_created))
            $this->setDateCreated($params->date_created);
        
        if (isset($params->duty_cycle) && ! is_null($params->duty_cycle))
            $this->setDutyCycle($params->duty_cycle);
        
        if (isset($params->ppm_black) && ! is_null($params->ppm_black))
            $this->setPPMBlack($params->ppm_black);
        
        if (isset($params->ppm_color) && ! is_null($params->ppm_color))
            $this->setPPMColor($params->ppm_color);
        
        if (isset($params->is_leased) && ! is_null($params->is_leased))
            $this->setIsLeased($params->is_leased);
        
        if (isset($params->leased_toner_yield) && ! is_null($params->leased_toner_yield))
            $this->setLeasedTonerYield($params->leased_toner_yield);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                "id" => $this->getId(), 
                "manufacturer_id" => $this->getManufacturerId(), 
                "printer_model" => $this->getPrinterModel(), 
                "toner_config_id" => $this->getTonerConfigId(), 
                "is_copier" => $this->getIsCopier(), 
                "is_fax" => $this->getIsFax(), 
                "is_scanner" => $this->getIsScanner(), 
                "is_duplex" => $this->getIsDuplex(), 
                "is_replacement_device" => $this->getIsReplacementDevice(), 
                "watts_power_normal" => $this->getWattsPowerNormal(), 
                "watts_power_idle" => $this->getWattsPowerIdle(), 
                "cost" => $this->getCost(), 
                "service_cost_per_page" => $this->getServiceCostPerPage(), 
                "launch_date" => $this->getLaunchDate(), 
                "date_created" => $this->getDateCreated(), 
                "duty_cycle" => $this->getDutyCycle(), 
                "ppm_black" => $this->getPPMBlack(), 
                "ppm_color" => $this->getPPMColor(), 
                "is_leased" => $this->getIsLeased(), 
                "leased_toner_yield" => $this->getLeasedTonerYield() 
        );
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
        
        // If we don't have a toner, return false.
        if (! isset($cheapestToner))
        {
            return false;
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
        if (! isset($this->_costPerPage))
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
            $tempTonerList = array ();
            foreach ( $tonerColors as $tonerColor )
            {
                $tempTonerList [$tonerColor] = $this->getCheapestToner($tonerColor, self::getGrossMarginPricingConfig());
            }
            
            // Black Base CPP = Toner Cost / Toner Yield
            // Color CPP = Toner Cost / Toner Yield
            foreach ( $tempTonerList as $toner )
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
            
            $this->_costPerPage = $costPerPage;
        }
        return $this->_costPerPage;
    }

    /**
     *
     * @param field_type $CostPerPage            
     */
    public function setCostPerPage ($CostPerPage)
    {
        $this->_costPerPage = $CostPerPage;
        return $this;
    }

    /**
     *
     * @return the $MasterDeviceId
     */
    public function getId ()
    {
        if (! isset($this->_id))
        {
            
            $this->_id = null;
        }
        return $this->_id;
    }

    /**
     *
     * @param field_type $Id            
     */
    public function setId ($Id)
    {
        $this->_id = $Id;
        return $this;
    }

    /**
     *
     * @return Proposalgen_Model_Manufacturer
     */
    public function getManufacturer ()
    {
        if (! isset($this->_manufacturer))
        {
            $manufacturerMapper = Proposalgen_Model_Mapper_Manufacturer::getInstance();
            $this->_manufacturer = $manufacturerMapper->find($this->_manufacturerId);
        }
        return $this->_manufacturer;
    }

    /**
     *
     * @param Proposalgen_Model_Manufacturer $Manufacturer            
     */
    public function setManufacturer ($Manufacturer)
    {
        $this->_manufacturer = $Manufacturer;
        return $this;
    }

    /**
     *
     * @return the $PrinterModel
     */
    public function getPrinterModel ()
    {
        if (! isset($this->_printerModel))
        {
            
            $this->_printerModel = null;
        }
        return $this->_printerModel;
    }

    /**
     *
     * @param field_type $PrinterModel            
     */
    public function setPrinterModel ($PrinterModel)
    {
        $this->_printerModel = $PrinterModel;
        return $this;
    }

    /**
     * Gets the toner config object for this device
     *
     * @return Proposalgen_Model_TonerConfig
     */
    public function getTonerConfig ()
    {
        if (! isset($this->_tonerConfig))
        {
            $tonerConfigMapper = Proposalgen_Model_Mapper_TonerConfig::getInstance();
            $this->_tonerConfig = $tonerConfigMapper->find($this->_tonerConfigId);
        }
        return $this->_tonerConfig;
    }

    /**
     * Sets the toner config object for this device
     *
     * @param field_type $TonerConfig            
     */
    public function setTonerConfig ($TonerConfig)
    {
        $this->_tonerConfig = $TonerConfig;
        return $this;
    }

    /**
     *
     * @return the $IsCopier
     */
    public function getIsCopier ()
    {
        if (! isset($this->_isCopier))
        {
            
            $this->_isCopier = null;
        }
        return $this->_isCopier;
    }

    /**
     *
     * @param field_type $IsCopier            
     */
    public function setIsCopier ($IsCopier)
    {
        $this->_isCopier = $IsCopier;
        return $this;
    }

    /**
     *
     * @return the $IsFax
     */
    public function getIsFax ()
    {
        if (! isset($this->_isFax))
        {
            
            $this->_isFax = null;
        }
        return $this->_isFax;
    }

    /**
     *
     * @param field_type $IsFax            
     */
    public function setIsFax ($IsFax)
    {
        $this->_isFax = $IsFax;
        return $this;
    }

    /**
     *
     * @return the $IsScanner
     */
    public function getIsScanner ()
    {
        if (! isset($this->_isScanner))
        {
            
            $this->_isScanner = null;
        }
        return $this->_isScanner;
    }

    /**
     *
     * @param field_type $IsScanner            
     */
    public function setIsScanner ($IsScanner)
    {
        $this->_isScanner = $IsScanner;
        return $this;
    }

    /**
     *
     * @return the $IsDuplex
     */
    public function getIsDuplex ()
    {
        if (! isset($this->_isDuplex))
        {
            
            $this->_isDuplex = null;
        }
        return $this->_isDuplex;
    }

    /**
     *
     * @param field_type $IsDuplex            
     */
    public function setIsDuplex ($IsDuplex)
    {
        $this->_isDuplex = $IsDuplex;
        return $this;
    }

    /**
     *
     * @return the $IsReplacementDevice
     */
    public function getIsReplacementDevice ()
    {
        if (! isset($this->_isReplacementDevice))
        {
            
            $this->_isReplacementDevice = null;
        }
        return $this->_isReplacementDevice;
    }

    /**
     *
     * @param field_type $IsReplacementDevice            
     */
    public function setIsReplacementDevice ($IsReplacementDevice)
    {
        $this->_isReplacementDevice = $IsReplacementDevice;
        return $this;
    }

    /**
     *
     * @return the $WattsPowerNormal
     */
    public function getWattsPowerNormal ()
    {
        if (! isset($this->_wattsPowerNormal))
        {
            $this->_wattsPowerNormal = null;
        }
        return $this->_wattsPowerNormal;
    }

    /**
     *
     * @param field_type $WattsPowerNormal            
     */
    public function setWattsPowerNormal ($WattsPowerNormal)
    {
        $this->_wattsPowerNormal = $WattsPowerNormal;
        return $this;
    }

    /**
     *
     * @return the $WattsPowerIdle
     */
    public function getWattsPowerIdle ()
    {
        if (! isset($this->_wattsPowerIdle))
        {
            $this->_wattsPowerIdle = 10;
        }
        return $this->_wattsPowerIdle;
    }

    /**
     *
     * @param field_type $WattsPowerIdle            
     */
    public function setWattsPowerIdle ($WattsPowerIdle)
    {
        $this->_wattsPowerIdle = $WattsPowerIdle;
        return $this;
    }

    /**
     * Gets the cost of the device
     *
     * @return number The cost of the device
     */
    public function getCost ()
    {
        if (! isset($this->_cost))
        {
            
            $this->_cost = null;
        }
        return $this->_cost;
    }

    /**
     * Sets a new cost for the device
     *
     * @param number $cost
     *            The new cost of the device
     */
    public function setCost ($cost)
    {
        $this->_cost = $cost;
        return $this;
    }

    /**
     *
     * @return the $LaunchDate
     */
    public function getLaunchDate ()
    {
        if (! isset($this->_launchDate))
        {
            
            $this->_launchDate = null;
        }
        return $this->_launchDate;
    }

    /**
     *
     * @param field_type $LaunchDate            
     */
    public function setLaunchDate ($LaunchDate)
    {
        $this->_launchDate = date('Y-m-d', strtotime($LaunchDate));
        return $this;
    }

    /**
     *
     * @return the $DateCreated
     */
    public function getDateCreated ()
    {
        if (! isset($this->_dateCreated))
        {
            
            $this->_dateCreated = null;
        }
        return $this->_dateCreated;
    }

    /**
     *
     * @param field_type $DateCreated            
     */
    public function setDateCreated ($DateCreated)
    {
        $this->_dateCreated = $DateCreated;
        return $this;
    }

    /**
     *
     * @return the $Toners
     */
    public function getToners ()
    {
        if (! isset($this->_toners))
        {
            // Get the toners for the device
            /*
             * $toners = array (); $deviceTonerMapper = Proposalgen_Model_Mapper_DeviceToner::getInstance();
             * $tonerMapper = Proposalgen_Model_Mapper_Toner::getInstance(); $tonerRows =
             * $deviceTonerMapper->fetchAll(array ( "master_device_id = ?" => $this->MasterDeviceId )); if ($tonerRows)
             * { foreach ( $tonerRows as $tonerRow ) { $toner = $tonerMapper->find($tonerRow->getTonerId()); $toners
             * [$toner->getPartType()->getPartTypeId()] [$toner->getTonerColor()->getTonerColorId()] [] = $toner; } }
             */
            $this->_toners = Proposalgen_Model_Mapper_Toner::getInstance()->getTonersForDevice($this->getId());
        }
        return $this->_toners;
    }

    /**
     *
     * @param field_type $Toners            
     */
    public function setToners ($Toners)
    {
        $this->_toners = $Toners;
        return $this;
    }

    /**
     *
     * @return the $ManufacturerId
     */
    public function getManufacturerId ()
    {
        if (! isset($this->_manufacturerId))
        {
            
            $this->_manufacturerId = null;
        }
        return $this->_manufacturerId;
    }

    /**
     *
     * @param field_type $ManufacturerId            
     */
    public function setManufacturerId ($ManufacturerId)
    {
        $this->_manufacturerId = $ManufacturerId;
        return $this;
    }

    /**
     *
     * @return the $TonerConfigId
     */
    public function getTonerConfigId ()
    {
        if (! isset($this->_tonerConfigId))
        {
            
            $this->_tonerConfigId = null;
        }
        return $this->_tonerConfigId;
    }

    /**
     *
     * @param field_type $TonerConfigId            
     */
    public function setTonerConfigId ($TonerConfigId)
    {
        $this->_tonerConfigId = $TonerConfigId;
        return $this;
    }

    /**
     *
     * @return the $AdminCostPerPage
     */
    public function getAdminCostPerPage ()
    {
        if (! isset($this->_adminCostPerPage))
        {
            $this->_adminCostPerPage = null;
        }
        return $this->_adminCostPerPage;
    }

    /**
     *
     * @param field_type $AdminCostPerPage            
     */
    public function setAdminCostPerPage ($AdminCostPerPage)
    {
        $this->_adminCostPerPage = $AdminCostPerPage;
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
     *
     * @return the $UsingIncompleteBlackTonerData
     */
    public function isUsingIncompleteBlackTonerData ()
    {
        return ($this->_usingIncompleteBlackTonerData);
    }

    public function isUsingIncompleteColorTonerData ()
    {
        return ($this->_usingIncompleteColorTonerData);
    }

    /**
     *
     * @return the $DutyCycle
     */
    public function getDutyCycle ()
    {
        if (! isset($this->_dutyCycle))
        {
            
            $this->_dutyCycle = null;
        }
        return $this->_dutyCycle;
    }

    /**
     *
     * @param field_type $DutyCycle            
     */
    public function setDutyCycle ($DutyCycle)
    {
        $this->_dutyCycle = $DutyCycle;
        return $this;
    }

    /**
     *
     * @return the $PPMBlack
     */
    public function getPPMBlack ()
    {
        if (! isset($this->_ppmBlack))
        {
            
            $this->_ppmBlack = null;
        }
        return $this->_ppmBlack;
    }

    /**
     *
     * @param field_type $PPMBlack            
     */
    public function setPPMBlack ($PPMBlack)
    {
        $this->_ppmBlack = $PPMBlack;
        return $this;
    }

    /**
     *
     * @return the $PPMColor
     */
    public function getPPMColor ()
    {
        if (! isset($this->_ppmColor))
        {
            
            $this->_ppmColor = null;
        }
        return $this->_ppmColor;
    }

    /**
     *
     * @param field_type $PPMColor            
     */
    public function setPPMColor ($PPMColor)
    {
        $this->_ppmColor = $PPMColor;
        return $this;
    }

    /**
     *
     * @return the $ServiceCostPerPage
     */
    public function getServiceCostPerPage ()
    {
        if (! isset($this->_serviceCostPerPage))
        {
            
            $this->_serviceCostPerPage = null;
        }
        return $this->_serviceCostPerPage;
    }

    /**
     *
     * @param field_type $ServiceCostPerPage            
     */
    public function setServiceCostPerPage ($ServiceCostPerPage)
    {
        $this->_serviceCostPerPage = $ServiceCostPerPage;
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
        if (! isset($this->_isLeased))
        {
            
            $this->_isLeased = null;
        }
        return $this->_isLeased;
    }

    /**
     *
     * @param field_type $IsLeased            
     */
    public function setIsLeased ($IsLeased)
    {
        $this->_isLeased = $IsLeased;
        return $this;
    }

    /**
     *
     * @return the $LeasedTonerYield
     */
    public function getLeasedTonerYield ()
    {
        if (! isset($this->_leasedTonerYield))
        {
            $this->_leasedTonerYield = null;
        }
        return $this->_leasedTonerYield;
    }

    /**
     *
     * @param field_type $LeasedTonerYield            
     */
    public function setLeasedTonerYield ($LeasedTonerYield)
    {
        $this->_leasedTonerYield = $LeasedTonerYield;
        return $this;
    }

    /**
     *
     * @return the $HasValidMonoGrossMarginToners
     */
    public function getHasValidMonoGrossMarginToners ()
    {
        if (! isset($this->_hasValidMonoGrossMarginToners))
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
            
            $this->_hasValidMonoGrossMarginToners = $usesAllValidToners;
        }
        return $this->_hasValidMonoGrossMarginToners;
    }

    /**
     *
     * @param field_type $HasValidMonoGrossMarginToners            
     */
    public function setHasValidMonoGrossMarginToners ($HasValidMonoGrossMarginToners)
    {
        $this->_hasValidMonoGrossMarginToners = $HasValidMonoGrossMarginToners;
        return $this;
    }

    /**
     *
     * @return the $HasValidColorGrossMarginToners
     */
    public function getHasValidColorGrossMarginToners ()
    {
        if (! isset($this->_hasValidColorGrossMarginToners))
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
            
            $this->_hasValidColorGrossMarginToners = $usesAllValidToners;
        }
        return $this->_hasValidColorGrossMarginToners;
    }

    /**
     *
     * @param field_type $HasValidColorGrossMarginToners            
     */
    public function setHasValidColorGrossMarginToners ($HasValidColorGrossMarginToners)
    {
        $this->_hasValidColorGrossMarginToners = $HasValidColorGrossMarginToners;
        return $this;
    }

    /**
     *
     * @return the $TonersForAssessment
     */
    public function getTonersForAssessment ()
    {
        if (! isset($this->_tonersForAssessment))
        {
            $toners = array ();
            foreach ( $this->getRequiredTonerColors() as $tonerColor )
            {
                $toners [$tonerColor] = $this->getCheapestToner($tonerColor, self::getPricingConfig());
            }
            $this->_tonersForAssessment = $toners;
        }
        return $this->_tonersForAssessment;
    }

    /**
     *
     * @return the $TonersForGrossMargin
     */
    public function getTonersForGrossMargin ()
    {
        if (! isset($this->_tonersForGrossMargin))
        {
            $toners = array ();
            foreach ( $this->getRequiredTonerColors() as $tonerColor )
            {
                $toners [$tonerColor] = $this->getCheapestToner($tonerColor, self::getGrossMarginPricingConfig());
            }
            $this->_tonersForGrossMargin = $toners;
        }
        return $this->_tonersForGrossMargin;
    }

    /**
     *
     * @param field_type $TonersForAssessment            
     */
    public function setTonersForAssessment ($TonersForAssessment)
    {
        $this->_tonersForAssessment = $TonersForAssessment;
        return $this;
    }

    /**
     *
     * @param field_type $TonersForGrossMargin            
     */
    public function setTonersForGrossMargin ($TonersForGrossMargin)
    {
        $this->_tonersForGrossMargin = $TonersForGrossMargin;
        return $this;
    }

    /**
     *
     * @return the $RequiredTonerColors
     */
    public function getRequiredTonerColors ()
    {
        if (! isset($this->_requiredTonerColors))
        {
            $this->_requiredTonerColors = Proposalgen_Model_TonerConfig::getRequiredTonersForTonerConfig($this->getTonerConfigId());
        }
        return $this->_requiredTonerColors;
    }

    /**
     *
     * @param field_type $RequiredTonerColors            
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
        return "{$this->getManufacturer()->getDisplayName()} {$this->getPrinterModel()}";
    }
}