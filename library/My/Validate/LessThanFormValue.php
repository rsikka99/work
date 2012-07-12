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
    protected $_messageTemplates = array (
            self::NOT_LESS_THAN => "'%value%' must be less than %max%" 
    );
    
    /**
     * Additional variables available for validation failure messages
     *
     * @var array
     */
    protected $_messageVariables = array (
            'max' => '_max' 
    );
    private $_validator;
    private $_elementToValidateAgainst;

    public function __construct (Zend_Form_Element $elementToBeLessThan)
    {
        $this->_elementToValidateAgainst = $elementToBeLessThan;
        $this->_validator = new Zend_Validate_LessThan(0);
    }

    /**
     * Checks a date string to ensure it matches a specific format and that strtotime will accept the string.
     *
     * @param String $value            
     * @return boolean
     */
    public function isValid ($value)
    {
        $this->_max = (float)$this->_elementToValidateAgainst->getValue();
        $this->_validator->setMax($this->_max);
        $this->_setValue($value);
        if (! $this->_validator->isValid($value))
        {
            $this->_error(self::NOT_LESS_THAN, $value);
            return false;
        }
        return true;
    }
}