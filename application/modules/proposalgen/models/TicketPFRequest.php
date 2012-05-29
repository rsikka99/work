<?php

/**
 * Class Proposalgen_Model_TicketPFRequest
 * @author "John Sadler"
 */
class Proposalgen_Model_TicketPFRequest extends Tangent_Model_Abstract
{
    protected $TicketId;
    protected $UserId;
    protected $DevicePfId;
    protected $DevicePf;
    protected $DeviceManufacturer;
    protected $PrinterModel;
    protected $LaunchDate;
    protected $DevicePrice;
    protected $ServiceCostPerPage;
    protected $TonerConfig;
    protected $IsCopier;
    protected $IsFax;
    protected $IsDuplex;
    protected $IsScanner;
    protected $PpmBlack;
    protected $PpmColor;
    protected $DutyCycle;
    protected $WattsPowerNormal;
    protected $WattsPowerIdle;

    /**
     * @return the $TicketId
     */
    public function getTicketId ()
    {
        if (! isset($this->TicketId))
        {
            
            $this->TicketId = null;
        }
        return $this->TicketId;
    }

    /**
     * @param field_type $TicketId
     */
    public function setTicketId ($TicketId)
    {
        $this->TicketId = $TicketId;
        return $this;
    }

	/**
     * @return the $UserId
     */
    
    public function getUserId ()
    {
        if (!isset($this->UserId))
        {
        	
        	$this->UserId = null;
        }	
        return $this->UserId;
    }

	/**
     * @param field_type $UserId
     */
    public function setUserId ($UserId)
    {
        $this->UserId = $UserId;
        return $this;
    }

    /**
     * @return the $DevicePfId
     */
    public function getDevicePfId ()
    {
        if (! isset($this->DevicePfId))
        {
            
            $this->DevicePfId = null;
        }
        return $this->DevicePfId;
    }

    /**
     * @param field_type $DevicePfId
     */
    public function setDevicePfId ($DevicePfId)
    {
        $this->DevicePfId = $DevicePfId;
        return $this;
    }

    /**
     * @return the $DevicePf
     */
    public function getDevicePf ()
    {
        if (! isset($this->DevicePf))
        {
            $id = $this->getDevicePfId();
            if (isset($id))
            {
                $this->DevicePf = Proposalgen_Model_Mapper_DevicePf::getInstance()->find($id);
            }
        }
        return $this->DevicePf;
    }

    /**
     * @param field_type $DevicePf
     */
    public function setDevicePf ($DevicePf)
    {
        $this->DevicePf = $DevicePf;
        return $this;
    }

    /**
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
     * @param field_type $DeviceManufacturer
     */
    public function setDeviceManufacturer ($DeviceManufacturer)
    {
        $this->DeviceManufacturer = $DeviceManufacturer;
        return $this;
    }

    /**
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
     * @param field_type $PrinterModel
     */
    public function setPrinterModel ($PrinterModel)
    {
        $this->PrinterModel = $PrinterModel;
        return $this;
    }

    /**
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
     * @param field_type $LaunchDate
     */
    public function setLaunchDate ($LaunchDate)
    {
        $this->LaunchDate = $LaunchDate;
        return $this;
    }

    /**
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
     * @param field_type $DevicePrice
     */
    public function setDevicePrice ($DevicePrice)
    {
        $this->DevicePrice = $DevicePrice;
        return $this;
    }

    /**
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
     * @param field_type $TonerConfig
     */
    public function setTonerConfig ($TonerConfig)
    {
        $this->TonerConfig = $TonerConfig;
        return $this;
    }

    /**
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
     * @param field_type $IsCopier
     */
    public function setIsCopier ($IsCopier)
    {
        $this->IsCopier = $IsCopier;
        return $this;
    }

    /**
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
     * @param field_type $IsFax
     */
    public function setIsFax ($IsFax)
    {
        $this->IsFax = $IsFax;
        return $this;
    }

    /**
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
     * @param field_type $IsDuplex
     */
    public function setIsDuplex ($IsDuplex)
    {
        $this->IsDuplex = $IsDuplex;
        return $this;
    }

    /**
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
     * @param field_type $IsScanner
     */
    public function setIsScanner ($IsScanner)
    {
        $this->IsScanner = $IsScanner;
        return $this;
    }

    /**
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
     * @param field_type $PpmBlack
     */
    public function setPpmBlack ($PpmBlack)
    {
        $this->PpmBlack = $PpmBlack;
        return $this;
    }

    /**
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
     * @param field_type $PpmColor
     */
    public function setPpmColor ($PpmColor)
    {
        $this->PpmColor = $PpmColor;
        return $this;
    }

    /**
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
     * @param field_type $DutyCycle
     */
    public function setDutyCycle ($DutyCycle)
    {
        $this->DutyCycle = $DutyCycle;
        return $this;
    }

    /**
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
     * @param field_type $WattsPowerNormal
     */
    public function setWattsPowerNormal ($WattsPowerNormal)
    {
        $this->WattsPowerNormal = $WattsPowerNormal;
        return $this;
    }

    /**
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
     * @param field_type $WattsPowerIdle
     */
    public function setWattsPowerIdle ($WattsPowerIdle)
    {
        $this->WattsPowerIdle = $WattsPowerIdle;
        return $this;
    }
	/**
     * @return the $ServiceCostPerPage
     */
    public function getServiceCostPerPage ()
    {
        if (!isset($this->ServiceCostPerPage))
        {
        	
        	$this->ServiceCostPerPage = null;
        }	
        return $this->ServiceCostPerPage;
    }

	/**
     * @param field_type $ServiceCostPerPage
     */
    public function setServiceCostPerPage ($ServiceCostPerPage)
    {
        $this->ServiceCostPerPage = $ServiceCostPerPage;
        return $this;
    }


}