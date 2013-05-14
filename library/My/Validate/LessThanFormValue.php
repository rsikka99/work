<?php

/**
 * Class My_Validate_LessThanFormValue
 * A validator for that checks to see if element1 is less than element 2
 *
 * @author "Lee Robert"
 */
class My_Validate_LessThanFormValue extends Zend_Validate_Abstract
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

    private $_elementToValidateAgainst;

    protected $_greaterThanOrEqual;
    protected $_max;

    public function __construct (Zend_Form_Element $elementToBeLessThan, $greaterThanOrEqual = false)
    {
        $this->_greaterThanOrEqual       = $greaterThanOrEqual;
        $this->_elementToValidateAgainst = $elementToBeLessThan;
    }

    /**
     * Checks a date string to ensure it matches a specific format and that strtotime will accept the string.
     *
     * @param String $value
     *
     * @return boolean
     */
    public function isValid ($value)
    {
        $this->_setValue($value);
        $this->_max = (float)$this->_elementToValidateAgainst->getValue();

        if ($this->_greaterThanOrEqual)
        {
            if ($value > (float)$this->_elementToValidateAgainst->getValue())
            {
                $this->_error(self::NOT_LESS_THAN, $value);

                return false;
            }
        }
        else
        {
            if ($value >= (float)$this->_elementToValidateAgainst->getValue())
            {
                $this->_error(self::NOT_LESS_THAN, $value);

                return false;
            }
        }

        return true;
    }
}