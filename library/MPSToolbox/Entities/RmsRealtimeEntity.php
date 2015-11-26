<?php

namespace MPSToolbox\Entities;

/**
 * Class RmsRealtimeEntity
 *
 * @Entity
 * @Table(name="rms_realtime")
 */
class RmsRealtimeEntity extends BaseEntity {

    /**
     * @Id
     * @ManyToOne(targetEntity="RmsDeviceInstanceEntity")
     * @JoinColumn(name="rmsDeviceInstanceId", referencedColumnName="id")
     */
    private $rmsDeviceInstance;

    /** @Id @Column(type=""datetime) */
    private $scanDate;

    /**
     * @Id
     * @ManyToOne(targetEntity="ClientEntity")
     * @JoinColumn(name="clientId", referencedColumnName="id")
     */
    private $client;

    /** @Id @Column(type="string") */
    private $assetId;
    /** @Id @Column(type="string") */
    private $ipAddress;
    /** @Id @Column(type="string") */
    private $serialNumber;

    /** @Column(type="string") */
    private $rawDeviceName;
    /** @Column(type="string") */
    private $fullDeviceName;
    /** @Column(type="string") */
    private $manufacturer;
    /** @Column(type="string") */
    private $modelName;

    /** @Column(type="string") */
    private $location;

    /**
     * @ManyToOne(targetEntity="MasterDeviceEntity")
     * @JoinColumn(name="masterDeviceId", referencedColumnName="id")
     **/
    private $masterDevice;

    /** @Column(type="integer") */
    private $lifeCount;
    /** @Column(type="integer") */
    private $lifeCountBlack;
    /** @Column(type="integer") */
    private $lifeCountColor;
    /** @Column(type="integer") */
    private $copyCountBlack;
    /** @Column(type="integer") */
    private $copyCountColor;
    /** @Column(type="integer") */
    private $printCountBlack;
    /** @Column(type="integer") */
    private $scanCount;
    /** @Column(type="integer") */
    private $faxCount;

    /** @Column(type="integer") */
    private $tonerLevelBlack;
    /** @Column(type="integer") */
    private $tonerLevelCyan;
    /** @Column(type="integer") */
    private $tonerLevelMagenta;
    /** @Column(type="integer") */
    private $tonerLevelYellow;

    /**
     * @return mixed
     */
    public function getRmsDeviceInstance()
    {
        return $this->rmsDeviceInstance;
    }

    /**
     * @param mixed $rmsDeviceInstance
     */
    public function setRmsDeviceInstance($rmsDeviceInstance)
    {
        $this->rmsDeviceInstance = $rmsDeviceInstance;
    }

    /**
     * @return mixed
     */
    public function getScanDate()
    {
        return $this->scanDate;
    }

    /**
     * @param mixed $scanDate
     */
    public function setScanDate($scanDate)
    {
        $this->scanDate = $scanDate;
    }

    /**
     * @return mixed
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param mixed $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * @return mixed
     */
    public function getAssetId()
    {
        return $this->assetId;
    }

    /**
     * @param mixed $assetId
     */
    public function setAssetId($assetId)
    {
        $this->assetId = $assetId;
    }

    /**
     * @return mixed
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * @param mixed $ipAddress
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;
    }

    /**
     * @return mixed
     */
    public function getSerialNumber()
    {
        return $this->serialNumber;
    }

    /**
     * @param mixed $serialNumber
     */
    public function setSerialNumber($serialNumber)
    {
        $this->serialNumber = $serialNumber;
    }

    /**
     * @return mixed
     */
    public function getRawDeviceName()
    {
        return $this->rawDeviceName;
    }

    /**
     * @param mixed $rawDeviceName
     */
    public function setRawDeviceName($rawDeviceName)
    {
        $this->rawDeviceName = $rawDeviceName;
    }

    /**
     * @return mixed
     */
    public function getFullDeviceName()
    {
        return $this->fullDeviceName;
    }

    /**
     * @param mixed $fullDeviceName
     */
    public function setFullDeviceName($fullDeviceName)
    {
        $this->fullDeviceName = $fullDeviceName;
    }

    /**
     * @return mixed
     */
    public function getManufacturer()
    {
        return $this->manufacturer;
    }

