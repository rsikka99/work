<?php

namespace Tangent\Grid\Order;

/**
 * Class Column
 *
 * @package Tangent\Grid\Order
 */
class Column
{
    const ORDER_ASCENDING  = 0;
    const ORDER_DESCENDING = 1;

    /**
     * @var string
     */
    protected $columnName;

    /**
     * @var int
     */
    protected $direction;

    /**
     * @param string $columnName
     * @param int    $direction The sort direction. 0 being ascending and 1 being descending
     */
    public function __construct ($columnName, $direction = self::ORDER_ASCENDING)
    {
        $this->columnName = $columnName;
        $this->direction  = ($direction === self::ORDER_ASCENDING) ? self::ORDER_ASCENDING : self::ORDER_DESCENDING;
    }

    /**
     * Gets the column name to order by
     *
     * @return string
     */
    public function getColumnName ()
    {
        return $this->columnName;
    }

    /**
     * Whether or not we should sort ascending
     *
     * @return bool
     */
    public function orderIsAscending ()
    {
        return ($this->direction === self::ORDER_ASCENDING);
    }
}