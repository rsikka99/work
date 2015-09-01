<?php

namespace MPSToolbox\Entities;

/**
 * Class ExtComputerEntity
 * @package MPSToolbox\Entities
 *
 * @Entity
 * @Table(name="ext_peripheral")
 */
class ExtPeripheralEntity extends ExtHardwareEntity {

    /** @Column(type="string")
     * @var string
     */
    private $appliesTo = '';

    /**
     * @return string
     */
    public function getAppliesTo()
    {
        if (is_string($this->appliesTo)) $this->appliesTo=explode(',',$this->appliesTo);
        return $this->appliesTo;
    }

    /**
     * @param string $appliesTo
     */
    public function setAppliesTo($appliesTo)
    {
        if (is_array($appliesTo)) $appliesTo=implode(',',$appliesTo);
        $this->appliesTo = $appliesTo;
    }


}