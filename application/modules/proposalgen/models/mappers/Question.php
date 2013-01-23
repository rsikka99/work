<?php
class Proposalgen_Model_Mapper_Question extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_Question";
    static $_instance;

    /**
     * @return Proposalgen_Model_Mapper_Question
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
     * @return Proposalgen_Model_Question
     */
    public function mapRowToObject ($row)
    {
        $object = null;
        try
        {
            // NOTE: We are not pulling in answers here because we require a report id to be able to get answers
            $object                      = new Proposalgen_Model_Question();
            $object->questionId          = $row->id;
            $object->questionDescription = $row->description;
        }
        catch (Exception $e)
        {
            throw new Exception("Failed to map a question row", 0, $e);
        }

        return $object;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     *
     * @param Proposalgen_Model_Question $object
     *
     * @throws Exception
     * @return string
     */
    public function save ($object)
    {
        try
        {
            $data ["question_id"]   = $object->questionId;
            $data ["question_desc"] = $object->questionDescription;
            $primaryKey             = $this->saveRow($data);
        }
        catch (Exception $e)
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }

        return $primaryKey;
    }
}