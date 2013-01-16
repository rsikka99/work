<?php
class Proposalgen_Model_Mapper_DevicePf extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_PFDevice";
    static $_instance;

    /**
     * @return Proposalgen_Model_Mapper_DevicePf
     */
    public static function getInstance ()
    {
        if (!isset(self::$_instance))
        {
            $className       = get_class();
            self::$_instance = new $className();
        }

        return self::$_instance;
    }

    /**
     * @param mixed $primaryKey
     *
     * @return Proposalgen_Model_DevicePf
     */
    public function find ($primaryKey)
    {
        return parent::find($primaryKey);
    }

    /**
     * Searches the database for a printfleet device that matches the device name or model id
     *
     * @param string $deviceName
     *            The device name
     * @param string $modelId
     *            The printfleet model id
     *
     * @return Proposalgen_Model_DevicePf
     */
    public function fetchByDeviceNameOrModelId ($deviceName, $modelId)
    {
        $select = $this->getDbTable()->select(true);
        $select->where('pf_db_devicename = ?', $deviceName);
        $select->orWhere('pf_model_id = ?', $modelId);
        $where  = implode(' ', $select->getPart(Zend_Db_Table_Select::WHERE));
        $result = $this->fetchRow(array(
                                       $where
                                  ));
        if (!$result)
        {
            return false;
        }

        return $result;
    }

    /**
     * Maps a database row object to an Proposalgen_Model
     *
     * @param Zend_Db_Table_Row $row
     *
     * @throws Exception
     * @return \Proposalgen_Model_DevicePf
     */
    public function mapRowToObject ($row)
    {
        $object = null;
        try
        {
            $object                   = new Proposalgen_Model_DevicePf();
            $object->devicesPfId      = $row->id;
            $object->pfModelId        = $row->pf_model_id;
            $object->pfDbDeviceName   = $row->pf_db_devicename;
            $object->pfDbManufacturer = $row->pf_db_manufacturer;
            $object->dateCreated      = $row->date_created;
            $object->createdBy        = $row->created_by;
        }
        catch (Exception $e)
        {
            throw new Exception("Failed to map a device pf row", 0, $e);
        }

        return $object;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     *
     * @param \Proposalgen_Model_DevicePf $object
     *
     * @throws Exception
     * @return string
     */
    public function save ($object)
    {
        try
        {
            $data ["id"]                 = $object->devicesPfId;
            $data ["pf_model_id"]        = $object->pfModelId;
            $data ["pf_db_devicename"]   = $object->pfDbDeviceName;
            $data ["pf_db_manufacturer"] = $object->pfDbManufacturer;
            $data ["date_created"]       = $object->dateCreated;
            $data ["created_by"]         = $object->createdBy;
            $primaryKey                  = $this->saveRow($data);
        }
        catch (Exception $e)
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }

        return $primaryKey;
    }
}