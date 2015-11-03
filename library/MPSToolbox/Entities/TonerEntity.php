<?php

namespace MPSToolbox\Entities;

/**
 * Class TonerEntity
 *
 * @Entity
 * @Table(name="toners")
 */
class TonerEntity extends BaseEntity {

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="UserEntity")
     * @JoinColumn(name="userId", referencedColumnName="id")
     **/
    private $user;

    /** @Column(type="integer") */
    private $isSystemDevice;

    /** @Column(type="string") */
    private $sku;

    /** @Column(type="float") */
    private $cost;

    /** @Column(type="integer") */
    private $yield;

    /**
     * @ManyToOne(targetEntity="ManufacturerEntity")
     * @JoinColumn(name="manufacturerId", referencedColumnName="id")
     **/
    private $manufacturer;

    /**
     * @ManyToOne(targetEntity="TonerColorEntity")
     * @JoinColumn(name="tonerColorId", referencedColumnName="id")
     **/
    private $tonerColor;

    /** @Column(type="string") */
    private $imageUrl = '';

    /** @Column(type="string") */
    private $imageFile = '';

    /** @Column(type="string") */
    private $name = '';

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
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * @param mixed $sku
     */
    public function setSku($sku)
    {
        $this->sku = $sku;
    }

    /**
     * @return mixed
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * @param mixed $cost
     */
    public function setCost($cost)
    {
        $this->cost = $cost;
    }

    /**
     * @return mixed
     */
    public function getYield()
    {
        return $this->yield;
    }

    /**
     * @param mixed $yield
     */
    public function setYield($yield)
    {
        $this->yield = $yield;
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * @return TonerColorEntity
     */
    public function getTonerColor()
    {
        return $this->tonerColor;
    }

    /**
     * @param mixed $tonerColor
     */
    public function setTonerColor($tonerColor)
    {
        $this->tonerColor = $tonerColor;
    }




}