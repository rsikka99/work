<?php

class My_Form_Decorator_MoreInformation extends Zend_Form_Decorator_Abstract
{
    public function render ($content)
    {
        // Append is default - Prepend
        $placement = $this->getPlacement();

        $text = $this->getOption('text');
        $output    = "<p class='more_information>" . $text . "</p>";

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