    /**
     * @param mixed $manufacturer
     */
    public function setManufacturer($manufacturer)
    {
        $this->manufacturer = $manufacturer;
    }

    /**
     * @return mixed
     */
    public function getModelName()
    {
        return $this->modelName;
    }

    /**
     * @param mixed $modelName
     */
    public function setModelName($modelName)
    {
        $this->modelName = $modelName;
    }

    /**
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param mixed $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return mixed
     */
    public function getMasterDevice()
    {
        return $this->masterDevice;
    }

    /**
     * @param mixed $masterDevice
     */
    public function setMasterDevice($masterDevice)
    {
        $this->masterDevice = $masterDevice;
    }

    /**
     * @return mixed
     */
    public function getLifeCount()
    {
        return $this->lifeCount;
    }

    /**
     * @param mixed $lifeCount
     */
    public function setLifeCount($lifeCount)
    {
        $this->lifeCount = $lifeCount;
    }

    /**
     * @return mixed
     */
    public function getLifeCountBlack()
    {
        return $this->lifeCountBlack;
    }

    /**
     * @param mixed $lifeCountBlack
     */
    public function setLifeCountBlack($lifeCountBlack)
    {
        $this->lifeCountBlack = $lifeCountBlack;
    }

    /**
     * @return mixed
     */
    public function getLifeCountColor()
    {
        return $this->lifeCountColor;
    }

    /**
     * @param mixed $lifeCountColor
     */
    public function setLifeCountColor($lifeCountColor)
    {
        $this->lifeCountColor = $lifeCountColor;
    }

    /**
     * @return mixed
     */
    public function getCopyCountBlack()
    {
        return $this->copyCountBlack;
    }

    /**
     * @param mixed $copyCountBlack
     */
    public function setCopyCountBlack($copyCountBlack)
    {
        $this->copyCountBlack = $copyCountBlack;
    }

    /**
     * @return mixed
     */
    public function getCopyCountColor()
    {
        return $this->copyCountColor;
    }

    /**
     * @param mixed $copyCountColor
     */
    public function setCopyCountColor($copyCountColor)
    {
        $this->copyCountColor = $copyCountColor;
    }

    /**
     * @return mixed
     */
    public function getPrintCountBlack()
    {
        return $this->printCountBlack;
    }

    /**
     * @param mixed $printCountBlack
     */
    public function setPrintCountBlack($printCountBlack)
    {
        $this->printCountBlack = $printCountBlack;
    }

    /**
     * @return mixed
     */
    public function getScanCount()
    {
        return $this->scanCount;
    }

    /**
     * @param mixed $scanCount
     */
    public function setScanCount($scanCount)
    {
        $this->scanCount = $scanCount;
    }

    /**
     * @return mixed
     */
    public function getFaxCount()
    {
        return $this->faxCount;
    }

    /**
     * @param mixed $faxCount
     */
    public function setFaxCount($faxCount)
    {
        $this->faxCount = $faxCount;
    }

    /**
     * @return mixed
     */
    public function getTonerLevelBlack()
    {
        return $this->tonerLevelBlack;
    }

    /**
     * @param mixed $tonerLevelBlack
     */
    public function setTonerLevelBlack($tonerLevelBlack)
    {
        $this->tonerLevelBlack = $tonerLevelBlack;
    }

    /**
     * @return mixed
     */
    public function getTonerLevelCyan()
    {
        return $this->tonerLevelCyan;
    }

    /**
     * @param mixed $tonerLevelCyan
     */
    public function setTonerLevelCyan($tonerLevelCyan)
    {
        $this->tonerLevelCyan = $tonerLevelCyan;
    }

    /**
     * @return mixed
     */
    public function getTonerLevelMagenta()
    {
        return $this->tonerLevelMagenta;
    }

    /**
     * @param mixed $tonerLevelMagenta
     */
    public function setTonerLevelMagenta($tonerLevelMagenta)
    {
        $this->tonerLevelMagenta = $tonerLevelMagenta;
    }

    /**
     * @return mixed
     */
    public function getTonerLevelYellow()
    {
        return $this->tonerLevelYellow;
    }

    /**
     * @param mixed $tonerLevelYellow
     */
    public function setTonerLevelYellow($tonerLevelYellow)
    {
        $this->tonerLevelYellow = $tonerLevelYellow;
    }



}