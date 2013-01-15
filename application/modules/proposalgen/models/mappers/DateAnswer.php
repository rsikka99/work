<?php

class Proposalgen_Model_Mapper_DateAnswer extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_DateAnswer";
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
            $object = new Proposalgen_Model_DateAnswer();
            $object->questionId = $row->question_id;
            $object->reportId = $row->report_id;
            $object->answer = $row->date_answer;
        }
        catch ( Exception $e )
        {
            throw new Exception("Failed to map a date answer row", 0, $e);
        }
        return $object;
    }

    /**
     * Finds an answer to a related question
     *
     * @param unknown_type $questionId            
     */
    public function getQuestionAnswer ($questionId, $reportId)
    {
        if ($questionId === null)
        {
            throw new InvalidArgumentException("You must supply a question id.");
        }
        if ($reportId === null)
        {
            return null;
        }
        $answer = null;
        $result = $this->getDbTable()->fetchAll(array (
                "question_id = ?" => $questionId, 
                "report_id = ?" => $reportId 
        ));
        if ($result->current())
        {
            $answer = $result->current()->date_answer;
        }
        return $answer;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     *
     * @param unknown_type $object            
     */
    public function save (Proposalgen_Model_DateAnswer $object)
    {
        $primaryKey = 0;
        try
        {
            $data ["question_id"] = $object->questionId;
            $data ["report_id"] = $object->reportId;
            $data ["date_answer"] = $object->answer;
            
            $primaryKey = $this->saveRow($data);
        }
        catch ( Exception $e )
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }
        return $primaryKey;
    }
}