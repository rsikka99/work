<?php
/**
 * Class Proposalgen_Model_Mapper_PartType
 */
class Proposalgen_Model_Mapper_PartType extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_PartType";
    static $_instance;

    /**
     * @return Proposalgen_Model_Mapper_PartType
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

    static $_partTypes = array();

    /**
     * @param mixed $id
     *
     * @return Proposalgen_Model_PartType
     */
    public function find ($id)
    {
        if (!array_key_exists($id, self::$_partTypes))
        {
            self::$_partTypes [$id] = parent::find($id);
        }

        return self::$_partTypes [$id];
    }

    /**
     * Maps a database row object to an Proposalgen_Model
     *
     * @param Zend_Db_Table_Row $row
     *
     * @throws Exception
     * @return Proposalgen_Model_PartType
     */
    public function mapRowToObject ($row)
    {
        $object = null;
        try
        {
            $object             = new Proposalgen_Model_PartType();
            $object->partTypeId = $row->id;
            $object->typeName   = $row->name;
        }
        catch (Exception $e)
        {
            throw new Exception("Failed to map a part type row", 0, $e);
        }

        return $object;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     *
     * @param Proposalgen_Model_PartType $object
     *
     * @throws Exception
     * @return string
     */
    public function save ($object)
    {
        try
        {
            $data ["id"]   = $object->partTypeId;
            $data ["name"] = $object->typeName;
            $primaryKey    = $this->saveRow($data);
        }
        catch (Exception $e)
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }

        return $primaryKey;
    }
}