<?php
class Proposalgen_Model_Mapper_MasterMatchupPf extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_PFMasterMatchup";
    static $_instance;

    /**
     * @return Proposalgen_Model_Mapper_MasterMatchupPf
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
     * @throws Exception
     * @return \Proposalgen_Model_MasterMatchupPf
     */
    public function mapRowToObject ($row)
    {
        $object = null;
        try
        {
            $object                 = new Proposalgen_Model_MasterMatchupPf();
            $object->masterDeviceId = $row->master_device_id;
            $object->devicesPfId    = $row->devices_pf_id;
        }
        catch (Exception $e)
        {
            throw new Exception("Failed to map a master matchup pf row", 0, $e);
        }

        return $object;
    }

    /**
     * @param \Proposalgen_Model_MasterMatchupPf $object
     *
     * @return string
     * @throws Exception
     */
    public function save ($object)
    {
        try
        {
            $data ["master_device_id"] = $object->masterDeviceId;
            $data ["devices_pf_id"]    = $object->devicesPfId;
            $primaryKey                = $this->saveRow($data);
        }
        catch (Exception $e)
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }

        return $primaryKey;
    }
}