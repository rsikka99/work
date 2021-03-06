<?php

namespace Tangent\Validate;

/**
 * Custom_Validate_FieldDependsOnValue
 * Requires field presence based on provided value of radio element.
 *
 * Example would be radio element with Yes, No, Other option, followed by an "If
 * other, please explain" text area.
 *
 * IMPORTANT: For this validator to work, allowEmpty must be set to false on
 * the child element being validated.
 *
 * From Zend Framework Documentation 15.3: "By default, when an
 * element is required, a flag, 'allowEmpty', is also true. This means that if
 * a value evaluating to empty is passed to isValid(), the validators will be
 * skipped. You can toggle this flag using the accessor setAllowEmpty($flag);
 * when the flag is false, then if a value is passed, the validators will still
 *
 * @author "Lee Robert"
 * @uses   Zend_Validate_Abstract
 */
class FieldDependsOnValue extends \Zend_Validate_Abstract
{

    /**
     * Validation failure message key for when the value of the parent field is an empty string
     */
    const KEY_NOT_FOUND = 'keyNotFound';

    /**
     * Validation failure message key for when the value is an empty string
     */
    const KEY_IS_EMPTY     = 'keyIsEmpty';
    const VALIDATOR_FAILED = 'validatorFailed';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = [
        self::VALIDATOR_FAILED => '%customError%',
        self::KEY_NOT_FOUND    => 'You must select an option',
        self::KEY_IS_EMPTY     => 'Based on your previous answer, this field is required'
    ];

    protected $_messageVariables  = [
        'customError' => 'customErrorMessage',
    ];
    protected $customErrorMessage = "Please ensure to enter valid data";
    /**
     * Key to test against
     *
     * @var string|array
     */
    protected $_contextKey;

    /**
     * String to test for
     *
     * @var string
     */
    protected $_testValue;


    /**
     * @var \Zend_Validate[]
     */
    protected $_validatorsToRun;

    /**
     * FieldDependsOnValue constructor
     *
     * @param string           $contextKey Name of parent field to test against
     * @param string           $testValue  Value of multi option that, if selected, child field required
     * @param \Zend_Validate[] $validatorsToRun
     */
    public function __construct ($contextKey, $testValue = null, $validatorsToRun = null)
    {
        $this->setTestValue($testValue);
        $this->setContextKey($contextKey);
        $this->setValidatorsToRun($validatorsToRun);
    }

    /**
     * Defined by Zend_Validate_Interface
     *
     * Wrapper around doValid()
     *
     * @param  string $value
     * @param  array  $context
     *
     * @return boolean
     */
    public function isValid ($value, $context = null)
    {
        $contextKey = $this->getContextKey();
        $isValid    = true;
        // If context key is an array, doValid for each context key
        if (is_array($contextKey))
        {
            foreach ($contextKey as $ck)
            {
                $this->setContextKey($ck);
                if (!$this->doValid($value, $context))
                {
                    $isValid = false;
                    break;
                }
            }
        }
        else
        {
            if (!$this->doValid($value, $context))
            {
                $isValid = false;
            }
        }

        return $isValid;
    }

    /**
     * Returns true if dependant field value is not empty when parent field value
     * indicates that the dependant field is required
     *
     * @param  string $value
     * @param  array  $context
     *
     * @return boolean
     */
    public function doValid ($value, $context = null)
    {
        $testValue  = $this->getTestValue();
        $contextKey = $this->getContextKey();
        $value      = (string)$value;
        $this->_setValue($value);

        // Make sure the parent element exists?
        if ((null === $context) || !is_array($context) || !array_key_exists($contextKey, $context))
        {
//            $this->_error(self::KEY_NOT_FOUND);

            return true;
        }
        if (is_array($context [$contextKey]))
        {
            $parentField = $context [$contextKey] [0];
        }
        else
        {
            $parentField = $context [$contextKey];
        }
        if ($testValue)
        {

            if ($testValue == ($parentField))
            {
                foreach ($this->_validatorsToRun as $validator)
                {
                    if (!$validator->isValid($value, $context))
                    {
                        foreach ($validator->getMessages() as $key => $message)
                        {
                            $this->customErrorMessage = $message;
                            $this->_error(self::VALIDATOR_FAILED);
                            break;
                        }

                        return false;
                    }
                }
            }
        }
        else
        {
            $notEmpty = new \Zend_Validate_NotEmpty();
            if ($notEmpty->isValid($parentField))
            {
                foreach ($this->_validatorsToRun as $validator)
                {
                    if (!$validator->isValid($value, $context))
                    {
                        foreach ($validator->getMessages() as $message)
                        {
                            $this->_error(self::VALIDATOR_FAILED);
                            $this->setMessage($message);
                            break;
                        }

                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * @return string
     */
    protected function getContextKey ()
    {
        return $this->_contextKey;
    }

    /**
     * @param string $contextKey
     */
    protected function setContextKey ($contextKey)
    {
        $this->_contextKey = $contextKey;
    }

    /**
     * @return string
     */
    protected function getTestValue ()
    {
        return $this->_testValue;
    }

    /**
     * @param string $testValue
     */
    protected function setTestValue ($testValue)
    {
        $this->_testValue = $testValue;
    }

    /**
     * @return \Zend_Validate[]
     */
    public function getValidatorsToRun ()
    {
        if (!isset($this->_validatorsToRun))
        {
            $this->_validatorsToRun = null;
        }

        return $this->_validatorsToRun;
    }

    /**
     * @param \Zend_Validate[] $_validatorsToRun
     *
     * @return $this
     */
    public function setValidatorsToRun ($_validatorsToRun)
    {
        $this->_validatorsToRun = $_validatorsToRun;

        return $this;
    }

}