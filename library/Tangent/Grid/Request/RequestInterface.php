<?php

namespace Tangent\Grid\Request;

use Tangent\Grid\Order\Column;

/**
 * Interface RequestInterface
 *
 * Provides a neat interface for creating Request Adapters for grids
 *
 * @package Tangent\Grid\Request
 */
interface RequestInterface
{
    /**
     * The record number to start at
     *
     * @return int
     */
    public function getStartRecord ();

    /**
     * The number of records to fetch
     *
     * @return int
     */
    public function getRecordFetchLimit ();

    /**
     * The filter information
     *
     * @return mixed
     */
    public function getFilterData ();

    /**
     * The order information
     *
     * @return Column[]
     */
    public function getOrderColumns ();

    /**
     * @param array $data
     *
     * @return $this
     */
    public function parseInput ($data);
}