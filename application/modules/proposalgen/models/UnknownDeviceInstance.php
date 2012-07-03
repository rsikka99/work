<?php

/**
 * Class Proposalgen_Model_UnknownDeviceInstance
 *
 * @author "Lee Robert"
 */
class Proposalgen_Model_UnknownDeviceInstance extends Tangent_Model_Abstract
{
    protected $UnknownDeviceInstanceId;
    protected $UserId;
    protected $ReportId;
    protected $UploadDataCollectorRowId;
    protected $PrinterModelid;
    protected $MpsMonitorStartdate;
    protected $MpsMonitorEnddate;
    protected $MpsDiscoveryDate;
    protected $InstallDate;
    protected $DeviceManufacturer;
    protected $PrinterModel;
    protected $PrinterSerialNumber;
    protected $TonerConfig;
    protected $IsCopier;
    protected $IsFax;
    protected $IsDuplex;
    protected $IsScanner;
    protected $JitSuppliesSupported;
    protected $WattsPowerNormal;
    protected $WattsPowerIdle;
    protected $DevicePrice;
    protected $LaunchDate;
    protected $DateCreated;
    protected $BlackTonerSKU;
    protected $BlackTonerPrice;
    protected $BlackTonerYield;
    protected $CyanTonerSKU;
    protected $CyanTonerPrice;
    protected $CyanTonerYield;
    protected $MagentaTonerSKU;
    protected $MagentaTonerPrice;
    protected $MagentaTonerYield;
    protected $YellowTonerSKU;
    protected $YellowTonerPrice;
    protected $YellowTonerYield;
    protected $ThreeColorTonerSKU;
    protected $ThreeColorTonerPrice;
    protected $ThreeColorTonerYield;
    protected $FourColorTonerSKU;
    protected $FourColorTonerPrice;
    protected $FourColorTonerYield;
    protected $BlackCompSKU;
    protected $BlackCompPrice;
    protected $BlackCompYield;
    protected $CyanCompSKU;
    protected $CyanCompPrice;
    protected $CyanCompYield;
    protected $MagentaCompSKU;
    protected $MagentaCompPrice;
    protected $MagentaCompYield;
    protected $YellowCompSKU;
    protected $YellowCompPrice;
    protected $YellowCompYield;
    protected $ThreeColorCompSKU;
    protected $ThreeColorCompPrice;
    protected $ThreeColorCompYield;
    protected $FourColorCompSKU;
    protected $FourColorCompPrice;
    protected $FourColorCompYield;
    protected $StartMeterLife;
    protected $EndMeterLife;
    protected $StartMeterBlack;
    protected $EndMeterBlack;
    protected $StartMeterColor;
    protected $EndMeterColor;
    protected $StartMeterPrintblack;
    protected $EndMeterPrintblack;
    protected $StartMeterPrintcolor;
    protected $EndMeterPrintcolor;
    protected $StartMeterCopyblack;
    protected $EndMeterCopyblack;
    protected $StartMeterCopycolor;
    protected $EndMeterCopycolor;
    protected $StartMeterFax;
    protected $EndMeterFax;
    protected $StartMeterScan;
    protected $EndMeterScan;
    protected $IsExcluded;
    protected $IsLeased;
    protected $IpAddress;
    protected $ServiceCostPerPage;

    /**
     *
     * @return the $Id
     */
    public function getId ()
    {
        if (! isset($this->Id))
        {
            
            $this->Id = null;
        }
        return $this->Id;
    }

    /**
     *
     * @param field_type $Id      
     */
    public function setId ($Id)
    {
        $this->Id = $Id;
        return $this;
    }

    /**
     *
     * @return the $UserId
     */
    public function getUserId ()
    {
        if (! isset($this->UserId))
        {
            
            $this->UserId = null;
        }
        return $this->UserId;
    }

    /**
     *
     * @param field_type $UserId            
     */
    public function setUserId ($UserId)
    {
        $this->UserId = $UserId;
        return $this;
    }

    /**
     *
     * @return the $ReportId
     */
    public function getReportId ()
    {
        if (! isset($this->ReportId))
        {
            
            $this->ReportId = null;
        }
        return $this->ReportId;
    }

    /**
     *
     * @param field_type $ReportId            
     */
    public function setReportId ($ReportId)
    {
        $this->ReportId = $ReportId;
        return $this;
    }

    /**
     *
     * @return the $UploadDataCollectorRowId
     */
    public function getUploadDataCollectorRowId ()
    {
        if (! isset($this->UploadDataCollectorRowId))
        {
            
            $this->UploadDataCollectorRowId = null;
        }
        return $this->UploadDataCollectorRowId;
    }

