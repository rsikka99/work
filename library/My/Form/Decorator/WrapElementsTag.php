<?php

class My_Form_Decorator_WrapElementsTag extends Zend_Form_Decorator_Abstract
{
    public function render ($content)
    {
        $element     = $this->getOption('elementName');
        $tagPosition = $this->getOption('position');
        $className   = $this->getOption('className');

         if ($tagPosition == "open")
        {
            $output    = "<{$element} class=\"{$className}\" >";
            $placement = 'PREPEND';
        }
        elseif ($tagPosition == "close")
        {
            $output    = "</{$element}>";
            $placement = 'APPEND';
        }

        switch ($placement)
        {
            case 'PREPEND':
                return $output . $content;
            case 'APPEND':
            default:
                return $content . $output;
        }
    }
}