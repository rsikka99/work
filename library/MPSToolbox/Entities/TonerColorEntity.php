<?php

namespace MPSToolbox\Entities;

/**
 * Class TonerColorEntity
 * @package MPSToolbox\Entities
 *
 * @Entity
 * @Table(name="toner_colors")
 */
class TonerColorEntity extends BaseEntity {

    const BLACK       = 1;
    const CYAN        = 2;
    const MAGENTA     = 3;
    const YELLOW      = 4;
    const THREE_COLOR = 5;
    const FOUR_COLOR  = 6;

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