    /**
     *
     * @param field_type $UploadDataCollectorRowId            
     */
    public function setUploadDataCollectorRowId ($UploadDataCollectorRowId)
    {
        $this->UploadDataCollectorRowId = $UploadDataCollectorRowId;
        return $this;
    }

    /**
     *
     * @return the $PrinterModelid
     */
    public function getPrinterModelid ()
    {
        if (! isset($this->PrinterModelid))
        {
            
            $this->PrinterModelid = null;
        }
        return $this->PrinterModelid;
    }

    /**
     *
     * @param field_type $PrinterModelid            
     */
    public function setPrinterModelid ($PrinterModelid)
    {
        $this->PrinterModelid = $PrinterModelid;
        return $this;
    }

    /**
     *
     * @return the $MpsMonitorStartdate
     */
    public function getMpsMonitorStartdate ()
    {
        if (! isset($this->MpsMonitorStartdate))
        {
            
            $this->MpsMonitorStartdate = null;
        }
        return $this->MpsMonitorStartdate;
    }

    /**
     *
     * @param field_type $MpsMonitorStartdate            
     */
    public function setMpsMonitorStartdate ($MpsMonitorStartdate)
    {
        $this->MpsMonitorStartdate = $MpsMonitorStartdate;
        return $this;
    }

    /**
     *
     * @return the $MpsMonitorEnddate
     */
    public function getMpsMonitorEnddate ()
    {
        if (! isset($this->MpsMonitorEnddate))
        {
            
            $this->MpsMonitorEnddate = null;
        }
        return $this->MpsMonitorEnddate;
    }

    /**
     *
     * @param field_type $MpsMonitorEnddate            
     */
    public function setMpsMonitorEnddate ($MpsMonitorEnddate)
    {
        $this->MpsMonitorEnddate = $MpsMonitorEnddate;
        return $this;
    }

    /**
     *
     * @return the $MpsDiscoveryDate
     */
    public function getMpsDiscoveryDate ()
    {
        if (! isset($this->MpsDiscoveryDate))
        {
            
            $this->MpsDiscoveryDate = null;
        }
        return $this->MpsDiscoveryDate;
    }

    /**
     *
     * @param field_type $MpsDiscoveryDate            
     */
    public function setMpsDiscoveryDate ($MpsDiscoveryDate)
    {
        $this->MpsDiscoveryDate = $MpsDiscoveryDate;
        return $this;
    }

    /**
     *
     * @return the $InstallDate
     */
    public function getInstallDate ()
    {
        if (! isset($this->InstallDate))
        {
            
            $this->InstallDate = null;
        }
        return $this->InstallDate;
    }

    /**
     *
     * @param field_type $InstallDate            
     */
    public function setInstallDate ($InstallDate)
    {
        $this->InstallDate = $InstallDate;
        return $this;
    }

    /**
     *
     * @return the $DeviceManufacturer
     */
    public function getDeviceManufacturer ()
    {
        if (! isset($this->DeviceManufacturer))
        {
            
            $this->DeviceManufacturer = null;
        }
        return $this->DeviceManufacturer;
    }

