<?php

class Proposalgen_Model_Mapper_DevicePf extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_PFDevice";
    static $_instance;

    /**
     *
     * @return Proposalgen_Model_Mapper_DevicePf
     */
    public static function getInstance ()
    {
        if (! isset(self::$_instance))
        {
            $className = get_class();
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
     * @return Proposalgen_Model_DevicePf
     */
    public function fetchByDeviceNameOrModelId ($deviceName, $modelId)
    {
        $select = $this->getDbTable()->select(true);
        $select->where('pf_db_devicename = ?', $deviceName);
        $select->orWhere('pf_model_id = ?', $modelId);
        $where = implode(' ', $select->getPart(Zend_Db_Table_Select::WHERE));
        $result = $this->fetchRow(array (
                $where 
        ));
        if (! $result)
        {
            return false;
        }
        return $result;
    }

    /**
     * Maps a database row object to an Proposalgen_Model
     *
     * @param Zend_Db_Table_Row $row            
     * @return The appropriate Proposalgen_Model
     */
    public function mapRowToObject (Zend_Db_Table_Row $row)
    {
        $object = null;
        try
        {
            $object = new Proposalgen_Model_DevicePf();
            $object->setDevicesPfId($row->id)
                ->setPfModelId($row->pf_model_id)
                ->setPfDbDeviceName($row->pf_db_devicename)
                ->setPfDbManufacturer($row->pf_db_manufacturer)
                ->setDateCreated($row->date_created)
                ->setCreatedBy($row->created_by);
        }
        catch ( Exception $e )
        {
            throw new Exception("Failed to map a device pf row", 0, $e);
        }
        return $object;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     *
     * @param unknown_type $object            
     */
    public function save (Proposalgen_Model_DevicePf $object)
    {
        $primaryKey = 0;
        try
        {
            $data ["id"] = $object->getDevicesPfId();
            $data ["pf_model_id"] = $object->getPfModelId();
            $data ["pf_db_devicename"] = $object->getPfDbDeviceName();
            $data ["pf_db_manufacturer"] = $object->getPfDbManufacturer();
            $data ["date_created"] = $object->getDateCreated();
            $data ["created_by"] = $object->getCreatedBy();
            
            $primaryKey = $this->saveRow($data);
        }
        catch ( Exception $e )
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }
        return $primaryKey;
    }
}
