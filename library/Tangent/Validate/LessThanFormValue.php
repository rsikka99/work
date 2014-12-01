<?php

/**
 * Class My_Validate_LessThanFormValue
 * A validator for that checks to see if element1 is less than element 2
 *
 * @author "Lee Robert"
 */
class Tangent_Validate_LessThanFormValue extends Zend_Validate_Abstract
{
    const NOT_LESS_THAN = 'not_less_than';
    protected $_messageTemplates = array(
        self::NOT_LESS_THAN => "'%value%' must be less than %max%"
    );

    /**
     * Additional variables available for validation failure messages
     *
     * @var array
     */
    protected $_messageVariables = array(
        'max' => '_max'
    );

    /**
     * @var string Element name
     */
    private $_elementToValidateAgainst;

    protected $_greaterThanOrEqual;
    protected $_max;

    public function __construct ($elementToBeLessThan, $greaterThanOrEqual = false)
    {
        $this->_greaterThanOrEqual       = $greaterThanOrEqual;
        $this->_elementToValidateAgainst = $elementToBeLessThan;
    }

    /**
     * Checks a date string to ensure it matches a specific format and that strtotime will accept the string.
     *
     * @param String $value
     * @param null   $context
     *
     * @return boolean
     */
    public function isValid ($value, $context = null)
    {
        if (is_null($context) || !isset($context[$this->_elementToValidateAgainst]))
        {
            $this->_error(self::NOT_LESS_THAN, $value);
        }

        $elementToTestAgainstValue = (float)$context[$this->_elementToValidateAgainst];

        $this->_setValue($value);

        $this->_max = $elementToTestAgainstValue;

        if ($this->_greaterThanOrEqual)
        {
            if ($value > $elementToTestAgainstValue)
            {
                $this->_error(self::NOT_LESS_THAN, $value);

                return false;
            }
        }
        else
        {
            if ($value >= $elementToTestAgainstValue)
            {
                $this->_error(self::NOT_LESS_THAN, $value);

                return false;
            }
        }

        return true;
    }
}