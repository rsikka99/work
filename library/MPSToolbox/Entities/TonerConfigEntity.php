<?php

namespace MPSToolbox\Entities;

/**
 * Class TonerConfigEntity
 * @package MPSToolbox\Entities
 *
 * @Entity
 * @Table(name="toner_configs")
 */
class TonerConfigEntity extends BaseEntity {

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /**
     * @var string
     * @Column(type="string") */
    private $name;

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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }



}