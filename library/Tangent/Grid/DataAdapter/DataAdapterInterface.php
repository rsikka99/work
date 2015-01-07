<?php

namespace Tangent\Grid\DataAdapter;

use Tangent\Grid\Filter\AbstractFilter;
use Tangent\Grid\Order\Column;

/**
 * Interface DataAdapterInterface
 *
 * Provides a neat interface for creating Data Adapters for grids
 *
 * @package Tangent\Grid\DataAdapter
 */
interface DataAdapterInterface
{
    /**
     * Adds a filter
     *
     * @param AbstractFilter $filter
     *
     * @return $this
     */
    public function addFilter (AbstractFilter $filter);

    /**
     * Adds an order by
     *
     * @param Column $orderBy
     *
     * @return $this
     */
    public function addOrderBy (Column $orderBy);

    /**
     * Fetches the data and returns it in an array format consumable by grids
     *
     * @return array
     */
    public function fetchAll ();

    /**
     * Gets the count of all the records available to the current **filtered** dataset
     *
     * @return int
     */
    public function count ();

    /**
     * Gets the count of all the records available to the current **un-filtered** dataset
     *
     * @return int
     */
    public function countWithoutFilter ();

    /**
     * Sets how many records will be returned
     *
     * @param int $limit
     *
     * @return $this
     */
    public function setLimit ($limit);

    /**
     * Sets the start record for fetching
     *
     * @param int $startRecord
     *
     * @return $this
     */
    public function setStartRecord ($startRecord);
}