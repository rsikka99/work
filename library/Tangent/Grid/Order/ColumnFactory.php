<?php

namespace Tangent\Grid\Order;

/**
 * Class ColumnFactory
 *
 * @package Tangent\Grid\Order
 */
class ColumnFactory
{
    /**
     * @var string[]
     */
    protected $validColumns = array();

    /**
     * @param string[] $validColumns
     */
    public function __construct ($validColumns)
    {
        if (is_array($validColumns))
        {
            foreach ($validColumns as $columnName)
            {
                if (!isset($this->validColumns[strtolower($columnName)]))
                {
                    // We use hashes for faster look ups
                    $this->validColumns[strtolower($columnName)] = $columnName;
                }
            }
        }
    }

    /**
     * Checks to see if a column is valid to order by
     *
     * @param $columnName
     *
     * @return bool
     */
    public function columnIsValid ($columnName)
    {
        return (isset($this->validColumns[strtolower($columnName)]));
    }

    /**
     * @param string $columnName
     * @param int    $direction
     *
     * @return bool|Column
     */
    public function make ($columnName, $direction)
    {
        if ($this->columnIsValid($columnName))
        {
            return new Column($this->validColumns[strtolower($columnName)], $direction);
        }

        return false;
    }
}