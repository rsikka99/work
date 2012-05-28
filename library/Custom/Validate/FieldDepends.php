<?php

/**
 * Custom_Validate_FieldDepends
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

 * @author "Lee Robert"
 * @uses Zend_Validate_Abstract
 */
class Custom_Validate_FieldDepends extends Custom_Validate_FieldDependsOnValue
{
    /**
     * FieldDepends constructor
     *
     * @param string $contextKey Name of parent field to test against
     * @param string $testValue Value of multi option that, if selected, child field required
     */
    public function __construct ($contextKey, $validatorsToRun = null)
    {
        parent::__construct($contextKey, null, $validatorsToRun);
    }
}