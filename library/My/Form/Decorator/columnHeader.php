<?php

class My_Form_Decorator_ColumnHeader extends Zend_Form_Decorator_Abstract
{
    public function render ($content)
    {
        $data = $this->getOption('data');

        $rowData = '';
        foreach ($data as $column)
        {
            $rowData .= '<td class="headerData">' . $column . '</td>';
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