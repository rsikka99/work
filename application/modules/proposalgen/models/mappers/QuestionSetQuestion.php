<?php
class Proposalgen_Model_Mapper_QuestionSetQuestion extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_QuestionSetQuestion";
    static $_instance;

    /**
     *
     * @return Proposalgen_Model_Mapper_QuestionSetQuestion
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
     * @return \Proposalgen_Model_QuestionSetQuestion
     * @throws Exception
     */
    public function mapRowToObject ($row)
    {
        $object = null;
        try
        {
            $object                = new Proposalgen_Model_QuestionSetQuestion();
            $object->questionId    = $row->question_id;
            $object->questionSetId = $row->questionset_id;
        }
        catch (Exception $e)
        {
            throw new Exception("Failed to map a question set question row", 0, $e);
        }

        return $object;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     *
     * @param \Proposalgen_Model_QuestionSetQuestion $object
     *
     * @throws Exception
     * @return string
     */
    public function save ($object)
    {
        try
        {
            $data ["question_id"]    = $object->questionId;
            $data ["questionset_id"] = $object->questionSetName;
            $primaryKey              = $this->saveRow($data);
        }
        catch (Exception $e)
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }

        return $primaryKey;
    }
}