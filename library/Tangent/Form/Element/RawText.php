<?php

/* 
 * Rawtext element can be used to insert raw text into the middle of
 * a zend form.
 *
 * Example of how to use this from inside zend form:
 *       $users->addPrefixPath('Element', 'Element/', 'element');
 *       $users->addElement('RawText', 'foo', array(
 *       'value' => '<p>'."TEXT TO INSERT INTO FORM".'</p><br />',
 *       ));
 */
class Tangent_Form_Element_RawText extends Zend_Form_Element
{

    public function render (Zend_View_Interface $view = null)
    {
        return $this->getValue();
    }

    public function isValid ($value, $context = null)
    {
        return true;
    }
} // end class
?>