    /**
     *
     * @param field_type $DeviceManufacturer            
     */
    public function setDeviceManufacturer ($DeviceManufacturer)
    {
        $this->DeviceManufacturer = $DeviceManufacturer;
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
     * @return the $PrinterSerialNumber
     */
    public function getPrinterSerialNumber ()
    {
        if (! isset($this->PrinterSerialNumber))
        {
            
            $this->PrinterSerialNumber = null;
        }
        return $this->PrinterSerialNumber;
    }

    /**
     *
     * @param field_type $PrinterSerialNumber            
     */
    public function setPrinterSerialNumber ($PrinterSerialNumber)
    {
        $this->PrinterSerialNumber = $PrinterSerialNumber;
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
            
            $this->TonerConfig = null;
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
     * @return the $JitSuppliesSupported
     */
    public function getJitSuppliesSupported ()
    {
        if (! isset($this->JitSuppliesSupported))
        {
            
            $this->JitSuppliesSupported = null;
        }
        return $this->JitSuppliesSupported;
    }

    /**
     *
     * @param field_type $JitSuppliesSupported            
     */
    public function setJitSuppliesSupported ($JitSuppliesSupported)
    {
        $this->JitSuppliesSupported = $JitSuppliesSupported;
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
            
            $this->WattsPowerIdle = null;
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
     * @return the $BlackTonerSKU
     */
    public function getBlackTonerSKU ()
    {
        if (! isset($this->BlackTonerSKU))
        {
            
            $this->BlackTonerSKU = null;
        }
        return $this->BlackTonerSKU;
    }

    /**
     *
     * @param field_type $BlackTonerSKU            
     */
    public function setBlackTonerSKU ($BlackTonerSKU)
    {
        $this->BlackTonerSKU = $BlackTonerSKU;
        return $this;
    }

    /**
     *
     * @return the $BlackTonerPrice
     */
    public function getBlackTonerPrice ()
    {
        if (! isset($this->BlackTonerPrice))
        {
            
            $this->BlackTonerPrice = null;
        }
        return $this->BlackTonerPrice;
    }

    /**
     *
     * @param field_type $BlackTonerPrice            
     */
    public function setBlackTonerPrice ($BlackTonerPrice)
    {
        $this->BlackTonerPrice = $BlackTonerPrice;
        return $this;
    }

    /**
     *
     * @return the $BlackTonerYield
     */
    public function getBlackTonerYield ()
    {
        if (! isset($this->BlackTonerYield))
        {
            
            $this->BlackTonerYield = null;
        }
        return $this->BlackTonerYield;
    }

    /**
     *
     * @param field_type $BlackTonerYield            
     */
    public function setBlackTonerYield ($BlackTonerYield)
    {
        $this->BlackTonerYield = $BlackTonerYield;
        return $this;
    }

    /**
     *
     * @return the $CyanTonerSKU
     */
    public function getCyanTonerSKU ()
    {
        if (! isset($this->CyanTonerSKU))
        {
            
            $this->CyanTonerSKU = null;
        }
        return $this->CyanTonerSKU;
    }

    /**
     *
     * @param field_type $CyanTonerSKU            
     */
    public function setCyanTonerSKU ($CyanTonerSKU)
    {
        $this->CyanTonerSKU = $CyanTonerSKU;
        return $this;
    }

    /**
     *
     * @return the $CyanTonerPrice
     */
    public function getCyanTonerPrice ()
    {
        if (! isset($this->CyanTonerPrice))
        {
            
            $this->CyanTonerPrice = null;
        }
        return $this->CyanTonerPrice;
    }

    /**
     *
     * @param field_type $CyanTonerPrice            
     */
    public function setCyanTonerPrice ($CyanTonerPrice)
    {
        $this->CyanTonerPrice = $CyanTonerPrice;
        return $this;
    }

    /**
     *
     * @return the $CyanTonerYield
     */
    public function getCyanTonerYield ()
    {
        if (! isset($this->CyanTonerYield))
        {
            
            $this->CyanTonerYield = null;
        }
        return $this->CyanTonerYield;
    }

    /**
     *
     * @param field_type $CyanTonerYield            
     */
    public function setCyanTonerYield ($CyanTonerYield)
    {
        $this->CyanTonerYield = $CyanTonerYield;
        return $this;
    }

    /**
     *
     * @return the $MagentaTonerSKU
     */
    public function getMagentaTonerSKU ()
    {
        if (! isset($this->MagentaTonerSKU))
        {
            
            $this->MagentaTonerSKU = null;
        }
        return $this->MagentaTonerSKU;
    }

    /**
     *
     * @param field_type $MagentaTonerSKU            
     */
    public function setMagentaTonerSKU ($MagentaTonerSKU)
    {
        $this->MagentaTonerSKU = $MagentaTonerSKU;
        return $this;
    }

    /**
     *
     * @return the $MagentaTonerPrice
     */
    public function getMagentaTonerPrice ()
    {
        if (! isset($this->MagentaTonerPrice))
        {
            
            $this->MagentaTonerPrice = null;
        }
        return $this->MagentaTonerPrice;
    }

    /**
     *
     * @param field_type $MagentaTonerPrice            
     */
    public function setMagentaTonerPrice ($MagentaTonerPrice)
    {
        $this->MagentaTonerPrice = $MagentaTonerPrice;
        return $this;
    }

    /**
     *
     * @return the $MagentaTonerYield
     */
    public function getMagentaTonerYield ()
    {
        if (! isset($this->MagentaTonerYield))
        {
            
            $this->MagentaTonerYield = null;
        }
        return $this->MagentaTonerYield;
    }

    /**
     *
     * @param field_type $MagentaTonerYield            
     */
    public function setMagentaTonerYield ($MagentaTonerYield)
    {
        $this->MagentaTonerYield = $MagentaTonerYield;
        return $this;
    }

    /**
     *
     * @return the $YellowTonerSKU
     */
    public function getYellowTonerSKU ()
    {
        if (! isset($this->YellowTonerSKU))
        {
            
            $this->YellowTonerSKU = null;
        }
        return $this->YellowTonerSKU;
    }

    /**
     *
     * @param field_type $YellowTonerSKU            
     */
    public function setYellowTonerSKU ($YellowTonerSKU)
    {
        $this->YellowTonerSKU = $YellowTonerSKU;
        return $this;
    }

    /**
     *
     * @return the $YellowTonerPrice
     */
    public function getYellowTonerPrice ()
    {
        if (! isset($this->YellowTonerPrice))
        {
            
            $this->YellowTonerPrice = null;
        }
        return $this->YellowTonerPrice;
    }

    /**
     *
     * @param field_type $YellowTonerPrice            
     */
    public function setYellowTonerPrice ($YellowTonerPrice)
    {
        $this->YellowTonerPrice = $YellowTonerPrice;
        return $this;
    }

    /**
     *
     * @return the $YellowTonerYield
     */
    public function getYellowTonerYield ()
    {
        if (! isset($this->YellowTonerYield))
        {
            
            $this->YellowTonerYield = null;
        }
        return $this->YellowTonerYield;
    }

    /**
     *
     * @param field_type $YellowTonerYield            
     */
    public function setYellowTonerYield ($YellowTonerYield)
    {
        $this->YellowTonerYield = $YellowTonerYield;
        return $this;
    }

    /**
     *
     * @return the $ThreeColorTonerSKU
     */
    public function getThreeColorTonerSKU ()
    {
        if (! isset($this->ThreeColorTonerSKU))
        {
            
            $this->ThreeColorTonerSKU = null;
        }
        return $this->ThreeColorTonerSKU;
    }

    /**
     *
     * @param field_type $ThreeColorTonerSKU            
     */
    public function setThreeColorTonerSKU ($ThreeColorTonerSKU)
    {
        $this->ThreeColorTonerSKU = $ThreeColorTonerSKU;
        return $this;
    }

    /**
     *
     * @return the $ThreeColorTonerPrice
     */
    public function getThreeColorTonerPrice ()
    {
        if (! isset($this->ThreeColorTonerPrice))
        {
            
            $this->ThreeColorTonerPrice = null;
        }
        return $this->ThreeColorTonerPrice;
    }

    /**
     *
     * @param field_type $ThreeColorTonerPrice            
     */
    public function setThreeColorTonerPrice ($ThreeColorTonerPrice)
    {
        $this->ThreeColorTonerPrice = $ThreeColorTonerPrice;
        return $this;
    }

    /**
     *
     * @return the $ThreeColorTonerYield
     */
    public function getThreeColorTonerYield ()
    {
        if (! isset($this->ThreeColorTonerYield))
        {
            
            $this->ThreeColorTonerYield = null;
        }
        return $this->ThreeColorTonerYield;
    }

    /**
     *
     * @param field_type $ThreeColorTonerYield            
     */
    public function setThreeColorTonerYield ($ThreeColorTonerYield)
    {
        $this->ThreeColorTonerYield = $ThreeColorTonerYield;
        return $this;
    }

    /**
     *
     * @return the $FourColorTonerSKU
     */
    public function getFourColorTonerSKU ()
    {
        if (! isset($this->FourColorTonerSKU))
        {
            
            $this->FourColorTonerSKU = null;
        }
        return $this->FourColorTonerSKU;
    }

    /**
     *
     * @param field_type $FourColorTonerSKU            
     */
    public function setFourColorTonerSKU ($FourColorTonerSKU)
    {
        $this->FourColorTonerSKU = $FourColorTonerSKU;
        return $this;
    }

    /**
     *
     * @return the $FourColorTonerPrice
     */
    public function getFourColorTonerPrice ()
    {
        if (! isset($this->FourColorTonerPrice))
        {
            
            $this->FourColorTonerPrice = null;
        }
        return $this->FourColorTonerPrice;
    }

    /**
     *
     * @param field_type $FourColorTonerPrice            
     */
    public function setFourColorTonerPrice ($FourColorTonerPrice)
    {
        $this->FourColorTonerPrice = $FourColorTonerPrice;
        return $this;
    }

    /**
     *
     * @return the $FourColorTonerYield
     */
    public function getFourColorTonerYield ()
    {
        if (! isset($this->FourColorTonerYield))
        {
            
            $this->FourColorTonerYield = null;
        }
        return $this->FourColorTonerYield;
    }

    /**
     *
     * @param field_type $FourColorTonerYield            
     */
    public function setFourColorTonerYield ($FourColorTonerYield)
    {
        $this->FourColorTonerYield = $FourColorTonerYield;
        return $this;
    }

    /**
     *
     * @return the $BlackCompSKU
     */
    public function getBlackCompSKU ()
    {
        if (! isset($this->BlackCompSKU))
        {
            
            $this->BlackCompSKU = null;
        }
        return $this->BlackCompSKU;
    }

    /**
     *
     * @param field_type $BlackCompSKU            
     */
    public function setBlackCompSKU ($BlackCompSKU)
    {
        $this->BlackCompSKU = $BlackCompSKU;
        return $this;
    }

    /**
     *
     * @return the $BlackCompPrice
     */
    public function getBlackCompPrice ()
    {
        if (! isset($this->BlackCompPrice))
        {
            
            $this->BlackCompPrice = null;
        }
        return $this->BlackCompPrice;
    }

    /**
     *
     * @param field_type $BlackCompPrice            
     */
    public function setBlackCompPrice ($BlackCompPrice)
    {
        $this->BlackCompPrice = $BlackCompPrice;
        return $this;
    }

    /**
     *
     * @return the $BlackCompYield
     */
    public function getBlackCompYield ()
    {
        if (! isset($this->BlackCompYield))
        {
            
            $this->BlackCompYield = null;
        }
        return $this->BlackCompYield;
    }

    /**
     *
     * @param field_type $BlackCompYield            
     */
    public function setBlackCompYield ($BlackCompYield)
    {
        $this->BlackCompYield = $BlackCompYield;
        return $this;
    }

    /**
     *
     * @return the $CyanCompSKU
     */
    public function getCyanCompSKU ()
    {
        if (! isset($this->CyanCompSKU))
        {
            
            $this->CyanCompSKU = null;
        }
        return $this->CyanCompSKU;
    }

    /**
     *
     * @param field_type $CyanCompSKU            
     */
    public function setCyanCompSKU ($CyanCompSKU)
    {
        $this->CyanCompSKU = $CyanCompSKU;
        return $this;
    }

    /**
     *
     * @return the $CyanCompPrice
     */
    public function getCyanCompPrice ()
    {
        if (! isset($this->CyanCompPrice))
        {
            
            $this->CyanCompPrice = null;
        }
        return $this->CyanCompPrice;
    }

    /**
     *
     * @param field_type $CyanCompPrice            
     */
    public function setCyanCompPrice ($CyanCompPrice)
    {
        $this->CyanCompPrice = $CyanCompPrice;
        return $this;
    }

    /**
     *
     * @return the $CyanCompYield
     */
    public function getCyanCompYield ()
    {
        if (! isset($this->CyanCompYield))
        {
            
            $this->CyanCompYield = null;
        }
        return $this->CyanCompYield;
    }

    /**
     *
     * @param field_type $CyanCompYield            
     */
    public function setCyanCompYield ($CyanCompYield)
    {
        $this->CyanCompYield = $CyanCompYield;
        return $this;
    }

    /**
     *
     * @return the $MagentaCompSKU
     */
    public function getMagentaCompSKU ()
    {
        if (! isset($this->MagentaCompSKU))
        {
            
            $this->MagentaCompSKU = null;
        }
        return $this->MagentaCompSKU;
    }

    /**
     *
     * @param field_type $MagentaCompSKU            
     */
    public function setMagentaCompSKU ($MagentaCompSKU)
    {
        $this->MagentaCompSKU = $MagentaCompSKU;
        return $this;
    }

    /**
     *
     * @return the $MagentaCompPrice
     */
    public function getMagentaCompPrice ()
    {
        if (! isset($this->MagentaCompPrice))
        {
            
            $this->MagentaCompPrice = null;
        }
        return $this->MagentaCompPrice;
    }

    /**
     *
     * @param field_type $MagentaCompPrice            
     */
    public function setMagentaCompPrice ($MagentaCompPrice)
    {
        $this->MagentaCompPrice = $MagentaCompPrice;
        return $this;
    }

    /**
     *
     * @return the $MagentaCompYield
     */
    public function getMagentaCompYield ()
    {
        if (! isset($this->MagentaCompYield))
        {
            
            $this->MagentaCompYield = null;
        }
        return $this->MagentaCompYield;
    }

    /**
     *
     * @param field_type $MagentaCompYield            
     */
    public function setMagentaCompYield ($MagentaCompYield)
    {
        $this->MagentaCompYield = $MagentaCompYield;
        return $this;
    }

    /**
     *
     * @return the $YellowCompSKU
     */
    public function getYellowCompSKU ()
    {
        if (! isset($this->YellowCompSKU))
        {
            
            $this->YellowCompSKU = null;
        }
        return $this->YellowCompSKU;
    }

    /**
     *
     * @param field_type $YellowCompSKU            
     */
    public function setYellowCompSKU ($YellowCompSKU)
    {
        $this->YellowCompSKU = $YellowCompSKU;
        return $this;
    }

    /**
     *
     * @return the $YellowCompPrice
     */
    public function getYellowCompPrice ()
    {
        if (! isset($this->YellowCompPrice))
        {
            
            $this->YellowCompPrice = null;
        }
        return $this->YellowCompPrice;
    }

    /**
     *
     * @param field_type $YellowCompPrice            
     */
    public function setYellowCompPrice ($YellowCompPrice)
    {
        $this->YellowCompPrice = $YellowCompPrice;
        return $this;
    }

    /**
     *
     * @return the $YellowCompYield
     */
    public function getYellowCompYield ()
    {
        if (! isset($this->YellowCompYield))
        {
            
            $this->YellowCompYield = null;
        }
        return $this->YellowCompYield;
    }

    /**
     *
     * @param field_type $YellowCompYield            
     */
    public function setYellowCompYield ($YellowCompYield)
    {
        $this->YellowCompYield = $YellowCompYield;
        return $this;
    }

    /**
     *
     * @return the $ThreeColorCompSKU
     */
    public function getThreeColorCompSKU ()
    {
        if (! isset($this->ThreeColorCompSKU))
        {
            
            $this->ThreeColorCompSKU = null;
        }
        return $this->ThreeColorCompSKU;
    }

    /**
     *
     * @param field_type $ThreeColorCompSKU            
     */
    public function setThreeColorCompSKU ($ThreeColorCompSKU)
    {
        $this->ThreeColorCompSKU = $ThreeColorCompSKU;
        return $this;
    }

    /**
     *
     * @return the $ThreeColorCompPrice
     */
    public function getThreeColorCompPrice ()
    {
        if (! isset($this->ThreeColorCompPrice))
        {
            
            $this->ThreeColorCompPrice = null;
        }
        return $this->ThreeColorCompPrice;
    }

    /**
     *
     * @param field_type $ThreeColorCompPrice            
     */
    public function setThreeColorCompPrice ($ThreeColorCompPrice)
    {
        $this->ThreeColorCompPrice = $ThreeColorCompPrice;
        return $this;
    }

    /**
     *
     * @return the $ThreeColorCompYield
     */
    public function getThreeColorCompYield ()
    {
        if (! isset($this->ThreeColorCompYield))
        {
            
            $this->ThreeColorCompYield = null;
        }
        return $this->ThreeColorCompYield;
    }

    /**
     *
     * @param field_type $ThreeColorCompYield            
     */
    public function setThreeColorCompYield ($ThreeColorCompYield)
    {
        $this->ThreeColorCompYield = $ThreeColorCompYield;
        return $this;
    }

    /**
     *
     * @return the $FourColorCompSKU
     */
    public function getFourColorCompSKU ()
    {
        if (! isset($this->FourColorCompSKU))
        {
            
            $this->FourColorCompSKU = null;
        }
        return $this->FourColorCompSKU;
    }

    /**
     *
     * @param field_type $FourColorCompSKU            
     */
    public function setFourColorCompSKU ($FourColorCompSKU)
    {
        $this->FourColorCompSKU = $FourColorCompSKU;
        return $this;
    }

    /**
     *
     * @return the $FourColorCompPrice
     */
    public function getFourColorCompPrice ()
    {
        if (! isset($this->FourColorCompPrice))
        {
            
            $this->FourColorCompPrice = null;
        }
        return $this->FourColorCompPrice;
    }

    /**
     *
     * @param field_type $FourColorCompPrice            
     */
    public function setFourColorCompPrice ($FourColorCompPrice)
    {
        $this->FourColorCompPrice = $FourColorCompPrice;
        return $this;
    }

    /**
     *
     * @return the $FourColorCompYield
     */
    public function getFourColorCompYield ()
    {
        if (! isset($this->FourColorCompYield))
        {
            
            $this->FourColorCompYield = null;
        }
        return $this->FourColorCompYield;
    }

    /**
     *
     * @param field_type $FourColorCompYield            
     */
    public function setFourColorCompYield ($FourColorCompYield)
    {
        $this->FourColorCompYield = $FourColorCompYield;
        return $this;
    }

    /**
     *
     * @return the $StartMeterLife
     */
    public function getStartMeterLife ()
    {
        if (! isset($this->StartMeterLife))
        {
            
            $this->StartMeterLife = null;
        }
        return $this->StartMeterLife;
    }

    /**
     *
     * @param field_type $StartMeterLife            
     */
    public function setStartMeterLife ($StartMeterLife)
    {
        $this->StartMeterLife = $StartMeterLife;
        return $this;
    }

    /**
     *
     * @return the $EndMeterLife
     */
    public function getEndMeterLife ()
    {
        if (! isset($this->EndMeterLife))
        {
            
            $this->EndMeterLife = null;
        }
        return $this->EndMeterLife;
    }

    /**
     *
     * @param field_type $EndMeterLife            
     */
    public function setEndMeterLife ($EndMeterLife)
    {
        $this->EndMeterLife = $EndMeterLife;
        return $this;
    }

    /**
     *
     * @return the $StartMeterBlack
     */
    public function getStartMeterBlack ()
    {
        if (! isset($this->StartMeterBlack))
        {
            
            $this->StartMeterBlack = null;
        }
        return $this->StartMeterBlack;
    }

    /**
     *
     * @param field_type $StartMeterBlack            
     */
    public function setStartMeterBlack ($StartMeterBlack)
    {
        $this->StartMeterBlack = $StartMeterBlack;
        return $this;
    }

    /**
     *
     * @return the $EndMeterBlack
     */
    public function getEndMeterBlack ()
    {
        if (! isset($this->EndMeterBlack))
        {
            
            $this->EndMeterBlack = null;
        }
        return $this->EndMeterBlack;
    }

    /**
     *
     * @param field_type $EndMeterBlack            
     */
    public function setEndMeterBlack ($EndMeterBlack)
    {
        $this->EndMeterBlack = $EndMeterBlack;
        return $this;
    }

    /**
     *
     * @return the $StartMeterColor
     */
    public function getStartMeterColor ()
    {
        if (! isset($this->StartMeterColor))
        {
            
            $this->StartMeterColor = null;
        }
        return $this->StartMeterColor;
    }

    /**
     *
     * @param field_type $StartMeterColor            
     */
    public function setStartMeterColor ($StartMeterColor)
    {
        $this->StartMeterColor = $StartMeterColor;
        return $this;
    }

    /**
     *
     * @return the $EndMeterColor
     */
    public function getEndMeterColor ()
    {
        if (! isset($this->EndMeterColor))
        {
            
            $this->EndMeterColor = null;
        }
        return $this->EndMeterColor;
    }

    /**
     *
     * @param field_type $EndMeterColor            
     */
    public function setEndMeterColor ($EndMeterColor)
    {
        $this->EndMeterColor = $EndMeterColor;
        return $this;
    }

    /**
     *
     * @return the $StartMeterPrintblack
     */
    public function getStartMeterPrintblack ()
    {
        if (! isset($this->StartMeterPrintblack))
        {
            
            $this->StartMeterPrintblack = null;
        }
        return $this->StartMeterPrintblack;
    }

    /**
     *
     * @param field_type $StartMeterPrintblack            
     */
    public function setStartMeterPrintblack ($StartMeterPrintblack)
    {
        $this->StartMeterPrintblack = $StartMeterPrintblack;
        return $this;
    }

    /**
     *
     * @return the $EndMeterPrintblack
     */
    public function getEndMeterPrintblack ()
    {
        if (! isset($this->EndMeterPrintblack))
        {
            
            $this->EndMeterPrintblack = null;
        }
        return $this->EndMeterPrintblack;
    }

    /**
     *
     * @param field_type $EndMeterPrintblack            
     */
    public function setEndMeterPrintblack ($EndMeterPrintblack)
    {
        $this->EndMeterPrintblack = $EndMeterPrintblack;
        return $this;
    }

    /**
     *
     * @return the $StartMeterPrintcolor
     */
    public function getStartMeterPrintcolor ()
    {
        if (! isset($this->StartMeterPrintcolor))
        {
            
            $this->StartMeterPrintcolor = null;
        }
        return $this->StartMeterPrintcolor;
    }

    /**
     *
     * @param field_type $StartMeterPrintcolor            
     */
    public function setStartMeterPrintcolor ($StartMeterPrintcolor)
    {
        $this->StartMeterPrintcolor = $StartMeterPrintcolor;
        return $this;
    }

    /**
     *
     * @return the $EndMeterPrintcolor
     */
    public function getEndMeterPrintcolor ()
    {
        if (! isset($this->EndMeterPrintcolor))
        {
            
            $this->EndMeterPrintcolor = null;
        }
        return $this->EndMeterPrintcolor;
    }

    /**
     *
     * @param field_type $EndMeterPrintcolor            
     */
    public function setEndMeterPrintcolor ($EndMeterPrintcolor)
    {
        $this->EndMeterPrintcolor = $EndMeterPrintcolor;
        return $this;
    }

    /**
     *
     * @return the $StartMeterCopyblack
     */
    public function getStartMeterCopyblack ()
    {
        if (! isset($this->StartMeterCopyblack))
        {
            
            $this->StartMeterCopyblack = null;
        }
        return $this->StartMeterCopyblack;
    }

    /**
     *
     * @param field_type $StartMeterCopyblack            
     */
    public function setStartMeterCopyblack ($StartMeterCopyblack)
    {
        $this->StartMeterCopyblack = $StartMeterCopyblack;
        return $this;
    }

    /**
     *
     * @return the $EndMeterCopyblack
     */
    public function getEndMeterCopyblack ()
    {
        if (! isset($this->EndMeterCopyblack))
        {
            
            $this->EndMeterCopyblack = null;
        }
        return $this->EndMeterCopyblack;
    }

    /**
     *
     * @param field_type $EndMeterCopyblack            
     */
    public function setEndMeterCopyblack ($EndMeterCopyblack)
    {
        $this->EndMeterCopyblack = $EndMeterCopyblack;
        return $this;
    }

    /**
     *
     * @return the $StartMeterCopycolor
     */
    public function getStartMeterCopycolor ()
    {
        if (! isset($this->StartMeterCopycolor))
        {
            
            $this->StartMeterCopycolor = null;
        }
        return $this->StartMeterCopycolor;
    }

    /**
     *
     * @param field_type $StartMeterCopycolor            
     */
    public function setStartMeterCopycolor ($StartMeterCopycolor)
    {
        $this->StartMeterCopycolor = $StartMeterCopycolor;
        return $this;
    }

    /**
     *
     * @return the $EndMeterCopycolor
     */
    public function getEndMeterCopycolor ()
    {
        if (! isset($this->EndMeterCopycolor))
        {
            
            $this->EndMeterCopycolor = null;
        }
        return $this->EndMeterCopycolor;
    }

    /**
     *
     * @param field_type $EndMeterCopycolor            
     */
    public function setEndMeterCopycolor ($EndMeterCopycolor)
    {
        $this->EndMeterCopycolor = $EndMeterCopycolor;
        return $this;
    }

    /**
     *
     * @return the $StartMeterFax
     */
    public function getStartMeterFax ()
    {
        if (! isset($this->StartMeterFax))
        {
            
            $this->StartMeterFax = null;
        }
        return $this->StartMeterFax;
    }

    /**
     *
     * @param field_type $StartMeterFax            
     */
    public function setStartMeterFax ($StartMeterFax)
    {
        $this->StartMeterFax = $StartMeterFax;
        return $this;
    }

    /**
     *
     * @return the $EndMeterFax
     */
    public function getEndMeterFax ()
    {
        if (! isset($this->EndMeterFax))
        {
            
            $this->EndMeterFax = null;
        }
        return $this->EndMeterFax;
    }

    /**
     *
     * @param field_type $EndMeterFax            
     */
    public function setEndMeterFax ($EndMeterFax)
    {
        $this->EndMeterFax = $EndMeterFax;
        return $this;
    }

    /**
     *
     * @return the $StartMeterScan
     */
    public function getStartMeterScan ()
    {
        if (! isset($this->StartMeterScan))
        {
            
            $this->StartMeterScan = null;
        }
        return $this->StartMeterScan;
    }

    /**
     *
     * @param field_type $StartMeterScan            
     */
    public function setStartMeterScan ($StartMeterScan)
    {
        $this->StartMeterScan = $StartMeterScan;
        return $this;
    }

    /**
     *
     * @return the $EndMeterScan
     */
    public function getEndMeterScan ()
    {
        if (! isset($this->EndMeterScan))
        {
            
            $this->EndMeterScan = null;
        }
        return $this->EndMeterScan;
    }

    /**
     *
     * @param field_type $EndMeterScan            
     */
    public function setEndMeterScan ($EndMeterScan)
    {
        $this->EndMeterScan = $EndMeterScan;
        return $this;
    }

    /**
     *
     * @return the $IsExcluded
     */
    public function getIsExcluded ()
    {
        if (! isset($this->IsExcluded))
        {
            
            $this->IsExcluded = null;
        }
        return $this->IsExcluded;
    }

    /**
     *
     * @param field_type $IsExcluded            
     */
    public function setIsExcluded ($IsExcluded)
    {
        $this->IsExcluded = $IsExcluded;
        return $this;
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
     * @return the $IpAddress
     */
    public function getIpAddress ()
    {
        if (! isset($this->IpAddress))
        {
            $this->IpAddress = "";
        }
        return $this->IpAddress;
    }

    /**
     *
     * @param field_type $IpAddress            
     */
    public function setIpAddress ($IpAddress)
    {
        $this->IpAddress = $IpAddress;
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
}