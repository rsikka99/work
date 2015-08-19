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

    /** @Column(type="boolean")
     * @var boolean
     */
    private $usb3 = false;

    /** @Column(type="string")
     * @var string
     */
    private $usbDescription = false;

    /** @Column(type="string")
     * @var string
     */
    private $os = '';

    /** @Column(type="integer")
     * @var integer
     */
    private $hdd = 0;

    /** @Column(type="float")
     * @var float
     */
    private $screenSize = 0;

    /** @Column(type="boolean")
     * @var boolean
     */
    private $hdDisplay = false;

    /** @Column(type="float")
     * @var float
     */
    private $ledDisplay = false;

    /** @Column(type="float")
     * @var float
     */
    private $weight = 0;

    /** @Column(type="float")
     * @var float
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
     * @return boolean
     */
    public function isUsb3()
    {
        return $this->usb3;
    }

    /**
     * @param boolean $usb3
     */
    public function setUsb3($usb3)
    {
        $this->usb3 = $usb3;
    }

    /**
     * @return string
     */
    public function getUsbDescription()
    {
        return $this->usbDescription;
    }

    /**
     * @param string $usbDescription
     */
    public function setUsbDescription($usbDescription)
    {
        $this->usbDescription = $usbDescription;
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
     * @return float
     */
    public function getLedDisplay()
    {
        return $this->ledDisplay;
    }

    /**
     * @param float $ledDisplay
     */
    public function setLedDisplay($ledDisplay)
    {
        $this->ledDisplay = $ledDisplay;
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
     * @return float
     */
    public function getProcessorName()
    {
        return $this->processorName;
    }

    /**
     * @param float $processorName
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






}