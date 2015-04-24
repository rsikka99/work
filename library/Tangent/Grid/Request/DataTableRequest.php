<?php

namespace Tangent\Grid\Request;

use Tangent\Grid\Order\Column;
use Tangent\Grid\Order\ColumnFactory;

/**
 * Class DataTableRequest
 *
 * @package Tangent\Grid\Request
 */
class DataTableRequest implements RequestInterface
{
    const SORT_ASCENDING  = 'asc';
    const SORT_DESCENDING = 'desc';

    /**
     * @var Column[]
     */
    protected $orderColumns = [];

    /**
     * @var \Tangent\Grid\Filter\AbstractFilter[]
     */
    protected $filters = [];

    /**
     * @var int
     */
    protected $page;

    /**
     * @var int
     */
    protected $rows;

    /**
     * @var ColumnFactory
     */
    protected $columnFactory;

    /**
     * @var int
     */
    protected $requestIdentifier;

    /**
     * @param array         $data
     * @param ColumnFactory $columnFactory
     * @param int           $page
     * @param int           $rows
     */
    public function __construct ($data, ColumnFactory $columnFactory, $page = 1, $rows = 10)
    {
        $this->columnFactory = $columnFactory;

        if ($page >= 1)
        {
            $this->page = $page;
        }

        if ($rows > 1)
        {
            $this->rows = $rows;
        }

        // Do this last so that we have everything needed before parsing
        $this->parseInput($data);
    }

    /**
     * The record number to start at
     *
     * @return int
     */
    public function getStartRecord ()
    {
        return $this->page * $this->rows - $this->rows;
    }

    /**
     * The number of records to fetch
     *
     * @return int
     */
    public function getRecordFetchLimit ()
    {
        return $this->rows;
    }

    /**
     * The filter information
     *
     * @return \Tangent\Grid\Filter\AbstractFilter[]
     */
    public function getFilterData ()
    {
        return $this->filters;
    }

    /**
     * The order information
     *
     * @return Column[]
     */
    public function getOrderColumns ()
    {
        return $this->orderColumns;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function parseInput ($data)
    {
        if (is_array($data))
        {
            $data = new \ArrayObject($data, \ArrayObject::ARRAY_AS_PROPS);
        }

        /**
         * Sort columns and directions
         */
        foreach ($data->order as $order)
        {
            $column = $this->columnFactory->make($data->columns[$order['column']]['data'], strcasecmp($order['dir'], 'asc') === 0 ? Column::ORDER_ASCENDING : Column::ORDER_DESCENDING);
            if ($column instanceof Column)
            {
                $this->orderColumns[] = $column;
            }
        }

        /**
         * Paging
         */
        if (isset($data->length) && !is_null($data->length))
        {
            $this->rows = (int)$data->length;
        }

        if (isset($data->start) && !is_null($data->start))
        {
            $this->page = (int)$data->start / $this->rows + 1;
        }


        /**
         * Draw
         */
        if (isset($data->draw) && !is_null($data->draw))
        {
            $this->setRequestIdentifier($data->draw);
        }
    }

    /**
     * Getter for requestIdentifier
     *
     * @return int
     */
    public function getRequestIdentifier ()
    {
        return $this->requestIdentifier;
    }

    /**
     * Setter for requestIdentifier
     *
     * @param int $requestIdentifier
     */
    public function setRequestIdentifier ($requestIdentifier)
    {
        $this->requestIdentifier = (int)$requestIdentifier;
    }
}