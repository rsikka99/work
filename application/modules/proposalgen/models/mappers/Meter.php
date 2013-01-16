<?php
class Proposalgen_Model_Mapper_Meter extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_Meter";
    static $_instance;

    /**
     * @return Proposalgen_Model_Mapper_Meter
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
     * Maps a database row object to an Proposalgen_Model
     *
     * @param Zend_Db_Table_Row $row
     *
     * @return \Proposalgen_Model_Meter
     * @throws Exception
     */
    public function mapRowToObject ($row)
    {
        $object = null;
        try
        {
            $object             = new Proposalgen_Model_Meter();
            $object->meterId    = $row->id;
            $object->meterType  = $row->meter_type;
            $object->startMeter = $row->start_meter;
            $object->endMeter   = $row->end_meter;
        }
        catch (Exception $e)
        {
            throw new Exception("Failed to map a meter row", 0, $e);
        }

        return $object;
    }

    /**
     * @param int $DeviceInstanceID
     *
     * @return Proposalgen_Model_Meter[]
     * @throws Exception
     */
    public function fetchAllForDevice ($DeviceInstanceID)
    {
        $entries = array();
        try
        {
            $resultSet = $this->getDbTable()->fetchAll(array(
                                                            'device_instance_id = ?' => $DeviceInstanceID
                                                       ));
            foreach ($resultSet as $row)
            {
                $entries [$row->meter_type] = $this->mapRowToObject($row);
            }
        }
        catch (Exception $e)
        {
            throw new Exception("Error getting all the meters for a device", 0, $e);
        }

        return $entries;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     *
     * @param \Proposalgen_Model_Meter $object
     *
     * @throws Exception
     * @return string
     */
    public function save ($object)
    {
        try
        {
            $data ["id"]                 = $object->meterId;
            $data ["device_instance_id"] = $object->deviceInstanceId;
            $data ["meter_type"]         = $object->meterType;
            $data ["start_meter"]        = $object->startMeter;
            $data ["end_meter"]          = $object->endMeter;
            $primaryKey                  = $this->saveRow($data);
        }
        catch (Exception $e)
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }

        return $primaryKey;
    }
}