<?php

class My_Form_Decorator_ColumnHeader extends Zend_Form_Decorator_Abstract
{
    public function render ($content)
    {
        $data  = $this->getOption('data');
        $class = $this->getOption('class');

        $rowData = '';
        foreach ($data as $key => $column)
        {
            $rowData .= "<th class='headerData {$class[$key]}'>" . $column . '</th>';
        }


        $output = '<tr class="headerRow control-group">' . $rowData . '</tr>';

        $placement = $this->getPlacement();

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