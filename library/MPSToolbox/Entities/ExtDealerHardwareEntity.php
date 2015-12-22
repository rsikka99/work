<?php

namespace MPSToolbox\Entities;

/**
 * Class ManufacturerEntity
 * @package MPSToolbox\Entities
 *
 * @Entity
 * @Table(name="ext_dealer_hardware")
 */
class ExtDealerHardwareEntity extends BaseEntity {

    /**
    * @Id
    * @ManyToOne(targetEntity="ExtHardwareEntity", inversedBy="dealerHardware")
    * @JoinColumn(name="id", referencedColumnName="id")
    **/
    private $hardware;

    /**
    * @Id
    * @ManyToOne(targetEntity="DealerEntity")
    * @JoinColumn(name="dealerId", referencedColumnName="id")
    **/
    private $dealer;

    /**
     * @var float
     * @Column(type="float")
     */
    private $cost;

    /**
     * @var string
     * @Column(type="string")
     */
    private $dealerSku;

    /**
     * @var string
     * @Column(type="string")
     */
    private $oemSku;

    /**
     * @var string
     * @Column(type="string")
     */
    private $description;

    /**
     * @var float
     * @Column(type="float")
     */
    private $rent;

    /**
     * @var int
     * @Column(type="integer")
     */
    private $webId;

    /**
     * @var string
     * @Column(type="string")
     */
    private $dataSheetUrl;

    /**
     * @var string
     * @Column(type="string")
     */
    private $reviewsUrl;

    /**
     * @var boolean
     * @Column(type="boolean")
     */
    private $online;

    /**
     * @var string
     * @Column(type="text")
     */
    private $onlineDescription;

    public static function findExtDealerHardware(ExtHardwareEntity $hardware, DealerEntity $dealer) {
        return self::find(['hardware'=>$hardware, 'dealer'=>$dealer]);
    }

    /**
     * @return mixed
     */
    public function getDealer()
    {
        return $this->dealer;
    }

    /**
     * @param mixed $dealer
     */
    public function setDealer($dealer)
    {
        $this->dealer = $dealer;
    }

    /**
     * @return mixed
     */
    public function getHardware()
    {
        return $this->hardware;
    }

    /**
     * @param mixed $hardware
     */
    public function setHardware($hardware)
    {
        $this->hardware = $hardware;
    }

    /**
     * @return float
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * @param float $cost
     */
    public function setCost($cost)
    {
        $this->cost = $cost;
    }

    /**
     * @return string
     */
    public function getDealerSku()
    {
        return $this->dealerSku;
    }

    /**
     * @param string $dealerSku
     */
    public function setDealerSku($dealerSku)
    {
        $this->dealerSku = $dealerSku;
    }

    /**
     * @return string
     */
    public function getOemSku()
    {
        return $this->oemSku;
    }

    /**
     * @param string $oemSku
     */
    public function setOemSku($oemSku)
    {
        $this->oemSku = $oemSku;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getRent()
    {
        return $this->rent;
    }

    /**
     * @param mixed $rent
     */
    public function setRent($rent)
    {
        $this->rent = $rent;
    }

    /**
     * @return int
     */
    public function getWebId()
    {
        return $this->webId;
    }

    /**
     * @param int $webId
     */
    public function setWebId($webId)
    {
        $this->webId = $webId;
    }

    /**
     * @return string
     */
    public function getDataSheetUrl()
    {
        return $this->dataSheetUrl;
    }

    /**
     * @param string $dataSheetUrl
     */
    public function setDataSheetUrl($dataSheetUrl)
    {
        $this->dataSheetUrl = $dataSheetUrl;
    }

    /**
     * @return string
     */
    public function getReviewsUrl()
    {
        return $this->reviewsUrl;
    }

    /**
     * @param string $reviewsUrl
     */
    public function setReviewsUrl($reviewsUrl)
    {
        $this->reviewsUrl = $reviewsUrl;
    }

    /**
     * @return boolean
     */
    public function isOnline()
    {
        return $this->online;
    }

    /**
     * @param boolean $online
     */
    public function setOnline($online)
    {
        $this->online = $online;
    }

    /**
     * @return string
     */
    public function getOnlineDescription()
    {
        return $this->onlineDescription;
    }

    /**
     * @param string $onlineDescription
     */
    public function setOnlineDescription($onlineDescription)
    {
        $this->onlineDescription = $onlineDescription;
    }



}