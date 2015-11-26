<?php

namespace MPSToolbox\Entities;

/**
 * Class DeviceNeedsTonerEntity
 *
 * @Entity
 * @Table(name="device_needs_toner")
 */
class DeviceNeedsTonerEntity extends BaseEntity {

    /**
     * @ManyToOne(targetEntity="RmsDeviceInstanceEntity")
     * @JoinColumn(name="rmsDeviceInstanceId", referencedColumnName="id")
     */
    private $rmsDeviceInstance;

    /**
     * @id
     * @ManyToOne(targetEntity="TonerColorEntity")
     * @JoinColumn(name="color", referencedColumnName="id")
     **/
    private $color;

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

    /**
     * @ManyToOne(targetEntity="TonerEntity")
     * @JoinColumn(name="toner", referencedColumnName="id")
     **/
    private $toner;

    /**
     * @ManyToOne(targetEntity="MasterDeviceEntity")
     * @JoinColumn(name="masterDeviceId", referencedColumnName="id")
     **/
    private $masterDevice;

    /** @Column(type="string") */
    private $location;

    /** @Column(type="datetime") */
    private $firstReported;
    /** @Column(type="integer") */
    private $tonerLevel;

    /** @Column(type="integer") */
    private $daysLeft;

    /** @Column(type="string") */
    private $tonerOptions;

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
     * @return TonerColorEntity
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param mixed $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

    /**
     * @return mixed
     */
    public function getTonerOptions()
    {
        return $this->tonerOptions;
    }

    /**
     * @param mixed $tonerOptions
     */
    public function setTonerOptions($tonerOptions)
    {
        $this->tonerOptions = $tonerOptions;
    }

    /**
     * @return mixed
     */
    public function getDaysLeft()
    {
        return $this->daysLeft;
    }

    /**
     * @param mixed $daysLeft
     */
    public function setDaysLeft($daysLeft)
    {
        $this->daysLeft = $daysLeft;
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
     * @return TonerEntity
     */
    public function getToner()
    {
        return $this->toner;
    }

    /**
     * @param mixed $toner
     */
    public function setToner($toner)
    {
        $this->toner = $toner;
    }

    /**
     * @return mixed
     */
    public function getFirstReported()
    {
        return $this->firstReported;
    }

    /**
     * @param mixed $firstReported
     */
    public function setFirstReported($firstReported)
    {
        $this->firstReported = $firstReported;
    }

    /**
     * @return mixed
     */
    public function getTonerLevel()
    {
        return $this->tonerLevel;
    }

    /**
     * @param mixed $tonerLevel
     */
    public function setTonerLevel($tonerLevel)
    {
        $this->tonerLevel = $tonerLevel;
    }

    /**
     * @return MasterDeviceEntity
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

    public static function getByClient(ClientEntity $client) {
        $query = self::em()->createQuery("SELECT d FROM \MPSToolbox\Entities\DeviceNeedsTonerEntity d WHERE d.client = :client");
        $query->setParameter('client', $client);
        return $query->getResult();
    }

}
