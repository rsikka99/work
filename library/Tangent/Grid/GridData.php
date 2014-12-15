<?php

namespace Tangent\Grid;

/**
 * Class GridData
 *
 * This class handles storing data that is used with the grids. Requests will
 * generally populate some of this data and Responses will generate their
 * response using this data
 *
 * @package Tangent\Grid
 */
class GridData
{
    /**
     * The data we're dealing with
     *
     * @var array|mixed
     */
    protected $rows;

    /**
     * The total number of rows available to the grid
     *
     * @var int
     */
    protected $totalCount;

    /**
     * The total number of rows available after filtering has taken place. This
     * should be the same as total count if no filters have been applied.
     *
     * @var int
     */
    protected $totalFilteredCount;

    /**
     * The row we're starting on
     *
     * @var int
     */
    protected $startRow;

    /**
     * How many rows we have
     *
     * @var int
     */
    protected $rowsPerPage;

    /**
     * The page we're currently on
     *
     * @var int
     */
    protected $currentPage;

    /**
     * This originated from jQuery DataTables. They use a request identifier
     * to match up ajax responses with the requests
     *
     * @var string|int
     */
    protected $requestIdentifier;
}