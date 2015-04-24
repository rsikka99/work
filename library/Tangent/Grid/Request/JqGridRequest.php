<?php

namespace Tangent\Grid\Request;

use Tangent\Grid\Filter\Search;
use Tangent\Grid\Order\Column;
use Tangent\Grid\Order\ColumnFactory;

/**
 * Class JqGridRequest
 *
 * @package Tangent\Grid\Request
 */
class JqGridRequest implements RequestInterface
{
    const SORT_ASCENDING  = 'asc';
    const SORT_DESCENDING = 'desc';

    /**
     * @var Column[]
     */
    protected $orderColumns = [];

    /**
     * @var Search[]
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
     * @return Search[]
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
         * Sort Column and Directions
         */
        if (isset($data->sidx) && !is_null($data->sidx) && isset($data->sord) && !is_null($data->sord))
        {
            // 0 - Sort Column Name
            // 1 - Sort Direction ['asc', 'desc']
            $sortIndexSections = explode(', ', $data->sidx . ' ' . $data->sord);

            foreach ($sortIndexSections as $sortIndex)
            {
                $sortData = explode(' ', trim($sortIndex));
                if (strcasecmp(trim($sortData[1]), self::SORT_ASCENDING) === 0)
                {
                    $column = $this->columnFactory->make($sortData[0], Column::ORDER_ASCENDING);
                    if ($column instanceof Column)
                    {
                        $this->orderColumns[] = $column;
                    }
                }
                else if (strcasecmp(trim($sortData[1]), self::SORT_DESCENDING) === 0)
                {
                    $column = $this->columnFactory->make($sortData[0], Column::ORDER_DESCENDING);
                    if ($column instanceof Column)
                    {
                        $this->orderColumns[] = $column;
                    }
                }
            }
        }

        if (isset($data->page) && !is_null($data->page))
        {
            $this->page = (int)$data->page;
        }

        if (isset($data->rows) && !is_null($data->rows))
        {
            $this->rows = (int)$data->rows;
        }
    }
}