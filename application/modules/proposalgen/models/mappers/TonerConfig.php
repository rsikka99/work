<?php
/**
 * Class Proposalgen_Model_Mapper_TonerConfig
 */
class Proposalgen_Model_Mapper_TonerConfig extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_TonerConfig";
    static $_instance;

    /**
     * @return Proposalgen_Model_Mapper_TonerConfig
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

    static $_tonerConfigs = array();

    /**
     * @param int $id
     *
     * @return Proposalgen_Model_TonerConfig
     */
    public function find ($id)
    {
        if (!array_key_exists($id, self::$_tonerConfigs))
        {
            self::$_tonerConfigs [$id] = parent::find($id);
        }

        return self::$_tonerConfigs [$id];
    }

    /**
     * Maps a database row object to an Proposalgen_Model
     *
     * @param Zend_Db_Table_Row $row
     *
     * @throws Exception
     * @return Proposalgen_Model_TonerConfig
     */
    public function mapRowToObject ($row)
    {
        $object = null;
        try
        {
            $object                  = new Proposalgen_Model_TonerConfig();
            $object->tonerConfigId   = $row->id;
            $object->tonerConfigName = $row->name;
        }
        catch (Exception $e)
        {
            throw new Exception("Failed to map a toner config row", 0, $e);
        }

        return $object;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     *
     * @param Proposalgen_Model_TonerConfig $object
     *
     * @throws Exception
     * @return string
     */
    public function save ($object)
    {
        try
        {
            $data ["id"]   = $object->tonerConfigId;
            $data ["name"] = $object->tonerConfigName;
            $primaryKey    = $this->saveRow($data);
        }
        catch (Exception $e)
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }

        return $primaryKey;
    }
}