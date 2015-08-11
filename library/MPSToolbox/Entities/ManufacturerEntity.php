<?php

namespace MPSToolbox\Entities;

/**
 * Class ManufacturerEntity
 * @package MPSToolbox\Entities
 *
 * @property int    id
 * @property string fullname
 * @property string displayname
 * @property bool   isDeleted
 *
 * @Entity
 * @Table(name="manufacturers")
 */
class ManufacturerEntity {

    use EntityTrait;

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /** @Column(type="string") */
    private $fullname;

    /** @Column(type="string") */
    private $displayname;

    /** @Column(type="boolean") */
    private $isDeleted;

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
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * @param string $fullname
     */
    public function setFullname($fullname)
    {
        $this->fullname = $fullname;
    }

    /**
     * @return string
     */
    public function getDisplayname()
    {
        return $this->displayname;
    }

    /**
     * @param string $displayname
     */
    public function setDisplayname($displayname)
    {
        $this->displayname = $displayname;
    }

    /**
     * @return boolean
     */
    public function isIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * @param boolean $isDeleted
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;
    }




}