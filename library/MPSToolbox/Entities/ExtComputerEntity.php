<?php

namespace MPSToolbox\Entities;

/**
 * Class ExtComputerEntity
 * @package MPSToolbox\Entities
 *
 * @Entity
 * @Table(name="ext_computer")
 */
class ExtComputerEntity extends ExtHardwareEntity {

    use EntityTrait;

    /** @Column(type="integer")
     * @var float
     */
    private $ram = 0;

    /**
     * @return int
     */
    public function getRam()
    {
        return $this->ram;
    }

    /**
     * @param int $ram
     */
    public function setRam($ram)
    {
        $this->ram = $ram;
    }





}