<?php

namespace MPSToolbox\Entities;

/**
 * Class ExtComputerEntity
 * @package MPSToolbox\Entities
 *
 * @Entity
 * @Table(name="ext_computer")
 */
class ExtComputerEntity extends ExtHardwareEntity {

    /** @Column(type="integer")
     * @var float
     */
    private $ram = 0;

    /** @Column(type="boolean")
     * @var boolean
     */
    private $webcam = false;

    /** @Column(type="boolean")
     * @var boolean
     */
    private $mediaDrive = false;

    /** @Column(type="string")
     * @var string
     */
    private $usb = false;

    /** @Column(type="string")
     * @var string
     */
    private $os = '';

    /** @Column(type="integer")
     * @var integer
     */
    private $hdd = 0;

    /** @Column(type="boolean")
     * @var boolean
     */
    private $ssd = false;


    /** @Column(type="float")
     * @var float
     */
    private $screenSize = 0;

    /** @Column(type="boolean")
     * @var boolean
     */
    private $hdDisplay = false;

    /** @Column(type="string")
     * @var string
     */
    private $displayType = false;

    /** @Column(type="float")
     * @var float
     */
    private $weight = 0;

    /** @Column(type="string")
     * @var string
     */
    private $processorName = '';

    /** @Column(type="float")
     * @var float
     */
    private $processorSpeed = 0;

    /** @Column(type="string")
     * @var string
     */
    private $service = '';

    /**
     * @return int
     */
    public function getRam()
    {
        return $this->ram;
    }

    /**
     * @param int $ram
     */
    public function setRam($ram)
    {
        $this->ram = $ram;
    }

    /**
     * @return boolean
     */
    public function isWebcam()
    {
        return $this->webcam;
    }

    /**
     * @param boolean $webcam
     */
    public function setWebcam($webcam)
    {
        $this->webcam = $webcam;
    }

    /**
     * @return boolean
     */
    public function isMediaDrive()
    {
        return $this->mediaDrive;
    }

    /**
     * @param boolean $mediaDrive
     */
    public function setMediaDrive($mediaDrive)
    {
        $this->mediaDrive = $mediaDrive;
    }

    /**
     * @return string
     */
    public function getUsb()
    {
        return $this->usb;
    }

    /**
     * @param string $usb
     */
    public function setUsb($usb)
    {
        $this->usb = $usb;
    }

    /**
     * @return string
     */
    public function getOs()
    {
        return $this->os;
    }

    /**
     * @param string $os
     */
    public function setOs($os)
    {
        $this->os = $os;
    }

    /**
     * @return int
     */
    public function getHdd()
    {
        return $this->hdd;
    }

    /**
     * @param int $hdd
     */
    public function setHdd($hdd)
    {
        $this->hdd = $hdd;
    }

    /**
     * @return float
     */
    public function getScreenSize()
    {
        return $this->screenSize;
    }

    /**
     * @param float $screenSize
     */
    public function setScreenSize($screenSize)
    {
        $this->screenSize = $screenSize;
    }

    /**
     * @return boolean
     */
    public function isHdDisplay()
    {
        return $this->hdDisplay;
    }

    /**
     * @param boolean $hdDisplay
     */
    public function setHdDisplay($hdDisplay)
    {
        $this->hdDisplay = $hdDisplay;
    }

    /**
     * @return string
     */
    public function getDisplayType()
    {
        return $this->displayType;
    }

    /**
     * @param string $displayType
     */
    public function setDisplayType($displayType)
    {
        $this->displayType = $displayType;
    }

    /**
     * @return float
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param float $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    /**
     * @return string
     */
    public function getProcessorName()
    {
        return $this->processorName;
    }

    /**
     * @param string $processorName
     */
    public function setProcessorName($processorName)
    {
        $this->processorName = $processorName;
    }

    /**
     * @return float
     */
    public function getProcessorSpeed()
    {
        return $this->processorSpeed;
    }

    /**
     * @param float $processorSpeed
     */
    public function setProcessorSpeed($processorSpeed)
    {
        $this->processorSpeed = $processorSpeed;
    }

    /**
     * @return string
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param string $service
     */
    public function setService($service)
    {
        $this->service = $service;
    }

    /**
     * @return boolean
     */
    public function isSsd()
    {
        return $this->ssd;
    }

    /**
     * @param boolean $ssd
     */
    public function setSsd($ssd)
    {
        $this->ssd = $ssd;
    }

}