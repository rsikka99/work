<?php

/**
 * Class Proposalgen_Model_Mapper_TonerColor
 */
class Proposalgen_Model_Mapper_TonerColor extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_TonerColor";
    static $_instance;

    /**
     * @return Proposalgen_Model_Mapper_TonerColor
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

    static $_tonerColors = array();

    /**
     * @param int $id
     *
     * @return Proposalgen_Model_TonerColor
     */
    public function find ($id)
    {
        if (!array_key_exists($id, self::$_tonerColors))
        {
            self::$_tonerColors [$id] = parent::find($id);
        }

        return self::$_tonerColors [$id];
    }

    /**
     * Maps a database row object to an Proposalgen_Model
     *
     * @param Zend_Db_Table_Row $row
     *
     * @throws Exception
     * @return Proposalgen_Model_TonerColor
     */
    public function mapRowToObject ($row)
    {
        $object = null;
        try
        {
            $object                 = new Proposalgen_Model_TonerColor();
            $object->tonerColorId   = $row->id;
            $object->tonerColorName = $row->name;
        }
        catch (Exception $e)
        {
            throw new Exception("Failed to map a toner color row", 0, $e);
        }

        return $object;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     *
     * @param Proposalgen_Model_TonerColor $object
     *
     * @throws Exception
     * @return string
     */
    public function save ($object)
    {
        try
        {
            $data ["id"]   = $object->tonerColorId;
            $data ["name"] = $object->tonerColorName;
            $primaryKey    = $this->saveRow($data);
        }
        catch (Exception $e)
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }

        return $primaryKey;
    }
}