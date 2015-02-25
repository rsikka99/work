<?php

namespace Tangent\Grid\DataAdapter;

use Illuminate\Database\Query\Builder;
use Tangent\Grid\Filter\AbstractFilter;
use Tangent\Grid\Filter\Contains;
use Tangent\Grid\Filter\DoesNotContain;
use Tangent\Grid\Filter\Is;
use Tangent\Grid\Filter\IsNot;
use Tangent\Grid\Order\Column;

/**
 * Interface EloquentAdapter
 *
 * A wrapper around eloquent for grid functions
 *
 * @package Tangent\Grid\DataAdapter
 */
class EloquentAdapter implements DataAdapterInterface
{
    /**
     * @var Builder
     */
    protected $originalQuery;

    /**
     * @var Builder
     */
    protected $constructedQuery;

    /**
     * @var AbstractFilter[]
     */
    protected $filters = [];

    /**
     * @var Column[]
     */
    protected $orderBy = [];

    /**
     * @var Column[]
     */
    protected $defaultOrderBy = [];

    /**
     * @var int
     */
    protected $startRecord = 0;

    /**
     * @var int
     */
    protected $limit = 10;

    /**
     * Constructor for EloquentAdapter
     *
     * @param Builder  $query
     * @param Column[] $defaultOrder
     */
    public function __construct (Builder $query, $defaultOrder = [])
    {
        $this->originalQuery = $query;

        if (count($defaultOrder) > 0)
        {
            foreach ($defaultOrder as $columnName => $column)
            {
                $this->defaultOrderBy[$columnName] = $column;
            }
        }
    }

    public function getConstructedQuery ()
    {
        if (!isset($this->constructedQuery))
        {
            /**
             * We clone the original query so that we can make modifications later
             */
            $this->constructedQuery = clone $this->originalQuery;

            /**
             * Where/Filters
             */
            foreach ($this->filters as $filter)
            {
                if ($filter instanceof Is)
                {
                    $this->constructedQuery->where($filter->getFilterIndex(), '=', $filter->getFilterValue());
                }
                else if ($filter instanceof IsNot)
                {
                    $this->constructedQuery->where($filter->getFilterIndex(), '!=', $filter->getFilterValue());
                }
                else if ($filter instanceof Contains)
                {
                    $this->constructedQuery->where($filter->getFilterIndex(), 'LIKE', $filter->getFilterValue());
                }
                else if ($filter instanceof DoesNotContain)
                {
                    $this->constructedQuery->where($filter->getFilterIndex(), 'NOT LIKE', $filter->getFilterValue());
                }
            }

            /**
             * Order By
             */
            foreach ($this->getOrderBy() as $columnName => $orderBy)
            {
                $this->constructedQuery->orderBy($orderBy->getColumnName(), $orderBy->orderIsAscending() ? 'ASC' : 'DESC');
            }
        }

        return $this->constructedQuery;
    }

    /**
     * Resets the constructed query.
     * Usually called when modifying options such as orders and filters.
     *
     * @return $this
     */
    protected function resetConstructedQuery ()
    {
        if (isset($this->constructedQuery))
        {
            unset($this->constructedQuery);
        }

        return $this;
    }

    /**
     * Adds a filter
     *
     * @param \Tangent\Grid\Filter\AbstractFilter $filter
     *
     * @return $this
     */
    public function addFilter (AbstractFilter $filter)
    {
        $this->filters[] = $filter;

        $this->resetConstructedQuery();

        return $this;
    }

    /**
     * Adds an order by
     *
     * @param \Tangent\Grid\Order\Column $orderBy
     *
     * @return $this
     */
    public function addOrderBy (Column $orderBy)
    {
        $this->orderBy[$orderBy->getColumnName()] = $orderBy;

        $this->resetConstructedQuery();

        return $this;
    }

    /**
     * Gets the order by. Returns a default setting if no orders were added
     *
     * @return \Tangent\Grid\Order\Column[]
     */
    public function getOrderBy ()
    {
        return (count($this->orderBy) > 0) ? $this->orderBy : $this->defaultOrderBy;
    }

    /**
     * Resets the order by
     *
     * @return $this
     */
    public function resetOrderBy ()
    {
        $this->orderBy = [];

        return $this;
    }

    /**
     * Fetches the data and returns it in an array format consumable by grids
     *
     * @return array
     */
    public function fetchAll ()
    {
        return $this->getConstructedQuery()->limit($this->limit)->skip($this->startRecord)->get();
    }

    /**
     * Gets the count of all the records available to the current **filtered** data set
     *
     * @return int
     */
    public function count ()
    {
        return $this->getConstructedQuery()->count();
    }

    /**
     * Gets the count of all the records available to the current **un-filtered** data set
     *
     * @return int
     */
    public function countWithoutFilter ()
    {
        return $this->originalQuery->count();
    }

    /**
     * Sets how many records will be returned
     *
     * @param int $limit
     *
     * @return $this
     */
    public function setLimit ($limit)
    {
        $this->limit = (int)$limit;

        $this->resetConstructedQuery();

        return $this;
    }

    /**
     * Sets the start record for fetching
     *
     * @param int $startRecord
     *
     * @return $this
     */
    public function setStartRecord ($startRecord)
    {
        $this->startRecord = (int)$startRecord;

        $this->resetConstructedQuery();

        return $this;
    }
}