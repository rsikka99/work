<?php

namespace MPSToolbox\Entities;

/**
 * Class RmsDeviceInstanceEntity
 *
 * @Entity
 * @Table(name="rms_device_instances")
 */
class RmsDeviceInstanceEntity extends BaseEntity {

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="ClientEntity")
     * @JoinColumn(name="clientId", referencedColumnName="id")
     */
    private $client;

    /** @Column(type="string") */
    private $assetId;
    /** @Column(type="string") */
    private $ipAddress;
    /** @Column(type="string") */
    private $serialNumber;

    /**
     * @ManyToOne(targetEntity="MasterDeviceEntity")
     * @JoinColumn(name="masterDeviceId", referencedColumnName="id")
     **/
    private $masterDevice;

    /** @Column(type="string") */
    private $rawDeviceName;

    /** @Column(type="string") */
    private $fullDeviceName;

    /** @Column(type="string") */
    private $manufacturer;

    /** @Column(type="string") */
    private $modelName;

    /** @Column(type="string") */
    private $location;

    /** @Column(type="date") */
    private $reportDate;

    /**
     * @Column(type="boolean", name="`ignore`")
     * @var boolean
     */
    private $ignore = 0;

    /** @Column(type="integer") */
    private $email_template = 0;

    /**
     * @param $clientId
     * @param $ipAddress
     * @param $serialNumber
     * @param $assetId
     * @return RmsDeviceInstanceEntity
     */
    public static function findOne($clientId, $ipAddress, $serialNumber, $assetId, $throwException=false) {
        $db = \Zend_Db_Table::getDefaultAdapter();
        if ((''.$assetId)!=='') {
            $st = $db->prepare("SELECT id FROM rms_device_instances WHERE clientId=:c AND ipAddress=:i AND serialNumber=:s AND assetId=:a");
            $st->execute(['c' => '' . $clientId, 'i' => '' . $ipAddress, 's' => '' . $serialNumber, 'a' => '' . $assetId]);
            $arr = $st->fetchAll();
            if (!empty($arr)) {
                return self::find($arr[0]['id']);
            }
        }
        #--
        $st = $db->prepare("SELECT id FROM rms_device_instances WHERE clientId=:c AND ipAddress=:i AND serialNumber=:s and assetId=''");
        $st->execute(['c' => ''.$clientId, 'i' => ''.$ipAddress, 's' => ''.$serialNumber]);
        $arr = $st->fetchAll();
        if (!empty($arr)) {
            if (count($arr)==1) return self::find($arr[0]['id']);
            if ($throwException) {
                throw new \Exception("cannot uniquely identify device {$ipAddress} {$serialNumber} {$assetId}");
            }
        }
        #--
        return null;
    }

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
     * @return ClientEntity
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param mixed $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * @return mixed
     */
    public function getAssetId()
    {
        return $this->assetId;
    }

    /**
     * @param mixed $assetId
     */
    public function setAssetId($assetId)
    {
        $this->assetId = $assetId;
    }

    /**
     * @return mixed
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * @param mixed $ipAddress
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;
    }

    /**
     * @return mixed
     */
    public function getSerialNumber()
    {
        return $this->serialNumber;
    }

    /**
     * @param mixed $serialNumber
     */
    public function setSerialNumber($serialNumber)
    {
        $this->serialNumber = $serialNumber;
    }

    /**
     * @return MasterDeviceEntity
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
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param mixed $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return mixed
     */
    public function getRawDeviceName()
    {
        return $this->rawDeviceName;
    }

    /**
     * @param mixed $rawDeviceName
     */
    public function setRawDeviceName($rawDeviceName)
    {
        $this->rawDeviceName = $rawDeviceName;
    }

    /**
     * @return mixed
     */
    public function getFullDeviceName()
    {
        return $this->fullDeviceName;
    }

    /**
     * @param mixed $fullDeviceName
     */
    public function setFullDeviceName($fullDeviceName)
    {
        $this->fullDeviceName = $fullDeviceName;
    }

    /**
     * @return mixed
     */
    public function getManufacturer()
    {
        return $this->manufacturer;
    }

    /**
     * @param mixed $manufacturer
     */
    public function setManufacturer($manufacturer)
    {
        $this->manufacturer = $manufacturer;
    }

    /**
     * @return mixed
     */
    public function getModelName()
    {
        return $this->modelName;
    }

    /**
     * @param mixed $modelName
     */
    public function setModelName($modelName)
    {
        $this->modelName = $modelName;
    }

    /**
     * @return mixed
     */
    public function getReportDate()
    {
        return $this->reportDate;
    }

    /**
     * @param mixed $reportDate
     */
    public function setReportDate($reportDate)
    {
        $this->reportDate = $reportDate;
    }

    /**
     * @return mixed
     */
    public function getIgnore()
    {
        return $this->ignore;
    }

    /**
     * @param mixed $ignore
     */
    public function setIgnore($ignore)
    {
        $this->ignore = $ignore;
    }

    /**
     * @return mixed
     */
    public function getEmailTemplate()
    {
        return $this->email_template;
    }

    /**
     * @param mixed $email_template
     */
    public function setEmailTemplate($email_template)
    {
        $this->email_template = $email_template;
    }




}