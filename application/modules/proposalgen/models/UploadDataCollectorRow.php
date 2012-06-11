<?php

/**
 * Class Proposalgen_Model_UploadDataCollector
 *
 * @author "John Sadler"
 */
class Proposalgen_Model_UploadDataCollectorRow extends Tangent_Model_Abstract
{
    protected $UploadDataCollectorId;
    protected $ReportId;
    protected $DevicesPfId;
    protected $Startdate;
    protected $Enddate;
    protected $PrinterModelid;
    protected $IpAddress;
    protected $SerialNumber;
    protected $ModelName;
    protected $Manufacturer;
    protected $IsColor;
    protected $IsCopier;
    protected $IsScanner;
    protected $IsFax;
    protected $PpmBlack;
    protected $PpmColor;
    protected $DateIntroduction;
    protected $DateAdoption;
    protected $DiscoveryDate;
    protected $BlackProdCodeOem;
    protected $BlackYield;
    protected $BlackProdCostOem;
    protected $CyanProdCodeOem;
    protected $CyanYield;
    protected $CyanProdCostOem;
    protected $MagentaProdCodeOem;
    protected $MagentaYield;
    protected $MagentaProdCostOem;
    protected $YellowProdCodeOem;
    protected $YellowYield;
    protected $YellowProdCostOem;
    protected $WattsPowerNormal;
    protected $WattsPowerIdle;
    protected $DutyCycle;
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
    protected $StartMeterScan;
    protected $EndMeterScan;
    protected $StartMeterFax;
    protected $EndMeterFax;
    protected $InvalidData;
    protected $IsExcluded;
    
    // Extra Fields
    protected $ErrorMessage;

    /**
     * Constructor that takes a csv row and populates the model
     *
     * @param array $csvRow            
     */
    public function UploadDataCollector ($csvRow)
    {
        $manufacturerName = strtolower($csvRow ['manufacturer']);
    }

    /**
     * Validates the model
     */
    /**
     * Validates the information set in the model (assumed to be freshly populated from a CSV file.) The return value is
     * to be used with the
     *
     * @return boolean
     */
    public function IsValid ()
    {
        // Variables
        $minDeviceAgeInDays = 4;
        
        if (! $this->getModelName())
        {
            return false;
        }
        
        if (! $this->getManufacturer())
        {
            return false;
        }
        
        // Check Meters
        if (! $this->validateMeters())
        {
            return false;
        }
        
        // Device Age
        $startDate = new Zend_Date($this->getStartdate());
        $endDate = new Zend_Date($this->getEnddate());
        $discoveryDate = new Zend_Date($this->getDiscoveryDate());
        
        $interval1 = $startDate->diff($endDate);
        $interval2 = $discoveryDate->diff($endDate);
        
        $deviceAge = $interval1;
        
        // Use the smallest age that we have available
        if ($interval1->days > $interval2->days && ! $interval2->invert)
        {
            $deviceAge = $interval2;
        }
        
        if ($deviceAge->invert || $deviceAge->days < $minDeviceAgeInDays)
        {
            return false;
        }
        
        // If we get here, all is valid.
        return true;
    }

    /**
     * Validates all the meter values
     *
     * @return boolean
     */
    protected function validateMeters ()
    {
        // Get all the meters ready
        $StartMeter ["Black"] = $this->getStartMeterBlack();
        $StartMeter ["Color"] = $this->getStartMeterColor();
        $StartMeter ["Life"] = $this->getStartMeterLife();
        $StartMeter ["Printblack"] = $this->getStartMeterPrintblack();
        $StartMeter ["Printcolor"] = $this->getStartMeterPrintcolor();
        $StartMeter ["Copyblack"] = $this->getStartMeterCopyblack();
        $StartMeter ["Copycolor"] = $this->getStartMeterCopycolor();
        $StartMeter ["Fax"] = $this->getStartMeterFax();
        $StartMeter ["Scan"] = $this->getStartMeterScan();
        
        $EndMeter ["Black"] = $this->getEndMeterBlack();
        $EndMeter ["Color"] = $this->getEndMeterColor();
        $EndMeter ["Life"] = $this->getEndMeterLife();
        $EndMeter ["Printblack"] = $this->getEndMeterPrintblack();
        $EndMeter ["Printcolor"] = $this->getEndMeterPrintcolor();
        $EndMeter ["Copyblack"] = $this->getEndMeterCopyblack();
        $EndMeter ["Copycolor"] = $this->getEndMeterCopycolor();
        $EndMeter ["Fax"] = $this->getEndMeterFax();
        $EndMeter ["Scan"] = $this->getEndMeterScan();
        
        // If end meter black is empty, but has startmeterlife and startmetercolor then allow it
        if (empty($EndMeter ["Black"]) && (empty($StartMeter ["Life"]) || empty($StartMeter ["Color"])))
            return false;
            
            // Make sure that the end meter is greater than or equal to the end meter and that both meters are >= 0
        foreach ( $StartMeter as $meterType => $startValue )
        {
            if ($StartMeter < 0 || $EndMeter [$meterType] < 0 || $StartMeter > $EndMeter [$meterType])
            {
                return false;
            }
        }
        // If we get here, all our meters were valid
        return true;
    }

