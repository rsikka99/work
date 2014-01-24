<?php

/**
 * Class My_Validate_DateTime
 * A validator for DateTime strings.
 * Defaults to check the format of YYYY/MM/DD HH:SS and that the format will work with strtotime.
 *
 * @author "Lee Robert"
 */
class My_Validate_DateTime extends Zend_Validate_Abstract
{

    const INVALID_DATE_FORMAT = 'invalid_date_format';

    protected $_messageTemplates = array(
        self::INVALID_DATE_FORMAT => "'%value%' is not a valid date format"
    );

    private $_validator;

    public function __construct ($regex = '/\d{4}-\d{2}-\d{2} \d{2}(:\d{2}){1,2}/')
    {
        $this->_validator = new Zend_Validate_Regex(array(
            'pattern' => $regex
        ));
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
        if (!($this->_validator->isValid($value) && (bool)strtotime($value)))
        {
            $this->_error(self::INVALID_DATE_FORMAT);

            return false;
        }

        return true;
    }

}