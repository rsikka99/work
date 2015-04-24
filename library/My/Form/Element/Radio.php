<?php

/**
 * Form Element for adding text to a form
 *
 * @author     Lee Robert
 * @category   My
 * @package    My_Form
 * @subpackage Element
 */
class My_Form_Element_Radio extends Zend_Form_Element_Radio
{

    /**
     * Render a single radio button
     *
     * @param Zend_View_Interface $view
     * @param string              $radio_key_value
     *
     * @return string
     */
    public function renderValue (Zend_View_Interface $view = null, $radio_key_value)
    {
        if (!isset($this->options [$radio_key_value]))
        {
            throw new Zend_Form_Element_Exception('Cannot render non-existing radio value');
        }

        if (null !== $view)
        {
            $this->setView($view);
        }

        $all_options   = $this->options;
        $this->options = [
            $radio_key_value => $this->options [$radio_key_value]
        ];

        $view    = $this->getView();
        $content = '';
        foreach ($this->getDecorators() as $decorator)
        {
            if ($decorator instanceof Zend_Form_Decorator_ViewHelper)
            {
                $decorator->setElement($this);
                $content = $decorator->render($content);
            }
        }

        $this->options = $all_options;

        return $content;
    }
}
