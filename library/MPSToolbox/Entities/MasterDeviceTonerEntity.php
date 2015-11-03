<?php

namespace MPSToolbox\Entities;

/**
 * Class MasterDeviceTonerEntity
 * @package MPSToolbox\Entities
 *
 * @Entity
 * @Table(name="device_toners")
 */
class MasterDeviceTonerEntity extends BaseEntity {

    /**
     * @Id
     * @ManyToOne(targetEntity="MasterDeviceEntity")
     * @JoinColumn(name="master_device_id", referencedColumnName="id")
     **/
    private $masterDevice;

    /**
     * @id
     * @ManyToOne(targetEntity="TonerEntity")
     * @JoinColumn(name="toner_id", referencedColumnName="id")
     **/
    private $toner;

    /**
     * @ManyToOne(targetEntity="UserEntity")
     * @JoinColumn(name="userId", referencedColumnName="id")
     **/
    private $user;

    /** @Column(type="boolean") */
    private $isSystemDevice;

    /**
     * @return mixed
     */
    public function getMasterDevice()
    {
        return $this->masterDevice;
    }

    /**
     * @param mixed $masterDevice
     */
    public function setMasterDevice($masterDevice)
    {
        $this->masterDevice = $masterDevice;
    }

    /**
     * @return mixed
     */
    public function getToner()
    {
        return $this->toner;
    }

    /**
     * @param mixed $toner
     */
    public function setToner($toner)
    {
        $this->toner = $toner;
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


}