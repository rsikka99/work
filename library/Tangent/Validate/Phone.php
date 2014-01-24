<?php

/**
 * validators_ValidatePhone - Custom validater for a phone number field
 *
 * @author     Chris Garrah
 * @version    v1.0
 */

/**
 * Validates a phone number (very basic).
 */
class Tangent_Validate_Phone extends Zend_Validate_Abstract
{
    const PHONE_BAD_CHARS  = 'phoneBadChars';
    const PHONE_BAD_LENGTH = 'phoneBadLength';

    private $_allowedCharacters = array(
        '1',
        '2',
        '3',
        '4',
        '5',
        '6',
        '7',
        '8',
        '9',
        '0'
    );
    private $_separators = array(
        '-',
        '/',
        '.'
    );

    protected $_messageTemplates = array(
        self::PHONE_BAD_CHARS  => 'Can contain digits 0-9 and characters ". / -"',
        self::PHONE_BAD_LENGTH => "'%value%' is not a valid phone number.  Valid format is: 555-555-5555"
    );

    public function isValid ($value)
    {
        $valueString = (string)$value;
        $this->_setValue($valueString);
        $valArray = str_split($valueString);

        foreach ($valArray as $char)
        {
            if (!in_array($char, $this->_allowedCharacters) && !in_array($char, $this->_separators))
            {
                $this->_error(self::PHONE_BAD_CHARS);

                return false;
            } // end if
        } // end for
        $countStr = str_replace($this->_separators, '', $valueString);
        $len      = strlen($countStr);
        if ($len != 10 && $len != 11)
        {
            $this->_error(self::PHONE_BAD_LENGTH);

            return false;
        } // end if
        return true;
    } // end isValid
} // end class 
?>