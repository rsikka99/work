<?php

class My_Form_Decorator_AddRowData extends Zend_Form_Decorator_Abstract
{
    public function render ($content)
    {
        $header  = $this->getOption('header');
        $trClass = $this->getOption('trClass');
        $trAttr  = $this->getOption('trAttr');
        $tdClass = $this->getOption('tdClass');
        $tdAttr  = $this->getOption('tdAttr');

        $output = "<tr {$trAttr} class='{$trClass}'><td {$tdAttr} class='${tdClass}'>{$header}</td></tr>";

        switch ($this->getPlacement())
        {
            case 'PREPEND':
                return $output . $content;
            case 'APPEND':
            default:
                return $content . $output;
        }
    }
}