<?php

class Proposalgen_Model_Mapper_Meter extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_Meter";
    static $_instance;

    /**
     *
     * @return Tangent_Model_Mapper_Abstract
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
            $object = new Proposalgen_Model_Meter();
            $object->setMeterId($row->meter_id)
                ->setMeterType($row->meter_type)
                ->setStartMeter($row->start_meter)
                ->setEndMeter($row->end_meter);
        }
        catch ( Exception $e )
        {
            throw new Exception("Failed to map a meter row", 0, $e);
        }
        return $object;
    }

    public function fetchAllForDevice ($DeviceInstanceID)
    {
        $entries = array ();
        try
        {
            $resultSet = $this->getDbTable()->fetchAll(array (
                    'device_instance_id = ?' => $DeviceInstanceID 
            ));
            foreach ( $resultSet as $row )
            {
                $entries [$row->meter_type] = $this->mapRowToObject($row);
            }
        }
        catch ( Exception $e )
        {
            throw new Exception("Error getting all the meters for a device", 0, $e);
        }
        return $entries;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     * 
     * @param unknown_type $object            
     */
    public function save (Proposalgen_Model_Meter $object)
    {
        $primaryKey = 0;
        try
        {
            $data ["meter_id"] = $object->getMeterId();
            $data ["device_instance_id"] = $object->getDeviceInstanceId();
            $data ["meter_type"] = $object->getMeterType();
            $data ["start_meter"] = $object->getStartMeter();
            $data ["end_meter"] = $object->getEndMeter();
            
            $primaryKey = $this->saveRow($data);
        }
        catch ( Exception $e )
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }
        return $primaryKey;
    }
}
