<?php

/*
 * Note element can be used to insert raw text into the middle of
 * a zend form.
 *
 * Example of how to use this from inside zend form:
 *      $users->addPrefixPath('Element', 'Element/', 'element');
 *      $users->addElement(
 *               'Note',
 *               'myElementId',
 *               array(
 *                'value'=>'<p>'.'TEXT TO INSERT INTO FORM'.'</p>',
 *                'disableLoadDefaultDecorators' => true,
 *               ));
 *
 *      $users->getElement('myElementId')->setDecorators(array(
 *                                                   'ViewHelper'));
 */
class Tangent_Form_Element_Note extends Zend_Form_Element_Xhtml
{
    /**
     * Default form view helper to use for rendering
     * @var string
     */
    public $helper = 'formNote';

    public function isValid ($value, $context = null)
    {
        return TRUE;
    }
} // end class Element_Note
?>
