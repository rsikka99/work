<?php

namespace Tangent\Grid\Response;

/**
 * Interface ResponseInterface
 *
 * Provides a neat interface for creating Response Adapters for grids
 *
 * @package Tangent\Grid\Response
 */
interface ResponseInterface
{
    /**
     * Returns an array that can be JSONified and sent back to the client
     *
     * @return array
     */
    public function getResponseForGrid ();

    /**
     * Simple getter for data
     *
     * @return array
     */
    public function getData ();

    /**
     * Sets the data
     *
     * @param array $data The actual data to be presented in the grid.
     *
     * @return $this
     */
    public function setData ($data);

    /**
     * Simple getter for total records
     *
     * @return int
     */
    public function getTotalRecords ();

    /**
     * Sets the total number of records that could be displayed to the grid
     *
     * @param int $totalRecords
     *
     * @return $this
     */
    public function setTotalRecords ($totalRecords);

    /**
     * Simple getter for total filtered records
     *
     * @return int
     */
    public function getTotalFilteredRecords ();

    /**
     * Sets the total number of records after the grids filters are applied.
     *
     * @param int $totalFilteredRecords
     *
     * @return $this
     */
    public function setTotalFilteredRecords ($totalFilteredRecords);

    /**
     * Simple getter for records per page
     *
     * @return int
     */
    public function getRecordsPerPage ();

    /**
     * Sets the number of records that are displayed per page
     *
     * @param int $recordsPerPage
     *
     * @return $this
     */
    public function setRecordsPerPage ($recordsPerPage);

    /**
     * Simple getter for start record
     *
     * @return int
     */
    public function getStartRecord ();

    /**
     * Sets the current start record index
     *
     * @param int $startRecord
     *
     * @return $this
     */
    public function setStartRecord ($startRecord);

    /**
     * Gets the current page that we're on
     *
     * @return int
     */
    public function getCurrentPage ();

    /**
     * Sets the current page
     * (Uses records per page * current page to determine the start record)
     *
     * @param int $currentPage
     *
     * @return int
     */
    public function setCurrentPage ($currentPage);

    /**
     * Returns the total number of pages with the filter applied to the data
     *
     * @return int
     */
    public function getTotalFilteredPages ();

    /**
     * Returns the total number of pages with no filters applied to the data
     *
     * @return int
     */
    public function getTotalPages ();
}