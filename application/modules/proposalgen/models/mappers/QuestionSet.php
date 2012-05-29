<?php

class Proposalgen_Model_Mapper_QuestionSet extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_QuestionSet";
    static $_instance;

    /**
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
     * @param Zend_Db_Table_Row $row
     * @return The appropriate Proposalgen_Model
     */
    public function mapRowToObject (Zend_Db_Table_Row $row)
    {
        $object = null;
        try
        {
            $object = new Proposalgen_Model_QuestionSet();
            $object->setQuestionId($row->questionset_id)->setQuestionDescription($row->questionset_name);
        }
        catch ( Exception $e )
        {
            throw new Exception("Failed to map a questionset row", 0, $e);
        }
        return $object;
    }

    function getQuestionSetQuestions ($questionSetId, $reportId)
    {
        $questions = array ();
        $questionSetQuestionMapper = Proposalgen_Model_Mapper_QuestionSetQuestion::getInstance();
        $questionMapper = Proposalgen_Model_Mapper_Question::getInstance();
        $dateAnswerMapper = Proposalgen_Model_Mapper_DateAnswer::getInstance();
        $numericAnswerMapper = Proposalgen_Model_Mapper_NumericAnswer::getInstance();
        $texualAnswerMapper = Proposalgen_Model_Mapper_TextualAnswer::getInstance();
        
        $results = $questionSetQuestionMapper->fetchAll(array (
                "questionset_id" => $questionSetId ));
        if ($results)
        {
            foreach ( $results as $question )
            {
                $tempQuestion = $questionMapper->find($question->QuestionId);
                // Get the answers
                $tempQuestion->setDateAnswer($dateAnswerMapper->getQuestionAnswer($question->QuestionId, $reportId))
                    ->setNumericAnswer($numericAnswerMapper->getQuestionAnswer($question->QuestionId, $reportId))
                    ->setTextualAnswer($texualAnswerMapper->getQuestionAnswer($question->QuestionId, $reportId));
                $questions [$question->QuestionId] = $tempQuestion;
            }
        }
        return $questions;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     * @param unknown_type $object
     */
    public function save (Proposalgen_Model_QuestionSet $object)
    {
        $primaryKey = 0;
        try
        {
            $data ["questionset_id"] = $object->getQuestionId();
            $data ["questionset_name"] = $object->getQuestionSetName();
            
            $primaryKey = $this->saveRow($data);
        }
        catch ( Exception $e )
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }
        return $primaryKey;
    }
}
?>