<?php

namespace MPSToolbox\Entities;

/**
 * Class ManufacturerEntity
 * @package MPSToolbox\Entities
 *
 * @Entity
 * @Table(name="ext_dealer_hardware")
 */
class ExtDealerHardwareEntity {

    use EntityTrait;

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
    private $srp;

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
     * @return float
     */
    public function getSrp()
    {
        return $this->srp;
    }

    /**
     * @param float $srp
     */
    public function setSrp($srp)
    {
        $this->srp = $srp;
    }



}