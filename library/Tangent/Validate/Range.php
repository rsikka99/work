<?php

/**
 * validators_ValidateRange - Custom validater for a range of numbers
 *
 * @author     Mike Christie
 * @version    v1.0
 */

/**
 * Validates a number range.
 */
class Tangent_Validate_Range extends Zend_Validate_Abstract
{
    const NUMBER_OUT_OF_RANGE = 'outOfRange';
    const NUMBER_ALREADY_USED = 'inUse';

    protected $_messageTemplates = array(
        self::NUMBER_OUT_OF_RANGE => 'Can contain digits 1 through 5 inclusive.',
        self::NUMBER_ALREADY_USED => 'Can only use digits 1 through 5 once.'
    );

    public function isValid ($value)
    {
        if ($value < 1 || $value > 5)
        {
            $this->_error(self::NUMBER_OUT_OF_RANGE);

            return false;
        } // end if


        return true;
    } // end isValid
} // end class 
?>