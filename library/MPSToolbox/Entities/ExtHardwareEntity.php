<?php

namespace MPSToolbox\Entities;

/**
 * Class ExtHardware
 *
 * @property int    id
 * @property string category
 * @property string modelName
 * @property \DateTime dateCreated
 * @property \DateTime dateUpdated
 * @property \DateTime launchDate
 * @property ManufacturerEntity manufacturer
 * @property string userId
 * @property string isSystemDevice
 * @property string imageFile
 * @property string imageUrl
 *
 * @Entity
 * @Table(name="ext_hardware")
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="hardware_type", type="string")
 * @DiscriminatorMap({"computer" = "ExtComputerEntity", "peripheral" = "ExtPeripheralEntity", "service" = "ExtServiceEntity"})
 */
abstract class ExtHardwareEntity extends BaseEntity {

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /** @Column(type="string") */
    private $category;

    /** @Column(type="string") */
    private $modelName;

    /** @Column(type="datetime") */
    private $dateCreated;

    /** @Column(type="datetime") */
    private $dateUpdated;

    /** @Column(type="date") */
    private $launchDate;

    /**
     * @ManyToOne(targetEntity="ManufacturerEntity")
     * @JoinColumn(name="manufacturerId", referencedColumnName="id")
     **/
    private $manufacturer;

    /** @Column(type="string") */
    private $userId;

    /** @Column(type="string") */
    private $isSystemDevice;

    /** @Column(type="string") */
    private $imageFile;

    /** @Column(type="string") */
    private $imageUrl;

    /** @Column(type="string") */
    private $grade;

    /**
     * @OneToMany(targetEntity="ExtDealerHardwareEntity", mappedBy="hardware")
     **/
    private $dealerHardware;

    function __construct($modelName='', \DateTime $launchDate=null, ManufacturerEntity $manufacturer=null, $userId=1, $isSystemDevice=0)
    {
        $this->modelName = $modelName;
        $this->launchDate = $launchDate;
        $this->manufacturer = $manufacturer;
        $this->userId = $userId;
        $this->isSystemDevice = $isSystemDevice;
    }


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param string $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return string
     */
    public function getModelName()
    {
        return $this->modelName;
    }

    /**
     * @param string $modelName
     */
    public function setModelName($modelName)
    {
        $this->modelName = $modelName;
    }

    /**
     * @return ManufacturerEntity
     */
    public function getManufacturer()
    {
        return $this->manufacturer;
    }

    /**
     * @param ManufacturerEntity $manufacturer
     */
    public function setManufacturer(ManufacturerEntity $manufacturer)
    {
        $this->manufacturer = $manufacturer;
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param string $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getIsSystemDevice()
    {
        return $this->isSystemDevice;
    }

    /**
     * @param string $isSystemDevice
     */
    public function setIsSystemDevice($isSystemDevice)
    {
        $this->isSystemDevice = $isSystemDevice;
    }

    /**
     * @return string
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * @param string $imageFile
     */
    public function setImageFile($imageFile)
    {
        $this->imageFile = $imageFile;
    }

    /**
     * @return string
     */
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    /**
     * @param string $imageUrl
     */
    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreated()
    {
        if (empty($this->dateCreated)) $this->dateCreated=new \DateTime();
        return $this->dateCreated;
    }

    /**
     * @param \DateTime $dateCreated
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     * @return \DateTime
     */
    public function getDateUpdated()
    {
        if (empty($this->dateUpdated)) $this->dateUpdated=new \DateTime();
        return $this->dateUpdated;
    }

    /**
     * @param \DateTime $dateUpdated
     */
    public function setDateUpdated($dateUpdated)
    {
        $this->dateUpdated = $dateUpdated;
    }

    /**
     * @return \DateTime
     */
    public function getLaunchDate()
    {
        if (empty($this->launchDate)) $this->launchDate=new \DateTime();
        return $this->launchDate;
    }

    /**
     * @param \DateTime $launchDate
     */
    public function setLaunchDate($launchDate)
    {
        if (is_string($launchDate)) $launchDate=new \DateTime($launchDate);
        $this->launchDate = $launchDate;
    }

    /**
     * @return mixed
     */
    public function getGrade()
    {
        return $this->grade;
    }

    /**
     * @param mixed $grade
     */
    public function setGrade($grade)
    {
        $this->grade = $grade;
    }

    /**
     * @return mixed
     */
    public function getDealerHardware()
    {
        return $this->dealerHardware;
    }

    /**
     * @param mixed $dealerHardware
     */
    public function setDealerHardware($dealerHardware)
    {
        $this->dealerHardware = $dealerHardware;
    }

    public function save() {
        $this->dateUpdated = new \DateTime();
        parent::save();
    }


}
