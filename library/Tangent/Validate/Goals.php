<?php

namespace Tangent\Validate;

    /**
     * validators_ValidateGoals - Custom validater for validating goals.
     *
     * @author     Mike Christie
     * @version    v1.0
     */

/**
 *
 */
class Goals extends \Zend_Validate_Abstract
{
    const NUMBER_ALREADY_USED = 'inUse';

    protected $_messageTemplates = array(
        self::NUMBER_ALREADY_USED => 'Can only use digits 1 through 5 once.'
    );

    public function isValid ($value)
    {
        $this->_error(self::NUMBER_ALREADY_USED);

        return false;
    } // end isValid
} // end class 
?>