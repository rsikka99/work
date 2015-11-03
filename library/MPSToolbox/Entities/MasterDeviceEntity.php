<?php

namespace MPSToolbox\Entities;

/**
 * Class MasterDeviceEntity
 * @package MPSToolbox\Entities
 *
 * @Entity
 * @Table(name="master_devices")
 */
class MasterDeviceEntity extends BaseEntity {

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /**
     * @ManyToMany(targetEntity="TonerEntity")
     * @JoinTable(name="device_toners",
     *      joinColumns={@JoinColumn(name="master_device_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="toner_id", referencedColumnName="id")}
     *      )
     **/
    private $toners;

    public function __construct() {
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /** @Column(type="datetime") */
    private $dateCreated;
    /** @Column(type="boolean") */
    private $isCopier;
    /** @Column(type="boolean") */
    private $isDuplex;
    /** @Column(type="boolean") */
    private $isFax;
    /** @Column(type="boolean") */
    private $isLeased;
    /** @Column(type="boolean") */
    private $isReplacementDevice;
    /** @Column(type="date") */
    private $launchDate;

    /**
     * @ManyToOne(targetEntity="ManufacturerEntity")
     * @JoinColumn(name="manufacturerId", referencedColumnName="id")
     **/
    private $manufacturer;

    /** @Column(type="string") */
    private $modelName;
    /** @Column(type="integer") */
    private $leasedTonerYield;
    /** @Column(type="float") */
    private $ppmBlack;
    /** @Column(type="float") */
    private $ppmColor;

    /**
     * @ManyToOne(targetEntity="TonerConfigEntity")
     * @JoinColumn(name="tonerConfigId", referencedColumnName="id")
     **/
    private $tonerConfig;

    /** @Column(type="float") */
    private $wattsPowerNormal;
    /** @Column(type="float") */
    private $wattsPowerIdle;
    /** @Column(type="boolean") */
    private $isCapableOfReportingTonerLevels;

    /**
     * @ManyToOne(targetEntity="UserEntity")
     * @JoinColumn(name="userId", referencedColumnName="id")
     **/
    private $user;

    /** @Column(type="boolean") */
    private $isSystemDevice;
    /** @Column(type="boolean") */
    private $isA3;
    /** @Column(type="integer") */
    private $maximumRecommendedMonthlyPageVolume;
    /** @Column(type="string") */
    private $imageFile;
    /** @Column(type="string") */
    private $imageUrl;
    /** @Column(type="boolean") */
    private $isSmartphone;
    /** @Column(type="integer") */
    private $additionalTrays;
    /** @Column(type="boolean") */
    private $isPIN;
    /** @Column(type="boolean") */
    private $isAccessCard;
    /** @Column(type="boolean") */
    private $isWalkup;
    /** @Column(type="boolean") */
    private $isStapling;
    /** @Column(type="boolean") */
    private $isBinding;
    /** @Column(type="boolean") */
    private $isTouchscreen;
    /** @Column(type="boolean") */
    private $isADF;
    /** @Column(type="boolean") */
    private $isUSB;
    /** @Column(type="boolean") */
    private $isWired;
    /** @Column(type="boolean") */
    private $isWireless;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param mixed $dateCreated
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     * @return mixed
     */
    public function getIsCopier()
    {
        return $this->isCopier;
    }

    /**
     * @param mixed $isCopier
     */
    public function setIsCopier($isCopier)
    {
        $this->isCopier = $isCopier;
    }

    /**
     * @return mixed
     */
    public function getIsDuplex()
    {
        return $this->isDuplex;
    }

    /**
     * @param mixed $isDuplex
     */
    public function setIsDuplex($isDuplex)
    {
        $this->isDuplex = $isDuplex;
    }

    /**
     * @return mixed
     */
    public function getIsFax()
    {
        return $this->isFax;
    }

    /**
     * @param mixed $isFax
     */
    public function setIsFax($isFax)
    {
        $this->isFax = $isFax;
    }

    /**
     * @return mixed
     */
    public function getIsLeased()
    {
        return $this->isLeased;
    }

    /**
     * @param mixed $isLeased
     */
    public function setIsLeased($isLeased)
    {
        $this->isLeased = $isLeased;
    }

    /**
     * @return mixed
     */
    public function getIsReplacementDevice()
    {
        return $this->isReplacementDevice;
    }

    /**
     * @param mixed $isReplacementDevice
     */
    public function setIsReplacementDevice($isReplacementDevice)
    {
        $this->isReplacementDevice = $isReplacementDevice;
    }

    /**
     * @return mixed
     */
    public function getLaunchDate()
    {
        return $this->launchDate;
    }

    /**
     * @param mixed $launchDate
     */
    public function setLaunchDate($launchDate)
    {
        $this->launchDate = $launchDate;
    }

    /**
     * @return ManufacturerEntity
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
    public function getLeasedTonerYield()
    {
        return $this->leasedTonerYield;
    }

    /**
     * @param mixed $leasedTonerYield
     */
    public function setLeasedTonerYield($leasedTonerYield)
    {
        $this->leasedTonerYield = $leasedTonerYield;
    }

    /**
     * @return mixed
     */
    public function getPpmBlack()
    {
        return $this->ppmBlack;
    }

    /**
     * @param mixed $ppmBlack
     */
    public function setPpmBlack($ppmBlack)
    {
        $this->ppmBlack = $ppmBlack;
    }

    /**
     * @return mixed
     */
    public function getPpmColor()
    {
        return $this->ppmColor;
    }

    /**
     * @param mixed $ppmColor
     */
    public function setPpmColor($ppmColor)
    {
        $this->ppmColor = $ppmColor;
    }

    /**
     * @return mixed
     */
    public function getTonerConfig()
    {
        return $this->tonerConfig;
    }

    /**
     * @param mixed $tonerConfig
     */
    public function setTonerConfig($tonerConfig)
    {
        $this->tonerConfig = $tonerConfig;
    }

    /**
     * @return mixed
     */
    public function getWattsPowerNormal()
    {
        return $this->wattsPowerNormal;
    }

    /**
     * @param mixed $wattsPowerNormal
     */
    public function setWattsPowerNormal($wattsPowerNormal)
    {
        $this->wattsPowerNormal = $wattsPowerNormal;
    }

    /**
     * @return mixed
     */
    public function getWattsPowerIdle()
    {
        return $this->wattsPowerIdle;
    }

    /**
     * @param mixed $wattsPowerIdle
     */
    public function setWattsPowerIdle($wattsPowerIdle)
    {
        $this->wattsPowerIdle = $wattsPowerIdle;
    }

    /**
     * @return mixed
     */
    public function getIsCapableOfReportingTonerLevels()
    {
        return $this->isCapableOfReportingTonerLevels;
    }

    /**
     * @param mixed $isCapableOfReportingTonerLevels
     */
    public function setIsCapableOfReportingTonerLevels($isCapableOfReportingTonerLevels)
    {
        $this->isCapableOfReportingTonerLevels = $isCapableOfReportingTonerLevels;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getIsSystemDevice()
    {
        return $this->isSystemDevice;
    }

    /**
     * @param mixed $isSystemDevice
     */
    public function setIsSystemDevice($isSystemDevice)
    {
        $this->isSystemDevice = $isSystemDevice;
    }

    /**
     * @return mixed
     */
    public function getIsA3()
    {
        return $this->isA3;
    }

    /**
     * @param mixed $isA3
     */
    public function setIsA3($isA3)
    {
        $this->isA3 = $isA3;
    }

    /**
     * @return mixed
     */
    public function getMaximumRecommendedMonthlyPageVolume()
    {
        return $this->maximumRecommendedMonthlyPageVolume;
    }

    /**
     * @param mixed $maximumRecommendedMonthlyPageVolume
     */
    public function setMaximumRecommendedMonthlyPageVolume($maximumRecommendedMonthlyPageVolume)
    {
        $this->maximumRecommendedMonthlyPageVolume = $maximumRecommendedMonthlyPageVolume;
    }

    /**
     * @return mixed
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * @param mixed $imageFile
     */
    public function setImageFile($imageFile)
    {
        $this->imageFile = $imageFile;
    }

    /**
     * @return mixed
     */
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    /**
     * @param mixed $imageUrl
     */
    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;
    }

    /**
     * @return mixed
     */
    public function getIsSmartphone()
    {
        return $this->isSmartphone;
    }

    /**
     * @param mixed $isSmartphone
     */
    public function setIsSmartphone($isSmartphone)
    {
        $this->isSmartphone = $isSmartphone;
    }

    /**
     * @return mixed
     */
    public function getAdditionalTrays()
    {
        return $this->additionalTrays;
    }

    /**
     * @param mixed $additionalTrays
     */
    public function setAdditionalTrays($additionalTrays)
    {
        $this->additionalTrays = $additionalTrays;
    }

    /**
     * @return mixed
     */
    public function getIsPIN()
    {
        return $this->isPIN;
    }

    /**
     * @param mixed $isPIN
     */
    public function setIsPIN($isPIN)
    {
        $this->isPIN = $isPIN;
    }

    /**
     * @return mixed
     */
    public function getIsAccessCard()
    {
        return $this->isAccessCard;
    }

    /**
     * @param mixed $isAccessCard
     */
    public function setIsAccessCard($isAccessCard)
    {
        $this->isAccessCard = $isAccessCard;
    }

    /**
     * @return mixed
     */
    public function getIsWalkup()
    {
        return $this->isWalkup;
    }

    /**
     * @param mixed $isWalkup
     */
    public function setIsWalkup($isWalkup)
    {
        $this->isWalkup = $isWalkup;
    }

    /**
     * @return mixed
     */
    public function getIsStapling()
    {
        return $this->isStapling;
    }

    /**
     * @param mixed $isStapling
     */
    public function setIsStapling($isStapling)
    {
        $this->isStapling = $isStapling;
    }

    /**
     * @return mixed
     */
    public function getIsBinding()
    {
        return $this->isBinding;
    }

    /**
     * @param mixed $isBinding
     */
    public function setIsBinding($isBinding)
    {
        $this->isBinding = $isBinding;
    }

    /**
     * @return mixed
     */
    public function getIsTouchscreen()
    {
        return $this->isTouchscreen;
    }

    /**
     * @param mixed $isTouchscreen
     */
    public function setIsTouchscreen($isTouchscreen)
    {
        $this->isTouchscreen = $isTouchscreen;
    }

    /**
     * @return mixed
     */
    public function getIsADF()
    {
        return $this->isADF;
    }

    /**
     * @param mixed $isADF
     */
    public function setIsADF($isADF)
    {
        $this->isADF = $isADF;
    }

    /**
     * @return mixed
     */
    public function getIsUSB()
    {
        return $this->isUSB;
    }

    /**
     * @param mixed $isUSB
     */
    public function setIsUSB($isUSB)
    {
        $this->isUSB = $isUSB;
    }

    /**
     * @return mixed
     */
    public function getIsWired()
    {
        return $this->isWired;
    }

    /**
     * @param mixed $isWired
     */
    public function setIsWired($isWired)
    {
        $this->isWired = $isWired;
    }

    /**
     * @return mixed
     */
    public function getIsWireless()
    {
        return $this->isWireless;
    }

    /**
     * @param mixed $isWireless
     */
    public function setIsWireless($isWireless)
    {
        $this->isWireless = $isWireless;
    }

    /**
     * @return mixed
     */
    public function getToners()
    {
        return $this->toners;
    }

}