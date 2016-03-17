<?php

namespace MPSToolbox\Entities;

/**
 * Class DealerPriceLevelEntity
 * @package MPSToolbox\Entities
 *
 * @Entity
 * @Table(name="dealer_price_levels")
 */
class DealerPriceLevelEntity extends BaseEntity {

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="DealerEntity")
     * @JoinColumn(name="dealerId", referencedColumnName="id")
     **/
    private $dealer;


    /** @Column(type="string") */
    private $name;


    /** @Column(type="float") */
    private $margin;

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
    public function getMargin()
    {
        return $this->margin;
    }

    /**
     * @param mixed $margin
     */
    public function setMargin($margin)
    {
        $this->margin = $margin;
    }







}