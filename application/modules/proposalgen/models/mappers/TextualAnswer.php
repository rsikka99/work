<?php

class Proposalgen_Model_Mapper_TextualAnswer extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_TextAnswer";
    static $_instance;

    /**
     *
     * @return Proposalgen_Model_Mapper_TextualAnswer
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
     * @return Proposalgen_Model_TextualAnswer
     * @throws Exception
     */
    public function mapRowToObject (Zend_Db_Table_Row $row)
    {
        $object = null;
        try
        {
            $object                = new Proposalgen_Model_TextualAnswer();
            $object->setQuestionId = $row->question_id;
            $object->setReportId   = $row->report_id;
            $object->setAnswer     = $row->textual_answer;
        }
        catch (Exception $e)
        {
            throw new Exception("Failed to map a textual answer row", 0, $e);
        }

        return $object;
    }

    /**
     * Finds an answer to a related question
     *
     * @param int $questionId
     * @param int $reportId
     *
     * @throws InvalidArgumentException
     * @return null|string
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
        $result = $this->getDbTable()->fetchAll(array(
                                                     "question_id = ?" => $questionId,
                                                     "report_id = ?"   => $reportId
                                                ));
        if ($result->current())
        {
            $answer = $result->current()->textual_answer;
        }

        return $answer;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     *
     * @param Proposalgen_Model_TextualAnswer $object
     *
     * @throws Exception
     * @return string
     */
    public function save (Proposalgen_Model_TextualAnswer $object)
    {
        try
        {
            $data ["question_id"]    = $object->questionId;
            $data ["report_id"]      = $object->reportId;
            $data ["textual_answer"] = $object->answer;

            $primaryKey = $this->saveRow($data);
        }
        catch (Exception $e)
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }

        return $primaryKey;
    }
}