    /**
     *
     * @return the $UploadDataCollectorId
     */
    public function getUploadDataCollectorId ()
    {
        if (! isset($this->UploadDataCollectorId))
        {
            
            $this->UploadDataCollectorId = null;
        }
        return $this->UploadDataCollectorId;
    }

    /**
     *
     * @param field_type $UploadDataCollectorId            
     */
    public function setUploadDataCollectorId ($UploadDataCollectorId)
    {
        $this->UploadDataCollectorId = $UploadDataCollectorId;
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
     * @return the $DevicesPfId
     */
    public function getDevicesPfId ()
    {
        if (! isset($this->DevicesPfId))
        {
            
            $this->DevicesPfId = null;
        }
        return $this->DevicesPfId;
    }

    /**
     *
     * @param field_type $DevicesPfId            
     */
    public function setDevicesPfId ($DevicesPfId)
    {
        $this->DevicesPfId = $DevicesPfId;
        return $this;
    }

    /**
     *
     * @return the $Startdate
     */
    public function getStartdate ()
    {
        if (! isset($this->Startdate))
        {
            
            $this->Startdate = null;
        }
        return $this->Startdate;
    }

    /**
     *
     * @param field_type $Startdate            
     */
    public function setStartdate ($Startdate)
    {
        $this->Startdate = $Startdate;
        return $this;
    }

    /**
     *
     * @return the $Enddate
     */
    public function getEnddate ()
    {
        if (! isset($this->Enddate))
        {
            
            $this->Enddate = null;
        }
        return $this->Enddate;
    }

    /**
     *
     * @param field_type $Enddate            
     */
    public function setEnddate ($Enddate)
    {
        $this->Enddate = $Enddate;
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
     * @return the $SerialNumber
     */
    public function getSerialNumber ()
    {
        if (! isset($this->SerialNumber))
        {
            
            $this->SerialNumber = null;
        }
        return $this->SerialNumber;
    }

    /**
     *
     * @param field_type $SerialNumber            
     */
    public function setSerialNumber ($SerialNumber)
    {
        $this->SerialNumber = $SerialNumber;
        return $this;
    }

    /**
     *
     * @return the $ModelName
     */
    public function getModelName ()
    {
        if (! isset($this->ModelName))
        {
            
            $this->ModelName = null;
        }
        return $this->ModelName;
    }

    /**
     *
     * @param field_type $ModelName            
     */
    public function setModelName ($ModelName)
    {
        $this->ModelName = $ModelName;
        return $this;
    }

    /**
     *
     * @return the $Manufacturer
     */
    public function getManufacturer ()
    {
        if (! isset($this->Manufacturer))
        {
            
            $this->Manufacturer = null;
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
     * @return the $IsColor
     */
    public function getIsColor ()
    {
        if (! isset($this->IsColor))
        {
            
            $this->IsColor = null;
        }
        return $this->IsColor;
    }

    /**
     *
     * @param field_type $IsColor            
     */
    public function setIsColor ($IsColor)
    {
        $this->IsColor = $IsColor;
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
     * @return the $PpmBlack
     */
    public function getPpmBlack ()
    {
        if (! isset($this->PpmBlack))
        {
            
            $this->PpmBlack = null;
        }
        return $this->PpmBlack;
    }

    /**
     *
     * @param field_type $PpmBlack            
     */
    public function setPpmBlack ($PpmBlack)
    {
        $this->PpmBlack = $PpmBlack;
        return $this;
    }

    /**
     *
     * @return the $PpmColor
     */
    public function getPpmColor ()
    {
        if (! isset($this->PpmColor))
        {
            
            $this->PpmColor = null;
        }
        return $this->PpmColor;
    }

    /**
     *
     * @param field_type $PpmColor            
     */
    public function setPpmColor ($PpmColor)
    {
        $this->PpmColor = $PpmColor;
        return $this;
    }

    /**
     *
     * @return the $DateIntroduction
     */
    public function getDateIntroduction ()
    {
        if (! isset($this->DateIntroduction))
        {
            
            $this->DateIntroduction = null;
        }
        return $this->DateIntroduction;
    }

    /**
     *
     * @param field_type $DateIntroduction            
     */
    public function setDateIntroduction ($DateIntroduction)
    {
        $this->DateIntroduction = $DateIntroduction;
        return $this;
    }

    /**
     *
     * @return the $DateAdoption
     */
    public function getDateAdoption ()
    {
        if (! isset($this->DateAdoption))
        {
            
            $this->DateAdoption = null;
        }
        return $this->DateAdoption;
    }

    /**
     *
     * @param field_type $DateAdoption            
     */
    public function setDateAdoption ($DateAdoption)
    {
        $this->DateAdoption = $DateAdoption;
        return $this;
    }

    /**
     *
     * @return the $DiscoveryDate
     */
    public function getDiscoveryDate ()
    {
        if (! isset($this->DiscoveryDate))
        {
            
            $this->DiscoveryDate = null;
        }
        return $this->DiscoveryDate;
    }

    /**
     *
     * @param field_type $DiscoveryDate            
     */
    public function setDiscoveryDate ($DiscoveryDate)
    {
        $this->DiscoveryDate = $DiscoveryDate;
        return $this;
    }

    /**
     *
     * @return the $BlackProdCodeOem
     */
    public function getBlackProdCodeOem ()
    {
        if (! isset($this->BlackProdCodeOem))
        {
            
            $this->BlackProdCodeOem = null;
        }
        return $this->BlackProdCodeOem;
    }

    /**
     *
     * @param field_type $BlackProdCodeOem            
     */
    public function setBlackProdCodeOem ($BlackProdCodeOem)
    {
        $this->BlackProdCodeOem = $BlackProdCodeOem;
        return $this;
    }

    /**
     *
     * @return the $BlackYield
     */
    public function getBlackYield ()
    {
        if (! isset($this->BlackYield))
        {
            
            $this->BlackYield = null;
        }
        return $this->BlackYield;
    }

    /**
     *
     * @param field_type $BlackYield            
     */
    public function setBlackYield ($BlackYield)
    {
        $this->BlackYield = $BlackYield;
        return $this;
    }

    /**
     *
     * @return the $BlackProdCostOem
     */
    public function getBlackProdCostOem ()
    {
        if (! isset($this->BlackProdCostOem))
        {
            
            $this->BlackProdCostOem = null;
        }
        return $this->BlackProdCostOem;
    }

    /**
     *
     * @param field_type $BlackProdCostOem            
     */
    public function setBlackProdCostOem ($BlackProdCostOem)
    {
        $this->BlackProdCostOem = $BlackProdCostOem;
        return $this;
    }

    /**
     *
     * @return the $CyanProdCodeOem
     */
    public function getCyanProdCodeOem ()
    {
        if (! isset($this->CyanProdCodeOem))
        {
            
            $this->CyanProdCodeOem = null;
        }
        return $this->CyanProdCodeOem;
    }

    /**
     *
     * @param field_type $CyanProdCodeOem            
     */
    public function setCyanProdCodeOem ($CyanProdCodeOem)
    {
        $this->CyanProdCodeOem = $CyanProdCodeOem;
        return $this;
    }

    /**
     *
     * @return the $CyanYield
     */
    public function getCyanYield ()
    {
        if (! isset($this->CyanYield))
        {
            
            $this->CyanYield = null;
        }
        return $this->CyanYield;
    }

    /**
     *
     * @param field_type $CyanYield            
     */
    public function setCyanYield ($CyanYield)
    {
        $this->CyanYield = $CyanYield;
        return $this;
    }

    /**
     *
     * @return the $CyanProdCostOem
     */
    public function getCyanProdCostOem ()
    {
        if (! isset($this->CyanProdCostOem))
        {
            
            $this->CyanProdCostOem = null;
        }
        return $this->CyanProdCostOem;
    }

    /**
     *
     * @param field_type $CyanProdCostOem            
     */
    public function setCyanProdCostOem ($CyanProdCostOem)
    {
        $this->CyanProdCostOem = $CyanProdCostOem;
        return $this;
    }

    /**
     *
     * @return the $MagentaProdCodeOem
     */
    public function getMagentaProdCodeOem ()
    {
        if (! isset($this->MagentaProdCodeOem))
        {
            
            $this->MagentaProdCodeOem = null;
        }
        return $this->MagentaProdCodeOem;
    }

    /**
     *
     * @param field_type $MagentaProdCodeOem            
     */
    public function setMagentaProdCodeOem ($MagentaProdCodeOem)
    {
        $this->MagentaProdCodeOem = $MagentaProdCodeOem;
        return $this;
    }

    /**
     *
     * @return the $MagentaYield
     */
    public function getMagentaYield ()
    {
        if (! isset($this->MagentaYield))
        {
            
            $this->MagentaYield = null;
        }
        return $this->MagentaYield;
    }

    /**
     *
     * @param field_type $MagentaYield            
     */
    public function setMagentaYield ($MagentaYield)
    {
        $this->MagentaYield = $MagentaYield;
        return $this;
    }

    /**
     *
     * @return the $MagentaProdCostOem
     */
    public function getMagentaProdCostOem ()
    {
        if (! isset($this->MagentaProdCostOem))
        {
            
            $this->MagentaProdCostOem = null;
        }
        return $this->MagentaProdCostOem;
    }

    /**
     *
     * @param field_type $MagentaProdCostOem            
     */
    public function setMagentaProdCostOem ($MagentaProdCostOem)
    {
        $this->MagentaProdCostOem = $MagentaProdCostOem;
        return $this;
    }

    /**
     *
     * @return the $YellowProdCodeOem
     */
    public function getYellowProdCodeOem ()
    {
        if (! isset($this->YellowProdCodeOem))
        {
            
            $this->YellowProdCodeOem = null;
        }
        return $this->YellowProdCodeOem;
    }

    /**
     *
     * @param field_type $YellowProdCodeOem            
     */
    public function setYellowProdCodeOem ($YellowProdCodeOem)
    {
        $this->YellowProdCodeOem = $YellowProdCodeOem;
        return $this;
    }

    /**
     *
     * @return the $YellowYield
     */
    public function getYellowYield ()
    {
        if (! isset($this->YellowYield))
        {
            
            $this->YellowYield = null;
        }
        return $this->YellowYield;
    }

    /**
     *
     * @param field_type $YellowYield            
     */
    public function setYellowYield ($YellowYield)
    {
        $this->YellowYield = $YellowYield;
        return $this;
    }

    /**
     *
     * @return the $YellowProdCostOem
     */
    public function getYellowProdCostOem ()
    {
        if (! isset($this->YellowProdCostOem))
        {
            
            $this->YellowProdCostOem = null;
        }
        return $this->YellowProdCostOem;
    }

    /**
     *
     * @param field_type $YellowProdCostOem            
     */
    public function setYellowProdCostOem ($YellowProdCostOem)
    {
        $this->YellowProdCostOem = $YellowProdCostOem;
        return $this;
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
     * @return the $InvalidData
     */
    public function getInvalidData ()
    {
        if (! isset($this->InvalidData))
        {
            
            $this->InvalidData = null;
        }
        return $this->InvalidData;
    }

    /**
     *
     * @param field_type $InvalidData            
     */
    public function setInvalidData ($InvalidData)
    {
        $this->InvalidData = $InvalidData;
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
     * @return the $ErrorMessage
     */
    public function getErrorMessage ()
    {
        if (! isset($this->ErrorMessage))
        {
            
            $this->ErrorMessage = null;
        }
        return $this->ErrorMessage;
    }

    /**
     *
     * @param field_type $ErrorMessage            
     */
    public function setErrorMessage ($ErrorMessage)
    {
        $this->ErrorMessage = $ErrorMessage;
        return $this;
    }
}