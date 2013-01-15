<?php

class Proposalgen_Model_Mapper_QuestionSet extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_QuestionSet";
    static $_instance;

    /**
     *
     * @return Proposalgen_Model_Mapper_QuestionSet
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
     * @return Proposalgen_Model_QuestionSet
     */
    public function mapRowToObject (Zend_Db_Table_Row $row)
    {
        $object = null;
        try
        {
            $object                      = new Proposalgen_Model_QuestionSet();
            $object->questionId          = $row->id;
            $object->QuestionDescription = $row->name;
        }
        catch (Exception $e)
        {
            throw new Exception("Failed to map a question set row", 0, $e);
        }

        return $object;
    }

    /**
     * Gets all the questions for a given question set with the answers to them.
     * The array keys are the question ids so that you may look up a question using it's id.
     *
     * @param int $questionSetId
     * @param int $reportId
     *
     * @return array An array of Proposalgen_Model_Question with answers already fetched.
     */
    function getQuestionSetQuestions ($questionSetId, $reportId)
    {
        // In case we get a model, turn it into the id we want. MAGIC!
        if ($questionSetId instanceof Proposalgen_Model_QuestionSet)
        {
            $questionSetId = $questionSetId->questionId;
        }

        // Same deal with a report model.
        if ($reportId instanceof Proposalgen_Model_Report)
        {
            $reportId = $reportId->getReportId();
        }

        $questions                 = array();
        $questionSetQuestionMapper = Proposalgen_Model_Mapper_QuestionSetQuestion::getInstance();
        $questionMapper            = Proposalgen_Model_Mapper_Question::getInstance();
        $dateAnswerMapper          = Proposalgen_Model_Mapper_DateAnswer::getInstance();
        $numericAnswerMapper       = Proposalgen_Model_Mapper_NumericAnswer::getInstance();
        $texualAnswerMapper        = Proposalgen_Model_Mapper_TextualAnswer::getInstance();

        // Get all the questions for the question set
        $results = $questionSetQuestionMapper->fetchAll(array(
                                                             "questionset_id = ?" => $questionSetId
                                                        ));

        // Did we find them? Let's hope we did because otherwise it would be bad.
        if ($results)
        {
            // Loop through each question and get the answer for each one.
            /* @var $results Proposalgen_Model_QuestionSetQuestion[] */
            foreach ($results as $questionSetQuestion)
            {
                // Get the question
                $tempQuestion = $questionMapper->find($questionSetQuestion->questionId);

                // Get the answers
                $tempQuestion->DateAnswer    = $dateAnswerMapper->getQuestionAnswer($questionSetQuestion->questionId, $reportId);
                $tempQuestion->NumericAnswer = $numericAnswerMapper->getQuestionAnswer($questionSetQuestion->questionId, $reportId);
                $tempQuestion->TextualAnswer = $texualAnswerMapper->getQuestionAnswer($questionSetQuestion->questionId, $reportId);

                // Create an array with the complete question models (key is the question id for easy references elsewhere.
                $questions [$question->QuestionId] = $tempQuestion;
            }
        }

        // Back to you bob!
        return $questions;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     *
     * @param unknown_type $object
     */
    public function save (Proposalgen_Model_QuestionSet $object)
    {
        $primaryKey = 0;
        try
        {
            $data ["id"]   = $object->questionId;
            $data ["name"] = $object->questionSetName;

            $primaryKey = $this->saveRow($data);
        }
        catch (Exception $e)
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }

        return $primaryKey;
    }
}
