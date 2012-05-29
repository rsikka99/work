<?php
/**
 * Class Proposalgen_Model_Question
 * @author "Lee Robert"
 */
class Proposalgen_Model_Question extends Tangent_Model_Abstract
{
    protected $QuestionId;
    protected $QuestionDescription;
    
    protected $DateAnswer;
    protected $NumericAnswer;
    protected $TextualAnswer;
    
    
	/**
	 * @return the $QuestionId
	 */
	public function getQuestionId() {
		if (!isset($this->QuestionId))
		{
			
			$this->QuestionId = null;
		}
        return $this->QuestionId;
	}

	/**
	 * @param field_type $QuestionId
	 */
	public function setQuestionId($QuestionId) {
		$this->QuestionId = $QuestionId;
		return $this;
	}

	/**
	 * @return the $QuestionDescription
	 */
	public function getQuestionDescription() {
		if (!isset($this->QuestionDescription))
		{
			
			$this->QuestionDescription = null;
		}	
		return $this->QuestionDescription;
	}

	/**
	 * @param field_type $QuestionDescription
	 */
	public function setQuestionDescription($QuestionDescription) {
		$this->QuestionDescription = $QuestionDescription;
		return $this;
	}

	/**
	 * @return the $DateAnswer
	 */
	public function getDateAnswer() {
		if (!isset($this->DateAnswer))
		{
			
			$this->DateAnswer = null;
		}	
		return $this->DateAnswer;
	}

	/**
	 * @param field_type $DateAnswer
	 */
	public function setDateAnswer($DateAnswer) {
		$this->DateAnswer = $DateAnswer;
		return $this;
	}

	/**
	 * @return the $NumericAnswer
	 */
	public function getNumericAnswer() {
		if (!isset($this->NumericAnswer))
		{
			
			$this->NumericAnswer = null;
		}	
		return $this->NumericAnswer;
	}

	/**
	 * @param field_type $NumericAnswer
	 */
	public function setNumericAnswer($NumericAnswer) {
		$this->NumericAnswer = $NumericAnswer;
		return $this;
	}

	/**
	 * @return the $TextualAnswer
	 */
	public function getTextualAnswer() {
		if (!isset($this->TextualAnswer))
		{
			
			$this->TextualAnswer = null;
		}	
		return $this->TextualAnswer;
	}

	/**
	 * @param field_type $TextualAnswer
	 */
	public function setTextualAnswer($TextualAnswer) {
		$this->TextualAnswer = $TextualAnswer;
		return $this;
	}

	
    

}