<?php

namespace MPSToolbox\Entities;

/**
 * Class ClientEntity
 * @package MPSToolbox\Entities
 *
 * @Entity
 * @Table(name="clients")
 */
class ClientEntity extends BaseEntity {

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
    private $accountNumber;

    /** @Column(type="string") */
    private $companyName;

    /** @Column(type="string") */
    private $legalName;

    /** @Column(type="integer") */
    private $employeeCount;

    /** @Column(type="bigint") */
    private $webId;

    /** @Column(type="string") */
    private $notSupportedMasterDevices;

    /** @Column(type="string") */
    private $deviceGroup;

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
     * @return DealerEntity
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
    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    /**
     * @param mixed $accountNumber
     */
    public function setAccountNumber($accountNumber)
    {
        $this->accountNumber = $accountNumber;
    }

    /**
     * @return mixed
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * @param mixed $companyName
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;
    }

    /**
     * @return mixed
     */
    public function getLegalName()
    {
        return $this->legalName;
    }

    /**
     * @param mixed $legalName
     */
    public function setLegalName($legalName)
    {
        $this->legalName = $legalName;
    }

    /**
     * @return mixed
     */
    public function getEmployeeCount()
    {
        return $this->employeeCount;
    }

    /**
     * @param mixed $employeeCount
     */
    public function setEmployeeCount($employeeCount)
    {
        $this->employeeCount = $employeeCount;
    }

    /**
     * @return mixed
     */
    public function getWebId()
    {
        return $this->webId;
    }

    /**
     * @param mixed $webId
     */
    public function setWebId($webId)
    {
        $this->webId = $webId;
    }

    /**
     * @return mixed
     */
    public function getNotSupportedMasterDevices()
    {
        return $this->notSupportedMasterDevices;
    }

    /**
     * @param mixed $notSupportedMasterDevices
     */
    public function setNotSupportedMasterDevices($notSupportedMasterDevices)
    {
        $this->notSupportedMasterDevices = $notSupportedMasterDevices;
    }

    /**
     * @return mixed
     */
    public function getDeviceGroup()
    {
        return $this->deviceGroup;
    }

    /**
     * @param mixed $deviceGroup
     */
    public function setDeviceGroup($deviceGroup)
    {
        $this->deviceGroup = $deviceGroup;
    }





}